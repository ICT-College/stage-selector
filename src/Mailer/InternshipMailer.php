<?php

namespace App\Mailer;

use App\Model\Entity\Internship;
use App\Model\Entity\Shard;
use App\Model\Entity\User;
use App\ShardAwareTrait;
use Cake\Event\Event;
use Cake\Mailer\Mailer;

class InternshipMailer extends Mailer
{

    use ShardAwareTrait;

    /**
     * @inheritDoc
     */
    public function implementedEvents()
    {
        return [
            'Model.Internship.accepted' => 'onAccepted'
        ];
    }

    /**
     * Send an invite to an user
     *
     * @param User $user The user tp invite
     * @param Shard $shard
     *
     * @return void
     */
    public function accepted(User $user, Internship $internship, Shard $shard)
    {
        $this->to($user->email)
            ->domain($shard->subdomain . '.stage-selector.localhost')
            ->template('internship_accepted', 'student')
            ->set([
                'user' => $user,
                'internship' => $internship,
                'shard' => $shard,
            ]);
    }

    public function onAccepted(Event $event, Internship $internship)
    {
        debug($this->send('accepted', [$internship->user, $internship, $this->shard()]));
    }
}
