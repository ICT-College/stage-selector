<?php
namespace App\Model\Table;

use App\Model\Entity\Shard;
use App\Model\Entity\User;
use Cake\Database\Expression\FunctionExpression;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\Event\Event;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Association;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use CvoTechnologies\Gearman\Gearman;
use CvoTechnologies\Gearman\JobAwareTrait;
use Search\Manager;

class UsersTable extends Table
{

    use JobAwareTrait;
    use MailerAwareTrait;

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
     * @param array $config
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->displayField('name');

        $this->belongsTo('Roles');

        $this->addBehavior('Search.Search');
        $this->addBehavior('Acl.Acl', [
            'type' => 'requester'
        ]);

        try {
            // We only need to set the students relation when the secured alias is set.
            // Because alias doesn't have support for "normally" getting the alias, we must it do it this way.
            ConnectionManager::get('secured');

            $this->belongsTo('Students', [
                'strategy' => Association::STRATEGY_SELECT
            ]);
        }catch(MissingDatasourceConfigException $e) {}

        $this->eventManager()->on($this->getMailer('User'));
    }

    public function fromStudent($studentNumber, Shard $shard)
    {
        return $this->execute('get_user_from_student', [
            'student_number' => $studentNumber,
            'shard' => $shard
        ], false, Gearman::PRIORITY_HIGH);
    }

    public function invite(User $user, Shard $shard)
    {
        $user = $this->save($user, [
            'associated' => false
        ]);
        if (!$user) {
            return false;
        }

        $this->dispatchEvent('Model.User.invited', ['user' => $user, 'shard' => $shard], $this);

        return $user;
    }

    public function searchConfiguration()
    {
        $concatWithoutInsertion = new FunctionExpression('CONCAT', [
            $this->aliasField('firstname') => 'literal',
            ' ',
            $this->aliasField('lastname') => 'literal'
        ]);

        $concatWithInsertion = new FunctionExpression('CONCAT', [
            $this->aliasField('firstname') => 'literal',
            ' ',
            $this->aliasField('insertion') => 'literal',
            ' ',
            $this->aliasField('lastname') => 'literal'
        ]);

        $search = new Manager($this);
        $search
            ->like('q', [
                'before' => true,
                'after' => true,
                'field' => [
                    $concatWithoutInsertion,
                    $concatWithInsertion,
                    $this->aliasField('student_number'),
                    $this->aliasField('firstname'),
                    $this->aliasField('lastname'),
                    $this->aliasField('groupcode'),
                ]
            ]);
        return $search;
    }

    /**
     * afterSave
     *
     * @param Event $event
     * @param Entity $entity
     */
    public function afterSave(Event $event, Entity $entity) {
        /* @var \Acl\Model\Table\ArosTable $arosTable */
        $arosTable = TableRegistry::get('Aros');
        $aro = $this->node($entity)->firstOrFail();
        $aro->alias = $entity->email;
        $arosTable->save($aro);
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
