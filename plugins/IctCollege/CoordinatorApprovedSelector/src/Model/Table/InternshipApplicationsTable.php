<?php

namespace IctCollege\CoordinatorApprovedSelector\Model\Table;

use Cake\ORM\Entity;
use Cake\ORM\Rule\IsUnique;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;

class InternshipApplicationsTable extends Table
{

    /**
     * @inheritDoc
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Search.Search');

        $this->belongsTo('Positions');
    }

    /**
     *
     */
    public function searchConfiguration()
    {
        $manager = new Manager($this);

        return $manager;
    }

    /**
     * @inheritDoc
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->addCreate(function (Entity $entity, array $options) {
            return $options['repository']->find()->where(['student_id' => $entity->student_id])->count() <= $options['max'] - 1;
        }, 'maxPositions', ['max' => 4]);
        $rules->addCreate(new IsUnique(['position_id']), 'uniquePosition');

        return parent::buildRules($rules);
    }
}
