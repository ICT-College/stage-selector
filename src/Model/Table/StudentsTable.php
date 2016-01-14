<?php

namespace App\Model\Table;

use Cake\Database\Schema\Table as Schema;
use Cake\ORM\Table;

class StudentsTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->displayField('student_number');
    }

    /**
     * {@inheritDoc}
     */
    protected function _initializeSchema(Schema $table)
    {
        $table->columnType('initials', 'encrypted');
        $table->columnType('firstname', 'encrypted');
        $table->columnType('insertion', 'encrypted');
        $table->columnType('lastname', 'encrypted');
        $table->columnType('email', 'encrypted');
        $table->columnType('address', 'encrypted');
        $table->columnType('postcode', 'encrypted');
        $table->columnType('city', 'encrypted');
        $table->columnType('telephone', 'encrypted');
        $table->columnType('birthday', 'encrypted');
        $table->columnType('birthplace', 'encrypted');

        return $table;
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultConnectionName()
    {
        return 'secured';
    }
}
