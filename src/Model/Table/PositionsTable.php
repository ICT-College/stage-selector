<?php

namespace App\Model\Table;

use Cake\Database\Expression\Comparison;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\Table;

class PositionsTable extends Table
{

    public $filterArgs = array(
        'company_id' => array(
            'type' => 'value'
        ),
        'company_name' => array(
            'type' => 'like',
            'field' => 'Companies.name'
        ),
        'company_house_number' => array(
            'type' => 'like',
            'field' => 'Companies.address'
        ),
        'company_street' => array(
            'type' => 'like',
            'field' => 'Companies.address'
        ),
        'company_address' => array(
            'type' => 'like',
            'field' => 'Companies.address'
        ),
        'company_postcode' => array(
            'type' => 'value',
            'field' => 'Companies.postcode'
        ),
        'company_city' => array(
            'type' => 'like',
            'field' => 'Companies.city'
        ),
        'company_country' => array(
            'type' => 'value',
            'field' => 'Companies.country'
        ),
        'radius' => array(
            'type' => 'finder',
            'finder' => 'Radius',
            'field' => 'Companies.coordinates'
        ),
        'study_program_id' => array(
            'type' => 'value'
        ),
        'learning_pathway' => array(
            'type' => 'finder',
            'finder' => 'OrValue',
            'or' => [
                'BOL' => 'GV',
                'BBL' => 'GV'
            ]
        ),
        'description' => array(
            'type' => 'like'
        ),
    );

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Searchable');

        $this->belongsTo('Companies');
        $this->belongsTo('StudyPrograms');
    }

    public function findRadius(Query $query, array $options)
    {
        $otherComparisons = [];

        // Loop through the query parts
        $query->traverse(function ($value, $clause) use (&$otherComparisons) {
            // Ignore all non where clauses
            if ($clause !== 'where') {
                return;
            }

            /**
             * @var QueryExpression $value
             */

            // Loop through conditions
            $value->traverse(function (Comparison $comparison) use (&$otherComparisons) {
                switch ($comparison->getField()) {
                    // Add address related conditions to $addressComparisons
                    case 'Companies.address':
                    case 'Companies.postcode':
                    case 'Companies.city':
                    case 'Companies.country':
                        break;

                    // Add other conditions to $otherComparisons
                    default:
                        $otherComparisons[] = $comparison;
                }
            });
        });

        // Override the query conditions with the non address conditions
        $query->where($otherComparisons, [], true);

        $query->matching('Companies', function (Query $query) use ($options) {
            $radiusOptions = [
                'radius' => $options['radius'],
                'field' => $options['field']
            ];

            // Convert position address fields to company address fields
            if (isset($options['company_address'])) {
                $radiusOptions['address'] = $options['company_address'];
            }
            if (isset($options['company_postcode'])) {
                $radiusOptions['postcode'] = $options['company_postcode'];
            }
            if (isset($options['company_city'])) {
                $radiusOptions['city'] = $options['company_city'];
            }
            if (isset($options['company_country'])) {
                $radiusOptions['country'] = $options['company_country'];
            }

            $query->find('radius', $radiusOptions);

            return $query;
        });
    }

    public function findOrValue(Query $query, array $options)
    {
        if (isset($options['learning_pathway']) && isset($options['field']['or'][$options['learning_pathway']])) {
            $query->where([
                'learning_pathway IN' => [
                    $options['field']['or'][$options['learning_pathway']],
                    $options['learning_pathway']
                ]
            ]);
        } else {
            $query->where(['learning_pathway' => $options['learning_pathway']]);
        }

        return $query;
    }
}
