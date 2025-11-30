<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\User;
use app\models\RbacAudit;

class RbacController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index','users','assign','revoke'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();
        $rolesData = [];
        foreach ($roles as $role) {
            $perms = $auth->getPermissionsByRole($role->name);
            $rolesData[$role->name] = [
                'role' => $role,
                'permissions' => $perms,
            ];
        }
        return $this->render('index', ['rolesData' => $rolesData]);
    }

    public function actionUsers()
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();
        $users = User::find()->all();
        return $this->render('users', ['users' => $users, 'roles' => $roles]);
    }

    public function actionAssign()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $userId = (int)$request->post('user_id');
            $selected = $request->post('roles', []);
            if (!is_array($selected)) {
                $selected = $selected ? [$selected] : [];
            }

            $auth = Yii::$app->authManager;
            if ($auth) {
                // current assignments
                $current = array_keys($auth->getAssignments($userId));

                // roles to add
                $toAdd = array_diff($selected, $current);
                // roles to remove
                $toRemove = array_diff($current, $selected);

                foreach ($toRemove as $r) {
                    try {
                        $auth->revoke($auth->getRole($r), $userId);
                        // log revoke
                        RbacAudit::log(Yii::$app->user->id ?? null, 'revoke-role', $r, 'revoked from user ' . $userId);
                    } catch (\Exception $e) {
                        // ignore
                    }
                }
                foreach ($toAdd as $r) {
                    $role = $auth->getRole($r);
                    if ($role) {
                        try {
                            $auth->assign($role, $userId);
                            // log assign
                            RbacAudit::log(Yii::$app->user->id ?? null, 'assign-role', $r, 'assigned to user ' . $userId);
                        } catch (\Exception $e) {
                            // ignore
                        }
                    }
                }
            }
            Yii::$app->session->setFlash('success', 'Roles updated.');
        }
        return $this->redirect(['users']);
    }

    public function actionRevoke($user_id)
    {
        $auth = Yii::$app->authManager;
        if ($auth) {
            $auth->revokeAll((int)$user_id);
            RbacAudit::log(Yii::$app->user->id ?? null, 'revoke-all', 'user:' . $user_id, 'revoked all roles for user');
            Yii::$app->session->setFlash('success', 'All roles revoked for user.');
        }
        return $this->redirect(['users']);
    }
}
