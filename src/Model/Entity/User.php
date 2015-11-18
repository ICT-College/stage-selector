<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * @property string activation_token
 * @property bool active
 */
class User extends Entity
{

    public function activate()
    {
        $this->active = true;
    }

    /**
     * Hashes user password
     *
     * @param string $password Password to hash
     *
     * @return void|bool|string
     */
    protected function _setPassword($password)
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher)->hash($password);
        }
    }

    /**
     * @return string
     */
    protected function _getName()
    {
        return $this->firstname . ((!empty($this->insertion)) ? ' ' . $this->insertion : '') . ' ' . $this->lastname;
    }

    /**
     * parentNode
     *
     * @return array|null
     */
    public function parentNode() {
        if (!$this->id) {
            return null;
        }

        $shard_id = null;

        try {
            $connection = ConnectionManager::get('default');

            $shardTable = TableRegistry::get('Shards');
            $shard = $shardTable->find()->where([
                'datasource' => $connection->config()['name']
            ])->firstOrFail();

            $shard_id = $shard->id;
        } catch (MissingDatasourceConfigException $e) {
            // When default isn't set, we want the $parent_id remain NULL without showing an error to the visitor.
        }

        $usersTable = TableRegistry::get('Users');
        $user = $usersTable->find()->contain([
            'Shards' => function ($q) use ($shard_id) {
                return $q->where([
                    'Shards.id' => $shard_id
                ]);
            }
        ])->where([
            'Users.id' => $this->id
        ])->firstOrFail();

        if (!isset($user['shards'][0])) {
            return null;
        } else {
            return [
                'Roles' => [
                    'id' => $user['shards'][0]['_joinData']['role_id']
                ]
            ];
        }
    }
}
