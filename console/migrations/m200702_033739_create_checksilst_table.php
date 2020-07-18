<?php

use yii\db\Migration;

class m200702_033739_create_checklist_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%checklist}}', [
            'id' => $this->primaryKey()->comment('id чек-листа'),
            'name' => $this->string()->comment('Название чек-листа')->notNull(),
            'user_id' => $this->integer()->comment('id пользователя')->notNull(),
            'status' => $this->smallInteger()->comment('Статус')->notNull(),
            'created_at' => $this->integer()->comment('Дата создания')->notNull(),
            'updated_at' => $this->integer()->comment('Дата последнего редактирования')->notNull(),
        ]);

        $this->createIndex('user_id_index', '{{%checklist}}', 'user_id');
    }


    public function safeDown()
    {
        $this->dropTable('{{%checklist}}');
    }
}
