<?php

use yii\db\Migration;

/**
 * Class m230615_172312_update_users_table
 */
class m230615_172312_update_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('users', 'merchant_id', $this->integer()->defaultValue(null));
        $this->addColumn('users', 'store_id', $this->integer()->defaultValue(null));
        
        $this->addForeignKey(
            'fk-users-merchant_id', 
            'users',
            'merchant_id', 
            'merchants', 
            'id', 
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-users-store_id',
            'users',
            'store_id',
            'stores',
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
            '{{%fk-users-store_id}}',
            '{{%users}}'
        );
        $this->dropForeignKey(
            '{{%fk-users-merchant_id}}',
            '{{%users}}'
        );
        $this->dropColumn('users', 'store_id');
        $this->dropColumn('users', 'merchant_id');
    }

    
}
