<?php
namespace App\Controller\Coordinator;

use App\Controller\AppController as BaseController;

class AppController extends BaseController
{

    public function initialize()
    {
        parent::initialize();

        $this->viewBuilder()->layout('coordinator');

        $this->Crud->mapAction('add', [
            'messages' => [
                'success' => [
                    'element' => 'success'
                ]
            ]
        ]);

        $this->Crud->mapAction('edit', [
            'messages' => [
                'success' => [
                    'element' => 'success'
                ]
            ]
        ]);

        $this->Crud->mapAction('delete', [
            'messages' => [
                'success' => [
                    'element' => 'success'
                ]
            ]
        ]);
    }

    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.setFlash' => 'crudSetFlash'
        ];
    }

    public function crudSetFlash(Event $event) {
        unset($event->subject()->params['class']);

        return $event;
    }
}
