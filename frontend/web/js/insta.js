$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    const $instaPosts = $('.insta_posts');

    //получить посты, если свой профиль, то только свои посты, если лента, то получить все посты
    let getPosts = function () {
        if ($(this).scrollTop() >= $(document).height() - $(window).height() - 100) {
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

    $instaPosts.on('click', '.post_like_button', () => {
        const instaPostId = 2;
        $.ajax({
            url: '/insta/default/like',
            data: {'instaPostId': instaPostId},
            headers: {
                'X-CSRF-Token': csrfToken,
            },
            type: 'POST',
            success: function (data) {
                console.log(data);
            },
        });
    });


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
                        break;
                    }
                }
            },
        });
    });
});