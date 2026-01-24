<?php

namespace app\modules\chatbot\assets;

use yii\web\AssetBundle;

/**
 * Chatbot asset bundle
 */
class ChatbotAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/chatbot/assets';

    public $css = [
        'css/chatbot.css',
    ];

    public $js = [
        'js/chatbot.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}