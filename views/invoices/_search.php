<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\InvoicesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoices-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'merchant_id') ?>

    <?= $form->field($model, 'store_id') ?>

    <?= $form->field($model, 'pos_id') ?>

    <?= $form->field($model, 'invoiceType') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'metadata') ?>

    <?php // echo $form->field($model, 'checkout') ?>

    <?php // echo $form->field($model, 'receipt') ?>

    <?php // echo $form->field($model, 'invoiceId') ?>

    <?php // echo $form->field($model, 'storeId') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'currency') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'checkoutLink') ?>

    <?php // echo $form->field($model, 'createdTime') ?>

    <?php // echo $form->field($model, 'expirationTime') ?>

    <?php // echo $form->field($model, 'monitoringExpiration') ?>

    <?php // echo $form->field($model, 'additionalStatus') ?>

    <?php // echo $form->field($model, 'availableStatusesForManualMarking') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
