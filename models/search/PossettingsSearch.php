<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Possettings;

/**
 * PossettingsSearch represents the model behind the search form of `app\models\Possettings`.
 */
class PossettingsSearch extends Possettings
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'pos_id', 'showCustomAmount', 'showDiscount', 'enableTips', 'redirectAutomatically', 'requiresRefundEmail'], 'integer'],
            [['sin', 'appName', 'title', 'description', 'template', 'defaultView', 'currency', 'customAmountPayButtonText', 'fixedAmountPayButtonText', 'tipText', 'customCSSLink', 'embeddedCSS', 'notificationUrl', 'redirectUrl', 'checkoutType', 'formId'], 'safe'],
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
        $query = Possettings::find();

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
            'pos_id' => $this->pos_id,
            'showCustomAmount' => $this->showCustomAmount,
            'showDiscount' => $this->showDiscount,
            'enableTips' => $this->enableTips,
            'redirectAutomatically' => $this->redirectAutomatically,
            'requiresRefundEmail' => $this->requiresRefundEmail,
        ]);

        $query->andFilterWhere(['like', 'sin', $this->sin])
            ->andFilterWhere(['like', 'appName', $this->appName])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'template', $this->template])
            ->andFilterWhere(['like', 'defaultView', $this->defaultView])
            ->andFilterWhere(['like', 'currency', $this->currency])
            ->andFilterWhere(['like', 'customAmountPayButtonText', $this->customAmountPayButtonText])
            ->andFilterWhere(['like', 'fixedAmountPayButtonText', $this->fixedAmountPayButtonText])
            ->andFilterWhere(['like', 'tipText', $this->tipText])
            ->andFilterWhere(['like', 'customCSSLink', $this->customCSSLink])
            ->andFilterWhere(['like', 'embeddedCSS', $this->embeddedCSS])
            ->andFilterWhere(['like', 'notificationUrl', $this->notificationUrl])
            ->andFilterWhere(['like', 'redirectUrl', $this->redirectUrl])
            ->andFilterWhere(['like', 'checkoutType', $this->checkoutType])
            ->andFilterWhere(['like', 'formId', $this->formId]);

        return $dataProvider;
    }
}
