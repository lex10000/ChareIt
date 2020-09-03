$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    const $instaPosts = $('.insta_posts');

    let getPosts = function () {
        if ($(this).scrollTop() >= $(document).height() - $(window).height() - 100) {
            $(document).unbind('scroll', getPosts);
            const postCount = $instaPosts.children().length;
            $.get('/insta/default/get-feed', {'startPage': postCount}, (data) => {
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
});