<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\UserTokens]].
 *
 * @see \app\models\UserTokens
 */
class UserTokensQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\UserTokens[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\UserTokens|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
