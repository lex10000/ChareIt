<?php
declare(strict_types = 1);
namespace frontend\modules\chareit\models;
use Throwable;
use Yii;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property string $comment
 * @property int $date
 */
class Comment extends ActiveRecord
{
    public function rules()
    {
        return [
            ['comment', 'required'],
            ['comment', 'string', 'min' => 1, 'max' => 255],
            ['comment', 'trim'],
        ];
    }

    public function __construct(int $post_id, $config = [])
    {
        $this->post_id = $post_id;
        $this->user_id = Yii::$app->user->getId();
        parent::__construct($config);
    }

    /**
     * Создание комментария к посту
     * @param string $comment
     * @return array|null результат выполнения
     */
    public function createComment(string $comment) :?array
    {
        $this->comment = $comment;
        if($this->validate('comment')) {
            $this->date = time();
            $this->save(false);
            return self::find()
                ->select(['user.username', 'user.picture', 'comment.id', 'comment.user_id', 'comment.post_id', 'comment.comment', 'comment.date'])
                ->from('comment')
                ->where(['comment.id' => $this->id])
                ->innerJoin('user', 'comment.user_id = user.id')
                ->limit(1)
                ->asArray()
                ->one();
        } else return null;
    }

    /**
     * Удаление комментария
     * @param int $comment_id
     * @return bool
     * @throws Throwable
     */
    public function deleteComment(int $comment_id) :bool
    {
        return self::findOne(['id' => $comment_id, 'post_id' => $this->post_id])->delete();
    }

    /**
     * Получить все комментарии к посту
     * @param int $offset
     * @return array|null
     */
    public function getComments($offset = 0) :?array
    {
        return self::find()
            ->select(['user.username', 'user.picture', 'comment.id', 'comment.user_id', 'comment.post_id', 'comment.comment', 'comment.date'])
            ->from('comment')
            ->where(['post_id' => $this->post_id])
            ->innerJoin('user', 'comment.user_id = user.id')
            ->limit(5)
            ->offset($offset)
            ->asArray()
            ->all();
    }

    /**
     * Кол-во комментариев у поста
     * @param int $post_id
     * @return int
     */
    public static function countComments(int $post_id) :int
    {
        return intval(self::find()->where(['post_id' => $post_id])->count());
    }
}