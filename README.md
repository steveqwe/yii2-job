yii-job
=======

A simple module to have cron-like jobs in your database. In addition to yii's commands, yii-job can be used to create on-the-fly asynchronous jobs. 
Different kinds of jobs are supported:

1. Jobs with crontab that are triggered at defined times.
2. Ad-Hoc jobs that are executed at a defined time (or as soon as possible).

To actually process the jobs you can use the JobCommand, which itself can be triggered by a sytem cron job. 
A common scenario is system a cron job that is executed once per minute to trigger JobCommand.

Database Installing
----------

Run migration 

`yii migrate --migrationPath=@eurojet/yii2-job/migrations`

Yii Installing
----------

Mapping command `job` from this package to your configuration in main.php.




Run
----------

If you want to trigger the job processing from the command line you still need a cron job that executes the JobCommand.
It should be triggers like this:

```
yiic job
```

This is the index command which will sync your jobs in the config with your database and run all jobs that are due.
