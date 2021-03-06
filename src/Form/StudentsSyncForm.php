<?php
namespace App\Form;

use App\ShardAwareTrait;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use CvoTechnologies\Gearman\JobAwareTrait;

class StudentsSyncForm extends Form
{

    use JobAwareTrait {
        execute as executeJob;
    }

    use ShardAwareTrait;

    /**
     * {@inheritDoc}
     */
    protected function _buildSchema(Schema $schema)
    {
        return $schema->addField('csv', 'file');
    }

    /**
     * {@inheritDoc}
     */
    protected function _buildValidator(Validator $validator)
    {
        return $validator
            ->requirePresence('csv')
            ->notEmpty('csv')
            ->add('csv', 'file', [
                'rule' => [
                    'mimeType',
                    ['text/csv', 'text/plain']
                ]
            ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function _execute(array $data)
    {
        $handle = fopen($data['csv']['tmp_name'], 'r');
        if (!$handle) {
            return false;
        }

        $headers = [];
        $results = [];
        while (($row = fgetcsv($handle, 1000, ';')) !== false) {
            if (empty($headers)) {
                foreach ($row as $index => $field) {
                    if (($index === 0) && (substr($field, 0, 3) == pack('CCC', 239, 187, 191))) {
                        $field = substr($field, 3);
                    }

                    $headers[] = $field;
                }

                continue;
            }

            $result = [];
            foreach ($row as $index => $value) {
                $result[$headers[$index]] = $value;
            }

            $results[] = $result;
        }

        $this->executeJob('importEduArteStudents', [
            'shard' => $this->shard(),
            'results' => $results
        ], false);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $data)
    {
        return parent::execute($data);
    }
}
