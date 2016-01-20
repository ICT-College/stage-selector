<?php

namespace App\View\Cell;

use Cake\View\Cell;

class StudentsInviteCell extends Cell
{

    /**
     * Show an invite modal
     *
     * @return void
     */
    public function modal()
    {
        $this->loadModel('Periods');

        $this->set('periods', $this->Periods->find('list'));
    }
}
