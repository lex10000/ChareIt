$(document).ready(function () {
    const $instaPosts = $('.insta_posts');

    let getPosts = function() {
        if($(this).scrollTop() >= $(document).height() - $(window).height()) {
            $(document).unbind('scroll', getPosts);
            const postCount = $instaPosts.children().length;
            $.get('/insta/default/feed', {'startPage': postCount}, (data) => {
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
});