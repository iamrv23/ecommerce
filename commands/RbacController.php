<?php
namespace app\commands;

use Yii;
use yii\console\Controller;

/**
 * Simple console RBAC management commands.
 *
 * Usage:
 *  php yii rbac/assign <roleName> <userId>
 *  php yii rbac/revoke <roleName> <userId>
 *  php yii rbac/list [userId]
 */
class RbacController extends Controller
{
    public function actionAssign($roleName, $userId)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($roleName);
        if (!$role) {
            $this->stderr("Role not found: $roleName\n");
            return 1;
        }
        try {
            $auth->assign($role, (int)$userId);
            $this->stdout("Assigned role $roleName to user $userId\n");
            \app\models\RbacAudit::log(null, 'console-assign', $roleName, 'user:' . $userId);
            return 0;
        } catch (\Exception $e) {
            $this->stderr("Failed to assign: " . $e->getMessage() . "\n");
            return 1;
        }
    }

    public function actionRevoke($roleName, $userId)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($roleName);
        if (!$role) {
            $this->stderr("Role not found: $roleName\n");
            return 1;
        }
        try {
            $auth->revoke($role, (int)$userId);
            $this->stdout("Revoked role $roleName from user $userId\n");
            \app\models\RbacAudit::log(null, 'console-revoke', $roleName, 'user:' . $userId);
            return 0;
        } catch (\Exception $e) {
            $this->stderr("Failed to revoke: " . $e->getMessage() . "\n");
            return 1;
        }
    }

    public function actionList($userId = null)
    {
        $auth = Yii::$app->authManager;
        if ($userId) {
            $assign = $auth->getAssignments((int)$userId);
            if (empty($assign)) {
                $this->stdout("No assignments for user $userId\n");
            } else {
                foreach ($assign as $a) {
                    $this->stdout($a->roleName . "\n");
                }
            }
            return 0;
        }
        $roles = $auth->getRoles();
        foreach ($roles as $r) {
            $this->stdout($r->name . " - " . ($r->description ?? '') . "\n");
        }
        return 0;
    }
}
