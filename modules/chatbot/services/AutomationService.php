<?php

namespace app\modules\chatbot\services;

use Yii;
use yii\base\Component;
use app\models\User;
use app\models\Product;
use app\models\Order;
use app\models\ShoppingCart;

/**
 * Automation Service for executing business logic actions
 */
class AutomationService extends Component
{
    private $tenantId;

    public function __construct($tenantId = null, $config = [])
    {
        $this->tenantId = $tenantId ?: \app\modules\chatbot\Module::getTenantId();
        parent::__construct($config);
    }

    /**
     * Execute an automation action
     */
    public function execute($action, $params = [])
    {
        $method = 'action' . ucfirst($action);
        if (method_exists($this, $method)) {
            return $this->$method($params);
        }

        throw new \InvalidArgumentException("Automation action '{$action}' not found");
    }

    /**
     * Create a new customer
     */
    public function actionCreateCustomer($params)
    {
        $user = new User();
        $user->attributes = $params;
        $user->tenant_id = $this->tenantId;
        $user->role = User::ROLE_CUSTOMER;
        $user->status = User::STATUS_ACTIVE;
        $user->setPassword($params['password'] ?? Yii::$app->security->generateRandomString(8));
        $user->generateAuthKey();

        if ($user->save()) {
            return [
                'success' => true,
                'message' => 'Customer created successfully',
                'customer_id' => $user->id,
                'username' => $user->username,
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create customer: ' . implode(', ', $user->getFirstErrors()),
            ];
        }
    }

    /**
     * Process an order
     */
    public function actionProcessOrder($params)
    {
        $userId = $params['user_id'] ?? null;
        $user = Yii::$app->user->identity;
        if (!$userId) {
            $userId = $user->id;
        }

        if (!$userId) {
            return [
                'success' => false,
                'message' => 'User ID is required to process order',
            ];
        }

        // Get cart items
        $cartItems = ShoppingCart::find()
        ->where(['user_id' => $userId, 'tenant_id' => $this->tenantId])
        ->all();
        
        if (empty($cartItems)) {
            return [
                'success' => false,
                'message' => 'No items in cart',
            ];
        }

        // Create order
        $order = new Order();
        $order->user_id = $userId;
        $order->tenant_id = $this->tenantId;
        $order->status = 'PENDING';
        $order->total_amount = 0;
        $order->shipping_address = $params['shipping_address'] ?? '';

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$order->save()) {
                throw new \Exception('Failed to create order');
            }

            $total = 0;
            foreach ($cartItems as $cartItem) {
                $product = Product::findOne($cartItem->product_id);
                if (!$product || $product->inventory_quantity < $cartItem->quantity) {
                    throw new \Exception("Insufficient inventory for product {$product->name}");
                }

                $orderItem = new \app\models\OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $cartItem->product_id;
                $orderItem->quantity = $cartItem->quantity;
                $orderItem->price = $product->price;
                $orderItem->tenant_id = $this->tenantId;

                if (!$orderItem->save()) {
                    throw new \Exception('Failed to save order item');
                }

                $total += $orderItem->quantity * $orderItem->price;

                // Update inventory
                $product->inventory_quantity -= $cartItem->quantity;
                $product->save();

                // Remove from cart
                $cartItem->delete();
            }

            $order->total_amount = $total;
            $order->save();

            $transaction->commit();

            return [
                'success' => true,
                'message' => 'Order processed successfully',
                'order_id' => $order->id,
                'total_amount' => $total,
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => 'Failed to process order: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check inventory for a product
     */
    public function actionCheckInventory($params)
    {
        //check if role user is admin
        $user = Yii::$app->user->identity;
        if ($user->role !== User::ROLE_ADMIN) {
            return [
                'success' => false,
                'message' => 'Unauthorized: Only admins can check inventory',
            ];
        }
        $productName = $params['product_name'] ?? null;
        $sku = $params['sku'] ?? null;

        $query = Product::find()->where(['tenant_id' => $this->tenantId]);

        if ($sku) {
            $query->andWhere(['sku' => $sku]);
        } elseif ($productName) {
            $query->andWhere(['like', 'name', $productName]);
        } else {
            return [
                'success' => false,
                'message' => 'Product name or SKU is required',
            ];
        }

        $product = $query->one();

        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found',
            ];
        }

        return [
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'inventory_quantity' => $product->inventory_quantity,
                'price' => $product->price,
            ],
        ];
    }

    /**
     * Get order status
     */
    public function actionGetOrderStatus($params)
    {
        $orderId = $params['order_id'] ?? null;
        $userId = $params['user_id'] ?? null;

        if (!$orderId) {
            return [
                'success' => false,
                'message' => 'Order ID is required',
            ];
        }

        $query = Order::find()->where(['id' => $orderId, 'tenant_id' => $this->tenantId]);

        if ($userId) {
            $query->andWhere(['user_id' => $userId]);
        }

        $order = $query->one();

        if (!$order) {
            return [
                'success' => false,
                'message' => 'Order not found',
            ];
        }

        return [
            'success' => true,
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at,
            ],
        ];
    }

    /**
     * Assign license (placeholder for license management)
     */
    public function actionAssignLicense($params)
    {
        // Placeholder implementation
        return [
            'success' => true,
            'message' => 'License assigned successfully',
            'license_key' => 'LIC-' . strtoupper(uniqid()),
        ];
    }

    /**
     * Generate report (placeholder)
     */
    public function actionGenerateReport($params)
    {
        $reportType = $params['type'] ?? 'sales';

        // Placeholder implementation
        return [
            'success' => true,
            'message' => 'Report generated successfully',
            'report_url' => '/reports/' . $reportType . '_' . date('Y-m-d') . '.pdf',
        ];
    }
}