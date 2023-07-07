<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\Payments]].
 *
 * @see \app\models\Payments
 */
class PaymentsQuery extends \yii\db\ActiveQuery
{
    public function byInvoiceId($id)
    {
        return $this->andWhere([
            'invoice_id' => $id
        ]);
    }

    public function byPaymentsMethod($model)
    {
        return $this->andWhere([
            'invoice_id' => $model->invoice_id,
            'paymentMethod' => $model->paymentMethod,
            'destination' => $model->destination,
        ]);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Payments[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Payments|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
