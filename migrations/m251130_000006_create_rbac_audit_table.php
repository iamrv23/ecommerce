<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%rbac_audit}}`.
 */
class m251130_000006_create_rbac_audit_table extends Migration
{
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('rbac_audit', true) === null) {
            $this->createTable('{{%rbac_audit}}', [
                'id' => $this->primaryKey(),
                'actor_id' => $this->integer()->null(),
                'action' => $this->string(128)->notNull(),
                'target' => $this->string(255)->null(),
                'details' => $this->text()->null(),
                'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);

            $this->addForeignKey(
                'fk-rbac_audit-actor_id',
                '{{%rbac_audit}}',
                'actor_id',
                '{{%users}}',
                'id',
                'SET NULL'
            );
        } else {
            echo "Table 'rbac_audit' already exists, skipping creation.\n";
        }
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-rbac_audit-actor_id', '{{%rbac_audit}}');
        $this->dropTable('{{%rbac_audit}}');
    }
}
