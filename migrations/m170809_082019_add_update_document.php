<?php

use yii\db\Migration;

class m170809_082019_add_update_document extends Migration
{
    /**
     * NOTE: After this migration you should move manually `create_at` `create_by` values of columns
     * from `content` to `document` tables. After you should delete all `Document` objects in content/activity tables.
     * It's can't done automatic because this tables is part of HumHub, not of this Module.
     */
    public function safeUp()
    {
        $this->createTable('{{%tracker_document_files}}', [
            'id' => $this->primaryKey(),
            'document_id' => $this->integer()->notNull(),
            'filename' => $this->string()->notNull(),
            'is_show' => $this->boolean()->notNull()->defaultValue(true),
            'comments' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->string()->notNull(),
        ]);

        $this->addForeignKey(
            'fa-tracker_document_files-document_id-tracker_document-id',
            '{{%tracker_document_files}}', 'document_id',
            '{{%tracker_document}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addColumn('{{%tracker_document}}', 'registered_at', $this->integer()->notNull());
        $this->addColumn('{{%tracker_document}}', 'created_at', $this->integer()->notNull());
        $this->addColumn('{{%tracker_document}}', 'created_by', $this->string()->notNull());

        $query = new \yii\db\Query();
        foreach ($query->from('{{%tracker_document}}')->each() as $rowDocument) {
            Yii::$app->db->createCommand()->insert('{{%tracker_document_files}}', [
                'document_id' => $rowDocument['id'],
                'filename' => $rowDocument['file'],
            ])->execute();
        }

        $this->dropColumn('{{%tracker_document}}', 'file');
    }

    public function safeDown()
    {
        // only forward ¯\_(ツ)_/¯
        return false;
    }
}
