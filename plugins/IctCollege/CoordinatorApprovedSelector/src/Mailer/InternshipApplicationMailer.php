<?php

namespace IctCollege\CoordinatorApprovedSelector\Mailer;

use App\Model\Entity\Internship;
use App\Model\Entity\Period;
use App\Model\Entity\Shard;
use App\Model\Entity\User;
use App\ShardAwareTrait;
use Cake\Event\Event;
use Cake\Mailer\Mailer;

class InternshipApplicationMailer extends Mailer
{

    use ShardAwareTrait;

    /**
     * @inheritDoc
     */
    public function implementedEvents()
    {
        return [
            'Model.InternshipApplication.submit' => 'onSubmit'
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

    public function onSubmit(Event $event, User $user, Internship $internship, array $internshipApplications)
    {
        $this->send('submit', [$user, $internship, $this->shard(), $internshipApplications]);
    }
}
