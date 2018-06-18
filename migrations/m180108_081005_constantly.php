<?php

use yii\db\Migration;

class m180108_081005_constantly extends Migration
{
    public function safeUp()
    {
        $this->addColumn(
            '{{%tracker_issue}}',
            'continuous_use',
            $this->boolean()->defaultValue(false)
        );
    }

    public function safeDown()
    {
        $this->dropColumn('{{%tracker_issue}}','continuous_use');
    }
}
