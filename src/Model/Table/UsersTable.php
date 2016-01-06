<?php
namespace App\Model\Table;

use Acl\Controller\Component\AclComponent;
use App\Model\Entity\Period;
use App\Model\Entity\Shard;
use App\Model\Entity\User;
use App\ShardAwareTrait;
use Cake\Controller\ComponentRegistry;
use Cake\Database\Expression\FunctionExpression;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\Event\Event;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Association;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use CvoTechnologies\Gearman\Gearman;
use CvoTechnologies\Gearman\JobAwareTrait;
use Search\Manager;

class UsersTable extends Table
{

    use ShardAwareTrait;
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
        $this->belongsToMany('Shards');
        $this->hasMany('Internships', [
            'bindingKey' => 'student_id',
            'foreignKey' => 'student_id'
        ]);

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

        $registry = new ComponentRegistry();
        $this->Acl = new AclComponent($registry);
    }

    public function fromStudent($studentNumber, Shard $shard)
    {
        return $this->execute('get_user_from_student', [
            'student_number' => $studentNumber,
            'shard' => $shard
        ], false, Gearman::PRIORITY_HIGH);
    }

    public function invite(User $user, Shard $shard, Period $period)
    {
        $user = $this->patchEntity($user, [
            'shards' => [
                [
                    'id' => $this->shard()->id,
                    '_joinData' => [
                        'role_id' => 1
                    ]
                ]
            ],
            'internships' => [
                [
                    'period_id' => $period->id,
                    'student_id' => $user->student_id,
                    'active' => true
                ]
            ]
        ], [
            'validate' => false
        ]);

        if ($user->isNew()) {
            $user->activation_token = Text::uuid();
        }

        $user = $this->save($user, [
            'associated' => [
                'Shards',
                'Internships'
            ]
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
        // Set alias of the User's aco to his e-mail address

        /* @var \Acl\Model\Table\ArosTable $arosTable */
        $arosTable = TableRegistry::get('Aros');
        $aros = $arosTable->node($entity);

        foreach ($aros as $aro) {
            if ($aro->model != 'Users') {
                continue;
            }

            $aro->alias = $entity->email;
            $arosTable->save($aro);
        }

        // Grant access to the user's Aco from his shard.
        $entity = $this->loadInto($entity, [
            'Shards'
        ]);

        foreach ($entity['shards'] as $shard) {
            $this->Acl->allow($entity, $shard);
        }
    }

    /**
     * Validation for users table
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator->requirePresence('email', 'create')
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
            ->requirePresence('firstname', 'create')
            ->notEmpty('firstname', 'Firstname cannot be left blank')
            ->requirePresence('lastname', 'create')
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
            ])
            ->add('password_verification', 'no-misspelling', [
                'rule' => ['compareWith', 'password'],
                'message' => 'Passwords are not equal',
            ]);

        return $validator;
    }
}
