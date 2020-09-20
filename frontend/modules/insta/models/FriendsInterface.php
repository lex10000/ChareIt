<?php
namespace frontend\modules\insta\models;

interface FriendsInterface
{
    /**
     * Меняет статус в зависимости от первоначального состояния (если пользователь еще не в друзьях, то добавляет его,
     * иначе удаляет из друзей
     * @param int $friend_id id друга
     * @return string название метода (add\remove)
     */
   public function changeSubscribeStatus(int $friend_id) : string;

    /**
     * Является ли пользователем другом
     * @param int $friend_id id друга
     * @return bool результат
     */
    public function isSubscriber(int $friend_id) : bool;

    /**
     * Получить всех друзей пользователя
     * @return array массив id`шников друзей
     */
    public function getAllFriends() :array;
}