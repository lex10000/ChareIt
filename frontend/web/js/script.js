'use strict';
$(document).ready(function () {
    $('.like').on('click', function () {
        const id = $(this).attr('data-id');
        if($(this).hasClass('posts__card__btn-like')) {
            const action = 'like';
            $.post('/post/default/like', {id, action}, (data) => {
                if (data.success) {
                    $(this).html('favorite');
                    $(this).siblings('.posts__likes').html(data.countLikes);
                    $(this).removeClass('posts__card__btn-like');
                    $(this).addClass('posts__card__btn-dislike');
                }
            });
        } else if($(this).hasClass('posts__card__btn-dislike')) {
            const action = 'dislike';
            $.post('/post/default/like', {id, action}, (data) => {
                if (data.success) {
                    $(this).siblings('.posts__likes').html(data.countLikes);
                    $(this).html('favorite_border');
                    $(this).removeClass('posts__card__btn-dislike');
                    $(this).addClass('posts__card__btn-like');
                }
            });
        }
    });
    $(document).on('pjax:success', function() {
        fadeIn();
    });
});

let fadeIn = function () {
    $( ".welcome__login-auth" ).fadeIn( "slow", function() {
        // Анимация завершена.
    });
}