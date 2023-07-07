<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%stores}}`.
 */
class m230421_065749_create_stores_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%stores}}', [
            'id' => $this->primaryKey(),
            'merchant_id' => $this->integer()->notNull(),
            'description' => $this->string(256)->notNull(),
            'create_date' => $this->date()->notNull(),
            'close_date' => $this->date()->defaultValue(NULL),
            'historical' => $this->integer()->notNull()->defaultValue(0),
        ]);

        $this->createIndex('{{%idx-stores-merchant_id}}', '{{%stores}}', 'merchant_id');
        
        $this->addForeignKey(
            '{{%fk-stores-merchant_id}}',
            '{{%stores}}',
            'merchant_id',
            '{{%merchants}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-stores-merchant_id}}', '{{%stores}}');
        $this->dropIndex('{{%idx-stores-merchant_id}}', '{{%stores}}');

        $this->dropTable('{{%stores}}');
    }
}
