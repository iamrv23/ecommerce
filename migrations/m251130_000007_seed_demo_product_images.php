<?php

use yii\db\Migration;

class m251130_000007_seed_demo_product_images extends Migration
{
    public function safeUp()
    {
        // Seed product_images with free-license placeholder images for existing products
        $products = $this->db->createCommand('SELECT id FROM {{%products}}')->queryAll();
        if (empty($products)) {
            echo "No products found, skipping seeder.\n";
            return;
        }

        foreach ($products as $p) {
            $pid = (int)$p['id'];
            // primary image
            $url = 'https://picsum.photos/seed/product' . $pid . '/1200/800';
            $exists = (bool)$this->db->createCommand('SELECT COUNT(*) FROM {{%product_images}} WHERE product_id=:pid', [':pid' => $pid])->queryScalar();
            if (!$exists) {
                $this->insert('{{%product_images}}', [
                    'product_id' => $pid,
                    'image_url' => $url,
                    'is_primary' => 1,
                    'sort_order' => 0,
                ]);
                // add a second image
                $url2 = 'https://picsum.photos/seed/product' . $pid . 'b/800/600';
                $this->insert('{{%product_images}}', [
                    'product_id' => $pid,
                    'image_url' => $url2,
                    'is_primary' => 0,
                    'sort_order' => 1,
                ]);
            }
        }
        echo "Demo product images seeded.\n";
    }

    public function safeDown()
    {
        // remove seeded placeholder images (picsum pattern)
        $this->delete('{{%product_images}}', [
            'like', 'image_url', 'picsum.photos'
        ]);
    }
}
