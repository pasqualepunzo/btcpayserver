<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%invoices}}`.
 */
class m230530_174932_create_invoices_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%invoices}}', [
            'id' => $this->primaryKey(),
            'merchant_id' => $this->integer()->notNull(),
            'store_id' => $this->integer()->notNull(),
            'pos_id' => $this->integer()->notNull(),

            'invoiceType' => $this->string(20)->notNull(),
            'status' => $this->string(20)->notNull(),

            // data sent to create invoice
            'metadata' => $this->json(),
            'checkout' => $this->json(),
            'receipt' => $this->json(),
            
            // response from create invoice
            'invoiceId' => $this->string(60)->defaultValue(null),
            'storeId' => $this->string(512)->defaultValue(null),
            'amount' => $this->float()->defaultValue(0),
            'currency' => $this->string(20)->defaultValue(null),

            'type' => $this->string(20)->defaultValue(null),
            'checkoutLink' => $this->string(512)->defaultValue(null),
            'createdTime' => $this->integer()->defaultValue(null),
            'expirationTime' => $this->integer()->defaultValue(null),
            'monitoringExpiration' => $this->integer()->defaultValue(null),
            'additionalStatus' => $this->string(20)->defaultValue(null),
            'availableStatusesForManualMarking' => $this->json(),
            'archived' => $this->boolean()->defaultValue(0),
        ]);

        // creates index for column `merchant_id`
        $this->createIndex(
            '{{%idx-invoices-merchant_id}}',
            '{{%invoices}}',
            'merchant_id'
        );

        // creates index for column `store_id`
        $this->createIndex(
            '{{%idx-invoices-store_id}}',
            '{{%invoices}}',
            'store_id'
        );

        // creates index for column `pos_id`
        $this->createIndex(
            '{{%idx-invoices-pos_id}}',
            '{{%invoices}}',
            'pos_id'
        );

        // add foreign key for table `{{%invoices}}`
        $this->addForeignKey(
            '{{%fk-invoices-merchant_id}}',
            '{{%invoices}}',
            'merchant_id',
            '{{%merchants}}',
            'id',
            'CASCADE'
        );

        // add foreign key for table `{{%invoices}}`
        $this->addForeignKey(
            '{{%fk-invoices-store_id}}',
            '{{%invoices}}',
            'store_id',
            '{{%stores}}',
            'id',
            'CASCADE'
        );

        // add foreign key for table `{{%invoices}}`
        $this->addForeignKey(
            '{{%fk-invoices-pos_id}}',
            '{{%invoices}}',
            'pos_id',
            '{{%pos}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%invoices}}`
        $this->dropForeignKey(
            '{{%fk-invoices-pos_id}}',
            '{{%invoices}}'
        );

        // drops foreign key for table `{{%invoices}}`
        $this->dropForeignKey(
            '{{%fk-invoices-store_id}}',
            '{{%invoices}}'
        );

        // drops foreign key for table `{{%invoices}}`
        $this->dropForeignKey(
            '{{%fk-invoices-merchant_id}}',
            '{{%invoices}}'
        );

        // drops index for column `pos_id`
        $this->dropIndex(
            '{{%idx-invoices-pos_id}}',
            '{{%invoices}}'
        );

        // drops index for column `store_id`
        $this->dropIndex(
            '{{%idx-invoices-store_id}}',
            '{{%invoices}}'
        );

        // drops index for column `merchant_id`
        $this->dropIndex(
            '{{%idx-invoices-merchant_id}}',
            '{{%invoices}}'
        );

        

        $this->dropTable('{{%invoices}}');
    }
}
