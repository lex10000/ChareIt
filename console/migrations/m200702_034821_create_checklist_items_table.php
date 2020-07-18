<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%checklist_items}}`.
 */
class m200702_034821_create_checklist_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%checklist_items}}', [
            'id' => $this->primaryKey()->comment('id пункта'),
            'checklist_id' => $this->integer()->comment('id чек-листа'),
            'name' => $this->string()->comment('название пункта чек-листа'),
            'extra' => $this->integer()->comment('Обязательный/необязательный'),
        ]);

        $this->createIndex('checklist_id', '{{%checklist_items}}', 'checklist_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%checklist_items}}');
    }
}
