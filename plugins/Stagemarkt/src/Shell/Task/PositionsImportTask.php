<?php

namespace Stagemarkt\Shell\Task;

use App\Model\Entity\Company;
use App\Model\Entity\Position;
use App\Model\Entity\StudyProgram;
use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use DebugKit\DebugTimer;
use Stagemarkt\Entity\Entity;
use Stagemarkt\Locator\RepositoryLocator;
use Stagemarkt\WebserviceQuery;

class PositionsImportTask extends Shell
{

    public function initialize()
    {
        parent::initialize();

        $this->modelFactory('Repository', [new RepositoryLocator, 'get']);

        $this->loadModel('Stagemarkt.Positions', 'Repository');
    }

    public function import($conditions)
    {
        $positionsTable = TableRegistry::get('Positions');

        $results = $this->_importReadQuery($conditions)->count();
        $pages = ceil($results / 25);

        $timeEstimate = $pages * 1;
        $this->out(__('Importing {0} positions. This will take approximately {1} hours', $results, Time::createFromTimestamp($timeEstimate)->format('H:m:s')));

        $progress = $this->helper('Progress');

        $progress->init(['total' => $pages]);

        DebugTimer::start('positions-import');

        $this->io()->setLoggers(false);

        for ($page = 1; $page <= $pages; $page++) {
            $query = $this->_importReadQuery($conditions);
            $query->applyOptions(['page' => $page]);
            $entities = $query->all();

            /* @var Entity $remoteEntity */
            foreach ($entities as $remoteEntity) {
                if ($positionsTable->exists(['stagemarkt_id' => $remoteEntity->id] + $conditions)) {
                    $localPosition = $positionsTable->find()->where(['stagemarkt_id' => $remoteEntity->id] + $conditions)->first();
                    $localPosition->applyStagemarktEntity($remoteEntity);
                } else {
                    $localPosition = Position::createFromStagemarktEntity($remoteEntity);
                }

//                $remoteDetailPosition = $this->Positions->get($remoteEntity->id);
//                $localPosition->applyStagemarktEntity($remoteDetailPosition);

                if ($remoteEntity->has('company')) {
                    if ($positionsTable->Companies->exists(['stagemarkt_id' => $remoteEntity->company->id])) {
                        $localCompany = $positionsTable->Companies->find()->where(['stagemarkt_id' => $remoteEntity->company->id])->first();
                        $localCompany->applyStagemarktEntity($remoteEntity->company);
                    } else {
                        $localCompany = Company::createFromStagemarktEntity($remoteEntity->company);
                    }

                    $localPosition->set([
                        'company' => $localCompany
                    ]);
                }
                if ($remoteEntity->has('study_program')) {
                    if ($positionsTable->StudyPrograms->exists(['id' => $remoteEntity->study_program->id])) {
                        $localStudyProgram = $positionsTable->StudyPrograms->find()->where(['id' => $remoteEntity->study_program->id])->first();
                        $localStudyProgram->applyStagemarktEntity($remoteEntity->study_program);
                    } else {
                        $localStudyProgram = StudyProgram::createFromStagemarktEntity($remoteEntity->study_program);
                    }

                    $localPosition->set([
                        'study_program' => $localStudyProgram
                    ]);
                }

                $positionsTable->save($localPosition, [
                    'associated' => [
                        'Companies',
                        'StudyPrograms'
                    ]
                ]);
            }

            $progress->increment();
            $progress->draw();
        }

        $this->out();

        $this->io()->setLoggers(true);

        DebugTimer::stop('positions-import');

        $duration = DebugTimer::elapsedTime('positions-import');
        $this->out(__('Import took {0}, that\'s {1} off the estimate', Time::createFromTimestamp($duration)->format('H:m:s'), Time::createFromTimestamp(abs($duration - $timeEstimate))->format('H:m:s')));
    }

    public function getOptionParser()
    {
        $consoleOptionParser = parent::getOptionParser();

        $consoleOptionParser->addOption('study_program_id');

        return $consoleOptionParser;
    }

    /**
     * @return WebserviceQuery
     */
    protected function _importReadQuery(array $conditions = [])
    {
        $query = $this->Positions->find();
        $query->applyOptions(['limit' => '25']);
        $query->conditions($conditions);

        return $query;
    }

}
