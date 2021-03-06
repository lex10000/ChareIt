<?php

use yii\db\Migration;

/**
 * Class m200524_043250_alter_user_table
 */
class m200524_043250_alter_user_table extends Migration
{
    // Use up()/down() to run migration code without a transaction.

    public function up()
    {
        $this->addColumn('{{%user}}', 'about', $this->text());
        $this->addColumn('{{%user}}', 'type', $this->integer());
        $this->addColumn('{{%user}}', 'nickname', $this->string());
        $this->addColumn('{{%user}}', 'picture', $this->string());
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'about');
        $this->dropColumn('{{%user}}', 'type');
        $this->dropColumn('{{%user}}', 'nickname');
        $this->dropColumn('{{%user}}', 'picture');
    }

}
