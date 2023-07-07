<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%pos}}`.
 */
class m230529_132601_create_pos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%pos}}', [
            'id' => $this->primaryKey(),
            'merchant_id' => $this->integer()->notNull(),
            'store_id' => $this->integer()->notNull(),
            'appName' => $this->string(30)->notNull(),
            'description' => $this->string(512)->notNull(),

            'create_date' => $this->date()->notNull(),
            'close_date' => $this->date()->defaultValue(NULL),
            'historical' => $this->integer()->notNull()->defaultValue(0),
        ]);

        // creates index for column `merchant_id`
        $this->createIndex(
           '{{%idx-pos-merchant_id}}',
           '{{%pos}}',
           'merchant_id'
        );

        // creates index for column `store_id`
        $this->createIndex(
           '{{%idx-pos-store_id}}',
           '{{%pos}}',
           'store_id'
        );

        // add foreign key for table `{{%pos}}`
        $this->addForeignKey(
            '{{%fk-merchant_id}}',
            '{{%pos}}',
            'merchant_id',
            '{{%merchants}}',
            'id',
            'CASCADE'
        );

        // add foreign key for table `{{%pos}}`
        $this->addForeignKey(
            '{{%fk-store_id}}',
            '{{%pos}}',
            'store_id',
            '{{%stores}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%stores}}`
        $this->dropForeignKey(
            '{{%fk-merchant_id}}',
            '{{%pos}}'
        );

        // drops foreign key for table `{{%stores}}`
        $this->dropForeignKey(
            '{{%fk-store_id}}',
            '{{%pos}}'
        );

        // drops index for column `merchant_id`
        $this->dropIndex(
            '{{%idx-pos-merchant_id}}',
            '{{%pos}}'
        );

        // drops index for column `merchant_id`
        $this->dropIndex(
            '{{%idx-pos-store_id}}',
            '{{%pos}}'
        );

        $this->dropTable('{{%pos}}');
    }
}
