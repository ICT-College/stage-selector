<?php

namespace App\Shell\Task;

use App\Model\Entity\Company;
use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use CvoTechnologies\Gearman\JobAwareTrait;
use Muffin\Webservice\Model\EndpointRegistry;
use Psr\Log\LogLevel;

class PositionDetailsTask extends Shell
{

    use JobAwareTrait;

    /**
     * Updates the details of a position
     *
     * @param array $options Options to use in task
     *
     * @return void
     */
    public function main($options)
    {
        if (!is_array($options)) {
            return;
        }

        ConnectionManager::alias($options['datasource'], 'default');

        $this->loadModel('Positions');

        $companiesEndpoint = EndpointRegistry::get('IctCollege/Stagemarkt.Positions');

        $position = $options['position'];

        $this->log(__('Looking up details for position {0} with id {1}', $position->name, $position->id), LogLevel::INFO);

        $positionResource = $companiesEndpoint->get($position->stagemarkt_id);

        if (!$positionResource) {
            $this->log(__('Looking up details for position {0} with id {1} failed', $position->name, $position->id), LogLevel::NOTICE);

            return;
        }

        $fields = [
            'start',
            'end'
        ];
        foreach ($fields as $field) {
            if (empty($field)) {
                continue;
            }
            if ($position->get($field) === $positionResource->get($field)) {
                continue;
            }

            $position->set($field, $positionResource->get($field));
        }

        if (!$this->Positions->save($position)) {
            return;
        }

        $qualificationPartEntities = [];
        foreach ($positionResource->qualification_parts as $index => $qualificationPart) {
            $qualificationPartExists = $this->Positions->QualificationParts->exists([
                'description' => $qualificationPart['description'],
                'study_program_id' => $positionResource->study_program['id']
            ]);
            if ($qualificationPartExists) {
                $qualificationPartEntity = $this->Positions->QualificationParts->find()->where([
                    'description' => $qualificationPart['description'],
                    'study_program_id' => $positionResource->study_program['id']
                ])->first();

            } else {
                $qualificationPartEntity = $this->Positions->QualificationParts->newEntity([
                    'description' => $qualificationPart['description'],
                    'type' => $qualificationPart['type'],
                    'study_program_id' => $positionResource->study_program['id']
                ]);
            }

            $qualificationPartEntity = $this->Positions->QualificationParts->patchEntity($qualificationPartEntity, [
                'number' => $index + 1,
                'description' => $qualificationPart['description'],
                'type' => $qualificationPart['type'],
            ]);

            $qualificationPartEntity = $this->Positions->QualificationParts->save($qualificationPartEntity);

            $relationExists = $this->Positions->QualificationParts->junction()->exists([
                'position_id' => $position->id,
                'qualification_part_id' => $qualificationPartEntity->id
            ]);

            if ($relationExists) {
                continue;
            }

            $qualificationPartEntities[] = $qualificationPartEntity;
        }

        if (!$this->Positions->QualificationParts->link($position, $qualificationPartEntities)) {
            throw new \UnexpectedValueException(__('Could not link qualification parts to position '));
        }

        $positionWithQualificationParts = $this->Positions->get($position->id, [
            'contain' => ['QualificationParts']
        ]);
    }
}
