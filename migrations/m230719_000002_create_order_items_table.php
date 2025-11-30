<?php

use yii\db\Migration;

/**
 * Class m230719_000002_create_order_items_table
 */
class m230719_000002_create_order_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('order_items', true) === null) {
            $this->createTable('order_items', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'unit_price' => $this->decimal(10, 2)->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

            $this->addForeignKey(
                'fk-order_items-order_id',
                'order_items',
                'order_id',
                'orders',
                'id',
                'CASCADE'
            );

            $this->addForeignKey(
                'fk-order_items-product_id',
                'order_items',
                'product_id',
                'products',
                'id',
                'CASCADE'
            );
        } else {
            echo "Table 'order_items' already exists, skipping creation.\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-order_items-product_id', 'order_items');
        $this->dropForeignKey('fk-order_items-order_id', 'order_items');
        $this->dropTable('order_items');
    }
}
