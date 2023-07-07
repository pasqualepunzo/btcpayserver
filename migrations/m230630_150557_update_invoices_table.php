<?php

use yii\db\Migration;

/**
 * Class m230630_150557_update_invoices_table
 */
class m230630_150557_update_invoices_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('invoices', 'paid', $this->float()->defaultValue(0)->after('amount'));
        $this->addColumn('invoices', 'destination', $this->string(256)->defaultValue(null)->after('paid'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('invoices', 'destination');
        $this->dropColumn('invoices', 'paid');
    }

    
}
