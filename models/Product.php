<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $short_description
 * @property string $sku
 * @property float $price
 * @property float|null $compare_price
 * @property float|null $cost_price
 * @property int $track_inventory
 * @property int $inventory_quantity
 * @property float|null $weight
 * @property int $status
 * @property int $featured
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string $created_at
 * @property string $updated_at
 */
class Product extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('CURRENT_TIMESTAMP'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug', 'sku', 'price'], 'required'],
            [['description', 'short_description', 'meta_description'], 'string'],
            [['price', 'compare_price', 'cost_price', 'weight'], 'number'],
            [['track_inventory', 'inventory_quantity', 'status', 'featured'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'slug'], 'string', 'max' => 200],
            [['short_description'], 'string', 'max' => 500],
            [['sku'], 'string', 'max' => 100],
            [['meta_title'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['sku'], 'unique'],
            [['status'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            [['featured', 'track_inventory'], 'boolean'],
            [['inventory_quantity'], 'integer', 'min' => 0],
            [['price'], 'number', 'min' => 0],
            [['compare_price', 'cost_price'], 'number', 'min' => 0],
            [['weight'], 'number', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'description' => 'Description',
            'short_description' => 'Short Description',
            'sku' => 'SKU',
            'price' => 'Price',
            'compare_price' => 'Compare Price',
            'cost_price' => 'Cost Price',
            'track_inventory' => 'Track Inventory',
            'inventory_quantity' => 'Inventory Quantity',
            'weight' => 'Weight',
            'status' => 'Status',
            'featured' => 'Featured',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get product categories
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('product_categories', ['product_id' => 'id']);
    }

    /**
     * Get product images
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(ProductImage::class, ['product_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC]);
    }

    /**
     * Get primary product image
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrimaryImage()
    {
        return $this->hasOne(ProductImage::class, ['product_id' => 'id'])
            ->where(['is_primary' => true]);
    }

    /**
     * Get product attributes (renamed to avoid conflict with Model::getAttributes())
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductAttributes()
    {
        return $this->hasMany(ProductAttribute::class, ['product_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC]);
    }

    /**
     * Get product reviews
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Review::class, ['product_id' => 'id'])
            ->where(['status' => 1]);
    }

    /**
     * Get product order items
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['product_id' => 'id']);
    }

    /**
     * Get product shopping cart items
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShoppingCartItems()
    {
        return $this->hasMany(ShoppingCart::class, ['product_id' => 'id']);
    }

    /**
     * Get product wishlists
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWishlists()
    {
        return $this->hasMany(Wishlist::class, ['product_id' => 'id']);
    }

    /**
     * Get status options
     *
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
        ];
    }

    /**
     * Get status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? $this->status;
    }

    /**
     * Get primary image URL
     *
     * @return string
     */
    public function getPrimaryImageUrl()
    {
        $image = $this->primaryImage;
        if (!$image || empty($image->image_url)) {
            return 'https://picsum.photos/seed/product' . $this->id . '/1200/800';
        }

        $url = $image->image_url;
        // If absolute URL, return as-is
        if (stripos($url, 'http://') === 0 || stripos($url, 'https://') === 0) {
            return $url;
        }

        // Normalize leading slash
        if ($url[0] !== '/') {
            $url = '/' . $url;
        }

        $webrootPath = Yii::getAlias('@webroot') . $url;
        if (file_exists($webrootPath)) {
            return $url;
        }

        // fallback to picsum placeholder for missing local files
        return 'https://picsum.photos/seed/product' . $this->id . '/1200/800';
    }

    /**
     * Get formatted price
     *
     * @return string
     */
    public function getFormattedPrice()
    {
        return Yii::$app->formatter->asCurrency($this->price);
    }

    /**
     * Get formatted compare price
     *
     * @return string
     */
    public function getFormattedComparePrice()
    {
        return $this->compare_price ? Yii::$app->formatter->asCurrency($this->compare_price) : null;
    }

    /**
     * Check if product is on sale
     *
     * @return bool
     */
    public function isOnSale()
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    /**
     * Get discount percentage
     *
     * @return float|null
     */
    public function getDiscountPercentage()
    {
        if (!$this->isOnSale()) {
            return null;
        }

        return round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    /**
     * Check if product is in stock
     *
     * @return bool
     */
    public function isInStock()
    {
        if (!$this->track_inventory) {
            return true;
        }

        return $this->inventory_quantity > 0;
    }

    /**
     * Get average rating
     *
     * @return float
     */
    public function getAverageRating()
    {
        $reviews = $this->reviews;
        if (empty($reviews)) {
            return 0;
        }

        $totalRating = array_sum(ArrayHelper::getColumn($reviews, 'rating'));
        return round($totalRating / count($reviews), 1);
    }

    /**
     * Get review count
     *
     * @return int
     */
    public function getReviewCount()
    {
        return count($this->reviews);
    }

    /**
     * Get related products
     *
     * @param int $limit
     * @return array
     */
    public function getRelatedProducts($limit = 4)
    {
        $categoryIds = ArrayHelper::getColumn($this->categories, 'id');

        if (empty($categoryIds)) {
            return [];
        }

        return static::find()
            ->joinWith('categories')
            ->where(['!=', 'products.id', $this->id])
            ->andWhere(['categories.id' => $categoryIds])
            ->andWhere(['products.status' => self::STATUS_ACTIVE])
            ->limit($limit)
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->slug)) {
                $this->slug = $this->generateSlug($this->name);
            }
            return true;
        }
        return false;
    }

    /**
     * Generate slug from name
     *
     * @param string $name
     * @return string
     */
    protected function generateSlug($name)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $originalSlug = $slug;
        $counter = 1;

        while (static::find()->where(['slug' => $slug])->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get active products
     *
     * @return \yii\db\ActiveQuery
     */
    public static function findActive()
    {
        return static::find()->where(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Get featured products
     *
     * @return \yii\db\ActiveQuery
     */
    public static function findFeatured()
    {
        return static::findActive()->where(['featured' => 1]);
    }
}
