<?php
namespace App\Controller\Coordinator;

use App\Controller\AppController as BaseController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class AppController extends BaseController
{

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->set('periods', TableRegistry::get('Periods')->find('list')->toArray());
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
