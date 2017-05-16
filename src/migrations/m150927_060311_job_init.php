<?php
/**
 * Created by PhpStorm.
 * User: vinco
 * Date: 16.5.17
 * Time: 8:04
 */

use yii\db\Schema;
use yii\db\Migration;
class m150927_060311_job_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%Job}}', [
            'id' => Schema::TYPE_PK,
            'job_class'=> Schema::TYPE_STRING. ' NOT NULL',
            'job_data'=> Schema::TYPE_STRING,
            'crontab'=> Schema::TYPE_CHAR. '(128)',
            'planned_time'=> Schema::TYPE_DATETIME,
            'start_time'=> Schema::TYPE_DATETIME ,
            'job_status_id'=> Schema::TYPE_INTEGER ,
            'create_time'=> Schema::TYPE_DATETIME ,
            'update_time'=> Schema::TYPE_DATETIME
        ], $tableOptions);
        $this->createTable('{{%Job_Log}}', [
            'id' => Schema::TYPE_PK,
            'job_class'=> Schema::TYPE_STRING. ' NOT NULL',
            'start_time'=> Schema::TYPE_DATETIME ,
            'finish_time'=> Schema::TYPE_DATETIME ,
            'job_status_id'=> Schema::TYPE_INTEGER ,
            'finish_message'=> Schema::TYPE_TEXT ,
            'create_time'=> Schema::TYPE_DATETIME ,
            'update_time'=> Schema::TYPE_DATETIME,
            'queue'=> Schema::TYPE_CHAR. '(45)',
            'progress'=> Schema::TYPE_INTEGER,
            'job_data'=> Schema::TYPE_STRING,
            'job_id'=> Schema::TYPE_INTEGER ,
            'token'=> Schema::TYPE_CHAR. '(23)',
            'identifier1'=> Schema::TYPE_CHAR. '(64)',
            'identifier2'=> Schema::TYPE_CHAR. '(64)',
            'identifier3'=> Schema::TYPE_CHAR. '(64)',
            'identifier4'=> Schema::TYPE_CHAR. '(64)',
        ], $tableOptions);
    }
    public function down()
    {
        $this->dropTable('{{%Job_Log}}');
        $this->dropTable('{{%Job}}');

    }
}