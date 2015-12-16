<?php
namespace App\Model\Entity;

use App\ShardAwareTrait;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * @property string activation_token
 * @property bool active
 */
class User extends Entity
{

    use ShardAwareTrait;

    protected $_virtual = ['name'];

    /**
     * Activates an user
     */
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

        $shard = $this->shard();

        $usersTable = TableRegistry::get('Users');
        $user = $usersTable->find()->contain([
            'Shards' => function ($q) use ($shard) {
                return $q->where([
                    'Shards.id' => $shard->id
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
