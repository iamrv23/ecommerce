<?php

use yii\db\Migration;

/**
 * Class m230719_000001_create_orders_table
 */
class m230719_000001_create_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('orders', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'total_amount' => $this->decimal(10, 2)->notNull(),
            'status' => $this->string(20)->notNull()->defaultValue('new'),
            'shipping_address' => $this->text(),
            'billing_address' => $this->text(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-orders-user_id',
            'orders',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-orders-user_id', 'orders');
        $this->dropTable('orders');
    }
}
