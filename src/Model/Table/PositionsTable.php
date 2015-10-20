<?php

namespace App\Model\Table;

use Cake\Database\Expression\Comparison;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\ExpressionInterface;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Search\Manager;
use Search\Type\Callback;

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
        $this->addBehavior('Search.Search');

        $this->belongsTo('Companies');
        $this->belongsTo('StudyPrograms');
    }

    public function searchConfiguration()
    {
        $search = new Manager($this);
        $search->value('company_id', [
            'field' => $this->aliasField('company_id')
        ]);
        $search->like('company_name', [
            'before' => true,
            'after' => true,
            'field' => 'Companies.name'
        ]);
        $search->like('company_house_number', [
            'before' => true,
            'after' => true,
            'field' => 'Companies.address'
        ]);
        $search->like('company_street', [
            'before' => true,
            'after' => true,
            'field' => 'Companies.address'
        ]);
        $search->like('company_address', [
            'before' => true,
            'after' => true,
            'field' => 'Companies.address'
        ]);
        $search->value('company_postcode', [
            'field' => 'Companies.postcode'
        ]);
        $search->like('company_city', [
            'before' => true,
            'after' => true,
            'field' => 'Companies.city'
        ]);
        $search->value('company_country', [
            'field' => 'Companies.country'
        ]);
        $search->value('study_program_id', [
            'field' => $this->aliasField('study_program_id')
        ]);
        $search->callback('learning_pathway', [
            'callback' => function (Query $query, array $args, Callback $searchType) {
                return $query->find('orValue', [
                    'value' => $args[$searchType->name()],
                    'field' => $searchType->name(),
                    'or' => [
                        'BOL' => 'GV',
                        'BBL' => 'GV'
                    ]
                ]);
            }
        ]);
        $search->like('description', [
            'before' => true,
            'after' => true,
            'field' => $this->aliasField('description')
        ]);
        $search->callback('radius', [
            'callback' => function (Query $query, array $args, Callback $searchType) {
                $options = [
                    'radius' => $args[$searchType->name()],
                    'field' => 'Companies.coordinates'
                ];

                if (isset($args['company_address'])) {
                    $options['address'] = $args['company_address'];
                }
                if (isset($args['company_postcode'])) {
                    $options['postcode'] = $args['company_postcode'];
                }
                if (isset($args['company_city'])) {
                    $options['city'] = $args['company_city'];
                }
                if (isset($args['company_country'])) {
                    $options['country'] = $args['company_country'];
                }

                return $query->find('radius', $options);
            },
        ]);
        return $search;
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

        $query->clause('where')->traverse(function (ExpressionInterface $expression) use (&$otherComparisons) {
            if (!$expression instanceof Comparison) {
                return;
            }

            switch ($expression->getField()) {
                // Add address related conditions to $addressComparisons
                case 'Companies.address':
                case 'Companies.postcode':
                case 'Companies.city':
                case 'Companies.country':
                    break;

                // Add other conditions to $otherComparisons
                default:
                    $otherComparisons[] = $expression;
            }
        });

        // Override the query conditions with the non address conditions
        $query->where($otherComparisons, [], true);

        $query->matching('Companies', function (Query $query) use ($options) {
            $radiusOptions = [
                'radius' => $options['radius'],
                'field' => $options['field']
            ];

            // Convert position address fields to company address fields
            if (isset($options['address'])) {
                $radiusOptions['address'] = $options['address'];
            }
            if (isset($options['postcode'])) {
                $radiusOptions['postcode'] = $options['postcode'];
            }
            if (isset($options['city'])) {
                $radiusOptions['city'] = $options['city'];
            }
            if (isset($options['country'])) {
                $radiusOptions['country'] = $options['country'];
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
     * @param Query $query Query to apply the finder to
     * @param array $options Options that apply to the filter
     *
     * @return Query
     */
    public function findOrValue(Query $query, array $options)
    {
        $value = $options['value'];
        $field = $options['field'];

        if (isset($options['or'][$value])) {
            if (is_array($value)) {
                $query->where([
                    $field . ' IN' => [
                        $options['or'][$value],
                    ] + $value
                ]);
            } else {
                $query->where([
                    $field . ' IN' => [
                        $options['or'][$value],
                        $value
                    ]
                ]);
            }
        } else {
            $query->where([$field => $value]);
        }

        return $query;
    }
}
