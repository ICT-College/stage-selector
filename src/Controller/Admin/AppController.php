<?php
namespace App\Controller\Admin;

use App\Controller\AppController as BaseController;

class AppController extends BaseController
{

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->viewBuilder()->layout('admin');

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

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.setFlash' => 'crudSetFlash'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function crudSetFlash(Event $event)
    {
        unset($event->subject()->params['class']);

        return $event;
    }
}
