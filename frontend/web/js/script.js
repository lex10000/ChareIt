'use strict';
$(document).ready(function () {
    $('body,html').scrollTop(0);
    $('.get_signup_form').on('click', () => {
        $.get('/user/default/sign-up', {}, (data) => {
            $('.welcome__auth-form').html(data);
        });
    });
    $(".anchor").click(function(){
        let href = $(this).attr("href");
        $("html, body").animate({scrollTop: $(href).offset().top+"px"});
        return false;
    });
});