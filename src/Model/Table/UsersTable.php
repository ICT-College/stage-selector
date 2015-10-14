<?php
namespace App\Model\Table;

use App\Model\Entity\Shard;
use App\Model\Entity\User;
use Cake\Datasource\ConnectionManager;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Association;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use CvoTechnologies\Gearman\Gearman;
use CvoTechnologies\Gearman\JobAwareTrait;

class UsersTable extends Table
{

    use JobAwareTrait;
    use MailerAwareTrait;

    /**
     * @param array $config
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->displayField('name');
        $this->belongsTo('Students', [
            'strategy' => Association::STRATEGY_SELECT
        ]);
        $this->eventManager()->on($this->getMailer('User'));
    }

    public function fromStudent($studentNumber, Shard $shard)
    {
        return $this->execute('inviteStudent', [
            'student_number' => $studentNumber,
            'shard' => $shard
        ], false, Gearman::PRIORITY_HIGH);
    }

    public function invite(User $user, Shard $shard)
    {
        $user = $this->save($user);
        if (!$user) {
            return false;
        }

        $this->dispatchEvent('Model.User.invited', ['user' => $user, 'shard' => $shard], $this);

        return $user;
    }

    /**
     * Connection name for this Table
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'main';
    }

    /**
     * Validation for users table
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator->requirePresence('email')
            ->add('email', [
                'valid' => [
                    'rule' => 'email',
                    'message' => 'E-mail must be valid'
                ],
                'unique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => 'E-mail must be unique'
                ]
            ])
            ->requirePresence('firstname')
            ->notEmpty('firstname', 'Firstname cannot be left blank')
            ->requirePresence('lastname')
            ->notEmpty('lastname', 'Lastname cannot be left blank')
            ->requirePresence('student_number')
            ->notEmpty('student_number', 'Student number cannot be left blank')
            ->add('student_number', [
                'unique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => 'Student number must be unique'
                ]
            ])
            ->add('student_id', [
                'unique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => 'Only one user can be assigned to a student'
                ]
            ]);

        return $validator;
    }
}
