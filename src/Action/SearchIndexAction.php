<?php

namespace App\Action;

use Crud\Action\IndexAction;

class SearchIndexAction extends IndexAction
{

    /**
     * Creates a searchable CRUD index
     *
     * @return void
     */
    protected function _handle()
    {
        $this->_controller()->Prg->commonProcess();

        $searchParameters = $this->_controller()->Prg->parsedParams();

        $query = $this->_table()->find('searchable', $searchParameters);
        $subject = $this->_subject(['success' => true, 'query' => $query]);

        $this->_trigger('beforePaginate', $subject);
        $items = $this->_controller()->paginate($subject->query);
        $subject->set(['entities' => $items]);

        $this->_controller()->set(['search' => [
            'parameters' => $searchParameters
        ]]);
        $this->serialize(['search' => 'search']);

        $this->_trigger('afterPaginate', $subject);
        $this->_trigger('beforeRender', $subject);
    }
}
