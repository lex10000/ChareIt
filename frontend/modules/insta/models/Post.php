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
    private $limit = 5;

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
     * Получает новые посты (пока что с помощью setInterval, потом переделаю под веб-сокеты).
     * @param $last_post_time string время последнего поста,
     * @return array|ActiveRecord[] массив с новыми постами
     */
    public function getNewPosts($last_post_time)
    {
        return $this->find()
            ->andFilterCompare('created_at', $last_post_time, '>')
            ->orderBy('created_at')
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
        return $this->findOne($id);
    }

    /**
     * Лайк\дизлайк поста.
     * @param int $user_id
     * @param int $post_id
     * @param string $action
     * @return string|bool вернет false в том числе, если пользователь уже лайкнул данный пост (механизм множеств, нельзя
     * добавить несколько одинаковых членов в множество)
     */
    public static function changeStatus($user_id, $post_id, $action)
    {
        $redis = Yii::$app->redis;

        switch ($action) {
            case 'like' : {
                $index = 'likes';
                break;
            }
            case 'dislike' : {
                $index = 'dislikes';
                break;
            }
            default : {
                return false;
            }
        }

        $method = self::isChangedByUser($user_id, $post_id, $index);
        if($redis->$method("user:{$user_id}:{$index}", $post_id) && $redis->$method("post:{$post_id}:{$index}", $user_id)) {
            return $method;
        } else return false;
    }

    /**
     * Считает кол-во лайков у поста по формуле "Общее кол-во лайков - Кол-во дизлайков". Возможно отриц. значение
     * @param $post_id
     * @return int|false
     */
    public static function countLikes($post_id)
    {
        $redis = Yii::$app->redis;

        $likes = intval($redis->scard("post:{$post_id}:likes"));
        $dislikes = intval($redis->scard("post:{$post_id}:dislikes"));
        return ($likes - $dislikes);
    }

    /**
     * Проверка, ставил ли лайк пользователь за данный пост.
     * @param int $user_id
     * @param int $post_id
     * @param string $index
     * @return mixed
     */
    public static function isChangedByUser($user_id, $post_id, $index)
    {
        $redis = Yii::$app->redis;
        if($redis->sismember("post:{$post_id}:{$index}", $user_id)) {
            return 'srem';
        } else return 'sadd';
    }
}
