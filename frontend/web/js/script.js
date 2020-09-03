'use strict';
$(document).ready(function () {
    $('.get_signup_form').on('click', () => {
        $.get('/user/default/sign-up', {}, (data) => {
            $('.welcome__auth-form').html(data);
        });
    });
});
