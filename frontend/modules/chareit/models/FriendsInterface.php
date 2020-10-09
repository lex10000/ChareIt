<?php
namespace frontend\modules\chareit\models;

interface FriendsInterface
{
    /**
     * Меняет статус пользователя
     * @param int $friend_id id друга
     * @return string новый статус пользователя
     */
   public function changeSubscribeStatus(int $friend_id) : string;

    /**
     * Получить всех друзей пользователя
     * @param string $index_list список нужных юзеров (входящие заявки, исходящие заявки, друзья пользователя)
     * @return array массив id`шников друзей
     */
    public function getFriends($index_list) :array;
}