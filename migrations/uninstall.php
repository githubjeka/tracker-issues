<?php

/**
 * Will be up when Tracker module will be disabled
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class uninstall extends \yii\db\Migration
{
    public function up()
    {
        $this->dropForeignKey('fa_tracker_assignees', '{{%tracker_assignee}}');
        $this->dropForeignKey('fa_tracker_tag_issues', '{{%tracker_issues_tags}}');
        $this->dropForeignKey('fa_tracker_issue_tags', '{{%tracker_issues_tags}}');
        $this->dropForeignKey('fa_tracker_issue_parent', '{{%tracker_links}}');
        $this->dropForeignKey('fa_tracker_issue_child', '{{%tracker_links}}');

        $this->dropTable('{{%tracker_document_files}}');
        $this->dropTable('{{%tracker_documents_issues}}');
        $this->dropTable('{{%tracker_receiver_document}}');
        $this->dropTable('{{%tracker_issues_tags}}');
        $this->dropTable('{{%tracker_tag}}');
        $this->dropTable('{{%tracker_links}}');
        $this->dropTable('{{%tracker_assignee}}');
        $this->dropTable('{{%tracker_issue}}');
        $this->dropTable('{{%tracker_document}}');
    }
}
