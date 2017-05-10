<?php

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class m131023_165214_tracker_issues_initial extends \yii\db\Migration
{
    public function up()
    {
        $this->createTable('{{%tracker_issue}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255),
            'description' => $this->text(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'priority' => $this->smallInteger()->notNull()->defaultValue(100),
            'started_at' => $this->dateTime(),
            'deadline' => $this->dateTime(),
            'finished_at' => $this->dateTime(),
        ], '');

        $this->createTable('{{%tracker_tag}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(25)->notNull(),
            'description' => $this->text(),
            'bg_color' => $this->string()->notNull()->defaultValue('#d1d1d1'),
            'text_color' => $this->string()->notNull()->defaultValue('#7a7a7a'),
            'owner_id' => $this->integer()->notNull(),
        ], '');

        $this->createTable('{{%tracker_issues_tags}}', [
            'id' => $this->primaryKey(),
            'issue_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
        ], '');

        $this->createTable('{{%tracker_assignee}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'issue_id' => $this->integer()->notNull(),
            'view_mark' => $this->boolean()->notNull()->defaultValue(0),
            'finish_mark' => $this->smallInteger()->defaultValue(0),
            'created_at' => $this->dateTime(),
            'viewed_at' => $this->dateTime(),
            'finished_at' => $this->dateTime(),
        ], '');

        $this->addForeignKey(
            'fa_tracker_assignees',
            '{{%tracker_assignee}}',
            'issue_id',
            '{{%tracker_issue}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fa_tracker_tag_issues',
            '{{%tracker_issues_tags}}',
            'issue_id',
            '{{%tracker_issue}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fa_tracker_issue_tags',
            '{{%tracker_issues_tags}}',
            'tag_id',
            '{{%tracker_tag}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fa_tracker_assignees', '{{%tracker_assignee}}');
        $this->dropForeignKey('fa_tracker_tag_issues', '{{%tracker_issues_tags}}');
        $this->dropForeignKey('fa_tracker_issue_tags', '{{%tracker_issues_tags}}');
        $this->dropTable('{{%tracker_issue}}');
        $this->dropTable('{{%tracker_assignee}}');
        $this->dropTable('{{%tracker_issues_tags}}');
        $this->dropTable('{{%tracker_tag}}');
    }
}
