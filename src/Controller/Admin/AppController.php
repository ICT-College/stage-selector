<?php
namespace App\Controller\Admin;

use App\Controller\AppController as BaseController;

class AppController extends BaseController
{

    public function initialize()
    {
        parent::initialize();

        $this->viewBuilder()->className('CrudView.Crud');

        $this->Crud->addListener('CrudView.View');
        $this->Crud->addListener('Crud.Redirect');
        $this->Crud->addListener('Crud.RelatedModels');
    }
}
