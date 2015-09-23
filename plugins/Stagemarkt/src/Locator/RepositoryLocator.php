<?php

namespace Stagemarkt\Locator;

use Cake\Core\App;
use Cake\ORM\Locator\LocatorInterface;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use RuntimeException;

class RepositoryLocator implements LocatorInterface
{

    /**
     * Instances that belong to the registry.
     *
     * @var array
     */
    protected $_instances = [];

    /**
     * Stores a list of options to be used when instantiating an object
     * with a matching alias.
     *
     * @param string|null $alias Name of the alias
     * @param array|null $options list of options for the alias
     * @return array The config data.
     */
    public function config($alias = null, $options = null)
    {
        // TODO: Implement config() method.
    }

    /**
     * Get a table instance from the registry.
     *
     * @param string $alias The alias name you want to get.
     * @param array $options The options you want to build the table with.
     * @return \Cake\ORM\Table
     */
    public function get($alias, array $options = [])
    {
        if (isset($this->_instances[$alias])) {
            if (!empty($options) && $this->_options[$alias] !== $options) {
                throw new RuntimeException(sprintf(
                    'You cannot configure "%s", it already exists in the registry.',
                    $alias
                ));
            }
            return $this->_instances[$alias];
        }

        list(, $classAlias) = pluginSplit($alias);
        $options = ['alias' => $classAlias] + $options;

        if (empty($options['className'])) {
            $options['className'] = Inflector::camelize($alias);
        }
        $className = App::className($options['className'], 'Repository', 'Repository');
        $options['className'] = $className;

//        if (empty($options['connection'])) {
//            $connectionName = $options['className']::defaultConnectionName();
//            $options['connection'] = ConnectionManager::get($connectionName);
//        }

        $options['registryAlias'] = $alias;
        $this->_instances[$alias] = $this->_create($options);

        return $this->_instances[$alias];
    }

    /**
     * Wrapper for creating table instances
     *
     * @param array $options The alias to check for.
     * @return \Cake\ORM\Table
     */
    protected function _create(array $options)
    {
        return new $options['className']($options);
    }

    /**
     * Check to see if an instance exists in the registry.
     *
     * @param string $alias The alias to check for.
     * @return bool
     */
    public function exists($alias)
    {
        // TODO: Implement exists() method.
    }

    /**
     * Set an instance.
     *
     * @param string $alias The alias to set.
     * @param \Cake\ORM\Table $object The table to set.
     * @return \Cake\ORM\Table
     */
    public function set($alias, Table $object)
    {
        // TODO: Implement set() method.
    }

    /**
     * Clears the registry of configuration and instances.
     *
     * @return void
     */
    public function clear()
    {
        // TODO: Implement clear() method.
    }

    /**
     * Removes an instance from the registry.
     *
     * @param string $alias The alias to remove.
     * @return void
     */
    public function remove($alias)
    {
        // TODO: Implement remove() method.
    }
}
