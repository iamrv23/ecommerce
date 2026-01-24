<?php

use yii\db\Migration;

/**
 * Class m251130_000008_add_tenant_support
 * Adds tenant support to the e-commerce platform
 */
class m251130_000008_add_tenant_support extends Migration
{
    public function safeUp()
    {
        // Create tenants table if not exists
        if ($this->db->schema->getTableSchema('{{%tenants}}') === null) {
            $this->createTable('{{%tenants}}', [
                'id' => $this->primaryKey(),
                'name' => $this->string(100)->notNull(),
                'domain' => $this->string(100)->unique(),
                'settings' => $this->json(),
                'is_active' => $this->boolean()->defaultValue(true),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ]);

            // Insert default tenant
            $this->insert('{{%tenants}}', [
                'name' => 'Default Tenant',
                'domain' => 'localhost',
                'settings' => json_encode([
                    'chatbot_enabled' => true,
                    'currency' => 'USD',
                    'timezone' => 'UTC'
                ]),
                'is_active' => true,
            ]);
        }

        // Add tenant_id to existing tables if not exists
        $tables = [
            'users',
            'user_addresses',
            'categories',
            'products',
            'product_images',
            'orders',
            'order_items',
            'shopping_cart',
            'rbac_audit'
        ];

        foreach ($tables as $table) {
            $tableSchema = $this->db->schema->getTableSchema($table);
            if ($tableSchema && !$tableSchema->getColumn('tenant_id')) {
                $this->addColumn($table, 'tenant_id', $this->integer()->notNull()->defaultValue(1));
                $this->createIndex("idx_{$table}_tenant", $table, 'tenant_id');
                $this->addForeignKey("fk_{$table}_tenant", $table, 'tenant_id', 'tenants', 'id', 'CASCADE', 'CASCADE');
            }
        }

        // Create chatbot tables if not exist
        if ($this->db->schema->getTableSchema('{{%chatbot_intents}}') === null) {
            $this->createTable('{{%chatbot_intents}}', [
                'id' => $this->primaryKey(),
                'tenant_id' => $this->integer()->notNull(),
                'intent_name' => $this->string(100)->notNull(),
                'action_type' => $this->string(20)->notNull()->defaultValue('automation'),
                'action_config' => $this->json(),
                'is_active' => $this->boolean()->defaultValue(true),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ]);

            $this->createIndex('idx_chatbot_intents_tenant', 'chatbot_intents', 'tenant_id');
            $this->addForeignKey('fk_chatbot_intents_tenant', 'chatbot_intents', 'tenant_id', 'tenants', 'id', 'CASCADE', 'CASCADE');

            // Insert default intents
            $this->batchInsert('{{%chatbot_intents}}', ['tenant_id', 'intent_name', 'action_type', 'action_config'], [
                [1, 'create_customer', 'automation', json_encode(['service' => 'AutomationService', 'method' => 'createCustomer'])],
                [1, 'process_order', 'automation', json_encode(['service' => 'AutomationService', 'method' => 'processOrder'])],
                [1, 'check_inventory', 'query', json_encode(['service' => 'AutomationService', 'method' => 'checkInventory'])],
                [1, 'get_order_status', 'query', json_encode(['service' => 'AutomationService', 'method' => 'getOrderStatus'])],
                [1, 'assign_license', 'automation', json_encode(['service' => 'AutomationService', 'method' => 'assignLicense'])],
                [1, 'report_query', 'query', json_encode(['service' => 'AutomationService', 'method' => 'generateReport'])],
            ]);
        }

        if ($this->db->schema->getTableSchema('{{%chatbot_sessions}}') === null) {
            $this->createTable('{{%chatbot_sessions}}', [
                'id' => $this->primaryKey(),
                'tenant_id' => $this->integer()->notNull(),
                'session_id' => $this->string(100)->notNull(),
                'user_id' => $this->integer(),
                'conversation_log' => $this->json(),
                'last_activity' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);

            $this->createIndex('idx_chatbot_sessions_tenant', 'chatbot_sessions', 'tenant_id');
            $this->createIndex('idx_chatbot_sessions_session', 'chatbot_sessions', ['tenant_id', 'session_id']);
            $this->addForeignKey('fk_chatbot_sessions_tenant', 'chatbot_sessions', 'tenant_id', 'tenants', 'id', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_chatbot_sessions_user', 'chatbot_sessions', 'user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        }

        if ($this->db->schema->getTableSchema('{{%chatbot_logs}}') === null) {
            $this->createTable('{{%chatbot_logs}}', [
                'id' => $this->primaryKey(),
                'tenant_id' => $this->integer()->notNull(),
                'session_id' => $this->string(100),
                'user_message' => $this->text(),
                'bot_response' => $this->text(),
                'intent_detected' => $this->string(100),
                'action_executed' => $this->string(100),
                'execution_result' => $this->json(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);

            $this->createIndex('idx_chatbot_logs_tenant', 'chatbot_logs', 'tenant_id');
            $this->createIndex('idx_chatbot_logs_session', 'chatbot_logs', ['tenant_id', 'session_id']);
            $this->addForeignKey('fk_chatbot_logs_tenant', 'chatbot_logs', 'tenant_id', 'tenants', 'id', 'CASCADE', 'CASCADE');
        }
    }

    public function safeDown()
    {
        // Drop chatbot tables if exist
        if ($this->db->schema->getTableSchema('{{%chatbot_logs}}') !== null) {
            $this->dropTable('{{%chatbot_logs}}');
        }
        if ($this->db->schema->getTableSchema('{{%chatbot_sessions}}') !== null) {
            $this->dropTable('{{%chatbot_sessions}}');
        }
        if ($this->db->schema->getTableSchema('{{%chatbot_intents}}') !== null) {
            $this->dropTable('{{%chatbot_intents}}');
        }

        // Remove tenant_id from tables if exists
        $tables = [
            'users',
            'user_addresses',
            'categories',
            'products',
            'product_images',
            'orders',
            'order_items',
            'shopping_cart',
            'rbac_audit'
        ];

        foreach ($tables as $table) {
            $tableSchema = $this->db->schema->getTableSchema($table);
            if ($tableSchema && $tableSchema->getColumn('tenant_id')) {
                $this->dropForeignKey("fk_{$table}_tenant", $table);
                $this->dropIndex("idx_{$table}_tenant", $table);
                $this->dropColumn($table, 'tenant_id');
            }
        }

        if ($this->db->schema->getTableSchema('{{%tenants}}') !== null) {
            $this->dropTable('{{%tenants}}');
        }
    }
}