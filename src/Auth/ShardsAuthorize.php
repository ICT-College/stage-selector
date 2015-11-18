<?php
/**
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Auth;

use Acl\Auth\BaseAuthorize;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\Error\Debugger;
use Cake\Network\Request;
use Cake\ORM\TableRegistry;

/**
 * An authorization adapter for AuthComponent. Provides the ability to authorize using the AclComponent,
 * If AclComponent is not already loaded it will be loaded using the Controller's ComponentRegistry.
 *
 * @see AuthComponent::$authenticate
 * @see AclComponent::check()
 */
class ShardsAuthorize extends BaseAuthorize
{

    /**
     * Authorize a user using the AclComponent to a Shard.
     * When we're trying to authorize for an action.
     *
     * @param array $user The user to authorize
     * @param \Cake\Network\Request $request The request needing authorization.
     * @return bool
     */
    public function authorize($user, Request $request)
    {
        $Acl = $this->_registry->load('Acl');
        $user = [$this->_config['userModel'] => $user];

        $shardSubdomain = 'main';

        try {
            $connection = ConnectionManager::get('default');

            $shardTable = TableRegistry::get('Shards');
            $shard = $shardTable->find()->where([
                'datasource' => $connection->config()['name']
            ])->firstOrFail();

            $shardSubdomain = $shard->subdomain;
        } catch (MissingDatasourceConfigException $e) {
            // When default isn't set, we want the $shardSubdomain remain default without showing an error to the visitor.
        }

        $arosTable = TableRegistry::get('Aros');
        $aros = $arosTable->node($user);

        $authorized = false;

        foreach ($aros as $aro) {
            if ($aro->model != 'Users') {
                continue;
            }

            $authorized = $Acl->check($aro->id, 'shards/' . $shardSubdomain);

            if (!$authorized && $shardSubdomain != 'main') {
                $authorized = $Acl->check($aro->id, 'shards/main');
            }

            if ($authorized) {
                $path = 'controllers/' . $this->action($request);

                $authorized = $Acl->check($aro->id, $path);

                if ($authorized) {
                    break;
                } else {
                    Debugger::log('Unable to authorize for path: ' . $path);
                }
            }
        }

        return $authorized;
    }
}
