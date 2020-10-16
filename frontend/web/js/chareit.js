document.addEventListener("DOMContentLoaded", () => {
    const el = document.querySelector('#friends-tabs');
    const options = {};
    M.Tabs.init(el, options);
});
$(document).ready(function () {
    $('.materialboxed').materialbox();
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
});