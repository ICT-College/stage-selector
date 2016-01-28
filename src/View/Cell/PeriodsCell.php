<?php
namespace App\View\Cell;

use Cake\ORM\Query;
use Cake\View\Cell;

/**
 * Periods cell
 */
class PeriodsCell extends Cell
{

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Default display method.
     *
     * @param array $user User array
     *
     * @return void
     */
    public function display($user)
    {
        $this->loadModel('Periods');

        $periods = $this->Periods
            ->find('all')
            ->matching('Internships', function (Query $q) use ($user) {
                return $q->where([
                    'student_id' => $user['student_id']
                ]);
            })
            ->distinct('Periods.id')->contain([
                'Internships' => function (Query $q) use ($user) {
                    return $q->find('active', ['student' => $user['student_id']]);
                },
                'Internships.Positions.StudyPrograms',
                'Internships.Positions.Companies'
            ])->order([
                'Periods.id' => 'DESC'
            ]);

        $this->set('periods', $periods);
    }
}
