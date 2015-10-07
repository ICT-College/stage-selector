<?php

namespace App\Model\Table;

use Cake\Database\Expression\Comparison;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\Table;

class PositionsTable extends Table
{

    public $filterArgs = [
        'company_id' => [
            'type' => 'value'
        ],
        'company_name' => [
            'type' => 'like',
            'field' => 'Companies.name'
        ],
        'company_house_number' => [
            'type' => 'like',
            'field' => 'Companies.address'
        ],
        'company_street' => [
            'type' => 'like',
            'field' => 'Companies.address'
        ],
        'company_address' => [
            'type' => 'like',
            'field' => 'Companies.address'
        ],
        'company_postcode' => [
            'type' => 'value',
            'field' => 'Companies.postcode'
        ],
        'company_city' => [
            'type' => 'like',
            'field' => 'Companies.city'
        ],
        'company_country' => [
            'type' => 'value',
            'field' => 'Companies.country'
        ],
        'radius' => [
            'type' => 'finder',
            'finder' => 'Radius',
            'field' => 'Companies.coordinates'
        ],
        'study_program_id' => [
            'type' => 'value'
        ],
        'learning_pathway' => [
            'type' => 'finder',
            'finder' => 'OrValue',
            'or' => [
                'BOL' => 'GV',
                'BBL' => 'GV'
            ]
        ],
        'description' => [
            'type' => 'like'
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Searchable');

        $this->belongsTo('Companies');
        $this->belongsTo('StudyPrograms');
    }

    /**
     * Finds positions in the provided ranges
     *
     * @param Query $query The query to apply conditions to
     * @param array $options A set of options used by the finder
     *
     * @return void
     */
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

    /**
     * A finder which is able to find multiple values according to the value.
     * For example you have configured it like:
     * [
     *  'type' => 'finder',
     *  'finder' => 'OrValue',
     *  'or' => [
     *      'BOL' => 'GV',
     *      'BBL' => [ 'GV', 'AB' ]
     *   ]
     * ]
     * When the value is "BOL" it will search the values BOL or GV.
     * But when the value is BBL, it will search for BBL, GV or AB.
     * When no values match the item in the array it will perform a normal value find.
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findOrValue(Query $query, array $options)
    {
        if (isset($options[$options['field']['name']]) &&
            isset($options['field']['or'][$options[$options['field']['name']]])) {

            if (is_array($options[$options['field']['name']])) {
                $query->where([
                    $options['field']['field'] . ' IN' => [
                        $options['field']['or'][$options[$options['field']['name']]],
                    ] + $options[$options['field']['name']]
                ]);
            } else {
                $query->where([
                    $options['field']['field'] . ' IN' => [
                        $options['field']['or'][$options[$options['field']['name']]],
                        $options[$options['field']['name']]
                    ]
                ]);
            }
        } else {
            $query->where([$options['field']['field'] => $options[$options['field']['name']]]);
        }

        return $query;
    }
}
