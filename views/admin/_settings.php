<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\components\Crypt;
?>
<div class="card card-outline card-primary">
    <div class="card-header border-0">
        <h3 class="card-title">
            <?= Html::a('<i class="fa fa-list"></i>', Url::to(['settings/index']), [
                'title' => Yii::t('app', 'Lista'),
                'class' => 'btn btn-sm btn-default',
            ])  ?>
            <?= Yii::t('app', 'Impostazioni') ?>
        </h3>
    </div>
    <div class="card-body table-responsive p-1">
        <?= GridView::widget([
            'dataProvider' => $dataSettings,
            'tableOptions' => [
                'class' => 'table table-sm table-valign-middle small',
            ],
            'summary' => false,
            'columns' => [
                // view button
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'contentOptions' => [
                        'style' => 'width: 35px;'
                    ],
                    'buttons' => [
                        'view' => function ($url, $model) {

                            $url = Url::to(['settings/view', 'id' => Crypt::encrypt($model->id)]);
                            return Html::a('<i class="fa fa-eye"></i>', $url, [
                                'title' => Yii::t('app', 'Dettagli'),
                                'class' => 'btn btn-sm btn-default btn-xl',
                            ]);
                        },

                    ],

                ],

                'description',
                'code',
                [
                    'attribute' => 'value',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return Html::encode($data->value);
                    },
                    'contentOptions' => ['class' => 'text-break'],
                ],



            ],
        ]); ?>


    </div>
</div>