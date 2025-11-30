<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $users app\models\User[] */
/* @var $roles array */

$this->title = 'User Role Assignments';
?>
<div class="rbac-users">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Assign one or more roles to users. Hold Ctrl/Cmd to multi-select, or choose none to remove all roles.</p>

    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Assigned Roles</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user):
            $assignments = Yii::$app->authManager->getAssignments($user->id);
            $currentRoles = array_keys($assignments);
            ?>
            <tr>
                <td><?= Html::encode($user->id) ?></td>
                <td><?= Html::encode($user->username) ?></td>
                <td><?= Html::encode($user->email) ?></td>
                <td>
                    <?php if (!empty($assignments)): ?>
                        <?php foreach ($assignments as $aname => $a): ?>
                            <span class="badge bg-secondary"><?= Html::encode($aname) ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <em>None</em>
                    <?php endif; ?>
                </td>
                <td>
                    <?= Html::beginForm(['rbac/assign'], 'post', ['class' => 'd-inline']) ?>
                    <?= Html::hiddenInput('user_id', $user->id) ?>
                    <?php
                    // build options: name => label
                    $opts = [];
                    foreach ($roles as $rname => $robj) {
                        $opts[$rname] = $robj->description ?: $rname;
                    }
                    ?>
                    <?= Html::listBox('roles[]', $currentRoles, $opts, ['multiple' => true, 'class' => 'form-select d-inline', 'style' => 'width:220px; display:inline-block; margin-right:8px; height:130px;']) ?>
                    <?= Html::submitButton('Save', ['class' => 'btn btn-sm btn-primary', 'style' => 'vertical-align:top;']) ?>
                    <?= Html::endForm() ?>

                    <?= Html::a('Revoke All', ['rbac/revoke', 'user_id' => $user->id], ['class' => 'btn btn-sm btn-danger', 'style' => 'margin-left:8px;']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
