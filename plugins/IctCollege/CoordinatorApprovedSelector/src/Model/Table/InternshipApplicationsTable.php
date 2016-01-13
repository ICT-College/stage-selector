<?php

namespace IctCollege\CoordinatorApprovedSelector\Model\Table;

use App\Model\Entity\Internship;
use App\Model\Entity\User;
use Cake\Event\Event;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Entity;
use Cake\ORM\Rule\IsUnique;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use IctCollege\CoordinatorApprovedSelector\Model\Entity\InternshipApplication;
use Search\Manager;

/**
 * @property \App\Model\Table\PeriodsTable Periods
 * @property \App\Model\Table\PositionsTable Positions
 */
class InternshipApplicationsTable extends Table
{

    use MailerAwareTrait;

    /**
     * @inheritDoc
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Search.Search');

        $this->belongsTo('Positions');
        $this->belongsTo('Periods');

        $this->eventManager()->on($this->getMailer('IctCollege/CoordinatorApprovedSelector.InternshipApplication'));
    }

    /**
     * @param InternshipApplication $internshipApplication
     *
     * @return bool|\App\Model\Entity\Internship|mixed
     */
    public function approve(InternshipApplication $internshipApplication)
    {
        if ($internshipApplication->isNew()) {
            throw new \InvalidArgumentException();
        }

        $internship = $this->Periods->Internships->find('active', [
            'student' => $internshipApplication->student_id
        ])->where([
            'period_id' => $internshipApplication->period_id
        ])->firstOrFail();

        $internship->position_id = $internshipApplication->position_id;

        $internship = $this->Periods->Internships->save($internship);
        if (!$internship) {
            return false;
        }

        $internshipApplication->accepted_coordinator = true;

        if (!$this->save($internshipApplication)) {
            return false;
        }

        $internship = $this->Periods->Internships->loadInto($internship, [
            'Positions' => [
                'Companies',
                'StudyPrograms'
            ],
            'Users'
        ]);
        $this->dispatchEvent('Model.InternshipApplication.approved', [
            'user' => $internship->user,
            'internship' => $internship,
            'internshipApplications' => $internshipApplication
        ]);

        return $internship;
    }

    public function submit(User $user, Internship $internship, array $internshipApplications)
    {
        $event = $this->dispatchEvent('Model.InternshipApplication.submit', [
            'user' => $user,
            'internship' => $internship,
            'internshipApplications' => $internshipApplications
        ], $this);

        return !$event->isStopped();
    }

    /**
     *
     */
    public function searchConfiguration()
    {
        $manager = new Manager($this);

        return $manager;
    }

    /**
     * @inheritDoc
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->addCreate(function (Entity $entity, array $options) {
            return $options['repository']->find()->where(['student_id' => $entity->student_id])->count() <= $options['max'] - 1;
        }, 'maxPositions', ['max' => 4]);
        $rules->addCreate(new IsUnique(['position_id', 'student_id']), 'uniquePosition');

        return parent::buildRules($rules);
    }

    public function beforeSave(Event $event, Entity $application)
    {
        if (!empty($application->position)) {
            $application->position->student_made = true;
        }
    }

    public function afterDelete(Event $event, Entity $application)
    {
        $position = $this->Positions->get($application->position_id);
        if (!$position->student_made) {
            return;
        }

        $position->amount--;

        $this->Positions->save($position);
    }
}
