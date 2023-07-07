<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\Pos]].
 *
 * @see \app\models\Pos
 */
class PosQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere('[[historical]]=0');
    }

    public function byMerchantId($id)
    {
        return $this->andWhere([
            'merchant_id' => $id
        ]);
    }

    public function byStoreId($id)
    {
        return $this->andWhere([
            'store_id' => $id
        ]);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Pos[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Pos|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
