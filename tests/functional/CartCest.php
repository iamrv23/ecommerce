<?php
use FunctionalTester;

class CartCest
{
    public function _before(FunctionalTester $I)
    {
        // ensure db connection available
    }

    public function testGuestCartMergeOnLogin(FunctionalTester $I)
    {
        // create a product
        $product = new \app\models\Product();
        $product->name = 'Cest Test Product ' . uniqid();
        $product->price = 9.99;
        $product->inventory_quantity = 10;
        $product->status = \app\models\Product::STATUS_ACTIVE;
        $product->save(false);

        // create guest cart row under a fake session id
        $sessionId = 'test-session-' . uniqid();
        $cart = new \app\models\ShoppingCart();
        $cart->session_id = $sessionId;
        $cart->product_id = $product->id;
        $cart->quantity = 2;
        $cart->price = $product->price;
        $cart->save(false);

        // create a user
        $signup = new \app\models\SignupForm();
        $signup->username = 'testuser' . uniqid();
        $signup->email = 'test+' . uniqid() . '@example.com';
        $signup->password = 'TestPassword123';
        $user = $signup->signup();
        $I->assertNotNull($user, 'User created');

        // simulate login event: call CartEventHandler::onAfterLogin
        $event = new stdClass();
        $event->identity = $user;

        // set session id (since CartEventHandler reads PHP session)
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_id($sessionId);

        \app\components\CartEventHandler::onAfterLogin($event);

        // assert cart row moved to user_id and session rows removed
        $countUser = \app\models\ShoppingCart::find()->where(['user_id' => $user->id, 'product_id' => $product->id])->count();
        $countSession = \app\models\ShoppingCart::find()->where(['session_id' => $sessionId])->count();

        $I->assertEquals(1, $countUser, 'Guest cart merged into user cart');
        $I->assertEquals(0, $countSession, 'Session-bound cart rows removed');
    }

    public function testOrderCreateClearsCart(FunctionalTester $I)
    {
        // create product
        $product = new \app\models\Product();
        $product->name = 'Order Test Product ' . uniqid();
        $product->price = 5.00;
        $product->inventory_quantity = 10;
        $product->status = \app\models\Product::STATUS_ACTIVE;
        $product->save(false);

        // create user
        $signup = new \app\models\SignupForm();
        $signup->username = 'orderuser' . uniqid();
        $signup->email = 'order+' . uniqid() . '@example.com';
        $signup->password = 'TestPassword123';
        $user = $signup->signup();
        $I->assertNotNull($user);

        // add cart row for user
        $cart = new \app\models\ShoppingCart();
        $cart->user_id = $user->id;
        $cart->product_id = $product->id;
        $cart->quantity = 3;
        $cart->price = $product->price;
        $cart->save(false);

        // simulate login by setting user identity
        \Yii::$app->user->login($user);

        // call OrderController::actionCreate
        $controller = new \app\controllers\OrderController('order', \Yii::$app);
        $response = $controller->runAction('create');

        // check order created
        $order = \app\models\Order::find()->where(['user_id' => $user->id])->orderBy(['id' => SORT_DESC])->one();
        $I->assertNotNull($order, 'Order created');

        // shopping cart cleared for user
        $cartCount = \app\models\ShoppingCart::find()->where(['user_id' => $user->id])->count();
        $I->assertEquals(0, $cartCount, 'User shopping cart cleared after order');
    }
}
