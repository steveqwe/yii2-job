<?php
namespace eurojet\job\commands;

use eurojet\job\components\JobManager;
use yii\console\Controller;

class JobCommand extends Controller
{
	private $jobManager;

    public function __construct($id, $module, $config = [])
    {
        $this->jobManager=new JobManager();
        parent::__construct($id, $module, $config);
	}
	public function actionRemoveHangingJobs()
	{

        $this->jobManager->removeHangingJobs();
	}
	
	public function actionSyncJobs()
	{
        $this->jobManager->syncJobs();
	}
	
	public function actionRunJobs($queue = null)
	{
        $this->jobManager->runJobs($queue);
	}
	
	public function actionIndex($queue = null)
	{
        $this->jobManager->removeHangingJobs();
        $this->jobManager->syncJobs();
        $this->jobManager->runJobs($queue);
	}
}