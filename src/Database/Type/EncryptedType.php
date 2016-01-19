<?php

namespace App\Database\Type;

use Cake\Core\Configure;
use Cake\Database\Driver;
use Cake\Database\Type;
use Cake\Log\Log;
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

        return Security::encrypt(serialize($value), $this->encryptedTypeKey());
    }

    /**
     * {@inheritDoc}
     */
    public function toPHP($value, Driver $driver)
    {
        if (is_null($value)) {
            return null;
        }

        $serializedData = Security::decrypt($value, $this->encryptedTypeKey());
        if ($serializedData === false) {
            return false;
        }

        return unserialize($serializedData);
    }

    /**
     * Get the encryption key to be used
     *
     * @return string
     */
    public function encryptedTypeKey()
    {
        $encryptionKey = Configure::read('Security.encryptedTypeKey');
        if ($encryptionKey === 'Example key') {
            Log::critical('Default encryption key being used in Security.encryptedTypeKey!');
        }

        return $encryptionKey;
    }
}
