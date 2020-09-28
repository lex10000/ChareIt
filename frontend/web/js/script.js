'use strict';
$(document).ready(function () {
    $('.get_signup_form').on('click', () => {
        $.get('/user/default/sign-up', {}, (data) => {
            $('.welcome__auth-form').html(data);
        });
    });
    $(".anchor").click(function(){
        let _href = $(this).attr("href");
        $("html, body").animate({scrollTop: $(_href).offset().top+"px"});
        return false;
    });
});