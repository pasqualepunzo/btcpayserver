<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%payments}}`.
 */
class m230704_084438_create_payments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('invoices', 'destination');
        $this->dropColumn('invoices', 'paid');
        
        $this->createTable('{{%payments}}', [
            'id' => $this->primaryKey(),
            'invoice_id' => $this->integer()->notNull(),
            'paymentMethod' => $this->string(512), // (tipo pagamento )
            'destination' => $this->string(2048), // (indirizzo)
            'rate' => $this->float(), // (exchange rate)
            'paymentMethodPaid' => $this->integer()->defaultValue(0), // (se è stato usato questo metodo)
            'totalPaid' => $this->float(), // float (totale pagato)
            'due' => $this->float(), // float (totale dovuto)
            'amount' => $this->float(), //float (importo in crypto)
            'networkFee' => $this->float(), //float (fee di rete)
            'payments' => $this->json(), // ->json (transazioni di pagamento)
            'additionalData' => $this->json(), //-> json (ulteriori informazioni)
        ]);

        $this->createIndex('{{%idx-payments-invoice_id}}', '{{%payments}}', 'invoice_id');
        
        $this->addForeignKey(
            '{{%fk-payments-invoice_id}}',
            '{{%payments}}',
            'invoice_id',
            '{{%invoices}}',
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
        $this->addColumn('invoices', 'paid', $this->float()->defaultValue(0)->after('amount'));
        $this->addColumn('invoices', 'destination', $this->string(256)->defaultValue(null)->after('paid'));

        // drops foreign key for table `{{%payments}}`
        $this->dropForeignKey(
            '{{%fk-payments-invoice_id}}',
            '{{%payments}}'
        );

        // drops index for column `invoice_id`
        $this->dropIndex(
            '{{%idx-payments-invoice_id}}',
            '{{%payments}}'
        );

        $this->dropTable('{{%payments}}');
    }
}
