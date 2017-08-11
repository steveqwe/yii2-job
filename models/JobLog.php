<?php
namespace eurojet\job\models;

use eurojet\job\models\_base\BaseJobLog;

class JobLog extends BaseJobLog implements JobInterface
{
	public $progress;
	public $job_id;
	public $job_data;
	public $identifier1;
	public $identifier2;
	public $identifier3;
	public $identifier4;
	public $queue;
	public $token;
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	  /**
	     * @inheritdoc
	     */
    	public static function tableName()
    	{
		return 'Job_Cron_Log';
    	}

    	public function save($runValidation = true, $attributeNames = null)
    	{
        	$this->finish_message = json_encode($this->finish_message);
        	return parent::save($runValidation, $attributeNames);
   	}

   	public function afterFind()
	{
		$this->finish_message = json_decode($this->finish_message, true);
		parent::afterFind();
	}
	
	public function getJobId()
	{
		return $this->job_id;
	}
	
	public function getJobToken()
	{
		return $this->token;
	}
	
	public function getJobData()
	{
		return json_decode($this->job_data, true);
	}
	
	public function getJobStatusId()
	{
		return $this->job_status_id;
	}
	
	public function getJobProgress()
	{
		return $this->progress;
	}
	
	public function getJobResult()
	{
		return $this->finish_message;
	}
	
	public function getJobInfo()
	{
		return new JobInfo($this);
	}
}
