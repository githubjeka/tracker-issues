<?php

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class m131023_165214_initial extends \yii\db\Migration
{
    public function up()
    {
        $this->createTable('tracker_issue', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255),
            'description' => $this->text(),
            'deadline' => $this->dateTime(),
            'status' => $this->smallInteger()-> not Null()->defaultValue(10),
            'priority' => $this->smallInteger()-> not Null()->defaultValue(100),
        ], '');

        $this->createTable('tracker_assignee', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()-> not Null(),
            'issue_id' => $this->integer()-> not Null(),
            'view_mark' => $this->boolean()-> not Null()->defaultValue(0),
            'finish_mark' => $this->smallInteger()->defaultValue(0),
        ], '');
    }

    public function down()
    {
        $this->dropTable('tracker_issue');
        $this->dropTable('tracker_assignee');
    }
}
