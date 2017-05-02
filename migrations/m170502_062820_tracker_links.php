<?php

use yii\db\Migration;

class m170502_062820_tracker_links extends Migration
{
    public function up()
    {
        $this->createTable('{{%tracker_links}}', [
            'id' => $this->primaryKey(),
            'type' => $this->smallInteger()->notNull(),
            'parent_id' => $this->integer()->notNull(),
            'child_id' => $this->integer()->notNull(),
        ], '');

        $this->addForeignKey(
            'fa_tracker_issue_parent',
            '{{%tracker_links}}',
            'parent_id',
            '{{%tracker_issue}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fa_tracker_issue_child',
            '{{%tracker_links}}',
            'child_id',
            '{{%tracker_issue}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fa_tracker_issue_parent', '{{%tracker_links}}');
        $this->dropForeignKey('fa_tracker_issue_child', '{{%tracker_links}}');
        $this->dropTable('{{%tracker_links}}');
    }
}
