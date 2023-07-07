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
        'heading' => Yii::t('app', 'Generale'),
        'type' => DetailView::TYPE_INFO,
    ],
    'labelColOptions' => ['style' => 'width:15%'],
    'valueColOptions' => ['style' => 'width:35%'],
    'attributes' => [
        [
            'attribute' => 'merchant_id',
            'format' => 'raw',
            'value' => Html::encode($model->merchant->description ?? null),
            'visible' => User::can(30),
        ],
        [
            'label' => $model->getAttributeLabel('bps_storeid'),
            'format' => 'raw',
            'value' =>  call_user_func(function ($data) {
                return Html::encode($data->bps_storeid);
            }, $model->storesettings),
        ],
        
        [
            'attribute' => 'description',
            'format' => 'raw',
            'value' => Html::encode($model->description ?? null)
        ],
        // [
        //     'attribute' => 'website',
        //     'value' => Html::encode($model->storesettings->website),
        //     'format' => 'raw',
        // ],
       
        // [
        //     'attribute' => 'defaultCurrency',
        //     'value' => Html::encode($model->storesettings->defaultCurrency),
        //     'format' => 'raw',
        // ],
        [
            'attribute' => 'speedPolicy',
            'value' => Html::encode($model->storesettings->speedPolicy),
            'format' => 'raw',
        ],
        [
            'attribute' => 'networkFeeMode',
            'value' => Html::encode($model->storesettings->networkFeeMode),
            'format' => 'raw',
        ],
        [
            'attribute' => 'invoiceExpiration',
            'value' => Html::encode($model->storesettings->invoiceExpiration),
            'format' => 'raw',
        ],
        [
            'attribute' => 'Cambio',
            'value' => Html::encode($rates),
            'format' => 'raw',
        ],


    ],
]) ?>

<?php if (User::can(40)): ?>
<div class="card-footer">
    <div class="d-flex flex-row">
        <div class="p-1">
            <?= Html::a('<i class="fas fa-pen"></i> ' . Yii::t('app', 'Modifica'), ['update-general', 'id' => Crypt::encrypt($model->id)], ['class' => 'btn btn-primary']) ?>
        </div>

        <div class="p-1 ml-auto">
            <?= Html::a('<i class="fas fa-trash"></i> ' . Yii::t('app', 'Elimina'), ['delete', 'id' => Crypt::encrypt($model->id)], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Vuoi eliminare questo negozio?'),
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>
</div>
<?php endif; ?>