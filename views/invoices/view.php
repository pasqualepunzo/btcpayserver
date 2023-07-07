<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\Settings;

/* @var $this yii\web\View */
/* @var $model app\models\Invoices */

$this->title = $model->invoiceId;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Invoices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$settings = (object) ArrayHelper::map(Settings::find()->all(), 'code', 'value');
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="invoices-view">
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
                        <div class="d-flex flex-row">
                            <h3><?= Html::encode($this->title) ?></h3>
                            <div class="p-1 ml-auto">
                                <?php 
                                    $url = Url::to($settings->btcpayHost .'/i/'. $model->invoiceId . '/receipt', true);
                                    echo Html::a('<i class="fa fa-receipt"></i>', $url, [
                                    'title' => Yii::t('app', 'Ricevuta'),
                                    'class' => 'btn btn-lg btn-secondary',
                                    'target' => '_blank'
                                    ]);
                                ?>

                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                // 'id',
                                [
                                    'attribute' => 'merchant.description',
                                    'label' => Yii::t('app','Merchant'),
                                ],
                                [
                                    'attribute' => 'store.description',
                                    'label' => Yii::t('app', 'Store'),
                                ],
                                'storeId',
                                [
                                    'attribute' => 'pos.appName',
                                    'label' => Yii::t('app', 'App Name'),
                                ],
                                [
                                    'attribute' => 'pos.sin',
                                    'label' => Yii::t('app', 'SID'),
                                ],

                                'invoiceId',
                                [
                                    'attribute' => 'createdTime',
                                    // 'createdTime:datetime',
                                    'value' => function ($data) {
                                        return $data->createdTime;
                                        // return Yii::$app->formatter->asDate(($data->createdTime), 'php:d/m/Y');
                                    },
                                    'format' => ['DateTime', 'php:H:i:s d/m/Y'],


                                ],
                                'invoiceType',
                                [
                                    'attribute' => 'amount',
                                    'value' => function ($data) {
                                        return Yii::$app->formatter->asCurrency(($data->amount), $data->currency);
                                    },
                                ],

                                'status',
                                'metadata',
                                'checkout',
                                'receipt',
                                // 'amount',
                                // 'currency',
                                'type',
                                'checkoutLink:url',
                                'createdTime:datetime',
                                'expirationTime:datetime',
                                'monitoringExpiration',
                                'additionalStatus',
                                'availableStatusesForManualMarking',
                                'archived'
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="invoices-index">
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <div class="d-flex flex-row">
                            <h3 class="p-1">Dettagli pagamento</h3>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <?= GridView::widget([
                            'dataProvider' => $dataProviderPayments,
                            'columns' => [
                                'paymentMethod',
                                [
                                    'attribute' => 'destination',
                                    'format' => 'raw',
                                    'value' => function ($data) {
                                        $html = '';
                                        if (!empty($data)) {
                                            $html .= '<p>' . $data->paymentMethod . ': ' . $data->destination . '</p>';
                                        }
                                        return $html;
                                    },
                                    'contentOptions' => ['class' => 'text-break'],
                                ],
                                // 'rate',
                                [
                                    'attribute' => 'rate',
                                    'value' => function ($data) use ($model) {
                                        return Yii::$app->formatter->asCurrency(($data->rate), $model->currency);
                                    },
                                ],
                                'paymentMethodPaid',
                                // 'totalPaid',
                                // 'due',
                                // 'amount',
                                [
                                    'attribute' => 'totalPaid',
                                    'value' => function ($data) {
                                        return Yii::$app->formatter->asDecimal($data->totalPaid, 8);
                                    },
                                ],
                                [
                                    'attribute' => 'due',
                                    'value' => function ($data) {
                                        return Yii::$app->formatter->asDecimal($data->due, 8);
                                    },
                                ],
                                [
                                    'attribute' => 'amount',
                                    'value' => function ($data) {
                                        return Yii::$app->formatter->asDecimal($data->amount, 8);
                                    },
                                ],
                                [
                                    'attribute' => 'networkFee',
                                    'value' => function ($data) {
                                        return Yii::$app->formatter->asDecimal($data->networkFee, 8);
                                    },
                                ],
                                // 'networkFee',
                                'payments',
                                'additionalData',

                            ],
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>