<?php
declare(strict_types = 1);
namespace frontend\modules\chareit\models;
use Yii;
use yii\base\Model;
use frontend\modules\chareit\models\FriendsInterface;
use yii\db\Query;

class Friends extends Model implements FriendsInterface
{
    private $user_id;

    /** @var string подтверждение заявки в друзья*/
    const STATUS_CONFIRM = 'confirm';

    /** @var string отклонение заявки в друзья*/
    const STATUS_REJECT = 'reject';

    /** @var string удаление из друзей*/
    const STATUS_REMOVE = 'remove';

    /** @var string ожидание подтверждение заявки в друзья*/
    const STATUS_AWAIT = 'await';

    /** @var string отмена заявки в друзья */
    const STATUS_CANCEL = 'cancel';

    /** @var string индекс для входящих заявок */
    const INCOMING_REQUESTS = ':subscriber_incoming';

    /** @var string индекс для исходящих заявок */
    const OUTGOING_REQUESTS = ':subscriber_outgoing';

    /** @var string индекс для друзей */
    const FRIENDS = ':subscriber_friend';

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
        parent::__construct();
    }

    /**
     * Проверяет, находится ли пользователь в списке
     * @param int $friend_id id друга
     * @param string $index_list название списка (см. константы - индексы)
     * @return bool результат
     */
    public static function isUserIn(int $friend_id, string $index_list): bool
    {
        return (bool) Yii::$app
            ->redis
            ->sismember(Yii::$app->user->getId().$index_list, $friend_id);
    }

    /**
     * Меняет статус пользователя в зависимости от первоначального состояния (если пользователь еще не в друзьях,
     * то ставит его в статус "ожидает подтверждения", либо удаляет из друзей
     * @param int $friend_id id друга
     * @return mixed|void
     */
    public function changeSubscribeStatus(int $friend_id) :string
    {
        if(self::isUserIn($friend_id, self::FRIENDS)) {
            Yii::$app->redis->srem($this->user_id.self::FRIENDS, $friend_id);
            Yii::$app->redis->srem($friend_id.self::FRIENDS, $this->user_id);
            return self::STATUS_REMOVE;
        } elseif (self::isUserIn($friend_id, self::OUTGOING_REQUESTS)) {
            Yii::$app->redis->srem($this->user_id.self::OUTGOING_REQUESTS, $friend_id);
            Yii::$app->redis->srem($friend_id.self::INCOMING_REQUESTS, $this->user_id);
            return self::STATUS_CANCEL;
        } else {
            Yii::$app->redis->sadd($this->user_id.self::OUTGOING_REQUESTS, $friend_id);
            Yii::$app->redis->sadd($friend_id.self::INCOMING_REQUESTS, $this->user_id);
            return self::STATUS_AWAIT;
        }
    }

    /**
     * Подтверждение, либо отклонение заявки в друзья
     * @param int $friend_id id друга
     * @param bool $status статус подтверждения
     * @return string статус выполнения
     */
    public function confirmRequest(int $friend_id, bool $status) : string
    {
        Yii::$app->redis->srem($this->user_id.self::INCOMING_REQUESTS, $friend_id);
        Yii::$app->redis->srem($friend_id.self::OUTGOING_REQUESTS, $this->user_id);
        if($status) {
            Yii::$app->redis->sadd($this->user_id.self::FRIENDS, $friend_id);
            Yii::$app->redis->sadd($friend_id.self::FRIENDS, $this->user_id);
            return self::STATUS_CONFIRM;
        }
        return self::STATUS_REJECT;
    }

    /**
     * Получить массив с пользователями из списка, согласно переданного индекса
     * @param string $index_list название списка (см. константы - индексы)
     * @return array
     */
    public function getFriends($index_list) :array
    {
        $friends_ids = Yii::$app->redis->smembers(Yii::$app->user->getId().$index_list);
        return (new Query())
            ->select(['id', 'username', 'picture', 'about'])
            ->from('user')
            ->where(['id' => $friends_ids])
            ->all();
    }

    /**
     * Получить количество входящих заявок в друзья
     * @return int
     */
    public static function getFriendsRequestCount() :int
    {
        return intval(Yii::$app
            ->redis
            ->scard(Yii::$app->user->getId().self::INCOMING_REQUESTS));
    }
}