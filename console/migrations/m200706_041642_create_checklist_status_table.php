<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%checklist_status}}`.
 */
class m200706_041642_create_checklist_status_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%checklist_status}}', [
            'id' => $this->integer()->notNull()->comment('id статуса'),
            'name' => $this->string(50)->notNull()->comment('название статуса')
        ]);
    }


    public function safeDown()
    {
        $this->dropTable('{{%checklist_status}}');
    }
}
