<?php

use yii\db\Migration;

class m250718_190933_m_dummy_data_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Insert categories
        $this->batchInsert('{{%categories}}', [
            'name', 'slug', 'description', 'status', 'sort_order'
        ], [
            ['Electronics', 'electronics', 'Electronic gadgets and devices', 1, 1],
            ['Clothing', 'clothing', 'Fashion and apparel', 1, 2],
            ['Books', 'books', 'Books and literature', 1, 3],
            ['Home & Garden', 'home-garden', 'Home and garden products', 1, 4],
            ['Sports', 'sports', 'Sports and outdoor equipment', 1, 5],
            ['Smartphones', 'smartphones', 'Mobile phones and accessories', 1, 6],
            ['Laptops', 'laptops', 'Computers and laptops', 1, 7],
            ['Tablets', 'tablets', 'Tablets and accessories', 1, 8],
        ]);

        // Insert products
        $this->batchInsert('{{%products}}', [
            'name', 'slug', 'description', 'short_description', 'sku', 'price', 
            'compare_price', 'inventory_quantity', 'status', 'featured'
        ], [
            [
                'iPhone 15 Pro', 'iphone-15-pro', 
                'The iPhone 15 Pro features a titanium design, A17 Pro chip, and advanced camera system.',
                'Latest iPhone with titanium design', 
                'IPH15PRO001', 999.00, 1099.00, 50, 1, 1
            ],
            [
                'Samsung Galaxy S24', 'samsung-galaxy-s24',
                'Samsung Galaxy S24 with advanced AI features and stunning display.',
                'Flagship Android smartphone',
                'SAM24001', 799.00, 899.00, 30, 1, 1
            ],
            [
                'MacBook Pro 14"', 'macbook-pro-14',
                'MacBook Pro 14-inch with M3 chip, perfect for professionals.',
                'Professional laptop with M3 chip',
                'MBP14001', 1999.00, 2299.00, 25, 1, 1
            ],
            [
                'Nike Air Max 270', 'nike-air-max-270',
                'Comfortable running shoes with Air Max technology.',
                'Premium running shoes',
                'NIKE270001', 150.00, 180.00, 100, 1, 0
            ],
            [
                'Wireless Bluetooth Headphones', 'bluetooth-headphones',
                'High-quality wireless headphones with noise cancellation.',
                'Wireless headphones with ANC',
                'BT-HEAD001', 199.00, 249.00, 75, 1, 1
            ],
            [
                'Gaming Mechanical Keyboard', 'gaming-keyboard',
                'RGB mechanical keyboard perfect for gaming.',
                'RGB gaming keyboard',
                'GAME-KB001', 89.00, 120.00, 60, 1, 0
            ],
            [
                'Smart Watch Series 9', 'smart-watch-series-9',
                'Latest smart watch with health monitoring features.',
                'Advanced fitness tracking watch',
                'WATCH-S9001', 399.00, 449.00, 40, 1, 1
            ],
            [
                'Professional Camera Lens', 'camera-lens-50mm',
                '50mm f/1.8 lens for professional photography.',
                'Prime lens for portraits',
                'LENS-50001', 299.00, 350.00, 20, 1, 0
            ],
            [
                'Portable Power Bank', 'power-bank-20000mah',
                '20000mAh portable charger with fast charging.',
                'High capacity power bank',
                'PWR-BNK001', 49.00, 65.00, 150, 1, 0
            ],
            [
                'Coffee Maker Machine', 'coffee-maker-deluxe',
                'Automatic coffee maker with programmable settings.',
                'Programmable coffee maker',
                'COFFEE-001', 129.00, 159.00, 35, 1, 0
            ],
        ]);

        // Insert product categories relationships
        $this->batchInsert('{{%product_categories}}', [
            'product_id', 'category_id'
        ], [
            [1, 1], [1, 6], // iPhone - Electronics, Smartphones
            [2, 1], [2, 6], // Samsung - Electronics, Smartphones
            [3, 1], [3, 7], // MacBook - Electronics, Laptops
            [4, 5], // Nike shoes - Sports
            [5, 1], // Headphones - Electronics
            [6, 1], // Keyboard - Electronics
            [7, 1], // Smart Watch - Electronics
            [8, 1], // Camera Lens - Electronics
            [9, 1], // Power Bank - Electronics
            [10, 4], // Coffee Maker - Home & Garden
        ]);

        // Insert product images
        $this->batchInsert('{{%product_images}}', [
            'product_id', 'image_url', 'alt_text', 'is_primary', 'sort_order'
        ], [
            [1, '/images/products/iphone-15-pro-1.jpg', 'iPhone 15 Pro front view', 1, 1],
            [1, '/images/products/iphone-15-pro-2.jpg', 'iPhone 15 Pro back view', 0, 2],
            [2, '/images/products/samsung-s24-1.jpg', 'Samsung Galaxy S24 main', 1, 1],
            [2, '/images/products/samsung-s24-2.jpg', 'Samsung Galaxy S24 side', 0, 2],
            [3, '/images/products/macbook-pro-14-1.jpg', 'MacBook Pro 14 inch', 1, 1],
            [4, '/images/products/nike-air-max-270-1.jpg', 'Nike Air Max 270', 1, 1],
            [5, '/images/products/bluetooth-headphones-1.jpg', 'Bluetooth headphones', 1, 1],
            [6, '/images/products/gaming-keyboard-1.jpg', 'Gaming mechanical keyboard', 1, 1],
            [7, '/images/products/smart-watch-1.jpg', 'Smart Watch Series 9', 1, 1],
            [8, '/images/products/camera-lens-1.jpg', 'Professional camera lens', 1, 1],
            [9, '/images/products/power-bank-1.jpg', 'Portable power bank', 1, 1],
            [10, '/images/products/coffee-maker-1.jpg', 'Coffee maker machine', 1, 1],
        ]);

        // Insert product attributes
        $this->batchInsert('{{%product_attributes}}', [
            'product_id', 'attribute_name', 'attribute_value', 'sort_order'
        ], [
            [1, 'Color', 'Natural Titanium', 1],
            [1, 'Storage', '256GB', 2],
            [1, 'Screen Size', '6.1 inches', 3],
            [2, 'Color', 'Phantom Black', 1],
            [2, 'Storage', '256GB', 2],
            [2, 'Screen Size', '6.2 inches', 3],
            [3, 'Color', 'Space Gray', 1],
            [3, 'RAM', '16GB', 2],
            [3, 'Storage', '512GB', 3],
            [4, 'Color', 'Black/White', 1],
            [4, 'Size', 'US 10', 2],
            [4, 'Material', 'Mesh/Synthetic', 3],
            [5, 'Color', 'Matte Black', 1],
            [5, 'Battery Life', '30 hours', 2],
            [5, 'Connectivity', 'Bluetooth 5.0', 3],
        ]);

        // Insert coupons
        $this->batchInsert('{{%coupons}}', [
            'code', 'type', 'value', 'minimum_amount', 'usage_limit', 'status'
        ], [
            ['WELCOME10', 'percentage', 10.00, 50.00, 100, 1],
            ['SAVE50', 'fixed', 50.00, 200.00, 50, 1],
            ['FREESHIP', 'fixed', 15.00, 100.00, 200, 1],
            ['NEWUSER20', 'percentage', 20.00, 100.00, 50, 1],
            ['BULK25', 'percentage', 25.00, 500.00, 25, 1],
        ]);

        // Insert sample reviews
        $this->batchInsert('{{%reviews}}', [
            'product_id', 'name', 'email', 'rating', 'title', 'comment', 'status'
        ], [
            [1, 'John Doe', 'john@example.com', 5, 'Amazing phone!', 'The iPhone 15 Pro is incredible. The camera quality is outstanding and the performance is smooth.', 1],
            [1, 'Jane Smith', 'jane@example.com', 4, 'Great upgrade', 'Love the new design and features. Battery life could be better though.', 1],
            [2, 'Mike Johnson', 'mike@example.com', 5, 'Best Android phone', 'Samsung Galaxy S24 is the best Android phone I have ever used. Highly recommended!', 1],
            [3, 'Sarah Wilson', 'sarah@example.com', 5, 'Perfect for work', 'MacBook Pro 14 is perfect for my design work. The M3 chip is incredibly fast.', 1],
            [4, 'David Brown', 'david@example.com', 4, 'Comfortable shoes', 'Nike Air Max 270 are very comfortable for daily wear and running.', 1],
            [5, 'Lisa Davis', 'lisa@example.com', 5, 'Excellent sound quality', 'These headphones have amazing sound quality and the noise cancellation works perfectly.', 1],
        ]);

        echo "Dummy data inserted successfully!\n";
    }

    public function safeDown()
    {
        $this->delete('{{%reviews}}');
        $this->delete('{{%coupons}}');
        $this->delete('{{%product_attributes}}');
        $this->delete('{{%product_images}}');
        $this->delete('{{%product_categories}}');
        $this->delete('{{%products}}');
        $this->delete('{{%categories}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250718_190933_m_dummy_data_init cannot be reverted.\n";

        return false;
    }
    */
}
