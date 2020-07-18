<?php

use yii\db\Migration;

/**
 * Class m200523_064959_create_table_test
 */
class m200523_064959_create_table_test extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOpts = null;

        $this->createTable('{{%test}}', [
            'id' => $this->primaryKey(),
            'user' => $this->string()->notNull()->unique(),
        ], $tableOpts);
    }

    public function down()
    {
        $this->dropTable('{{%test}}');
    }

}
