'use strict';
import Checklist from '/js/Checklist.Class.js'

$(document).ready(function () {
    const preloader = document.querySelector('.preloader-wrapper');
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    $.ajax({
        type: 'get',
        url: '/checklist/default/get-all-checklists',
        beforeSend: () => preloader.classList.add('active'),
        success: (data) => {
            if (data.status === 'success') {
                const target = document.querySelector('.checklists');
                data.checklists.forEach((item) => {
                    let checklist = new Checklist(item);
                    checklist.renderChecklist(target);
                });
                $('.delete-all').on('click', () => {
                    Checklist.deleteAllChecklists(csrfToken);
                });
                $('.checklists').on('click', '.delete_item', (event) => {
                   Checklist.deleteChecklistItem(event.currentTarget);
                });

                } else if(data.status === 'empty') {
                $('.main-field').html('У вас еще нет ни одного чек-листа');
                $('.delete-all-modal').remove();
            }

            preloader.classList.remove('active');
        },
    });

    const collapsibleElements = document.querySelectorAll('.collapsible');
    let instances = M.Collapsible.init(collapsibleElements, {});

    $('.modal').modal();
});