<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_repeat;
    public $admin_code;

    public function rules()
    {
        return [
            [['username', 'email', 'password', 'password_repeat'], 'required'],
            ['username', 'string', 'min' => 3, 'max' => 50],
            ['email', 'email'],
            ['email', 'string', 'max' => 100],
            ['username', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'username'],
            ['email', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'email'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength'] ?? 8],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords don\'t match.'],
            ['admin_code', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'password_repeat' => 'Repeat Password',
            'admin_code' => 'Admin Code (optional)'
        ];
    }

    /**
     * Signs user up.
     * If `admin_code` matches `Yii::$app->params['adminSignupCode']`, role will be set to admin.
     *
     * @return User|null the saved model or null on failure
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;

        // default role
        $user->role = User::ROLE_CUSTOMER;

        // allow admin creation only when correct code provided
        $adminCode = Yii::$app->params['adminSignupCode'] ?? null;
        if (!empty($adminCode) && $this->admin_code && $this->admin_code === $adminCode) {
            $user->role = User::ROLE_ADMIN;
        }

        if ($user->save()) {
            return $user;
        }

        return null;
    }
}
