<?php
namespace App\Model\Table;

use Cake\Database\Expression\FunctionExpression;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Association;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;

/**
 * @property \App\Model\Table\InternshipsTable Internships
 */
class PeriodsTable extends Table
{

    /**
     * @param array $config
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
        }catch(MissingDatasourceConfigException $e) {}

        $this->hasMany('Internships');
    }

    public function searchConfiguration()
    {
        $search = new Manager($this);

        return $search;
    }

    /**
     * Validation for periods table
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        return $validator;
    }
}
