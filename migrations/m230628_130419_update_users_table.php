<?php

use yii\db\Migration;

/**
 * Class m230628_130419_update_users_table
 */
class m230628_130419_update_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey(
            'fk-users-privilege_id',
            'users',
            'privilege_id',
            'privileges',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-users-privilege_id}}',
            '{{%users}}'
        );
    }

 
}
