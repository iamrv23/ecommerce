<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $intents app\modules\chatbot\models\ChatbotIntent[] */
/* @var $logs app\modules\chatbot\models\ChatbotLog[] */

$this->title = 'Chatbot Administration';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="chatbot-admin-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Quick Actions</h3>
                </div>
                <div class="panel-body">
                    <p>
                        <?= Html::a('Manage Intents', ['intents'], ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('View Logs', ['logs'], ['class' => 'btn btn-info']) ?>
                        <?= Html::a('Settings', ['settings'], ['class' => 'btn btn-warning']) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">System Status</h3>
                </div>
                <div class="panel-body">
                    <p><strong>Active Intents:</strong> <?= count($intents) ?></p>
                    <p><strong>Recent Logs:</strong> <?= count($logs) ?></p>
                    <p><strong>Rasa Status:</strong>
                        <?php
                        $service = new \app\modules\chatbot\services\ChatbotService();
                        echo $service->isAvailable() ? '<span class="text-success">Online</span>' : '<span class="text-danger">Offline</span>';
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Recent Activity</h3>
                </div>
                <div class="panel-body">
                    <?php if (empty($logs)): ?>
                        <p>No recent activity.</p>
                    <?php else: ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User Message</th>
                                    <th>Intent</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($logs, 0, 10) as $log): ?>
                                    <tr>
                                        <td><?= Yii::$app->formatter->asDatetime($log->created_at) ?></td>
                                        <td><?= Html::encode(substr($log->user_message, 0, 50)) ?>...</td>
                                        <td><?= Html::encode($log->intent_detected) ?></td>
                                        <td><?= Html::encode($log->action_executed) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>