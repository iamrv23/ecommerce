<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // show featured and recent products on the homepage
        $products = [];
        try {
            $products = \app\models\Product::find()
                ->where(['status' => \app\models\Product::STATUS_ACTIVE])
                ->orderBy(['featured' => SORT_DESC, 'id' => SORT_DESC])
                ->limit(8)
                ->all();
        } catch (\Throwable $e) {
            Yii::error('Failed to load products for homepage: ' . $e->getMessage(), __METHOD__);
        }

        return $this->render('index', ['products' => $products]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        // no merge flags to clear (cart merging is stateless and handled via DB rows)

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signup action.
     *
     * @return string|\yii\web\Response
     */
    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignupForm();
        if (Yii::$app->request->isPost) {
            Yii::info('Signup POST received: ' . json_encode(Yii::$app->request->post()), __METHOD__);
        }

        if ($model->load(Yii::$app->request->post())) {
            if (($user = $model->signup())) {
                // merge any session or session-bound guest cart into the new user's cart
                try {
                    // Merge any DB rows stored for this guest session into the newly created user
                    $sessionId = session_id();
                    if ($sessionId) {
                        $rows = \app\models\ShoppingCart::find()->where(['session_id' => $sessionId])->all();
                        foreach ($rows as $r) {
                            \app\models\ShoppingCart::addOrIncrement($user->id, $r->product_id, $r->quantity);
                            $r->delete();
                        }
                    }
                    // ensure user cart exists in DB (no session array is required)
                } catch (\Throwable $e) {
                    Yii::error('Failed to merge guest cart on signup: ' . $e->getMessage(), __METHOD__);
                }

                Yii::$app->session->setFlash('success', 'Thank you for signing up. You can now log in.');
                return $this->redirect(['site/login']);
            }
            Yii::info('Signup validation failed: ' . json_encode($model->getErrors()), __METHOD__);
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }
}
