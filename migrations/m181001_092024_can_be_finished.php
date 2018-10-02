<?php

use yii\db\Migration;

/**
 * Class m181001_092024_can_be_finished
 */
class m181001_092024_can_be_finished extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%tracker_issue}}',
            'can_be_finished',
            $this->boolean()->defaultValue(false)
        );

        $query = new \yii\db\Query();
        $issues = $query->from('{{%tracker_issue}}');
        foreach ($issues->batch() as $issues100) {
            foreach ($issues100 as $issue) {
                $assignees = (new \yii\db\Query())
                    ->from('{{%tracker_assignee}}')
                    ->where(['issue_id' => $issue['id']])
                    ->all();

                $needChange = true;
                foreach ($assignees as $assignee) {
                    if (!$assignee['finish_mark']) {
                        $needChange = false;
                    }
                }
                if ($needChange && count($assignees) > 0) {
                    (new \yii\db\Query())
                        ->createCommand()
                        ->update('{{%tracker_issue}}', ['can_be_finished' => true], ['id' => $issue['id']])
                        ->execute();
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%tracker_issue}}', 'can_be_finished');
    }
}
