<?php
namespace App\Controller;

use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\ORM\TableRegistry;

class UsersController extends AppController
{

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
                $shardSubdomain = 'main';

                try {
                    $connection = ConnectionManager::get('default');

                    $shardTable = TableRegistry::get('Shards');
                    $shard = $shardTable->find()->where([
                        'datasource' => $connection->config()['name']
                    ])->firstOrFail();

                    $shardSubdomain = $shard->subdomain;
                } catch (MissingDatasourceConfigException $e) {
                    // When default isn't set, we want the $shardSubdomain remain default without showing an error to the visitor.
                }

                $authorized = $this->Acl->check(['foreign_key' => $user['id'], 'model' => 'Users'], 'shards/' . $shardSubdomain);

                if (!$authorized && $shardSubdomain != 'main') {
                    $authorized = $this->Acl->check($user, 'shards/main');
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
}
