<?php
namespace app\models;

use yii\base\Model;

class RoleForm extends Model
{
    public $name;
    public $description;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/', 'message' => 'Only letters, numbers, underscore and dash are allowed.'],
            [['description'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Role Name',
            'description' => 'Description',
        ];
    }
}
