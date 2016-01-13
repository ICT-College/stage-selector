<?php

namespace IctCollege\CoordinatorApprovedSelector\Mailer;

use App\Model\Entity\Internship;
use App\Model\Entity\Period;
use App\Model\Entity\Shard;
use App\Model\Entity\User;
use App\ShardAwareTrait;
use Cake\Event\Event;
use Cake\Mailer\Mailer;
use IctCollege\CoordinatorApprovedSelector\Model\Entity\InternshipApplication;

class InternshipApplicationMailer extends Mailer
{

    use ShardAwareTrait;

    /**
     * @inheritDoc
     */
    public function implementedEvents()
    {
        return [
            'Model.InternshipApplication.submit' => 'onSubmit',
            'Model.InternshipApplication.approved' => 'onApproved',
        ];
    }

    public function submit(User $user, Internship $internship, Shard $shard, array $internshipApplications)
    {
        $this->to($user->email)
            ->domain($shard->subdomain . '.stage-selector.localhost')
            ->template('IctCollege/CoordinatorApprovedSelector.submit', 'student')
            ->subject(__('Internship applications has been submitted'))
            ->set([
                'user' => $user,
                'internship' => $internship,
                'shard' => $shard,
                'internshipApplications' => $internshipApplications,
                'selectorUrl' => \Cake\Routing\Router::url(['_name' => 'selector'], true)
            ]);
    }

    public function approved(User $user, Internship $internship, Shard $shard, InternshipApplication $internshipApplication)
    {
        $this->to($user->email)
            ->domain($shard->subdomain . '.stage-selector.localhost')
            ->template('IctCollege/CoordinatorApprovedSelector.approved', 'student')
            ->subject(__('Internship application has been approved'))
            ->set([
                'user' => $user,
                'internship' => $internship,
                'shard' => $shard,
                'internshipApplication' => $internshipApplication,
                'internshipUrl' => \Cake\Routing\Router::url(['prefix' => false, 'plugin' => false, 'controller' => 'Internships', 'action' => 'view', $internship->id], true)
            ]);
    }

    public function onSubmit(Event $event, User $user, Internship $internship, array $internshipApplications)
    {
        $this->send('submit', [$user, $internship, $this->shard(), $internshipApplications]);
    }

    public function onApproved(Event $event, User $user, Internship $internship, InternshipApplication $internshipApplication)
    {
        $this->send('approved', [$user, $internship, $this->shard(), $internshipApplication]);
    }
}
