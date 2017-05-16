<?php
namespace eurojet\job\components;


use eurojet\job\models\JobStatus;
use eurojet\job\models\Job;

use Yii;

class JobManager
{

    public $jobs = array();

    //The time after start_time running jobs are removed from the queue
    public $removeHangingJobsThreshold = 28800; //8 hours

    protected function saveJob(Job $job)
    {
        if (!$job->save())
        {
            Yii::error('Saving a job failed: '.print_r($job->errors), 'error');
            return false;
        }
        return true;
    }
    /**
     *
     * @param Job $job
     */
    public function addJob(Job $job, $checkEnqueuedDuplicate = true)
    {
        if ($checkEnqueuedDuplicate)
        {
            $duplicate = $this->findEnqueuedDuplicateJob($job);
            if ($duplicate)
            {
                $duplicate->planned_time = $job->planned_time;
                return $this->saveJob($duplicate);
            }
        }

        return $this->saveJob($job);
    }

    protected function timestampToDatabaseDate($timestamp = null)
    {
        if ($timestamp === null)
        {
            $timestamp = time();
        }

        return date("Y-m-d G:i:s", $timestamp);
    }

    /**
     *
     * @param Job $job
     * @return Job $job
     */
    protected function findEnqueuedDuplicateJob(Job $job)
    {
        $jobs = Job::find()->andWhere(['crontab'=>null,'job_class'=>$job->job_class,'job_status_id'=>JobStatus::ENQUEUED])->all();
        foreach ($jobs as $compareJob)
        {
            if ($compareJob->isDuplicateOf($job))
                return $compareJob;
        }
        return null;
    }

    /**
     * checks the database for running jobs older than the treshold in {@link removeHangingJobsThreshold}, logs an error and removes them from queue
     */
    public function removeHangingJobs()
    {
        $startTime = $this->timestampToDatabaseDate(strtotime("- {$this->removeHangingJobsThreshold} seconds"));

        Yii::trace("checking for jobs started before $startTime");


        $jobs = Job::find()->andWhere(['<', 'start_time', $startTime])->andWhere(['job_status_id'=>JobStatus::RUNNING])->all();
        foreach ($jobs as $job)
        {
            $job->abort();
        }
    }

    /**
     * Will sync jobs from config with database. Updates the planned time when the job already exists and the crontab has changed.
     *
     * @throws Exception
     */
    public function syncJobs()
    {
        $ids = array();
        foreach ($this->jobs as $attributes)
        {
            if (!isset($attributes['class']))
            {
                throw new Exception('Job needs to define a class');
            }

            if (!isset($attributes['crontab']))
            {
                throw new Exception('Job needs to define a crontab value');
            }

            $model = Job::find()->where('job_class=:job_class AND crontab IS NOT NULL',['job_class'=>$attributes['class']])->one();
            if (!$model)
            {
                $model = new Job;
                $model->job_class = $attributes['class'];
            }
            elseif ($model->job_status_id == JobStatus::RUNNING)
            {
                $ids[] = $model->id;
                //do not sync a job which is currently running, we do not want to break it
                continue;
            }

            $oldCrontab = $model->crontab;

            unset($attributes['class']);

            $model->setAttributes($attributes);

            if ($attributes['crontab'] != $oldCrontab)
                $model->calculateNextPlannedTime();

            $this->addJob($model, false);

            $ids[] = $model->id;
        }

        //delete all jobs with crontab entry that were not modified during the loop before
        Job::deleteAll("`crontab` IS NOT NULL AND id=:id",[':id' => $ids]);
    }

    public function runJobs()
    {
        Yii::trace("Run Jobs");
        $now = $this->timestampToDatabaseDate();

        $job = Job::find()->where('planned_time <= :planned_time AND (start_time IS NULL OR start_time <=:start_time) AND job_status_id=:job_status_id',
            [':planned_time'=>$now,':job_status_id'=>JobStatus::ENQUEUED,':start_time'=>$now])
            ->orderBy(['planned_time' => SORT_ASC])->one();

        if ($job)
        {
            Yii::trace("execute job {$job->id}");
            $job->execute();
            $this->runJobs();
        }
    }
}