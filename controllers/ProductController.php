<?php

namespace app\controllers;

use Yii;
use app\models\Product;
use app\models\ProductImage;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

class ProductController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['@', '?'],
                    ],
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->role === 'admin';
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Product::find()->where(['status' => Product::STATUS_ACTIVE]),
            'pagination' => [
                'pageSize' => 12,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Product();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // handle uploaded images
            $files = UploadedFile::getInstancesByName('imageFiles');
            if (!empty($files)) {
                foreach ($files as $file) {
                    ProductImage::saveUploadedFile($file, $model->id);
                }
                // if no primary image set, set first uploaded as primary
                $primary = ProductImage::find()->where(['product_id' => $model->id, 'is_primary' => 1])->one();
                if (!$primary) {
                    $first = ProductImage::find()->where(['product_id' => $model->id])->orderBy(['id'=>SORT_ASC])->one();
                    if ($first) {
                        $first->is_primary = 1;
                        $first->save(false);
                    }
                }
            }

            Yii::$app->session->setFlash('success', 'Product created successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $files = UploadedFile::getInstancesByName('imageFiles');
            if (!empty($files)) {
                foreach ($files as $file) {
                    ProductImage::saveUploadedFile($file, $model->id);
                }
            }

            Yii::$app->session->setFlash('success', 'Product updated successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Product deleted successfully.');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested product does not exist.');
    }
}
