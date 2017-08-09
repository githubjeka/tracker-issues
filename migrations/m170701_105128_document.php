<?php

use yii\db\Migration;

class m170701_105128_document extends Migration
{
    public function up()
    {
        $this->createTable('{{%tracker_document}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'file' => $this->string()->notNull(),
            'description' => $this->text(),
            'number' => $this->string(),
            'from' => $this->string(),
            'to' => $this->string(),
            'type' => $this->string(),
            'category' => $this->string(),
        ]);

        $this->createTable('{{%tracker_receiver_document}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'document_id' => $this->integer()->notNull(),
            'view_mark' => $this->boolean()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime(),
            'viewed_at' => $this->dateTime(),
        ], '');

        $this->addForeignKey('fk-tracker_receiver_document-document_id-document-id', '{{%tracker_receiver_document}}',
            'document_id', 'tracker_document', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%tracker_documents_issues}}', [
            'id' => $this->primaryKey(),
            'document_id' => $this->integer()->notNull(),
            'issue_id' => $this->integer()->notNull(),
        ], '');

        $this->addForeignKey('fk-tracker_documents_issues-document_id-document-id', '{{%tracker_documents_issues}}',
            'document_id', 'tracker_document', 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('fk-tracker_tracker_documents_issues-issue_id-issue-id', '{{%tracker_documents_issues}}',
            'issue_id', '{{%tracker_issue}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%tracker_documents_issues}}');
        $this->dropTable('{{%tracker_receiver_document}}');
        $this->dropTable('{{%tracker_document}}');
    }
}
