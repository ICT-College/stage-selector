<?php
namespace App\Controller;

use App\ShardAwareTrait;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * Class UsersController
 *
 * @property \App\Model\Table\UsersTable Users
 * @package App\Controller
 */
class UsersController extends AppController
{

    use ShardAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->Auth->allow('activate');
    }

    /**
     * Login action for Users controller
     *
     * @return void|\Cake\Network\Response
     */
    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $shardSubdomain = $this->shardSubdomain();

                $authorized = $this->Acl->check(['foreign_key' => $user['id'], 'model' => 'Users'], 'shards/' . $shardSubdomain);

                if (!$authorized && $shardSubdomain != 'main') {
                    $authorized = $this->Acl->check(['foreign_key' => $user['id'], 'model' => 'Users'], 'shards/main');
                }

                if ($authorized) {
                    $this->Auth->setUser($user);
                    return $this->redirect($this->Auth->redirectUrl());
                } else {
                    $this->Flash->error(h(__('Not allowed to log into this department with your account.')));
                }
            } else {
                $this->Flash->error(h(__('Username or password incorrect, please try again.')));
            }
        }
    }

    /**
     * Logout action for Users controller
     *
     * @return void|\Cake\Network\Response
     */
    public function logout()
    {
        if ($this->request->is('post')) {
            return $this->redirect($this->Auth->logout());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function activate($activationToken)
    {
        /* @var \App\Model\Entity\User $user */
        $user = $this->Users->find()
            ->where([
                'active' => false,
                'activation_token' => $activationToken,
            ])
            ->first();

        if (!$user) {
            $this->Flash->error(__('Your account is already activated or your one-time token is invalid.'));
            return $this->redirect('/');
        }

        $this->set('user', $user);

        if (!$this->request->is('put')) {
            return null;
        }

        /* @var \App\Model\Entity\User $user */
        $user = $this->Users->patchEntity($user, $this->request->data(), [
            'fieldList' => [
                'password',
                'password_verification'
            ]
        ]);

        $user->activate();
        $user->activation_token = null;

        if (!$this->Users->save($user)) {
            $this->Flash->error(__('Could not save user'));

            return null;
        }

        $this->Auth->setUser($user->toArray());

        return $this->redirect(['_name' => 'selector']);
    }


    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Auth.afterIdentify' => 'afterIdentify'
        ];
    }

    public function afterIdentify(Event $event, array $identity)
    {
        /* @var \App\Model\Table\ShardsUsersTable $shardUsers */
        $shardUsers = $this->loadModel('ShardsUsers');

        $shardUser = $shardUsers->find()->where([
            'user_id' => $identity['id'],
            'shard_id' => $this->shard()->id
        ])->contain([
            'Roles'
        ])->firstOrFail();

        $this->Auth->redirectUrl([
            'prefix' => $shardUser->role->prefix,
            'controller' => 'Pages',
            'action' => 'display',
            'home'
        ]);
    }
}
