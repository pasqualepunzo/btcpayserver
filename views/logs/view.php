<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Logs */

$this->title = sprintf('Dettaglio - ID: %s', $model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="logs-view">
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <div class="d-flex flex-row">
                            <h3><?= Html::encode($this->title) ?></h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                // 'id',
                                [
                                    'attribute' => 'timestamp',
                                    'format' => 'raw',
                                    'value' => function ($data) {
                                        return Yii::$app->formatter->asDate(($data->timestamp), 'php:d/m/Y h:i:s');
                                    },
                                ],
                                // 'user_id',
                                'controller',
                                'action',
                                'remote_address',
                                'browser',
                                'description:ntext',
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>