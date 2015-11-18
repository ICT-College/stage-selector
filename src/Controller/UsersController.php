<?php
namespace App\Controller;

use App\ShardAwareTrait;
use Cake\Datasource\ConnectionManager;
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
     * @inheritDoc
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

    public function activate($activationToken)
    {
        /* @var \App\Model\Entity\User $user */
        $user = $this->Users->find()
            ->where([
                'active' => false,
                'activation_token' => $activationToken,
            ])
            ->firstOrFail();

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
}
