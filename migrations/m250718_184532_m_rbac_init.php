<?php

use yii\db\Migration;


class m250718_184532_m_rbac_init extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->getAuthManager();

        // Create permissions
        $createProduct = $auth->createPermission('createProduct');
        $createProduct->description = 'Create product';
        $auth->add($createProduct);

        $updateProduct = $auth->createPermission('updateProduct');
        $updateProduct->description = 'Update product';
        $auth->add($updateProduct);

        $deleteProduct = $auth->createPermission('deleteProduct');
        $deleteProduct->description = 'Delete product';
        $auth->add($deleteProduct);

        $viewProduct = $auth->createPermission('viewProduct');
        $viewProduct->description = 'View product';
        $auth->add($viewProduct);

        $manageOrders = $auth->createPermission('manageOrders');
        $manageOrders->description = 'Manage orders';
        $auth->add($manageOrders);

        $viewOrders = $auth->createPermission('viewOrders');
        $viewOrders->description = 'View orders';
        $auth->add($viewOrders);

        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Manage users';
        $auth->add($manageUsers);

        $viewUsers = $auth->createPermission('viewUsers');
        $viewUsers->description = 'View users';
        $auth->add($viewUsers);

        $manageCategories = $auth->createPermission('manageCategories');
        $manageCategories->description = 'Manage categories';
        $auth->add($manageCategories);

        $viewReports = $auth->createPermission('viewReports');
        $viewReports->description = 'View reports';
        $auth->add($viewReports);

        $manageSettings = $auth->createPermission('manageSettings');
        $manageSettings->description = 'Manage settings';
        $auth->add($manageSettings);

        // Create roles
        $customer = $auth->createRole('customer');
        $customer->description = 'Customer';
        $auth->add($customer);

        $reseller = $auth->createRole('reseller');
        $reseller->description = 'Reseller';
        $auth->add($reseller);

        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $auth->add($admin);

        // Assign permissions to roles
        // Customer permissions
        $auth->addChild($customer, $viewProduct);

        // Reseller permissions
        $auth->addChild($reseller, $customer);
        $auth->addChild($reseller, $viewOrders);
        $auth->addChild($reseller, $viewUsers);

        // Admin permissions
        $auth->addChild($admin, $reseller);
        $auth->addChild($admin, $createProduct);
        $auth->addChild($admin, $updateProduct);
        $auth->addChild($admin, $deleteProduct);
        $auth->addChild($admin, $manageOrders);
        $auth->addChild($admin, $manageUsers);
        $auth->addChild($admin, $manageCategories);
        $auth->addChild($admin, $viewReports);
        $auth->addChild($admin, $manageSettings);
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();
    }
}
