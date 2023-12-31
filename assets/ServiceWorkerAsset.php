<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Service Worker application asset bundle.
 *
 * @author Sergio Casizzone <jambtc@gmail.com>
 * @since 2.0
 */
class ServiceWorkerAsset extends AssetBundle
{
    public $basePath = '@webroot/bundles/sw';
    public $baseUrl = '@web/bundles/sw';
    public $css = [
        'css/style.css',
    ];
    public $js = [
        'src/js/promise.js',
        'src/js/fetch.js',
        'src/js/idb.js',
        'src/js/utility.js',
        'src/js/service.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset'
    ];
}
