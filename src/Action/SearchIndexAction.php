<?php

namespace App\Action;

use Crud\Action\IndexAction;

class SearchIndexAction extends IndexAction
{
    protected function _handle()
    {
        $this->_controller()->Prg->commonProcess();

        $query = $this->_table()->find('searchable', $this->_controller()->Prg->parsedParams());
        $subject = $this->_subject(['success' => true, 'query' => $query]);

        $this->_trigger('beforePaginate', $subject);
        $items = $this->_controller()->paginate($subject->query);
        $subject->set(['entities' => $items]);

        $this->_trigger('afterPaginate', $subject);
        $this->_trigger('beforeRender', $subject);

    }
}
