<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\Crypt;
use app\components\User;
use kartik\detail\DetailView;

?>

<?= DetailView::widget([
    'model' => $model,
    'condensed' => true,
    'hover' => true,
    'mode' => DetailView::MODE_VIEW,
    'enableEditMode' => FALSE,
    'panel' => [
        'heading' => Yii::t('app', 'Tassi di cambio'),
        'type' => DetailView::TYPE_INFO,
    ],
    'labelColOptions' => ['style' => 'width:15%'],
    'valueColOptions' => ['style' => 'width:35%'],
    'attributes' => [
        [
            'attribute' => 'preferredSource',
            'format' => 'raw',
            'value' => Html::encode($model->storesettings->preferredSource),
        ],
        [
            'attribute' => 'defaultCurrencyPair',
            'format' => 'raw',
            'value' => Html::encode($model->storesettings->defaultPaymentMethod . '_' . $model->storesettings->defaultCurrency)
        ],
        [
            'attribute' => 'spread',
            'format' => 'raw',
            'value' => Html::encode($model->storesettings->spread)
        ],
        [
            // 'attribute' => 'current price',
            'label' => Yii::t('app','Current Price'),
            'format' => 'raw',
            'value' => print_r($rates,true)
        ],

    ],
]) ?>

<div class="card-footer">
    <div class="d-flex flex-row">
        <div class="p-1">
            <?= Html::a('<i class="fas fa-pen"></i> ' . Yii::t('app', 'Update'), ['update-rates', 'id' => Crypt::encrypt($model->id)], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
</div>