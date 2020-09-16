<?php
declare(strict_types=1);

namespace frontend\modules\insta\models;

use Yii;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property int $user_id
 * @property string $filename
 * @property string|null $description
 * @property int $created_at
 */
class Post extends ActiveRecord
{
    /**
     * @var int кол-во получение постов за один запрос
     */
    private $limit = 5;

    /**
     * @var int кол-во постов в топ-листе
     */
    private $top = 10;

//    public static function tableName()
//    {
//        return 'post';
//    }
//
//    public function attributeLabels()
//    {
//        return [
//            'id' => 'ID',
//            'user_id' => 'User ID',
//            'filename' => 'Filename',
//            'description' => 'Description',
//            'created_at' => 'Created At',
//        ];
//    }

    /**
     * Удаление поста. При удалении - удалить все лайки-дизлайки из редиса.
     * @return bool|false|int
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function delete()
    {
        $redis = Yii::$app->redis;

        $redis->srem("post:{$this->id}:likes", $this->user_id);
        $redis->srem("post:{$this->id}:dislikes", $this->user_id);

        return parent::delete();
    }


    /**
     * Возвращает массив с инста-постами
     * @param int $start_page стартовый номер поиска постов (для асинхронной загрузки)
     * @param int|null $user_id если не указан, то вернуть посты всех пользователей
     * @return array|null массив с постами
     */
    public function getFeed(int $start_page = 0, int $user_id = null) : ?array
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
     * @param int $last_post_time  время последнего поста,
     * @return array массив с новыми постами
     */
    public function getNewPosts(int $last_post_time) : array
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
     * @return ActiveRecord|null
     */
    public function getPost(int $id) :? ActiveRecord
    {
        return $this->findOne($id);
    }

    /**
     * Лайк\дизлайк поста. При каждом дейстии пересчет топ-листа.
     * @param int $user_id
     * @param int $post_id
     * @param string $action
     * @return string|null вернет null в том числе, если пользователь уже лайкнул данный пост (механизм множеств, нельзя
     * добавить несколько одинаковых членов в множество)
     */
    public static function changeStatus(int $user_id, int $post_id, string $action) : ?string
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
                return null;
            }
        }

        $method = self::isChangedByUser($user_id, $post_id, $index) ? 'srem' : 'sadd';
        $increment = $method === 'sadd' ? 1 : -1;

        if($redis->$method("user:{$user_id}:{$index}", $post_id) && $redis->$method("post:{$post_id}:{$index}", $user_id)) {
            $redis->hincrby('top', $post_id, $increment);

            return $method;
        } else return null;
    }

    /**
     * Считает кол-во лайков у поста по формуле "Общее кол-во лайков - Кол-во дизлайков". Возможно отриц. значение
     * @param int $post_id
     * @return int
     */
    public static function countLikes(int $post_id) : int
    {
        $redis = Yii::$app->redis;

        $likes = intval($redis->scard("post:{$post_id}:likes"));
        $dislikes = intval($redis->scard("post:{$post_id}:dislikes"));
        return ($likes - $dislikes);
    }

    /**
     * Проверка, ставил ли лайк или дизлайк пользователь за данный пост.
     * @param int $user_id
     * @param int $post_id
     * @param string $index
     * @return bool
     */
    public static function isChangedByUser(int $user_id, int $post_id, string $index) : bool
    {
        $redis = Yii::$app->redis;
        return (bool) $redis->sismember("post:{$post_id}:{$index}", $user_id);
    }

    public function getTopPosts(int $top = null) : array
    {
        $top = $top ?? $this->top;
        $redis = Yii::$app->redis;

        return $redis->hgetall("top");
    }
}
