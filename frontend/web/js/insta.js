$(document).ready(function () {
    $('.materialboxed').materialbox();

    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    const $postCards = $('.postCards');

    /**
     * Получение ленты при скроллинге (ajax-пагинация)
     */
    let getPosts = function () {
        if(location.pathname.match(/\/get-feed/)) {
            if ($(this).scrollTop() >= $(document).height() - $(window).height() - 1000) {
                $(document).unbind('scroll', getPosts);
                const postCount = $postCards.children().length;
                $.get('/get-feed', {'startPage': postCount}, (data) => {
                    if (data) {
                        $postCards.append(data);
                        $('.materialboxed').materialbox();
                        $(document).on('scroll', getPosts);
                    } else {
                        $postCards.append('Пока больше записей нет.');
                    }
                })
            }
        }
    }
    $(document).on('scroll', getPosts);

    /**
     * Лайк\анлайк поста. Если статус fail, то вывод сообщения об ошибке
     */
    $(document).on('click', '.post_like_button', (e) => {
        const card = e.currentTarget.closest('.card');
        const postId = card.getAttribute('data-target');
        $.ajax({
            url: '/insta/default/like',
            data: {'postId': postId},
            headers: {
                'X-CSRF-Token': csrfToken,
            },
            type: 'POST',
            success: function (data) {
                switch (data.status) {
                    case 'success': {
                        const heart = e.currentTarget.querySelector('.material-icons');
                        if (data.action === 'srem') heart.innerHTML = 'favorite_border';
                        else if (data.action === 'sadd') heart.innerHTML = 'favorite';
                        card.querySelector('.count_likes').innerHTML = data.countLikes + ' лайков';
                        break;
                    }
                    case 'fail': {
                        M.toast({html: data.message});
                        break;
                    }
                }
            },
        });
    });

    //получить свежие посты
    // let newPosts = function () {
    //     if (location.pathname === '/get-feed') {
    //         $.get('/insta/default/get-new-posts', (data) => {
    //             if (data) {
    //                 $postCards.prepend(`<a href="" class="posts__get-new">Получить свежие посты</a> `);
    //                 sendNotification('Новый пост!', {
    //                     body: 'Посмотрите, кто то добавил интересный пост!',
    //                     dir: 'auto'
    //                 });
    //             } else {
    //                 console.log('nothing');
    //             }
    //         })
    //     }
    // }
    // setInterval(newPosts, 20000);

    /**
     * Удаление поста
     */
    $(document).on('click', '.post_delete_button', (event) => {
        console.log(234);
        let postId = event.currentTarget.getAttribute('data-target');
        $.ajax({
            url: '/insta/default/delete',
            data: {'instaPostId': postId},
            headers: {
                'X-CSRF-Token': csrfToken,
            },
            type: 'POST',
            success: function (data) {
                switch (data.status) {
                    case 'success': {
                        event.currentTarget.closest('.card').remove();
                        M.toast({html: 'Пост удален!'});
                        break;
                    }
                    case 'access fail': {
                        M.toast({html: 'Ошибка доступа'});
                        break;
                    }
                    default: {
                        M.toast({html: 'Упс, произошла ошибка, команда лучших разработчиков уже работает над этим'});
                    }
                }
            },
        });
    });

    // let sendNotification = function (title, options) {
    //     if (("Notification" in window)) {
    //         switch (Notification.permission) {
    //             case "granted": {
    //                 new Notification(title, options);
    //                 break;
    //             }
    //             case "default": {
    //                 Notification.requestPermission()
    //                     .then((permission) => {
    //                         sendNotification(title, options)
    //                     });
    //                 break;
    //             }
    //         }
    //     }
    // }

    $('.modal').modal();
    $('#delete-user-form').on('beforeSubmit', () => {
        if(!confirm('Вы точно уверены, что хотите удалить аккаунт?')) {
            return false;
        }
    })
    $(document).on('click', '.confirmRequest', (e) => {
        const friendId = e.currentTarget.closest('.friend-card').getAttribute('data-target');
        const status = e.currentTarget.getAttribute('data-target');
        $.ajax({
            url: '/insta/friends/confirm-request',
            data: {'friendId': friendId, 'status': status},
            headers: {
                'X-CSRF-Token': csrfToken,
            },
            type: 'POST',
            success: function (data) {
                if(data.status === 'success') {
                    if(data.action === 'confirm') {
                        e.currentTarget.closest('.friend-card__links').innerHTML = `<a href="#!" class="subscribe btn purple">Убрать из друзей</a>`;
                    } else if(data.action === 'reject') {
                        e.currentTarget.closest('.friend-card').remove();
                    }
                } else {
                    'fail';
                }
            }
        });
    });
    $(document).ready(function(){
        $('.fixed-action-btn').floatingActionButton();
    });
    $(document).on('click', '.subscribe', (e) => {
        const friendId = e.currentTarget.closest('.friend-card').getAttribute('data-target');
        $.ajax({
            url: '/insta/friends/change-subscribe-status',
            data: {'friendId': friendId},
            headers: {
                'X-CSRF-Token': csrfToken,
            },
            type: 'POST',
            success: function (data) {
                if(data.action === 'remove') {
                    M.toast({html: 'Пользователь удален из друзей'});
                    e.currentTarget.innerHTML = 'Добавить в друзья';
                } else if(data.action === 'await') {
                    M.toast({html: 'Запрос отправлен'});
                    e.currentTarget.innerHTML = 'Отменить запрос';
                } else if(data.action === 'cancel') {
                    M.toast({html: 'Вы отменили запрос'});
                    e.currentTarget.innerHTML = 'Добавить в друзья';
                }
            }
        });
    });
});