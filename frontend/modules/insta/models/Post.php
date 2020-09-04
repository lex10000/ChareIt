<?php

namespace frontend\modules\insta\models;

use Yii;
use yii\base\ArrayAccessTrait;
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
     * @var int кол-во получение постов за один запрос
     */
    private $limit = 3;

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

    /**
     * Возвращает массив с инста-постами
     * @param int $start_page стартовый номер поиска постов (для асинхронной загрузки)
     * @param int|null $user_id если не указан, то вернуть посты всех пользователей
     * @return array массив с постами
     */
    public function getFeed($start_page = 0, $user_id = null)
    {
        $condition = $user_id ? ['user_id' => $user_id] : null;

        return $this->find()
            ->where($condition)
            ->orderBy('created_at DESC')
            ->offset($start_page)
            ->limit($this->limit)
            ->asArray()
            ->all();
    }

    /**
     * Получить один пост
     * @param int $id
     * @return array|ActiveRecord|null
     */
    public function getPost(int $id)
    {
        return $this->find()
            ->where(['id' => $id])
            ->one();
    }

    public function like(IdentityInterface $user)
    {
        $redis = Yii::$app->redis;
        if(
            $redis->sadd("user:{$user->getId()}:likes", $this->id) && $redis->sadd("post:{$this->id}:likes", $user->getId())
        ) {
            return true;
        };
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
