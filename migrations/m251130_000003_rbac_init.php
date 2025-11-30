<?php

use yii\db\Migration;

/**
 * Initializes RBAC roles and permissions and assigns roles to existing users
 */
class m251130_000003_rbac_init extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        if (!$auth) {
            echo "Auth manager not configured, skipping RBAC initialization.\n";
            return true;
        }

        // Permissions
        $perms = [
            'manageProducts' => 'Manage products (create/update/delete)',
            'manageOrders' => 'Manage orders',
            'manageUsers' => 'Manage users',
            'viewOrders' => 'View own orders',
            'createOrder' => 'Create orders',
        ];

        foreach ($perms as $name => $desc) {
            $p = $auth->getPermission($name);
            if ($p === null) {
                $p = $auth->createPermission($name);
                $p->description = $desc;
                $auth->add($p);
            }
        }

        // Roles
        $roles = [];

        // customer
        $customer = $auth->getRole('customer');
        if ($customer === null) {
            $customer = $auth->createRole('customer');
            $customer->description = 'Customer (can place orders)';
            $auth->add($customer);
        }
        // assign customer permissions
        $auth->removeChildren($customer);
        $auth->addChild($customer, $auth->getPermission('createOrder'));
        $auth->addChild($customer, $auth->getPermission('viewOrders'));
        $roles[] = 'customer';

        // reseller
        $reseller = $auth->getRole('reseller');
        if ($reseller === null) {
            $reseller = $auth->createRole('reseller');
            $reseller->description = 'Reseller (can view orders, manage some products)';
            $auth->add($reseller);
        }
        $auth->removeChildren($reseller);
        $auth->addChild($reseller, $auth->getRole('customer'));
        // Resellers may manage products but not users
        $auth->addChild($reseller, $auth->getPermission('manageProducts'));
        $roles[] = 'reseller';

        // admin
        $admin = $auth->getRole('admin');
        if ($admin === null) {
            $admin = $auth->createRole('admin');
            $admin->description = 'Administrator (full access)';
            $auth->add($admin);
        }
        $auth->removeChildren($admin);
        // admin inherits reseller
        $auth->addChild($admin, $auth->getRole('reseller'));
        // admin also manages users and orders
        $auth->addChild($admin, $auth->getPermission('manageUsers'));
        $auth->addChild($admin, $auth->getPermission('manageOrders'));
        $roles[] = 'admin';

        // Map existing users' role column to RBAC assignments
        $db = Yii::$app->db;
        if ($db) {
            $users = $db->createCommand("SELECT id, role FROM users WHERE role IS NOT NULL")->queryAll();
            foreach ($users as $u) {
                $roleName = $u['role'];
                if (in_array($roleName, $roles)) {
                    $role = $auth->getRole($roleName);
                    // skip if already assigned
                    $assignments = $auth->getAssignments($u['id']);
                    if (!isset($assignments[$roleName])) {
                        try {
                            $auth->assign($role, $u['id']);
                        } catch (Exception $e) {
                            // ignore assignment errors
                        }
                    }
                }
            }
        }

        echo "RBAC roles and permissions initialized.\n";
        return true;
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        if (!$auth) {
            return false;
        }

        // remove permissions
        $permNames = ['manageProducts','manageOrders','manageUsers','viewOrders','createOrder'];
        foreach ($permNames as $pname) {
            $p = $auth->getPermission($pname);
            if ($p) $auth->remove($p);
        }

        // remove roles
        $roleNames = ['admin','reseller','customer'];
        foreach ($roleNames as $rname) {
            $r = $auth->getRole($rname);
            if ($r) $auth->remove($r);
        }

        return true;
    }
}
