<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Users;

/**
 * UsersSearch represents the model behind the search form of `app\models\Users`.
 */
class UsersSearch extends Users
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'privilege_id', 'is_active', 'merchant_id', 'store_id'], 'integer'],
            [['username', 'first_name', 'last_name', 'oauth_provider', 'oauth_uid', 'authKey', 'accessToken', 'email', 'picture'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Users::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'privilege_id' => $this->privilege_id,
            'is_active' => $this->is_active,
            'merchant_id' => $this->merchant_id,
            'store_id' => $this->store_id,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'authKey', $this->authKey])
            ->andFilterWhere(['like', 'accessToken', $this->accessToken])
            ->andFilterWhere(['like', 'oauth_provider', $this->oauth_provider])
            ->andFilterWhere(['like', 'oauth_uid', $this->oauth_uid])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'picture', $this->picture]);

        return $dataProvider;
    }
}
