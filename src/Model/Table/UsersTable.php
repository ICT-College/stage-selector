<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersTable extends Table
{

    /**
     * @param array $config
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->displayField('name');
    }

    /**
     * Connection name for this Table
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'main';
    }

    /**
     * Validation for users table
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator->requirePresence('email')
            ->add('email', [
                'valid' => [
                    'rule' => 'email',
                    'message' => 'E-mail must be valid'
                ],
                'unique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => 'E-mail must be unique'
                ]
            ])
            ->requirePresence('firstname')
            ->notEmpty('firstname', 'Firstname cannot be left blank')
            ->requirePresence('lastname')
            ->notEmpty('lastname', 'Lastname cannot be left blank')
            ->requirePresence('student_number')
            ->notEmpty('student_number', 'Student number cannot be left blank')
            ->add('student_number', [
                'unique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => 'Student number must be unique'
                ]
            ]);

        return $validator;
    }
}
