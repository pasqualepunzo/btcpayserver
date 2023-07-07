<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\models\Invoices */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoices-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="txt-left">
        <?= $form->errorSummary($model, ['id' => 'error-summary', 'class' => 'col-lg-12 callout callout-danger']) ?>
    </div>

    <?= $form->field($model, 'merchant_id')->dropDownList(
        $merchants_list,
        [
            'prompt' => Yii::t('app', 'Select Merchant'), 'id' => 'merchant_id'
        ]
    ); ?>

    <?php
    echo $form->field($model, 'store_id')->widget(DepDrop::class, [
        'options' => [
            'id' => 'store_id',
            'prompt' => Yii::t('app', 'Store'),
        ],
        'type' => DepDrop::TYPE_SELECT2,
        // 'select2Options' => ['pluginOptions' => ['allowClear' => true]],
        'pluginOptions' => [
            'initialize' => $model->isNewRecord ? false : true,
            'depends' => ['merchant_id'],
            'url' => Url::to(['stores/lista-negozi', 'id' => 0])

        ],
    ])->label('Store');
    ?>

    <?php
    echo $form->field($model, 'pos_id')->widget(DepDrop::class, [
        'options' => [
            'id' => 'pos_id',
            'prompt' => Yii::t('app', 'Pos'),
        ],
        'type' => DepDrop::TYPE_SELECT2,
        // 'select2Options' => ['pluginOptions' => ['allowClear' => true]],
        'pluginOptions' => [
            'initialize' => $model->isNewRecord ? false : true,
            'depends' => ['merchant_id', 'store_id'],
            'url' => Url::to(['pos/lista-pos'])
        ],
    ])->label('Pos');
    ?>

    
    <?= $form->field($model, 'amount')->textInput() ?>

   

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>