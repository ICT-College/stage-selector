<?php

namespace App\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type;
use Cake\Utility\Security;

class EncryptedType extends Type
{

    /**
     * {@inheritDoc}
     */
    public function toDatabase($value, Driver $driver)
    {
        if (is_null($value)) {
            return null;
        }

        return Security::encrypt(serialize($value), md5('aaa'));
    }

    /**
     * {@inheritDoc}
     */
    public function toPHP($value, Driver $driver)
    {
        if (is_null($value)) {
            return null;
        }

        return unserialize(Security::decrypt($value, md5('aaa')));
    }
}
