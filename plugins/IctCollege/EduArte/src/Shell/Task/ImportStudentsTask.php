<?php

namespace IctCollege\EduArte\Shell\Task;

use App\ShardAwareTrait;
use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Time;

class ImportStudentsTask extends Shell
{

    use ShardAwareTrait;

    public function main(array $workload, \GearmanJob $job)
    {
        $this->shard($workload['shard'])
            ->useShardDatabase();

        $this->loadModel('Students');

        $fieldMapping = [
            'Nummer' => 'student_number',
            'Voorletters' => 'initials',
            'Roepnaam' => 'firstname',
            'Voorvoegsel' => 'insertion',
            'Achternaam' => 'lastname',
            'E-mail school' => 'email',
            '(W)Postcode en plaats' => function ($value) {
                return array_combine([
                    'postcode',
                    'city'
                ], explode('  ', $value));
            },
            'Woonadres' => 'address',
            'Telefoon privé' => 'telephone',
            'Geslacht' => function ($value) {
                return [
                    'gender' => str_replace(['Man', 'Vrouw'], ['Mr', 'Ms'], $value)
                ];
            },
            'Geboortedatum' => function ($value) {
                $dateTime = \DateTime::createFromFormat('j-n-Y', $value, new \DateTimeZone('Europe/Amsterdam'));
                $dateTime->setTime(0, 0, 0);

                return [
                    'birthday' => new Time($dateTime)
                ];
            },
            'Geboren te' => 'birthplace',
            'Opleiding' => function ($value) {
                return [
                    'learning_pathway' => trim(substr($value, 5, 4)),
                    'study_program_id' => substr($value, 0, 5)
                ];
            },
            'Groepscode' => 'groupcode'
        ];

        foreach ($workload['results'] as $index => $remoteStudent) {
            $studentConditions = ['student_number' => $remoteStudent['Nummer']];

            $student = ($this->Students->exists($studentConditions))
                ? $this->Students->find()->where($studentConditions)->first() : $this->Students->newEntity([
                    'country' => 'NL'
                ]);

            foreach ($fieldMapping as $remoteField => $localField) {
                if (!isset($remoteStudent[$remoteField])) {
                    continue;
                }

                if (is_callable($localField)) {
                    $student->set($localField($remoteStudent[$remoteField]));

                    continue;
                }

                if (trim($remoteStudent[$remoteField]) === '') {
                    continue;
                }

                $student->set($localField, trim($remoteStudent[$remoteField]));
            }

            $this->Students->save($student);

            $job->sendStatus($index + 1, count($workload['results']));
        }
    }
}
