<?php

namespace frontend\modules\insta\models;

use Yii;
use yii\data\Pagination;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use frontend\models\User;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property int $user_id
 * @property string $filename
 * @property string|null $description
 * @property int $created_at
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post';
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'filename' => 'Filename',
            'description' => 'Description',
            'created_at' => 'Created At',
        ];
    }
    public function getAllPosts($id)
    {
        return $this->find()->where(['id' => $id])->all();
    }

    public function getFeed($start_page = 1)
    {
        return $this->find()->offset($start_page)->limit(3)->asArray()->all();
    }
    public function getPost(int $id)
    {
        return $this->find()->where(['id' => $id])->one();
    }


    public function like(IdentityInterface $user)
    {
        $redis = Yii::$app->redis;
        $redis->sadd("post:{$this->id}:likes", $user->getId());
        $redis->sadd("user:{$user->getId()}:likes", $this->id);
    }

    public function countLikes()
    {
        $redis = Yii::$app->redis;
        return $redis->scard("post:{$this->id}:likes").' likes';
    }

    public function removeLike(IdentityInterface $user)
    {
        $redis = Yii::$app->redis;
        $redis->srem("post:{$this->id}:likes", $user->getId());
        $redis->srem("user:{$user->getId()}:likes", $this->id);
    }

    public function isLikedByUser(IdentityInterface $user)
    {
        $redis = Yii::$app->redis;
        return $redis->sismember("post:{$this->id}:likes", $user->getId());
    }
}
