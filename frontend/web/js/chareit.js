document.addEventListener("DOMContentLoaded", () => {
    const el = document.querySelector('#friends-tabs');
    const options = {};
    M.Tabs.init(el, options);
});
$(document).ready(function () {
    $('.materialboxed').materialbox();

    const csrfToken = $('meta[name="csrf-token"]').attr("content");
    const $postCards = $('.main-page');

    /**
     * Получение ленты при скроллинге (ajax-пагинация)
     */
    let getPosts = function () {
        if (location.pathname.match(/\/get-feed/) || location.pathname.match(/\/profile\/\d+/)) {
            if ($(this).scrollTop() >= $(document).height() - $(window).height() - 1000) {
                $(document).unbind('scroll', getPosts);
                const postCard = document.querySelector('.main-page');
                const postCount = postCard.querySelectorAll('.post-card').length;
                $.get(location.pathname, {'startPage': postCount}, (data) => {
                    if (data) {
                        $postCards.append(data);
                        $('.materialboxed').materialbox();
                        $(document).on('scroll', getPosts);
                    }
                })
            }
        }
    }
    $(document).on('scroll', getPosts);

    /**
     * Лайк\анлайк поста. Если статус fail, то вывод сообщения об ошибке/
     */
    $(document).on('click', '.post_like_button', (e) => {
        const card = e.currentTarget.closest('.card');
        const postId = card.getAttribute('data-target');
        const target = card.querySelector('.liked-users__container');

        $.ajax({
            url: '/chareit/default/like',
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
                        card.querySelector('.likes-counter').innerHTML = data.countLikes;
                        showLikedUser(target, postId, data.users);
                        break;
                    }
                    case 'exceeded' || 'fail': {
                        M.toast({html: data.message});
                        break;
                    }
                }
            },
        });
        return false;

    });

    // let checkNewPosts = function () {
    //     $.get('/chareit/default/check-new-posts', (data) => {
    //         if (data.status == true) {
    //             $('.insta_main_page').prepend(`<a href="" class="posts__get-new">Получить свежие посты</a> `);
    //         } else {
    //             console.log('nothing');
    //         }
    //     })
    // }
    // if(location.pathname === '/get-feed') {
    //     setInterval(checkNewPosts, 10000);
    // }

    /**
     * Удаление поста
     */
    $(document).on('click', '.post_delete_button', (event) => {
        console.log(234);
        let postId = event.currentTarget.getAttribute('data-target');
        $.ajax({
            url: '/chareit/default/delete',
            data: {'postId': postId},
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
        if (!confirm('Вы точно уверены, что хотите удалить аккаунт?')) {
            return false;
        }
    })
    $(document).on('click', '.confirmRequest', (e) => {
        const friendId = e.currentTarget.closest('.profile-card').getAttribute('data-target');
        const status = e.currentTarget.getAttribute('data-target');
        $.ajax({
            url: '/chareit/friends/confirm-request',
            data: {'friendId': friendId, 'status': status},
            headers: {
                'X-CSRF-Token': csrfToken,
            },
            type: 'POST',
            success: function (data) {
                if (data.status === 'success') {
                    if (data.action === 'confirm') {
                        e.currentTarget.closest('.friend-card__links').innerHTML = `<a href="#!" class="subscribe btn purple">Убрать из друзей</a>`;
                    } else if (data.action === 'reject') {
                        e.currentTarget.closest('.friend-card').remove();
                    }
                } else {
                    M.toast({html: 'Упс, произошла ошибка, команда лучших разработчиков уже работает над этим'});
                }
            }
        });
    });
    $(document).ready(function () {
        $('.fixed-action-btn').floatingActionButton();
    });
    $(document).on('click', '.subscribe', (e) => {
        const friendId = e.currentTarget.closest('.profile-card').getAttribute('data-target');
        $.ajax({
            url: '/chareit/friends/change-subscribe-status',
            data: {'friendId': friendId},
            headers: {
                'X-CSRF-Token': csrfToken,
            },
            type: 'POST',
            success: function (data) {
                if (data.action === 'remove') {
                    M.toast({html: 'Пользователь удален из друзей'});
                    e.currentTarget.innerHTML = 'Добавить в друзья';
                } else if (data.action === 'await') {
                    M.toast({html: 'Запрос отправлен'});
                    e.currentTarget.innerHTML = 'Отменить запрос';
                } else if (data.action === 'cancel') {
                    M.toast({html: 'Вы отменили запрос'});
                    e.currentTarget.innerHTML = 'Добавить в друзья';
                } else if(data.action === 'self-subscribe') {
                    M.toast({html: 'Нельзя подписаться на самого себя.'});
                }
            }
        });
    });
    $(document).on('keyup', '.friends-search__field', (e) => {
        if (e.target.value.length <= 1) {
            $('.friends').css({'display': 'block'});
            $('.friends-search__result').html('');
        }
        if (e.target.value.length > 2) {
            $('.friends-search__form').submit();
        }
    });
    $('.friends-search__form').on('beforeSubmit', function () {
        $('.friends').css({'display': 'none'});
        $.ajax({
            url: '/chareit/friends/search-friends',
            data: $(this).serializeArray(),
            type: 'POST',
            success: (data) => {
                $('.friends-search__result').html(data);
            }
        });
        return false;
    });
    $(document).on('mouseenter', '.post_like_button', function (e) {
        const postCard = e.target.closest('.post-card');
        const postId = postCard.getAttribute('data-target');
        const target = postCard.querySelector('.liked-users__container');
        const userCards = target.querySelector('.liked-users__cards');
        if(userCards.children.length === 0) {
            fetch('/chareit/default/get-liked-users', {
                'method': 'POST',
                'headers': {'X-CSRF-Token': csrfToken, 'Content-Type': 'application/x-www-form-urlencoded'},
                'body': 'postId=' + postId
            })
                .then(Response => Response.json())
                .then((data) => {
                    showLikedUser(target, postId, data.users);
                });
        } else {
            showLikedUser(target, postId);
        }

        postCard.addEventListener('mouseleave', () => {
            target.style.opacity = '0';
        });
        document.addEventListener('scroll', () => {
            target.style.opacity = '0';
        });
        return false;
    });
    let showLikedUser = (target, postId, users = undefined) => {
        const userCards = target.querySelector('.liked-users__cards');
        const header = target.querySelector('.liked-users__header');
        if(users) {
            if (users.length > 0) {
                if (users.length > 4) {
                    header.innerHTML = `<a href="/liked-users/${postId}">Понравилось 4+ людям:</a>`;
                } else {
                    header.innerHTML = `<a href="/liked-users/${postId}">Понравилось ${users.length} людям:</a>`;
                }
                userCards.innerHTML = '';

                for (let i = 0; i<users.length; i++) {
                    if(i===4) break;
                    const div = document.createElement('div');
                    div.classList.add('liked-users__card');
                    div.innerHTML = `<a class="liked-users__avatar circle" href="/profile/${users[i].id}" title="${users[i].username}"><img src="${users[i].picture}" alt="здесь была аватарка.."></a>`;
                    userCards.insertAdjacentElement('beforeend', div);
                }
            } else {
                header.innerHTML = 'Пока что никто не лайкал пост:(';
                userCards.innerHTML = '';
            }
        }
        target.style.opacity = '1';
        target.addEventListener('mouseleave', () => {
            target.style.opacity = '0';
        });
        document.addEventListener('scroll', () => {
            target.style.opacity = '0';
        });
        target.querySelector('.liked-users__close').addEventListener('click', () => {
            target.style.opacity = '0';
            return false;
        })
    }
});