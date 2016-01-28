<?php

namespace IctCollege\CoordinatorApprovedSelector\Controller;

/**
 * Class PeriodsController
 *
 * @package IctCollege\CoordinatorApprovedSelector\Controller
 */
class PeriodsController extends AppController
{

    /**
     * The action for the selector
     *
     * @param int $id Period ID
     *
     * @return void
     */
    public function select($id = null)
    {
        $period = $this->Periods->find()->where([
            'id' => $id
        ])->firstOrFail();

        $this->set(compact('period'));
    }
}
