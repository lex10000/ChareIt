<?php
declare(strict_types = 1);
namespace frontend\modules\chareit\models;
use Yii;
use yii\base\Model;

class Comment extends Model
{
    public $post_id;
    public $user_id;

    public function __construct(int $post_id, $config = [])
    {
        $this->post_id = $post_id;
        $this->user_id = Yii::$app->user->getId();
        parent::__construct($config);
    }

    /**
     * Создание комментария к посту
     * @param string $comment
     * @return bool результат выполнения
     */
    public function createComment(string $comment) :bool
    {
        return (bool)
        Yii::$app->redis->hset("commentsPostId:$this->post_id", "user:$this->user_id", "comment:$comment");
    }

    /**
     * Удаление комментария
     * @param int $comment_id
     * @return bool
     */
    public function deleteComment(int $comment_id) :bool
    {
        return (bool)
        Yii::$app->redis->hdel("commentsPostId:$this->post_id", "user:$this->user_id", "comment:$this->comment");
    }

    /**
     * Получить все комментарии к посту
     * @return array|null
     */
    public function getComments() :?array
    {
        return Yii::$app->redis->hgetall("commentsPostId:$this->post_id");
    }
}