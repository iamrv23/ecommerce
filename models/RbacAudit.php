<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Model for RBAC audit entries
 * @property int $id
 * @property int|null $actor_id
 * @property string $action
 * @property string|null $target
 * @property string|null $details
 * @property string $created_at
 */
class RbacAudit extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%rbac_audit}}';
    }

    public static function log($actorId, $action, $target = null, $details = null)
    {
        try {
            $m = new self();
            $m->actor_id = $actorId ?: null;
            $m->action = (string)$action;
            $m->target = $target;
            $m->details = $details;
            return $m->save(false);
        } catch (\Exception $e) {
            Yii::error('Failed to write RBAC audit: ' . $e->getMessage());
            return false;
        }
    }
}
