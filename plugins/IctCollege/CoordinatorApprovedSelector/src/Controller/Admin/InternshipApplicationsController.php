<?php

namespace IctCollege\CoordinatorApprovedSelector\Controller\Admin;

use Cake\Event\Event;

/**
 * @property \IctCollege\CoordinatorApprovedSelector\Model\Table\InternshipApplicationsTable InternshipApplications
 */
class InternshipApplicationsController extends AppController
{

    /**
     * @inheritDoc
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $internship = $this->InternshipApplications->Periods->Internships->find('active', [
            'student' => $this->request->param('student_id')
        ])->where([
            'period_id' => $this->_getPeriod()->id
        ])->contain([
            'Periods',
            'Positions' => [
                'Companies',
                'StudyPrograms'
            ],
            'Users'
        ])->firstOrFail();

        $this->set('internship', $internship);
    }

    /**
     * @param int $id
     *
     * @return \Cake\Network\Response|null
     */
    public function approve($id)
    {
        $internshipApplication = $this->InternshipApplications->get($id);

        $internship = $this->InternshipApplications->approve($internshipApplication);
        if (!$internship) {
            $this->Flash->error(__('Could not approve application'));

            return $this->redirect([
                'action' => 'index'
            ]);
        }

        $this->Flash->success(__('The application has been approved'));

        return $this->redirect([
            'plugin' => false,
            'controller' => 'Internships',
            'action' => 'view',
            $internship->id
        ]);
    }

    /**
     * Redirects to given $url, after turning off $this->autoRender.
     * Script execution is halted after the redirect.
     *
     * @param string|array $url A string or array-based URL pointing to another location within the app,
     *     or an absolute URL
     * @param int $status HTTP status code (eg: 301)
     * @return \Cake\Network\Response|null
     * @link http://book.cakephp.org/3.0/en/controllers.html#Controller::redirect
     */
    public function redirect($url, $status = 302)
    {
        if ((!isset($url['controller'])) && (!isset($url['student_id']))) {
            $url['student_id'] = $this->request->param('student_id');
        }

        return parent::redirect($url, $status);
    }

    /**
     * @inheritDoc
     */
    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforePaginate' => 'beforePaginate'
        ];
    }

    public function beforePaginate(Event $event)
    {
        /* @var \Cake\ORM\Query $query */
        $query = $event->subject()->query;

        $query->andWhere([
            'period_id' => $this->_getPeriod()->id,
            'student_id' => $this->request->param('student_id')
        ]);

        $query->contain([
            'Positions' => [
                'Companies',
                'StudyPrograms'
            ]
        ]);
    }

    /**
     * @return \App\Model\Entity\Period
     */
    protected function _getPeriod()
    {
        $internship = $this->InternshipApplications->Periods->Internships->find('active', [
            'student' => $this->request->param('student_id')
        ])->contain([
            'Periods'
        ])->firstOrFail();

        return $internship->period;
    }
}
