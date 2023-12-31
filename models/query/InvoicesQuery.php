<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\Invoices]].
 *
 * @see \app\models\Invoices
 */
class InvoicesQuery extends \yii\db\ActiveQuery
{
    public function byInvoiceId($invoiceId)
    {
        return $this->andWhere([
            'invoiceId' => $invoiceId
        ]);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Invoices[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Invoices|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
