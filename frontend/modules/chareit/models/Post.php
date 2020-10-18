<?php
declare(strict_types=1);

namespace frontend\modules\chareit\models;

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
     * @type int кол-во получение постов за один запрос
     */
    private $limit = 5;

    /**
     * @type int кол-во постов в топ-листе
     */
    private $top = 10;

    /**
     * Удаление поста. При удалении - удалить все данные о посте из редиса.
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function delete()
    {
        Yii::$app->redis->srem("post:{$this->id}:likes", $this->user_id);
        Yii::$app->redis->zrem('topz', $this->id);
        return parent::delete();
    }

    /**
     * Возвращает массив с инста-постами
     * @param int $start_page стартовый номер поиска постов (для асинхронной загрузки)
     * @return array|null массив с постами
     */
    public function getFeed(int $start_page = 0): ?array
    {
        $condition = Yii::$app->redis->smembers(Yii::$app->user->getId().Friends::FRIENDS);
        $condition[] = Yii::$app->user->getId();
        return $this->find()
            ->select(['post.user_id', 'post.id', 'post.description', 'post.created_at','post.filename', 'user.username', 'user.picture'])
            ->from('post')
            ->where(['user_id' => $condition])
            ->innerJoin('user', 'post.user_id = user.id')
            ->orderBy('created_at DESC')
            ->offset($start_page)
            ->limit($this->limit)
            ->asArray()
            ->all();
    }


    /**
     * Возвращает массив с инста-постами конкретного пользователя
     * @param int $start_page стартовый номер поиска постов (для асинхронной загрузки)
     * @param int $user_id если не указан, то вернуть посты свои и всех друзей
     * @return array|null массив с постами
     */
    public function getProfileFeed(int $user_id, int $start_page = 0): ?array
    {
        $condition = $user_id;
        return $this->find()
            ->select(['post.user_id', 'post.id', 'post.description', 'post.created_at','post.filename', 'user.username', 'user.picture'])
            ->from('post')
            ->where(['user_id' => $condition])
            ->innerJoin('user', 'post.user_id = user.id')
            ->orderBy('created_at DESC')
            ->offset($start_page)
            ->limit($this->limit)
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
     * Возвращает массив с постами, отсортированный по количеству лайков.
     * @return array|null массив с постами
     */
    public function getTopFeed(): ?array
    {
        $top = $this->top;
        $redis = Yii::$app->redis;
        $tops = $redis->zrevrangebyscore('topz', '+inf', '-inf', 'limit', 0, $top + 1);
        $posts = $this->find()
            ->select(['post.user_id', 'post.id', 'post.description', 'post.created_at','post.filename', 'user.username', 'user.picture'])
            ->from('post')
            ->where(['post.id' => $tops])
            ->innerJoin('user', 'post.user_id = user.id')
            ->limit($top)
            ->asArray()
            ->all();
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
}
