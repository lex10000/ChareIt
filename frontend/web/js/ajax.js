'use strict';
$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    $('.item__name').on('click', function () {
        const checklist_id = $(this).attr('data-target');
        $.ajax({
            dataType: 'json',
            headers: {
                'X-CSRF-Token': csrfToken,
            },
            type: 'POST',
            url: '/checklist/default/setup-checklist',
            data: {"checklist_id": checklist_id},

            beforeSend: function () {
                $('.preloader-wrapper').addClass('active');
            },
            success: function (data) {
                let items = '';
                data.checklist_options.forEach((item, i) => {
                    items += `<p>
                                <label>
                                    <input type="checkbox" value="1"/>
                                    <span>${item.name}</span>
                                    <a href="#!" class="delete_item" data-target="${item.id}"><i class="material-icons">clear</i></a>
                                </label>
                            </p>`;
                });
                $('.preloader-wrapper').removeClass('active');
                const text = `<form action="#" class="checklist-form" data-target="${checklist_id}">
                        ${items}
                      </form>
                      <input class="item-text" type="text" placeholder="введите название">                       
                       <button class="checklist-form-add btn">Добавить пункт</button>`;
                $('.main-field').html(text);
            }
        });
    });
    $('.main-field').on('click', '.delete_item', (e) => {
        const checklist_item = e.target.closest('.delete_item');
        $.ajax({
            dataType: 'json',
            headers: {
                'X-CSRF-Token': csrfToken,
            },
            type: 'POST',
            url: '/checklist/default/delete-checklist-item',
            data: {"checklist_item_id": checklist_item.getAttribute('data-target')},

            beforeSend: function () {
                $('.preloader-wrapper').addClass('active');
            },
            success: function (data) {
                if (data.status === 'success') {
                    $('.preloader-wrapper').removeClass('active');
                    checklist_item.closest('p').remove();
                }
            }
        });
    });
    $('.main-field').on('click', '.checklist-form-add', (e) => {

        const checklist_id = document.querySelector('.checklist-form').getAttribute('data-target');
        const itemName = $('.item-text').val();
        $.ajax({
            dataType: 'json',
            headers: {
                'X-CSRF-Token': csrfToken,
            },
            type: 'POST',
            url: '/checklist/default/add-checklist-item',
            data: {
                'checklist_id': checklist_id,
                'item_name': itemName,
                'item_required': true
            },

            beforeSend: function () {
                $('.preloader-wrapper').addClass('active');
            },
            success: function (data) {
                if (data.status === 'success') {
                    $('.preloader-wrapper').removeClass('active');
                    const newItem = `<p>
                                <label>
                                    <input type="checkbox" value="1"/>
                                    <span>${data.checklist_options.item_name}</span>
                                    <a href="#!" class="delete_item" data-target="${data.checklist_options.item_id}">
                                        <i class="material-icons">clear</i>
                                    </a>
                                </label>
                            </p>`;
                    const target = document.querySelector('.checklist-form');
                    target.insertAdjacentHTML('beforeend', newItem);
                }
            }
        });
    });
    $('.main-field').on('change', '.checklist-form', (e) => {
        const items = $('.checklist-form').find('input[type=checkbox]');
        const checked_items = $('.checklist-form').find('input[type=checkbox]:checked');

        if(items.length===checked_items.length) {
            const checklist_id = e.currentTarget.getAttribute('data-target');

            $.ajax({
                dataType: 'json',
                headers: {
                    'X-CSRF-Token': csrfToken,
                },
                type: 'POST',
                url: '/checklist/default/complete-checklist',
                data: {
                    'checklist_id': checklist_id,
                },

                beforeSend: function () {
                    $('.preloader-wrapper').addClass('active');
                },
                success: function (data) {
                    if (data.status === 'success') {
                        $('.preloader-wrapper').removeClass('active');
                        location.reload();
                    }
                }
            });
        }
    });
});