<?php
namespace App\Form;

use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use CvoTechnologies\Gearman\JobAwareTrait;

class InviteStudentForm extends Form
{

    use JobAwareTrait {
        execute as executeJob;
    }

    protected function _buildSchema(Schema $schema)
    {
        return $schema->addField('student_number', 'string');
    }

    protected function _buildValidator(Validator $validator)
    {
        return $validator
            ->requirePresence('student_number')
            ->notEmpty('student_number');
    }

    protected function _execute(array $data)
    {
        $shard = null;

        try {
            $connection = ConnectionManager::get('default');

            $shardTable = TableRegistry::get('Shards');
            $shard = $shardTable->find()->where([
                'datasource' => $connection->config()['name']
            ])->firstOrFail();
        } catch (MissingDatasourceConfigException $e) {
            // When default isn't set, we want the $parent_id remain NULL without showing an error to the visitor.
        }

        if ($shard == null) {
            throw new NotFoundException('Unable to find Shard');
        }

        /* @var \App\Model\Table\UsersTable $users */
        $users = TableRegistry::get('Users');
        $user = $users->fromStudent($data['student_number'], $shard);
        if (!$user) {
            return;
        }

        $user = $users->invite($user, $shard);
        if (!$user) {
            return;
        }

        return true;
    }

    public function execute(array $data)
    {
        return parent::execute($data);
    }
}
