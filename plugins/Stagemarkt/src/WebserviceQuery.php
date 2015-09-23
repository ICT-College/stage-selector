<?php

namespace Stagemarkt;

use Cake\Database\TypeMapTrait;
use Cake\Datasource\QueryTrait;
use Cake\Datasource\RepositoryInterface;
use Cake\Utility\Hash;

class WebserviceQuery
{

    use QueryTrait;
    use TypeMapTrait;

    const ACTION_CREATE = 1;
    const ACTION_READ = 2;
    const ACTION_UPDATE = 3;
    const ACTION_DELETE = 4;

    /**
     * Indicates that the operation should append to the list
     *
     * @var int
     */
    const APPEND = 0;

    /**
     * Indicates that the operation should prepend to the list
     *
     * @var int
     */
    const PREPEND = 1;

    /**
     * Indicates that the operation should overwrite the list
     *
     * @var bool
     */
    const OVERWRITE = true;

    private $__action = WebserviceQuery::ACTION_READ;
    private $__conditions = [];

    /**
     * @var \Stagemarkt\Webservice
     */
    protected $_webservice;

    /**
     * @var ResultSet
     */
    protected $_resultSet;

    public function __construct(Webservice $webservice, RepositoryInterface $repository)
    {
        $this->_webservice = $webservice;
        $this->repository($repository);
    }

    public function action($action = null) {
        if ($action === null) {
            return $this->__action;
        }

        $this->__action = $action;

        return $this;
    }

    public function conditions(array $conditions = null, $merge = true)
    {
        if ($conditions === null) {
            return $this->__conditions;
        }

        $this->__conditions = ($merge) ? Hash::merge($this->__conditions, $conditions) : $conditions;

        return $this;
    }

    /**
     * Populates or adds parts to current query clauses using an array.
     * This is handy for passing all query clauses at once.
     *
     * @param array $options the options to be applied
     * @return $this This object
     */
    public function applyOptions(array $options)
    {
        $this->_options = Hash::merge($this->_options, $options);
    }

    public function count()
    {
        return $this->_resultSet->total();
    }

    /**
     * Executes this query and returns a traversable object containing the results
     *
     * @return \Traversable
     */
    protected function _execute()
    {
        return $this->_resultSet = $this->_webservice->execute($this);
    }
}
