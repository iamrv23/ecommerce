<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_images}}`.
 */
class m251130_000001_create_product_images_table extends Migration
{
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('product_images', true) === null) {
            $this->createTable('{{%product_images}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'image_url' => $this->string(1024)->notNull(),
            'is_primary' => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

            $this->addForeignKey(
                'fk-product_images-product_id',
                '{{%product_images}}',
                'product_id',
                '{{%products}}',
                'id',
                'CASCADE'
            );
        } else {
            echo "Table 'product_images' already exists, skipping creation.\n";
        }
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-product_images-product_id', '{{%product_images}}');
        $this->dropTable('{{%product_images}}');
    }
}
