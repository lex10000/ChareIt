<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%comment}}`.
 */
class m201017_094353_create_comment_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'post_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'comment' => $this->string(255)->notNull()->comment('комментарий к посту'),
            'date' => $this->integer()->notNull()->comment('дата создания')
        ]);
    }


    public function safeDown()
    {
        $this->dropTable('{{%comment}}');
    }
}
