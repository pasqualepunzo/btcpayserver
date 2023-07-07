<?php

namespace app\controllers;

use Yii;
use app\models\Pos;
use app\models\Possettings;
use app\models\search\PosSearch;
use app\models\Merchants;
use app\models\Stores;
use app\models\Settings;
use app\models\UserTokens;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;

use app\components\Crypt;
use app\components\User;
use app\components\Log;
use app\components\BTCPayServer;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * PosController implements the CRUD actions for Pos model.
 */
class PosController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'view',
                            'export',
                            'restore',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                        // 'matchCallback' => function ($rule, $action) {
                        //     return User::isSenior();
                        // },
                    ],
                    [
                        'actions' => [
                            'index',
                            'view',
                            'create',
                            'update',
                            'delete',
                            'lista-pos',
                            // 'export',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return User::can(40);
                        },
                    ],
                ]
            ]
        ];
    }

    /**
     * Esporta la selezione in un file excel
     */
    public function actionExport()
    {
        $queryParams = Json::decode($_POST['queryParams']);

        $searchModel = new PosSearch();
        $dataProvider = $searchModel->search($queryParams);
        $dataProvider->query->andwhere(['=', 'pos.historical', 0]);
        if (User::isSenior()) {
            $dataProvider->query->andwhere(['=', 'pos.merchant_id', Yii::$app->user->identity->merchant_id]);
        }
        if (User::isJunior()) {
            $dataProvider->query->andwhere(['=', 'pos.store_id', Yii::$app->user->identity->store_id]);
        }
        $dataProvider->pagination->pageSize = false;

        $allModels = $dataProvider->getModels();

        // inizializzo la classe Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Attributi da scaricare
        $attributeLabels = [
            'id' => Yii::t('app', 'ID'),
            'merchant.description' => Yii::t('app', 'Esercente'),
            'store.description' => Yii::t('app', 'Negozio'),
            'appName' => Yii::t('app', 'Pos'),
            'description' => Yii::t('app', 'Descrizione'),
            'sin' => Yii::t('app', 'Sin'),

            // 'create_date' => Yii::t('app', 'Create Date'),
            // 'close_date' => Yii::t('app', 'Close Date'),
            // 'historical' => Yii::t('app', 'Historical'),
        ];

        

        // create header
        $x = 0;
        foreach ($attributeLabels as $field => $text) {
            $sheet->setCellValue($this->getCellFromColnum($x) . '1', $text);
            $x++;
        }

        // load rows
        // adesso fare il ciclo sui campi dei titoli...
        $row = 2;
        foreach ($allModels as $n => $model) {
            $col = 0;
            foreach ($attributeLabels as $field => $text) {
                $explode = explode('.', $field);

                if (!isset($explode[1])) {
                    $writeText = $model->$field;
                } else {
                    $submodel = $explode[0];
                    $field = $explode[1];

                    $writeText = $model->$submodel->$field ?? '';
                }
                $sheet->setCellValue($this->getCellFromColnum($col) . $row, trim($writeText ?? ''), null);
                $col++;
            }
            $row++;
        }

        // output the file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $date = date('Y/m/d H:i:s', time());
        $filename = $date . '-pos.xlsx';
        $response = Yii::$app->getResponse();
        $headers = $response->getHeaders();
        $headers->set('Content-Type', 'application/vnd.ms-excel');
        $headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $headers->set('Cache-Control: max-age=0');
        ob_start();
        $writer->save("php://output");
        $content = ob_get_contents();
        ob_clean();
        return $content;
    }

    private function getCellFromColnum($colNum)
    {
        return ($colNum < 26 ? chr(65 + $colNum) : chr(65 + floor($colNum / 26) - 1) . chr(65 + ($colNum % 26)));
    }

    /**
     * Lists all Pos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andwhere(['=', 'pos.historical', 0]);
        if (User::isSenior()) {
            $dataProvider->query->andwhere(['=', 'pos.merchant_id', Yii::$app->user->identity->merchant_id]);
        }
        if (User::isJunior()) {
            $dataProvider->query->andwhere(['=', 'pos.store_id', Yii::$app->user->identity->store_id]);
        }
        $dataProvider->sort->defaultOrder = ['appName' => SORT_ASC];

       
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'queryParams' => Json::encode(Yii::$app->request->queryParams),
        ]);
    }

    /**
     * Displays a single Pos model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel(Crypt::decrypt($id)),
        ]);
    }

    /**
     * Restore POS session.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRestore($id)
    {
        UserTokens::deleteAll(['pos_id' => Crypt::decrypt($id)]);

        Yii::$app->session->setFlash('success', Yii::t('app', 'Sessione Pos ripristinata correttamente'));
        
        return $this->render('view', [
            'model' => $this->findModel(Crypt::decrypt($id)),
        ]);
    }

    /**
     * Creates a new Pos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Pos();

        if ($model->load(Yii::$app->request->post()) ) {

            // // TEST
            // $settings = (object) ArrayHelper::map(Settings::find()->all(), 'code', 'value');
            // $BTCPayServer = new BTCPayServer($settings->btcpayHost, $settings->btcpayApiKey);
            // // $client = $BTCPayServer->setStore();

            // $newpos = $BTCPayServer->createPos();
           
            // echo "<pre>" . print_r($model->attributes, true) . "</pre>";
            // exit;


            // Begin a database transaction
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $model->save();

                $settings = (object) ArrayHelper::map(Settings::find()->all(), 'code', 'value');
                // echo "<pre>" . print_r($settings, true) . "</pre>";
                // exit;

                // PRIMA DI TUTTO GENERO IL MODEL DELLA CONFIGURAZINOE DEL NEGOZIO
                $params = [
                    'pos_id' => $model->id,

                    "appName" => $model->appName,
                    "title" => $model->appName,
                    "description" => $model->description,
                    "template" => "",
                    "defaultView" => "Light", // only keypad
                    "currency" => "BTC",
                    "showCustomAmount" => 0,
                    "showDiscount" => 0,
                    "enableTips" => 0,
                    "customAmountPayButtonText" => Yii::t('app', "Pay"),
                    "fixedAmountPayButtonText" => Yii::t('app', "Buy for {PRICE_HERE}"),
                    "tipText" => Yii::t('app', "Do you want to leave a tip?"),
                    "customCSSLink" => "",
                    "embeddedCSS" => "",
                    "notificationUrl" => Url::to(['callback/index'], true),
                    "redirectUrl" => '',
                    "redirectAutomatically" => 0,
                    "requiresRefundEmail" => 1,
                    "checkoutType" => "V1",
                    "formId" => ""
                ];

                // echo "<pre>" . print_r($params, true) . "</pre>";
                // exit;


                $possettings = new Possettings($params);

                
                if ($possettings->save()) {
                    // echo "<pre>" . print_r($possettings, true) . "</pre>";
                    // exit;

                    $BTCPayServer = new BTCPayServer($settings->btcpayHost, $settings->btcpayApiKey);
                    $client = $BTCPayServer->setPos();

                    // echo "<pre>" . print_r($client, true) . "</pre>";
                    // exit;

                    unset($params['pos_id']);

                    try {
                        $bpsPos = $client->createPos($model->store->storesettings->bps_storeid, $params);
                    } catch (\Throwable $e) {
                        echo "Error: " . $e->getMessage();
                        echo "<pre>" . print_r($bpsPos, true) . "</pre>";
                        exit;
                    }

                    if (null !== $bpsPos) {
                        // salva il SIN nei settings
                        $possettings->sin = $bpsPos->getId();
                        $possettings->save();

                        // salva il SIN nel model Pos
                        $model->sin = $bpsPos->getId();
                        $model->save();
                        
                        // Commit the transaction
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', Yii::t('app', 'Pos creato correttamente'));

                        // Log message
                        $message_log = Yii::t('app', 'User {user} has created a new {item}: {itemname}', [
                            'user' => Yii::$app->user->identity->username,
                            'item' => Yii::$app->controller->id,
                            'itemname' => $model->appName
                        ]);
                        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message_log);
                        // end log message

                        return $this->redirect(['view', 'id' => Crypt::encrypt($model->id)]);
                    } else {
                        Yii::$app->session->setFlash('error', Yii::t('app', 'ERRORE BtcPay: Impossibile generare il Pos'));
                        $transaction->rollBack();
                    }
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'ERRORE: ' . "<pre>" . print_r($possettings->getErrors(), true) . "</pre>"));
                    $transaction->rollBack();
                }


            } catch (\Exception $e) {
                // Roll back the transaction if an error occurred
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('app', 'ERRORE: ' . "<pre>" . print_r($e, true) . "</pre>"));
            }
            
        }
        // TODO: FILTRATO PER PERMESSI UTENTE ??
        // NO. il pos viene creato solo da ADMIN che vede tutto
        $merchants_list = ArrayHelper::map(Merchants::find()->active()->all(), 'id', 'description');
        asort($merchants_list);

        return $this->render('create', [
            'model' => $model,
            'merchants_list' => $merchants_list,
        ]);

    }

    /**
     * Updates an existing Pos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(Crypt::decrypt($id));
        $pos = $model->possettings;

        if ($model->load(Yii::$app->request->post())) {
            $pos->appName = $model->appName;
            $pos->title = $model->appName;
            $pos->description = $model->description;
            
            // echo "<pre>" . print_r($_POST, true) . "</pre>";
            // echo "<pre>" . print_r($model->attributes, true) . "</pre>";
            // echo "<pre>" . print_r($pos->attributes, true) . "</pre>";
            // exit;
            // Begin a database transaction
            $transaction = Yii::$app->db->beginTransaction();
            
            try {
                if ($model->save() && $pos->save()) {
                    $params = [
                        "appName" => $model->appName,
                        "title" => $model->appName,
                        "description" => $model->description,
                        "template" => $pos->template,
                        "defaultView" => $pos->defaultView,
                        "currency" => $pos->currency,
                        "showCustomAmount" => $pos->showCustomAmount,
                        "showDiscount" => $pos->showDiscount,
                        "enableTips" => $pos->enableTips,
                        "customAmountPayButtonText" => $pos->customAmountPayButtonText,
                        "fixedAmountPayButtonText" => $pos->fixedAmountPayButtonText,
                        "tipText" => $pos->tipText,
                        // unico modificabile
                        "customCSSLink" => $pos->customCSSLink,
                        // 
                        "embeddedCSS" => $pos->embeddedCSS,
                        "notificationUrl" => $pos->notificationUrl,
                        "redirectUrl" => $pos->redirectUrl,
                        "redirectAutomatically" => $pos->redirectAutomatically,
                        "requiresRefundEmail" => $pos->requiresRefundEmail,
                        "checkoutType" => $pos->checkoutType,
                        "formId" => $pos->formId,
                    ];

                    $settings = (object) ArrayHelper::map(Settings::find()->all(), 'code', 'value');

                    // inizializzo la classe BTCPayServer
                    $BTCPayServer = new BTCPayServer($settings->btcpayHost, $settings->btcpayApiKey);
                    $client = $BTCPayServer->setPos();

                    if ($client->updatePos($pos->sin, $params)) {
                        // Commit the transaction
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', Yii::t('app', 'Pos aggiornato correttamente'));
                        // Log message
                        $message_log = Yii::t('app', 'User {user} has updated {item}: {itemname}', [
                            'user' => Yii::$app->user->identity->username,
                            'item' => Yii::$app->controller->id,
                            'itemname' => $model->appName
                        ]);
                        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message_log);
                        // end log message
                        return $this->redirect(['view', 'id' => Crypt::encrypt($model->id)]);
                    } else {
                        Yii::$app->session->setFlash('error', Yii::t('app', 'ERRORE BtcPay: Impossibile aggiornare il pos'));
                        $transaction->rollBack();
                    }
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'ERRORE: Impossibile aggiornare il pos'));
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('app', 'ERRORE: ' . "<pre>" . print_r($e->getMessage(), true) . "</pre>"));
            }
           
        }

        // TODO: FILTRATO PER PERMESSI UTENTE ?????
        // NO. Il pos viene creato sol oda ADMIN che vede tutto
        $merchants_list = ArrayHelper::map(Merchants::find()->active()->all(), 'id', 'description');
        $stores_list = ArrayHelper::map(Stores::find()->active()->all(), 'id', 'description');
        asort($merchants_list);
        asort($stores_list);

        return $this->render('update', [
            'model' => $model,
            'merchants_list' => $merchants_list,
            'stores_list' => $stores_list,
        ]);
    }

    /**
     * Deletes an existing Pos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $closed_at = new \DateTime('now');
        $close_date = $closed_at->format('Y-m-d');

        // storicizza il pos
        $model = $this->findModel(Crypt::decrypt($id));
        $model->close_date = $close_date;
        $model->historical = 1;
        $model->save();

        // Log message
        $message_log = Yii::t('app', 'User {user} has deleted {item}: {itemname}', [
            'user' => Yii::$app->user->identity->username,
            'item' => Yii::$app->controller->id,
            'itemname' => $model->appName
        ]);
        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message_log);
        // end log message

        
        return $this->redirect(['index']);
        // NON ELIMINO IL POS DA BTCPAYSERVER

        // $model = $this->findModel(Crypt::decrypt($id));

        // $transaction = Yii::$app->db->beginTransaction();
        // try {
        //     $settings = (object) ArrayHelper::map(Settings::find()->all(), 'code', 'value');
        //     // inizializzo la classe BTCPayServer
        //     $BTCPayServer = new BTCPayServer($settings->btcpayHost, $settings->btcpayApiKey);

        //     $removePos = $BTCPayServer->removePos($model->possettings->sin);

        //     if (null !== $removePos) {
        //         $model->delete();
                
        //         // Commit the transaction
        //         Yii::$app->session->setFlash('success', Yii::t('app', 'Pos eliminato correttamente'));
        //         $transaction->commit();
        //         return $this->redirect(['index']);
        //     } else {
        //         Yii::$app->session->setFlash('error', Yii::t('app', 'ERRORE BtcPay: Impossibile eliminare il pos'));
        //         $transaction->rollBack();
        //     }
        // } catch (\Exception $e) {
        //     // Roll back the transaction if an error occurred
        //     Yii::$app->session->setFlash('error', Yii::t('app', 'ERRORE: ' . "<pre>" . print_r($e->getMessage(), true) . "</pre>"));
        //     $transaction->rollBack();
        // }
        // return $this->redirect(['view', 'id' => Crypt::encrypt($model->id)]);
    }

    /**
     * return list from jquery dropdown
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionListaPos()
    {
        $out = [];
        $id = null;
        // echo '<pre>'.print_r($_POST,true);exit;

        if (isset($_POST['depdrop_all_params'])) {
            $parents = $_POST['depdrop_all_params'];
            if ($parents !== null) {
                $id = (int) $parents['store_id'];

                $out = Pos::find()
                    ->select(['id', 'description as name'])
                    ->byStoreId($id)
                    ->asArray()
                    ->all();


                return Json::encode(['output' => $out, 'selected' => $id]);
            }
        }
        return Json::encode(['output' => $out, 'selected' => '']);
    }

    

    /**
     * Finds the Pos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
