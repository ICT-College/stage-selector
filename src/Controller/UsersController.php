<?php
namespace App\Controller;

use IctCollege\CoordinatorApprovedSelector\Controller\AppController;

class UsersController extends AppController {

    public function login() {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
        }
    }

    public function logout() {
        if ($this->request->is('post')) {
            return $this->redirect($this->Auth->logout());
        }
    }

}
