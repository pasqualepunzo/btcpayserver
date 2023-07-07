<?php

namespace app\controllers;

use Yii;
use app\models\Invoices;
use app\models\search\InvoicesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\models\Merchants;
use app\models\Stores;
use app\models\Pos;
use app\models\Settings;
use app\components\Crypt;
use app\components\User;
use app\components\BTCPayServer;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

use app\models\search\PaymentsSearch;

/**
 * InvoicesController implements the CRUD actions for Invoices model.
 */
class InvoicesController extends Controller
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
                            // 'create', // solo in fase di test.
                            // 'update',
                            // 'delete',
                            'lista-negozi', // solo in fase di test
                            'export',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
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

        $searchModel = new InvoicesSearch();
        $dataProvider = $searchModel->search($queryParams);
        $dataProvider->query->andwhere(['=', 'invoices.archived', 0]);

        if (User::isSenior()) {
            $dataProvider->query->andwhere(['=', 'invoices.merchant_id', Yii::$app->user->identity->merchant_id]);
        }
        if (User::isJunior()) {
            $dataProvider->query->andwhere(['=', 'invoices.store_id', Yii::$app->user->identity->store_id]);
        }
        $dataProvider->pagination->pageSize = false;

        $allModels = $dataProvider->getModels();

        // inizializzo la classe Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Attributi da scaricare
        $attributeLabels = [
            'id' => Yii::t('app', 'ID'),
            // 'merchant.description' => Yii::t('app', 'Esercente'),
            // 'store.description' => Yii::t('app', 'Negozio'),
            // 'pos.appName' => Yii::t('app', 'Pos'),
            'invoiceType' => Yii::t('app', 'Network'),
            'status' => Yii::t('app', 'Stato'),
            'metadata' => Yii::t('app', 'Metadata'),
            'checkout' => Yii::t('app', 'Pagamento'),
            'receipt' => Yii::t('app', 'Ricevuta'),
            'invoiceId' => Yii::t('app', 'ID Transazione'),
            'storeId' => Yii::t('app', 'ID Negozio'),
            'amount' => Yii::t('app', 'Importo'),
            'currency' => Yii::t('app', 'Valuta'),
            'type' => Yii::t('app', 'Tipo'),
            'checkoutLink' => Yii::t('app', 'Link del pagamento'),
            'createdTime' => Yii::t('app', 'Data'),
            'expirationTime' => Yii::t('app', 'Data Scadenza'),

            // payments method 
            'payments.paymentMethod' => Yii::t('app', 'Payment Method'),

            // 'monitoringExpiration' => Yii::t('app', 'Monitoraggio Scadenza'),
            // 'additionalStatus' => Yii::t('app', 'Stati addizionali'),
            // 'availableStatusesForManualMarking' => Yii::t('app', 'Stati disponibili per modifica manuale'),
            // 'archived' => Yii::t('app', 'Archiviata'),
        ];

        $dataType = ['createdTime', 'expirationTime'];
        

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

                    if (in_array($field, $dataType)) {
                        $writeText = date('Y-m-d H:i:s', $writeText);
                    }
                } else {
                    $submodel = $explode[0];
                    $attributes = [];
                    $writeText = '';

                    foreach ($model->$submodel as $element){
                        $attributes[] = $element->attributes;
                    }
                    if (is_array($attributes)){
                        $writeText = (string) json_encode($attributes, JSON_THROW_ON_ERROR, 512);
                    }
                }

                $sheet->setCellValue($this->getCellFromColnum($col) . $row, trim($writeText ?? ''), null);
                $col++;
            }
            $row++;
        }
        
        // output the file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $date = date('Y/m/d H:i:s', time());
        $filename = $date . '-invoices.xlsx';
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
     * Lists all Invoices models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvoicesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andwhere(['=', 'invoices.archived', 0]);
        
        if (User::isSenior()) {
            $dataProvider->query->andwhere(['=', 'invoices.merchant_id', Yii::$app->user->identity->merchant_id]);
        }
        if (User::isJunior()) {
            $dataProvider->query->andwhere(['=', 'invoices.store_id', Yii::$app->user->identity->store_id]);
        }

        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'queryParams' => Json::encode(Yii::$app->request->queryParams),
        ]);
    }

    /**
     * Displays a single Invoices model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $params = ['invoice_id' => Crypt::decrypt($id)];
        $searchModel = new PaymentsSearch($params);
        $dataProviderPayments = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model' => $this->findModel(Crypt::decrypt($id)),
            'dataProviderPayments' => $dataProviderPayments,
        ]);
    }

    
    /**
     * return list from jquery dropdown
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionListaNegozi()
    {
        $out = [];
        $id = null;
        // echo '<pre>'.print_r($_POST,true);exit;

        if (isset($_POST['depdrop_all_params'])) {
            $parents = $_POST['depdrop_all_params'];
            if ($parents != null) {
                $id = (int) $parents['merchant_id'];

                $out = Stores::find()
                    ->select(['id', 'description as name'])
                    ->where(['merchant_id' => $id, 'historical' => 0])
                    ->asArray()
                    ->all();


                return Json::encode(['output' => $out, 'selected' => $id]);
            }
        }
        return Json::encode(['output' => $out, 'selected' => '']);
    }

    /**
     * Finds the Invoices model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoices the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoices::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
