<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Invoices */

$this->title = Yii::t('app', 'Create Invoices');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Invoices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="invoices-create">
                <?php if (Yii::$app->session->hasFlash('success')) : ?>
                    <div class="alert alert-success">
                        <?php echo Yii::$app->session->getFlash('success') ?>
                    </div>
                <?php endif; ?>
                <?php if (Yii::$app->session->hasFlash('warning')) : ?>
                    <div class="alert alert-warning">
                        <?php echo Yii::$app->session->getFlash('warning') ?>
                    </div>
                <?php endif; ?>
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3><?= Html::encode($this->title) ?></h3>
                    </div>
                    <div class="card-body">
                        <?= $this->render('_form', [
                            'model' => $model,
                            'merchants_list' => $merchants_list,
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
