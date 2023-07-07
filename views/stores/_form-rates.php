<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Stores */
/* @var $form yii\bootstrap4\ActiveForm */


$preferredSource = [
    'bitstamp' => 'Bitstamp',
    'binance' => 'Binance',
    'kraken' => 'Kraken',
    'bitfinex' => 'Bitfinex'
];


?>

<div class="stores-form">

    <?php $form = ActiveForm::begin(); ?>
   
    <?= $form->field($model, 'preferredSource')->dropDownList($preferredSource) ?>
    <?= $form->field($model, 'spread')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-save"></i> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>