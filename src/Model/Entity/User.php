<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

class User extends Entity
{

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

        if (empty($this->get('role_id'))) {
            return null;
        } else {
            return [
                'Roles' => [
                    'id' => $this->get('role_id')
                ]
            ];
        }
    }
}
