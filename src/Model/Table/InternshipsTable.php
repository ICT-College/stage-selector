<?php
namespace App\Model\Table;

use Cake\Database\Expression\FunctionExpression;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Association;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;

class InternshipsTable extends Table
{

    /**
     * @param array $config
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Search.Search');
        $this->addBehavior('CounterCache', [
            'Positions' => [
                'internship_count' => [
                    'conditions' => ['Internships.active' => true]
                ]
            ],
        ]);

        try {
            // We only need to set the students relation when the secured alias is set.
            // Because alias doesn't have support for "normally" getting the alias, we must it do it this way.
            ConnectionManager::get('secured');

            $this->belongsTo('Students', [
                'strategy' => Association::STRATEGY_SELECT
            ]);
        }catch(MissingDatasourceConfigException $e) {}

        $this->belongsTo('Periods');
        $this->belongsTo('Users', [
            'foreignKey' => 'student_id',
            'bindingKey' => 'student_id',
        ]);
        $this->belongsTo('Positions');
    }

    public function findActive(Query $query, array $options)
    {
        $query->where([
            $this->aliasField('active') => true,
            $this->aliasField('student_id') => $options['student']
        ]);

        return $query;
    }

    public function searchConfiguration()
    {
        $concatWithoutInsertion = new FunctionExpression('CONCAT', [
            $this->Users->aliasField('firstname') => 'literal',
            ' ',
            $this->Users->aliasField('lastname') => 'literal'
        ]);

        $concatWithInsertion = new FunctionExpression('CONCAT', [
            $this->Users->aliasField('firstname') => 'literal',
            ' ',
            $this->Users->aliasField('insertion') => 'literal',
            ' ',
            $this->Users->aliasField('lastname') => 'literal'
        ]);

        $search = new Manager($this);
        $search
            ->like('q', [
                'before' => true,
                'after' => true,
                'field' => [
                    $concatWithoutInsertion,
                    $concatWithInsertion,
                    $this->Users->aliasField('student_number'),
                    $this->Users->aliasField('firstname'),
                    $this->Users->aliasField('lastname'),
                    $this->Users->aliasField('groupcode'),
                ]
            ]);

        return $search;
    }

    /**
     * Validation for internships table
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        return $validator;
    }
}
