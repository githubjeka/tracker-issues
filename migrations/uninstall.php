<?php

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class uninstall extends \yii\db\Migration
{
    public function up()
    {
        $this->dropTable('tracker_issue');
        $this->dropTable('tracker_assignee');
    }
}
