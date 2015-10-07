<?php
namespace App\Controller;

use IctCollege\CoordinatorApprovedSelector\Controller\AppController;

class UsersController extends AppController
{

    /**
     * Login action for Users controller
     *
     * @return void
     */
    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error(h(__('Username or password incorrect, please try again.')));
            }
        }
    }

    /**
     * Logout action for Users controller
     *
     * @return void
     */
    public function logout()
    {
        if ($this->request->is('post')) {
            return $this->redirect($this->Auth->logout());
        }
    }
}
