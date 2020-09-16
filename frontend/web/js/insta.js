$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    const $instaPosts = $('.insta_posts');

    //получить посты, если свой профиль, то только свои посты, если лента, то получить все посты
    let getPosts = function () {
        if ($(this).scrollTop() >= $(document).height() - $(window).height() - 1000) {
            $(document).unbind('scroll', getPosts);

            const postCount = $instaPosts.children().length;
            $.get(location.pathname, {'startPage': postCount}, (data) => {
                if (data) {
                    $instaPosts.append(data);
                    $(document).on('scroll', getPosts);
                } else {
                    $instaPosts.append('Пока больше записей нет.');
                }
            })
        }
    }

    $(document).on('scroll', getPosts);

    //Лайк\анлайк поста.
    $instaPosts.on('click', '.post_like_button', (e) => {
        change(e, 'like', 'favorite', 'favorite_border');
    });

    let change = function(e, action, onIcon, offIcon) {
        const card = e.currentTarget.closest('.card');
        const instaPostId = card.getAttribute('data-target');

        $.ajax({
            url: '/insta/default/like',
            data: {'instaPostId': instaPostId, 'action' : action},
            headers: {
                'X-CSRF-Token': csrfToken,
            },
            type: 'POST',
            success: function (data) {
                switch (data.status) {
                    case 'success': {
                        const heart = e.currentTarget.querySelector('.material-icons');
                        if(data.action === 'srem') heart.innerHTML = offIcon;
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
        change(e, 'dislike',  'thumb_up','thumb_down');
    });

    $('.get_create_form').on('click', () => {
        $('.create_post').show();
    });

    /**
     * Создание поста. Перехват submit`а формы, и отправка ее ajax`ом.
     */
    $(document).on('beforeSubmit', '.create_post', function () {
        const $form = $(this);
        const href = $form.attr('action');
        $.ajax({
            type: "POST",
            url: href,
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: (data) => {
                $instaPosts.prepend(data);
                M.toast({html: 'Пост добавлен!'});
                this.reset();
                $form.hide();
            }
        });
        return false;
    });

    //получить свежие посты
    let newPosts = function () {
        if (location.pathname === '/insta/get-feed') {
            let created_at = $instaPosts.children().first().find('.created_at').html();
            if(!created_at) created_at = new Date().getTime();
            $.get('/insta/default/get-new-posts', {'created_at': created_at}, (data) => {
                if (data) {
                    $instaPosts.prepend(data);
                    sendNotification('Новый пост!', {
                        body: 'Посмотрите, кто то добавил интересный пост!',
                        dir: 'auto'
                    });
                } else {
                    console.log('nothing');
                }
            })
        }
    }
    setInterval(newPosts, 10000);

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

    let sendNotification = function (title, options) {
        if (("Notification" in window)) {
            switch (Notification.permission) {
                case "granted": {
                    new Notification(title, options);
                    break;
                }
                case "default": {
                    Notification.requestPermission()
                        .then((permission) => {sendNotification(title, options)});
                    break;
                }
            }
        }
    }
});