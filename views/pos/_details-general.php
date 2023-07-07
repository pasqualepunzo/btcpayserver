<?php

use yii\helpers\Html;
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
    // 'panel' => [
    //     'heading' => Yii::t('app', 'Generale'),
    //     'type' => DetailView::TYPE_INFO,
    // ],
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
            'attribute' => 'store_id',
            'format' => 'raw',
            'value' => Html::encode($model->store->description ?? null),
            'visible' => User::can(30),
        ],

        [
            'attribute' => 'appName',
            'format' => 'raw',
            'value' => Html::encode($model->appName ?? null)
        ],

        [
            'attribute' => 'description',
            'format' => 'raw',
            'value' => Html::encode($model->description ?? null)
        ],
        // [
        //     'attribute' => 'sin',
        //     'value' => Html::encode($model->sin),
        //     'format' => 'raw',
        // ],
        [
            'attribute' => 'sin',
            'label' => 'SID',
            'format' => 'raw',
            'value' => call_user_func(function ($data) {
                $html = '<p>' . Html::encode($data->sin) . '</p>';
                $html .= '<p class="text-info">Questo codice è l\'identificativo del POS. Dovrai immettere questa stringa nel POS al primo accesso.</p>';
                return $html;
            }, $model),
        ],
        // [
        //     'attribute' => 'customCSSLink',
        //     'value' => Html::encode($model->possettings->customCSSLink),
        //     'format' => 'raw',
        // ],





    ],
]) ?>

<?php if (User::can(40)) : ?>
    <div class="card-footer">
        <div class="d-flex flex-row">
            <div class="p-1">
                <?= Html::a('<i class="fas fa-pen"></i> ' . Yii::t('app', 'Modifica'), ['update', 'id' => Crypt::encrypt($model->id)], ['class' => 'btn btn-primary']) ?>
            </div>

            <div class="p-1">
                <?= Html::a('<i class="fas fa-undo"></i> ' . Yii::t('app', 'Ripristina sessione POS'), ['restore', 'id' => Crypt::encrypt($model->id)], [
                    'class' => 'btn btn-secondary',
                    'data' => [
                        'confirm' => Yii::t('app', 'Vuoi ripristinare la sessione per permettere un nuovo collegamento a questo Pos?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </div>

            <div class="p-1 ml-auto">
                <?= Html::a('<i class="fas fa-trash"></i> ' . Yii::t('app', 'Elimina'), ['delete', 'id' => Crypt::encrypt($model->id)], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Vuoi eliminare questo Pos?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
<?php endif; ?>