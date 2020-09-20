<?php
declare(strict_types = 1);
namespace frontend\modules\insta\models;
use Yii;
use yii\base\Model;
use frontend\modules\insta\models\FriendsInterface;
use yii\db\Query;

class Friends extends Model implements FriendsInterface
{
    private $redis;
    private $user_id;
    private $user_index;

    public function __construct(int $user_id)
    {
        $this->redis = Yii::$app->redis;
        $this->user_id = $user_id;
        $this->user_index = "$this->user_id:subscribers";
        parent::__construct();
    }

    /**
     * Является ли пользователем другом
     * @param int $friend_id id друга
     * @return bool результат
     */
    public function isSubscriber(int $friend_id): bool
    {
        return (bool) $this->redis->sismember($this->user_index, $friend_id);
    }

    /**
     * Меняет статус в зависимости от первоначального состояния (если пользователь еще не в друзьях, то добавляет его,
     * иначе удаляет из друзей
     * @param int $friend_id id друга
     * @return mixed|void
     */
    public function changeSubscribeStatus(int $friend_id) :string
    {
        if($this->isSubscriber($friend_id)) {
            $this->redis->srem($this->user_index, $friend_id);
            $this->redis->srem($friend_id.':subscribers', $this->user_id);
            return 'remove';
        } else {
            $this->redis->sadd($this->user_index, $friend_id);
            $this->redis->sadd($friend_id.':subscribers', $this->user_id);
            return 'add';
        }
    }

    /**
     * Получить всех друзей пользователя
     * @return array массив id`шников друзей
     */
    public function getAllFriends(): array
    {
        $friends_ids = self::getAllFriendsIds();
        return (new Query())
            ->select(['id', 'username', 'picture'])
            ->from('user')
            ->where(['id' => $friends_ids])
            ->all();
    }

    /**
     * Вернуть id подписчиков всех
     * @return array|null
     */
    public static function getAllFriendsIds() : ?array
    {
        return Yii::$app->redis->smembers(Yii::$app->user->getId().':subscribers');
    }
}