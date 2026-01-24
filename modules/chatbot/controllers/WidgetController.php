<?php

namespace app\modules\chatbot\controllers;

use Yii;
use yii\web\Controller;

/**
 * Widget controller for chatbot interface
 */
class WidgetController extends Controller
{
    /**
     * Chat widget view
     */
    public function actionChat()
    {
        $this->layout = false; // Use no layout for widget

        return $this->render('chat');
    }
}