<?php

namespace App\Model\Table;

use App\Model\Entity\Position;
use Cake\Database\Expression\Comparison;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\ExpressionInterface;
use Cake\Event\Event;
use Cake\ORM\Association;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use CvoTechnologies\Gearman\Gearman;
use CvoTechnologies\Gearman\JobAwareTrait;
use Search\Manager;

class PositionsTable extends Table
{

    use JobAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');

        $this->belongsTo('Companies');
        $this->belongsTo('StudyPrograms', [
            'strategy' => Association::STRATEGY_SELECT
        ]);
        $this->belongsToMany('QualificationParts', [
            'through' => 'PositionQualificationParts'
        ]);
    }

    /**
     * @inheritDoc
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('learning_pathway', 'create')
            ->add('learning_pathway', [
                'valid' => [
                    'rule' => ['inList', ['GV', 'VMBO', 'BBL', 'BOL', 'HBO']],
                ]
            ])
            ->requirePresence('company_id', 'create')
            ->requirePresence('study_program_id', 'create');

        return $validator;
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
        $search->value('stagemarkt_id', [
            'field' => 'Companies.stagemarkt_id'
        ]);
        $search->value('study_program_id', [
            'field' => $this->aliasField('study_program_id')
        ]);
        $search->callback('learning_pathway', [
            'callback' => function (Query $query, array $args, $searchType) {
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
            'callback' => function (Query $query, array $args, $searchType) {
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

    /**
     * Delete positions made by students with the amount of available positions being set lower than 0
     *
     * @param Event $event The event that was dispatched
     * @param Position $position The position to check
     *
     * @return true|null
     */
    public function beforeSave(Event $event, Position $position)
    {
        if (!$position->student_made) {
            return null;
        }
        if ($position->isNew()) {
            $positionConditions = [
                'company_id' => $position->company_id,
                'study_program_id' => $position->study_program_id,
                'student_made' => true
            ];
            if ($this->exists($positionConditions)) {
                $existingEntity = $this
                    ->find()
                    ->where($positionConditions)
                    ->firstOrFail();

                $position->isNew(false);
                $position->id = $existingEntity->id;
                $position->amount = $existingEntity->amount + 1;
            } else {
                $position->amount = 1;
            }
        }

        if ($position->amount > 0) {
            return null;
        }

        $event->stopPropagation();

        return $this->delete($position);
    }

    /**
     * Updates coordinates or details when needed
     *
     * @param Event $event The event that was dispatched
     * @param Position $position The position to check the details of
     *
     * @return void
     */
    public function afterSave(Event $event, Position $position)
    {
        $detailFields = [
            'start',
            'end'
        ];
        $detailsStored = false;
        foreach ($detailFields as $field) {
            if (!$position->has($field)) {
                continue;
            }
            if (!$position->get($field)) {
                continue;
            }

            $detailsStored = true;
        }

        if (!$detailsStored) {
            $this->updateDetails($position);
        }
    }

    /**
     * Starts a background job to get more details of a position
     *
     * @param Position $position The position to get the details of
     *
     * @return void
     */
    public function updateDetails(Position $position)
    {
        $this->execute('position_details', [
            'position' => $position,
            'datasource' => $this->connection()->configName()
        ], true, Gearman::PRIORITY_LOW);
    }
}
