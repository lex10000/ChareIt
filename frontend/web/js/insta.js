$(document).ready(function () {
    $('.materialboxed').materialbox();

    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    const $instaPosts = $('.insta_posts');

    //получить посты, если свой профиль, то только свои посты, если лента, то получить все посты
    let getPosts = function () {
        if(location.pathname === '/insta/default/get-top') return;
        if ($(this).scrollTop() >= $(document).height() - $(window).height() - 1000) {
            $(document).unbind('scroll', getPosts);

            const postCount = $instaPosts.children().length;
            $.get(location.pathname, {'startPage': postCount}, (data) => {
                if (data) {
                    $instaPosts.append(data);
                    $('.materialboxed').materialbox();

                    $(document).on('scroll', getPosts);
                } else {
                    $instaPosts.append('Пока больше записей нет.');
                }
            })
        }
    }

    $(document).on('scroll', getPosts);
    let change = function (e, action, onIcon, offIcon) {
        const card = e.currentTarget.closest('.card');
        const instaPostId = card.getAttribute('data-target');
        $.ajax({
            url: '/insta/default/like',
            data: {'instaPostId': instaPostId, 'action': action},
            headers: {
                'X-CSRF-Token': csrfToken,
            },
            type: 'POST',
            success: function (data) {
                switch (data.status) {
                    case 'success': {
                        const heart = e.currentTarget.querySelector('.material-icons');
                        if (data.action === 'srem') heart.innerHTML = offIcon;
                        else if (data.action === 'sadd') heart.innerHTML = onIcon;
                        card.querySelector('.count_likes').innerHTML = data.countLikes + ' лайков';
                        break;
                    }
                }
            },
        });
    }
    //дизЛайк\андизлайк поста.
    $instaPosts.on('click', '.post_dislike_button', (e) => {
        change(e, 'dislike', 'thumb_up', 'thumb_down');
    });

    //Лайк\анлайк поста.
    $instaPosts.on('click', '.post_like_button', (e) => {
        change(e, 'like', 'favorite', 'favorite_border');
    });
    /**
     * Создание поста. Перехват submit`а формы, и отправка ее ajax`ом.
     */
    // $(document).on('beforeSubmit', '.create_post', function () {
    //     const $form = $(this);
    //     const href = $form.attr('action');
    //     $.ajax({
    //         type: "POST",
    //         url: href,
    //         data: new FormData(this),
    //         processData: false,
    //         contentType: false,
    //         beforeSend: () => {
    //             //здесь был прелоадер
    //         },
    //         success: (data) => {
    //             if (!data || data === 'not save') {
    //                 M.toast({html: 'Упс!! Произошла ошибка, команда лучших разработчиков уже разбирается с данной проблемой'});
    //                 return false;
    //             }
    //             $instaPosts.prepend(data);
    //             $('.materialboxed').materialbox();
    //             $('.modal').modal('close');
    //             M.toast({html: 'Пост добавлен!'});
    //             this.reset();
    //         }
    //     });
    //     return false;
    // });

    //получить свежие посты
    // let newPosts = function () {
    //     if (location.pathname === '/insta/get-feed') {
    //         let created_at = $instaPosts.children().first().find('.created_at').html();
    //         if (!created_at) created_at = new Date().getTime();
    //         $.get('/insta/default/get-new-posts', {'created_at': created_at}, (data) => {
    //             if (data) {
    //                 $instaPosts.prepend(data);
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
    $instaPosts.on('click', '.post_delete_button', (event) => {
        let instaPostId = event.currentTarget.getAttribute('data-target');
        $.ajax({
            url: '/insta/default/delete',
            data: {'instaPostId': instaPostId},
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
    //
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
        const friendId = e.currentTarget.closest('.user_card').getAttribute('data-target');
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
                        e.currentTarget.closest('.user_links').innerHTML = `<a href="#!" class="subscribe">Убрать из друзей</a>`;
                    } else if(data.action === 'reject') {
                        e.currentTarget.closest('.user_card').remove();
                    }
                } else {
                    'fail';
                }
            }
        });
    });

    $(document).on('click', '.subscribe', (e) => {
        const friendId = e.currentTarget.closest('.user_card').getAttribute('data-target');
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