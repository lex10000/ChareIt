<?php

use yii\db\Migration;

/**
 * Class m200918_100245_alter_post_table
 */
class m200918_100245_alter_post_table extends Migration
{

    private $table = '{{%post}}';

    public function safeUp()
    {
        $this->addColumn($this->table, 'thumbnail', $this->string()->notNull());
    }

    public function safeDown()
    {
       $this->dropColumn($this->table, 'thumbnail');
    }
}
