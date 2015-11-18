<?php
namespace App\Form;

use App\ShardAwareTrait;
use Cake\Datasource\ConnectionManager;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use CvoTechnologies\Gearman\JobAwareTrait;

class InviteStudentForm extends Form
{

    use ShardAwareTrait;

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
        $shard = $this->shard();

        if ($shard == null) {
            throw new NotFoundException('Unable to find Shard');
        }

        /* @var \App\Model\Table\UsersTable $users */
        $users = TableRegistry::get('Users');
        $user = $users->fromStudent($data['student_number'], $shard);
        if (!$user) {
            return;
        }

        /* @var \App\Model\Table\PeriodsTable $periods */
        $periods = TableRegistry::get('Periods');
        $period = $periods->get($data['period_id']);

        $user = $users->invite($user, $shard, $period);
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
