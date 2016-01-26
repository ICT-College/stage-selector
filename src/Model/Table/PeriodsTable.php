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

/**
 * @property \App\Model\Table\InternshipsTable Internships
 */
class PeriodsTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Search.Search');

        try {
            // We only need to set the students relation when the secured alias is set.
            // Because alias doesn't have support for "normally" getting the alias, we must it do it this way.
            ConnectionManager::get('secured');

            $this->belongsToMany('Students', [
                'through' => 'Internships'
            ]);
        } catch (MissingDatasourceConfigException $e) {

        }

        $this->hasMany('Internships');

        $this->displayField('title');
    }

    /**
     * {@inheritDoc}
     */
    public function searchConfiguration()
    {
        $search = new Manager($this);

        return $search;
    }

    /**
     * Find periods relevant for a student
     *
     * @param Query $query Query to find
     * @param array $options Options for find
     *
     * @return Query
     */
    public function findForStudent(Query $query, array $options)
    {
        $query->matching('Internships', function (Query $q) use ($options) {
            return $q->where(['Internships.student_id' => $options['student_id']]);
        })->distinct('Periods.id');

        return $query;
    }
}
