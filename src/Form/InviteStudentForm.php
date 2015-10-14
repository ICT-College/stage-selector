<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
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
        /* @var \App\Model\Table\UsersTable $users */
        $users = TableRegistry::get('Users');
        $shard = TableRegistry::get('Shards')->get(1);
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
