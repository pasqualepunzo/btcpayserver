<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Settings */

$this->title = Yii::t('app', 'Crea nuovo');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Impostazioni'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="settings-update">
                <?php if (Yii::$app->session->hasFlash('error')) : ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?php echo Yii::$app->session->getFlash('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h5><?= Html::encode($this->title) ?></h5>
                    </div>
                    <div class="card-body">
                        <?= $this->render('_form', [
                            'model' => $model,
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>