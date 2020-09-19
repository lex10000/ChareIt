<?php
declare(strict_types=1);

namespace frontend\modules\insta\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;


/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property int $user_id
 * @property string $filename
 * @property string|null $description
 * @property int $created_at
 * @property string $thumbnail
 */
class Post extends ActiveRecord
{
    /**
     * @type int кол-во получение постов за один запрос
     */
    private $limit = 5;

    /**
     * @type int кол-во постов в топ-листе
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
     * Удаление поста. При удалении - удалить все данные о посте из редиса.
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function delete() :bool
    {
        $redis = Yii::$app->redis;

        $redis->srem("post:{$this->id}:likes", $this->user_id);
        $redis->srem("post:{$this->id}:dislikes", $this->user_id);
        $redis->zrem('topz', $this->id);

        return parent::delete();
    }


    /**
     * Возвращает массив с инста-постами
     * @param int $start_page стартовый номер поиска постов (для асинхронной загрузки)
     * @param int|null $user_id если не указан, то вернуть посты всех пользователей
     * @return array|null массив с постами
     */
    public function getFeed(int $start_page = 0, int $user_id = null): ?array
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
     * @param int $last_post_time время последнего поста,
     * @return array|null массив с новыми постами
     */
    public function getNewPosts(int $last_post_time): ?array
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
    public function getPost(int $id): ?ActiveRecord
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
    public function changeStatus(int $user_id, int $post_id, string $action): ?string
    {
        $redis = Yii::$app->redis;

        switch ($action) {
            case 'like' :
            {
                $index = 'likes';
                break;
            }
            case 'dislike' :
            {
                $index = 'dislikes';
                break;
            }
            default :
            {
                return null;
            }
        }

        $method = self::isChangedByUser($user_id, $post_id, $index) ? 'srem' : 'sadd';

        if ($redis->$method("user:{$user_id}:{$index}", $post_id) && $redis->$method("post:{$post_id}:{$index}", $user_id)) {
            $this->changeTopPost($method, $action, $post_id);
            return $method;
        } else return null;
    }

    /**
     * Считает кол-во лайков у поста по формуле "Общее кол-во лайков - Кол-во дизлайков". Возможно отриц. значение
     * @param int $post_id
     * @return int
     */
    public static function countLikes(int $post_id): int
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
    public static function isChangedByUser(int $user_id, int $post_id, string $index): bool
    {
        $redis = Yii::$app->redis;
        return (bool)$redis->sismember("post:{$post_id}:{$index}", $user_id);
    }

    /**
     * Возвращает массив с постами, отсортированный по количеству лайков.
     * @param int|null $top количество постов для топ-листа
     * @return array|null массив с постами
     */
    public function getTopPosts(int $top = null): ?array
    {
        $top = $top ?? $this->top;
        $redis = Yii::$app->redis;
        $tops = $redis->zrevrangebyscore('topz', 1, -1, 'limit', 0, $top);
        $posts = $this->find()->where(['id' => $tops])->limit($top)->asArray()->all();
        $final = [];
        foreach ($tops as $top) {
            foreach ($posts as $post) {
                if($top==$post['id']) {
                    $final[] = $post;
                }
            }
        }
        return $final;
    }

    /**
     * Меняет топ-лист, при лайке\дизлайке поста
     * @param string $method srem\sadd
     * @param string $action like\dislike
     * @param int $post_id id поста
     * @return void
     */
    private function changeTopPost(string $method, string $action, int $post_id) : void
    {
        $redis = Yii::$app->redis;

        if ($action === 'like') {
            $topPostIncrement = $method === 'srem' ? -1 : 1;
        } elseif ($action === 'dislike') {
            $topPostIncrement = $method === 'srem' ? 1 : -1;
        }
        $redis->zincrby('topz', $topPostIncrement, $post_id);
    }

    /**
     * Добавляет пост в топ-лист, при создании поста.
     * @return void
     */
    public function addToTop() :void
    {
        $redis = Yii::$app->redis;
        $redis->zadd('topz', 0, $this->id);
    }
}
