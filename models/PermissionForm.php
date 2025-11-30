<?php
namespace app\models;

use yii\base\Model;

class PermissionForm extends Model
{
    public $name;
    public $description;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'match', 'pattern' => '/^[a-zA-Z0-9:_-]+$/', 'message' => 'Only letters, numbers, colon, underscore and dash are allowed.'],
            [['description'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Permission Name',
            'description' => 'Description',
        ];
    }
}
