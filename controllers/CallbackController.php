<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;

use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\models\Invoices;
use app\models\Payments;
use app\models\Settings;
use yii\helpers\ArrayHelper;
use app\components\Log;
use app\components\BTCPayServer;

class CallbackController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        define('LOGACTIVE', Yii::$app->params['webHookLogs']);

        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

   
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), 'Callback start');
        
        // intercetta la request
        $headers = Yii::$app->request->headers;
        $raw_post_data = file_get_contents('php://input');

        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), '$headers<pre>' . print_r($headers, true) . '</pre>');
        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), '$rawcontent<pre>' . print_r($raw_post_data, true) . '</pre>');


        if (false === $raw_post_data) {
            $message = 'Could not read from the php://input stream or invalid BTCPayServer payload received.';
            Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message);
            throw new \Exception($message);
        }

        $payload = json_decode($raw_post_data, false, 512, JSON_THROW_ON_ERROR);
        // Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), '$payload<pre>' . print_r($payload, true) . '</pre>');
    
        if (true === empty($payload)) {
            $message = 'Could not decode the JSON payload from BTCPay.';
            Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message);
            throw new \Exception($message);
        }

        /**
         * check signature
         * 
         * Example: sha256=b438519edde5c8144a4f9bcec51a9d346eca6506887c2ceeae1c0092884a97b9
         * The HMAC of the body's byte with the secret's of the webhook. sha256=HMAC256(UTF8(webhook's secret), body)
         */
        $settings = (object) ArrayHelper::map(Settings::find()->all(), 'code', 'value');
        $secret = $settings->webhookSecret;

        // verify hmac256
        $btcpaySig = null;
        foreach ($headers as $name => $value) {
            if (strtoupper($name) == 'BTCPAY-SIG') {
                $btcpaySig = $value[0];
            }
        }

        // --------------------------------------------
        // DISABILITA LA SIGNATURE PER I TEST
        // --------------------------------------------

        if ($btcpaySig !== "sha256=" . hash_hmac('sha256', $raw_post_data, $secret)) {
            $message = "Error. Invalid Signature detected! \n was: " . $btcpaySig . " should be: " . hash_hmac('sha256', $raw_post_data, $secret) . "\n";
            $message .= 'Invalid BTCPayServer payment notification message received - signature did not match.';
            Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message);
            throw new \Exception($message);
        }
        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), 'Signature is valid.');


        /**
         * check if store model and storeId exist
         */
        // check if store id exist
        if (false === $payload->storeId){
            $message = 'Store id isn\'t set or invalid.';
            Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message);
            throw new \Exception($message);
        }
        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), 'StoreId is: ' . $payload->storeId);

        // Build the SQL query using QueryBuilder
        $query = (new \yii\db\Query())
            ->select('*')
            ->from('stores')
            ->join('JOIN', 'storesettings', 'storesettings.store_id = stores.id')
            ->where('storesettings.bps_storeid = "' . $payload->storeId .'"');

        // Execute the query and fetch one row
        $store = (object) $query->one();
        // echo '<pre>' . print_r($store, true) . '</pre>'; exit;
        
        if (null === $store) {
            $message = 'Store account not found.';
            Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message);
            throw new \Exception($message);
        }
        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), 'Store account found: id #' . $store->id);

        /**
         * check if invoiceId  and invoice model exist
         */
        if (true === empty($payload->invoiceId)) {
            $message = 'Invoice id isn\'t set or invalid.';
            Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message);
            throw new \Exception($message);
        }
        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), 'InvoiceId is: ' . $payload->invoiceId);

        $invoice = Invoices::find()->byInvoiceId($payload->invoiceId)->one();
        if (null === $store) {
            $message = 'Invoice not found.';
            Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message);
            throw new \Exception($message);
        }
        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), 'Invoice found: id #' . $invoice->id);

        
        /**
         * Richiama la classe BTCPayServer
         */
        $BTCPayServer = new BTCPayServer($settings->btcpayHost, $settings->btcpayApiKey);
        $client = $BTCPayServer->setInvoice();
        
        /**
         * Get Invoice from BTCPayserver
         */
        try {
            $btcInvoice = $client->getInvoice($payload->storeId, $payload->invoiceId);
        } catch (\Throwable $e) {
            $message = "Error: " . $e->getMessage();
            Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message);
            throw new \Exception($message);
        }
        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), '$btcInvoice<pre>' . print_r($btcInvoice, true) . '</pre>');
        // echo '<pre>btcInvoice' . print_r($btcInvoice, true) . '</pre>'; 

        /**
         * confronta gli stati della invoice salvata con quella di btcpayserver
         * Se diverso, la aggiorna
         */
        // if ($btcInvoice->getStatus() == $invoice->status) {
        //     $message = 'Duplicate status. The invoice has already been updated.';
        //     Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message);
        //     Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), 'Callback end');

        //     return \Yii::createObject([
        //         'class' => 'yii\web\Response',
        //         'format' => \yii\web\Response::FORMAT_JSON,
        //         'statusCode' => 200,
        //         'data' => [
        //             'result' => $message,
        //         ],
        //     ]);
        //     // throw new \Exception($message);
        // }

        /**
         * Get info of invoice payment method
         */
        try {
            $btcPaymentMethods = $client->getPaymentMethods($payload->storeId, $payload->invoiceId);
        } catch (\Throwable $e) {
            $message = "Error: " . $e->getMessage();
            Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message);
            throw new \Exception($message);
        }
        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), '$btcPaymentMethods<pre>' . print_r($btcPaymentMethods, true) . '</pre>');
        // echo '<pre>btcInvoice' . print_r($btcPaymentMethods, true) . '</pre>'; exit;

        // update Invoice Status and amount
        $invoice->status = $btcInvoice->getStatus();
        $invoice->amount = $btcInvoice->getData()['amount'];
        $invoice->archived = (int) $btcInvoice->isArchived();

        if (!$invoice->save()) {
            $message = "Error: " . print_r($invoice->getErrors(), true);
            Log::save(Yii::$app->controller->id, Yii::$app->controller->action->id, $message);
            throw new \Exception($message);
        }
        $message = 'Invoice status has been updated to: ' . $invoice->status;
        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message);


        // genera l'array per i diversi metodi di pagamento
        foreach ($btcPaymentMethods as $btcPayment){
            $content = new \stdClass;
            $content->invoice_id = $invoice->id;
            $content->paymentMethod = $btcPayment->getPaymentMethod();
            $content->destination = $btcPayment->getDestination();
            $content->rate = $btcPayment->getRate();
            $content->paymentMethodPaid = $btcPayment->getPaymentMethodPaid();
            $content->totalPaid = $btcPayment->getTotalPaid();
            $content->due = $btcPayment->getDue();
            $content->amount = $btcPayment->getAmount();
            $content->networkFee = $btcPayment->getNetworkFee();
            $content->payments = null;
            $content->additionalData = null;

            $dataArray[] = $content;
        }


        if (isset($dataArray) && !empty($dataArray)){
            // Verifica se esiste quindi crea/aggiorna i pagamenti del pagamento
            foreach ($dataArray as $attributes){
                $payment = Payments::find()->byPaymentsMethod($attributes)->one();

                if (null === $payment){
                    $payment = new Payments;
                }
                $payment->invoice_id = $attributes->invoice_id;
                $payment->paymentMethod = $attributes->paymentMethod;
                $payment->destination = $attributes->destination;
                $payment->rate = $attributes->rate;
                $payment->paymentMethodPaid = $attributes->paymentMethodPaid;
                $payment->totalPaid = $attributes->totalPaid;
                $payment->due = $attributes->due;
                $payment->amount = $attributes->amount;
                $payment->networkFee = $attributes->networkFee;
                $payment->payments = json_encode($attributes->payments);
                $payment->additionalData = json_encode($attributes->additionalData);

                if (!$payment->save()) {
                    $message = "Error: " . print_r($invoice->getErrors(), true);
                    Log::save(Yii::$app->controller->id, Yii::$app->controller->action->id, $message);
                    throw new \Exception($message);
                }
                $message = 'Invoice payment method has been updated: ' . $payment->paymentMethod;
                Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message);

                // echo '<pre>payment attributes' . print_r($payment->attributes, true) . '</pre>'; exit;

            }
            
        }

        $message = 'Invoice has been updated to status: ' . $invoice->status;
        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), $message);
        

        Log::save(Yii::$app->controller->id, (Yii::$app->controller->action->id), 'Callback end');
        // Respond with HTTP 200
        return \Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => \yii\web\Response::FORMAT_JSON,
            'statusCode' => 200,
            'data' => [
                'result' => $message,
            ],
        ]);
    }
}
