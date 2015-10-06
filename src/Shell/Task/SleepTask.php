<?php
namespace App\Shell\Task;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use GearmanJob;

class SleepTask extends Shell {

    public function main($return, GearmanJob $job)
    {
        $job->sendStatus(0, 2);

        sleep($return['timeout']/2);

        $job->sendStatus(1, 2);

        sleep($return['timeout']/2);

        return array(
            'Ik heb zojuist zoveel seconden gewacht' => $return['timeout']
        );
    }
}
