<?php

use yii\db\Migration;

/**
 * Class m_ecommerce_init
 * Creates all necessary tables for e-commerce application
 */
class m250718_183425_m_ecommerce_init extends Migration
{
    public function safeUp()
    {
        // Create users table
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(50)->notNull()->unique(),
            'email' => $this->string(100)->notNull()->unique(),
            'password_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'password_reset_token' => $this->string(255),
            'first_name' => $this->string(50),
            'last_name' => $this->string(50),
            'phone' => $this->string(20),
            'status' => $this->tinyInteger()->defaultValue(1),
            'role' => $this->string(20)->defaultValue('customer'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Create user addresses table
        $this->createTable('{{%user_addresses}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'type' => $this->string(10)->notNull()->comment('billing or shipping'),
            'first_name' => $this->string(50)->notNull(),
            'last_name' => $this->string(50)->notNull(),
            'address_line_1' => $this->string(255)->notNull(),
            'address_line_2' => $this->string(255),
            'city' => $this->string(100)->notNull(),
            'state' => $this->string(100)->notNull(),
            'postal_code' => $this->string(20)->notNull(),
            'country' => $this->string(100)->notNull(),
            'is_default' => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Create categories table
        $this->createTable('{{%categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'slug' => $this->string(100)->notNull()->unique(),
            'description' => $this->text(),
            'image' => $this->string(255),
            'parent_id' => $this->integer(),
            'sort_order' => $this->integer()->defaultValue(0),
            'status' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Create products table
        $this->createTable('{{%products}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(200)->notNull(),
            'slug' => $this->string(200)->notNull()->unique(),
            'description' => $this->text(),
            'short_description' => $this->string(500),
            'sku' => $this->string(100)->notNull()->unique(),
            'price' => $this->decimal(10, 2)->notNull(),
            'compare_price' => $this->decimal(10, 2),
            'cost_price' => $this->decimal(10, 2),
            'track_inventory' => $this->boolean()->defaultValue(true),
            'inventory_quantity' => $this->integer()->defaultValue(0),
            'weight' => $this->decimal(8, 2),
            'status' => $this->tinyInteger()->defaultValue(1),
            'featured' => $this->boolean()->defaultValue(false),
            'meta_title' => $this->string(255),
            'meta_description' => $this->string(500),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Create product categories junction table
        $this->createTable('{{%product_categories}}', [
            'product_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
            'PRIMARY KEY(product_id, category_id)',
        ]);

        // Create product images table
        $this->createTable('{{%product_images}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'image_url' => $this->string(255)->notNull(),
            'alt_text' => $this->string(255),
            'sort_order' => $this->integer()->defaultValue(0),
            'is_primary' => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Create product attributes table
        $this->createTable('{{%product_attributes}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'attribute_name' => $this->string(100)->notNull(),
            'attribute_value' => $this->string(255)->notNull(),
            'sort_order' => $this->integer()->defaultValue(0),
        ]);

        // Create shopping cart table
        $this->createTable('{{%shopping_cart}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'session_id' => $this->string(100),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Create orders table
        $this->createTable('{{%orders}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'order_number' => $this->string(50)->notNull()->unique(),
            'status' => $this->string(20)->notNull()->defaultValue('pending'),
            'subtotal' => $this->decimal(10, 2)->notNull(),
            'tax_amount' => $this->decimal(10, 2)->defaultValue(0),
            'shipping_amount' => $this->decimal(10, 2)->defaultValue(0),
            'discount_amount' => $this->decimal(10, 2)->defaultValue(0),
            'total_amount' => $this->decimal(10, 2)->notNull(),
            'currency' => $this->string(3)->defaultValue('USD'),
            'payment_method' => $this->string(50),
            'payment_status' => $this->string(20)->defaultValue('pending'),
            'shipping_method' => $this->string(50),
            'notes' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Create order items table
        $this->createTable('{{%order_items}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'product_name' => $this->string(200)->notNull(),
            'product_sku' => $this->string(100)->notNull(),
            'quantity' => $this->integer()->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'total' => $this->decimal(10, 2)->notNull(),
        ]);

        // Create order addresses table
        $this->createTable('{{%order_addresses}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'type' => $this->string(10)->notNull()->comment('billing or shipping'),
            'first_name' => $this->string(50)->notNull(),
            'last_name' => $this->string(50)->notNull(),
            'email' => $this->string(100),
            'phone' => $this->string(20),
            'address_line_1' => $this->string(255)->notNull(),
            'address_line_2' => $this->string(255),
            'city' => $this->string(100)->notNull(),
            'state' => $this->string(100)->notNull(),
            'postal_code' => $this->string(20)->notNull(),
            'country' => $this->string(100)->notNull(),
        ]);

        // Create coupons table
        $this->createTable('{{%coupons}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(50)->notNull()->unique(),
            'type' => $this->string(20)->notNull()->comment('percentage or fixed'),
            'value' => $this->decimal(10, 2)->notNull(),
            'minimum_amount' => $this->decimal(10, 2)->defaultValue(0),
            'usage_limit' => $this->integer(),
            'used_count' => $this->integer()->defaultValue(0),
            'starts_at' => $this->timestamp(),
            'expires_at' => $this->timestamp(),
            'status' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Create reviews table
        $this->createTable('{{%reviews}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'user_id' => $this->integer(),
            'name' => $this->string(100)->notNull(),
            'email' => $this->string(100)->notNull(),
            'rating' => $this->tinyInteger()->notNull(),
            'title' => $this->string(200),
            'comment' => $this->text(),
            'status' => $this->tinyInteger()->defaultValue(0)->comment('0=pending, 1=approved'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Create wishlists table
        $this->createTable('{{%wishlists}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Add foreign keys
        $this->addForeignKey(
            'fk-user_addresses-user_id',
            '{{%user_addresses}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-categories-parent_id',
            '{{%categories}}',
            'parent_id',
            '{{%categories}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-product_categories-product_id',
            '{{%product_categories}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-product_categories-category_id',
            '{{%product_categories}}',
            'category_id',
            '{{%categories}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-product_images-product_id',
            '{{%product_images}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-product_attributes-product_id',
            '{{%product_attributes}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-shopping_cart-user_id',
            '{{%shopping_cart}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-shopping_cart-product_id',
            '{{%shopping_cart}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-orders-user_id',
            '{{%orders}}',
            'user_id',
            '{{%users}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-order_items-order_id',
            '{{%order_items}}',
            'order_id',
            '{{%orders}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-order_items-product_id',
            '{{%order_items}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-order_addresses-order_id',
            '{{%order_addresses}}',
            'order_id',
            '{{%orders}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-reviews-product_id',
            '{{%reviews}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-reviews-user_id',
            '{{%reviews}}',
            'user_id',
            '{{%users}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-wishlists-user_id',
            '{{%wishlists}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-wishlists-product_id',
            '{{%wishlists}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Create indexes for better performance
        $this->createIndex('idx-users-email', '{{%users}}', 'email');
        $this->createIndex('idx-users-username', '{{%users}}', 'username');
        $this->createIndex('idx-users-role', '{{%users}}', 'role');
        $this->createIndex('idx-users-status', '{{%users}}', 'status');
        $this->createIndex('idx-products-slug', '{{%products}}', 'slug');
        $this->createIndex('idx-products-sku', '{{%products}}', 'sku');
        $this->createIndex('idx-products-status', '{{%products}}', 'status');
        $this->createIndex('idx-products-featured', '{{%products}}', 'featured');
        $this->createIndex('idx-categories-slug', '{{%categories}}', 'slug');
        $this->createIndex('idx-categories-parent_id', '{{%categories}}', 'parent_id');
        $this->createIndex('idx-orders-order_number', '{{%orders}}', 'order_number');
        $this->createIndex('idx-orders-status', '{{%orders}}', 'status');
        $this->createIndex('idx-orders-payment_status', '{{%orders}}', 'payment_status');
        $this->createIndex('idx-shopping_cart-session_id', '{{%shopping_cart}}', 'session_id');
        $this->createIndex('idx-coupons-code', '{{%coupons}}', 'code');
        $this->createIndex('idx-reviews-status', '{{%reviews}}', 'status');
        $this->createIndex('idx-wishlists-user_product', '{{%wishlists}}', ['user_id', 'product_id'], true);
    }

    public function safeDown()
    {
        // Drop foreign keys first
        $this->dropForeignKey('fk-user_addresses-user_id', '{{%user_addresses}}');
        $this->dropForeignKey('fk-categories-parent_id', '{{%categories}}');
        $this->dropForeignKey('fk-product_categories-product_id', '{{%product_categories}}');
        $this->dropForeignKey('fk-product_categories-category_id', '{{%product_categories}}');
        $this->dropForeignKey('fk-product_images-product_id', '{{%product_images}}');
        $this->dropForeignKey('fk-product_attributes-product_id', '{{%product_attributes}}');
        $this->dropForeignKey('fk-shopping_cart-user_id', '{{%shopping_cart}}');
        $this->dropForeignKey('fk-shopping_cart-product_id', '{{%shopping_cart}}');
        $this->dropForeignKey('fk-orders-user_id', '{{%orders}}');
        $this->dropForeignKey('fk-order_items-order_id', '{{%order_items}}');
        $this->dropForeignKey('fk-order_items-product_id', '{{%order_items}}');
        $this->dropForeignKey('fk-order_addresses-order_id', '{{%order_addresses}}');
        $this->dropForeignKey('fk-reviews-product_id', '{{%reviews}}');
        $this->dropForeignKey('fk-reviews-user_id', '{{%reviews}}');
        $this->dropForeignKey('fk-wishlists-user_id', '{{%wishlists}}');
        $this->dropForeignKey('fk-wishlists-product_id', '{{%wishlists}}');

        // Drop tables
        $this->dropTable('{{%wishlists}}');
        $this->dropTable('{{%reviews}}');
        $this->dropTable('{{%coupons}}');
        $this->dropTable('{{%order_addresses}}');
        $this->dropTable('{{%order_items}}');
        $this->dropTable('{{%orders}}');
        $this->dropTable('{{%shopping_cart}}');
        $this->dropTable('{{%product_attributes}}');
        $this->dropTable('{{%product_images}}');
        $this->dropTable('{{%product_categories}}');
        $this->dropTable('{{%products}}');
        $this->dropTable('{{%categories}}');
        $this->dropTable('{{%user_addresses}}');
        $this->dropTable('{{%users}}');
    }
}
