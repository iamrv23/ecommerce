<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\RoleForm;
use app\models\PermissionForm;
use app\models\RbacAudit;

class RbacManageController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index','create-role','update-role','delete-role','create-permission','update-permission','delete-permission','audit'],
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
        $perms = $auth->getPermissions();
        return $this->render('index', ['roles' => $roles, 'perms' => $perms]);
    }

    public function actionCreateRole()
    {
        $model = new RoleForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $auth = Yii::$app->authManager;
            if ($auth->getRole($model->name)) {
                Yii::$app->session->setFlash('error', 'Role already exists.');
            } else {
                $role = $auth->createRole($model->name);
                $role->description = $model->description;
                $auth->add($role);
                RbacAudit::log(Yii::$app->user->id ?? null, 'create-role', $role->name, $role->description);
                Yii::$app->session->setFlash('success', 'Role created.');
                return $this->redirect(['index']);
            }
        }
        return $this->render('create-role', ['model' => $model]);
    }

    public function actionUpdateRole($name)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);
        if (!$role) {
            throw new \yii\web\NotFoundHttpException('Role not found');
        }
        $model = new RoleForm();
        $model->name = $role->name;
        $model->description = $role->description;
        // we don't allow renaming via this simple UI
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $role->description = $model->description;
            $auth->update($role->name, $role);
            RbacAudit::log(Yii::$app->user->id ?? null, 'update-role', $role->name, $role->description);
            Yii::$app->session->setFlash('success', 'Role updated.');
            return $this->redirect(['index']);
        }
        return $this->render('update-role', ['model' => $model, 'role' => $role]);
    }

    public function actionDeleteRole($name)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);
        if ($role) {
            $auth->remove($role);
            RbacAudit::log(Yii::$app->user->id ?? null, 'delete-role', $role->name, null);
            Yii::$app->session->setFlash('success', 'Role deleted.');
        }
        return $this->redirect(['index']);
    }

    public function actionCreatePermission()
    {
        $model = new PermissionForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $auth = Yii::$app->authManager;
            if ($auth->getPermission($model->name)) {
                Yii::$app->session->setFlash('error', 'Permission already exists.');
            } else {
                $p = $auth->createPermission($model->name);
                $p->description = $model->description;
                $auth->add($p);
                RbacAudit::log(Yii::$app->user->id ?? null, 'create-permission', $p->name, $p->description);
                Yii::$app->session->setFlash('success', 'Permission created.');
                return $this->redirect(['index']);
            }
        }
        return $this->render('create-permission', ['model' => $model]);
    }

    public function actionUpdatePermission($name)
    {
        $auth = Yii::$app->authManager;
        $p = $auth->getPermission($name);
        if (!$p) {
            throw new \yii\web\NotFoundHttpException('Permission not found');
        }
        $model = new PermissionForm();
        $model->name = $p->name;
        $model->description = $p->description;
        // do not allow renaming here
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $p->description = $model->description;
            $auth->update($p->name, $p);
            RbacAudit::log(Yii::$app->user->id ?? null, 'update-permission', $p->name, $p->description);
            Yii::$app->session->setFlash('success', 'Permission updated.');
            return $this->redirect(['index']);
        }
        return $this->render('update-permission', ['model' => $model, 'perm' => $p]);
    }

    public function actionDeletePermission($name)
    {
        $auth = Yii::$app->authManager;
        $p = $auth->getPermission($name);
        if ($p) {
            $auth->remove($p);
            RbacAudit::log(Yii::$app->user->id ?? null, 'delete-permission', $p->name, null);
            Yii::$app->session->setFlash('success', 'Permission deleted.');
        }
        return $this->redirect(['index']);
    }

    public function actionEditRolePermissions($name)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);
        if (!$role) {
            throw new \yii\web\NotFoundHttpException('Role not found');
        }

        $allPerms = $auth->getPermissions();
        $children = $auth->getChildren($role->name);
        $current = array_keys($children);

        if (Yii::$app->request->isPost) {
            $selected = Yii::$app->request->post('permissions', []);
            if (!is_array($selected)) $selected = $selected ? [$selected] : [];

            // add new children
            $added = [];
            $removed = [];
            foreach (array_diff($selected, $current) as $pname) {
                $p = $auth->getPermission($pname);
                if ($p) {
                    try { $auth->addChild($role, $p); $added[] = $pname; } catch (\Exception $e) {}
                }
            }
            // remove unselected
            foreach (array_diff($current, $selected) as $pname) {
                $p = $auth->getPermission($pname);
                if ($p) {
                    try { $auth->removeChild($role, $p); $removed[] = $pname; } catch (\Exception $e) {}
                }
            }

            if ($added || $removed) {
                $details = json_encode(['added' => $added, 'removed' => $removed]);
                RbacAudit::log(Yii::$app->user->id ?? null, 'edit-role-permissions', $role->name, $details);
            }

            Yii::$app->session->setFlash('success', 'Role permissions updated.');
            return $this->redirect(['index']);
        }

        return $this->render('role-permissions', [
            'role' => $role,
            'allPerms' => $allPerms,
            'current' => $current,
        ]);
    }

    public function actionAudit()
    {
        $query = \app\models\RbacAudit::find()->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 50 ],
            'sort' => false,
        ]);

        return $this->render('audit', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
