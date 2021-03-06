<?php

namespace App\Mailer;

use App\Model\Entity\Shard;
use App\Model\Entity\User;
use Cake\Event\Event;
use Cake\Mailer\Mailer;

class UserMailer extends Mailer
{
    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Model.User.invited' => 'onInvite'
        ];
    }

    /**
     * Send an invite to an user
     *
     * @param User $user The user tp invite
     * @param Shard $shard Shard to use
     *
     * @return void
     */
    public function invite(User $user, Shard $shard)
    {
        $this->to($user->email)
            ->domain($shard->subdomain . '.stage-selector.localhost')
            ->template('invite', 'student')
            ->set([
                'user' => $user,
                'shard' => $shard,
                'selectorUrl' => \Cake\Routing\Router::url(['_name' => 'selector'], true)
            ]);

        if ($user->active) {
            $this->template('invite_existing', 'student');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function onInvite(Event $event, User $user, Shard $shard)
    {
        $this->send('invite', [$user, $shard]);
    }
}
