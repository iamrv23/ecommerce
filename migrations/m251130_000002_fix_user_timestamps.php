<?php

use yii\db\Migration;

/**
 * Converts `users.created_at` and `users.updated_at` to TIMESTAMP with CURRENT_TIMESTAMP defaults
 */
class m251130_000002_fix_user_timestamps extends Migration
{
    public function safeUp()
    {
        $schema = $this->db->schema;
        $table = $schema->getTableSchema('users', true);
        if ($table === null) {
            echo "Table 'users' does not exist, skipping.\n";
            return true;
        }

        // Helper: detect if column is integer-like
        $isIntLike = function ($column) {
            if ($column === null) return false;
            $type = strtolower($column->dbType);
            return strpos($type, 'int') !== false || strpos($type, 'bigint') !== false || strpos($type, 'smallint') !== false;
        };

        // created_at
        $created = $table->getColumn('created_at');
        if ($created !== null && $isIntLike($created)) {
            $this->execute("ALTER TABLE `users` MODIFY `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
            echo "Converted users.created_at to TIMESTAMP.\n";
        } else {
            echo "users.created_at does not need conversion or missing.\n";
        }

        // updated_at
        $updated = $table->getColumn('updated_at');
        if ($updated !== null && $isIntLike($updated)) {
            $this->execute("ALTER TABLE `users` MODIFY `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
            echo "Converted users.updated_at to TIMESTAMP with ON UPDATE.\n";
        } else {
            echo "users.updated_at does not need conversion or missing.\n";
        }

        return true;
    }

    public function safeDown()
    {
        // We won't automatically revert to integer timestamps because that could lose information.
        echo "m251130_000002_fix_user_timestamps cannot be reverted safely.\n";
        return false;
    }
}
