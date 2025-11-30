<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * This is the model class for table "product_images".
 *
 * @property int $id
 * @property int $product_id
 * @property string $image_url
 * @property int $is_primary
 * @property int $sort_order
 * @property string $created_at
 * @property string $updated_at
 */
class ProductImage extends ActiveRecord
{
    public static function tableName()
    {
        return 'product_images';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('CURRENT_TIMESTAMP'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['product_id', 'image_url'], 'required'],
            [['product_id', 'is_primary', 'sort_order'], 'integer'],
            [['image_url'], 'string', 'max' => 1024],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'image_url' => 'Image URL',
            'is_primary' => 'Is Primary',
            'sort_order' => 'Sort Order',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get a thumbnail URL if available, otherwise return the main image URL or an external placeholder.
     *
     * @return string
     */
    public function getThumbnailUrl()
    {
        $url = $this->image_url ?? '';
        if (empty($url)) {
            return 'https://picsum.photos/seed/product' . $this->product_id . '/400/300';
        }

        if (stripos($url, 'http://') === 0 || stripos($url, 'https://') === 0) {
            return $url;
        }

        if ($url[0] !== '/') {
            $url = '/' . $url;
        }

        $webroot = Yii::getAlias('@webroot');
        $info = pathinfo($url);
        $thumbPath = $info['dirname'] . '/' . $info['filename'] . '_thumb.' . ($info['extension'] ?? 'jpg');
        if (file_exists($webroot . $thumbPath)) {
            return $thumbPath;
        }

        if (file_exists($webroot . $url)) {
            return $url;
        }

        return 'https://picsum.photos/seed/product' . $this->product_id . '/400/300';
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Save uploaded file and create ProductImage record
     *
     * @param UploadedFile $file
     * @param int $productId
     * @return ProductImage|null
     */
    public static function saveUploadedFile(UploadedFile $file, $productId)
    {
        if (!$file) {
            return null;
        }

        $uploadDir = Yii::getAlias('@webroot') . '/uploads/products';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $baseName = $productId . '_' . uniqid();
        $fileName = $baseName . '.' . $file->extension;
        $filePath = $uploadDir . '/' . $fileName;

        if (!$file->saveAs($filePath)) {
            return null;
        }

        // create a reasonable resized image for web
        $thumbName = $baseName . '_thumb.' . $file->extension;
        $thumbPath = $uploadDir . '/' . $thumbName;
        try {
            static::createThumbnail($filePath, $thumbPath, 800);
        } catch (\Throwable $e) {
            // silently continue if thumbnail fails
        }

        $model = new static();
        $model->product_id = $productId;
        $model->image_url = '/uploads/products/' . $fileName;
        $model->is_primary = 0;
        $model->sort_order = 0;
        if ($model->save(false)) {
            return $model;
        }

        return null;
    }

    /**
     * Create a thumbnail / resized image using GD
     *
     * @param string $src
     * @param string $dest
     * @param int $maxWidth
     * @return void
     */
    public static function createThumbnail($src, $dest, $maxWidth = 800)
    {
        if (!file_exists($src)) {
            return;
        }

        list($width, $height, $type) = getimagesize($src);
        if ($width <= $maxWidth) {
            // copy original
            copy($src, $dest);
            return;
        }

        $ratio = $height / $width;
        $newWidth = $maxWidth;
        $newHeight = intval($newWidth * $ratio);

        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImg = imagecreatefromjpeg($src);
                break;
            case IMAGETYPE_PNG:
                $srcImg = imagecreatefrompng($src);
                break;
            case IMAGETYPE_GIF:
                $srcImg = imagecreatefromgif($src);
                break;
            default:
                copy($src, $dest);
                return;
        }

        $dstImg = imagecreatetruecolor($newWidth, $newHeight);
        // preserve transparency for png/gif
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagecolortransparent($dstImg, imagecolorallocatealpha($dstImg, 0, 0, 0, 127));
            imagealphablending($dstImg, false);
            imagesavealpha($dstImg, true);
        }

        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($dstImg, $dest, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($dstImg, $dest);
                break;
            case IMAGETYPE_GIF:
                imagegif($dstImg, $dest);
                break;
        }

        imagedestroy($srcImg);
        imagedestroy($dstImg);
    }
}
