'use strict';
import Checklist from '/js/Checklist.Class.js'

$(document).ready(function () {
    const preloader = document.querySelector('.preloader-wrapper');
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    const target = document.querySelector('.checklists');
    const props = {
        preloader: preloader,
        ajaxHeaders: csrfToken,
        domTarget: target
    }
    const $checklists = $('.checklists');

    //Создаем объект чек-лист
    let checklist = new Checklist(props);
    preloader.classList.add('active');

    $('.delete-all').on('click', () => {
        checklist.deleteAllChecklists();
    });

    $checklists.on('click', '.delete_item', (event) => {
        checklist.deleteChecklistItem(event.currentTarget);
    });

    //получаем данные чек-листов в json формате
    fetch('/checklist/default/get-all-checklists')
        .then(Response => Response.json())
        .then(data => {
            //если успех, то рендерим каждый чек-лист
            if (data.status === 'success') {
                data.checklists.forEach((item) => {
                    //рендерим чек-лист
                    checklist.renderChecklist(item);
                });

                //далее вешаем все необходимые обработчики, и инициализируем плагины из Materialize.css
                $('.checklists').on('click', '.delete_checklist', (event) => {
                    checklist.deleteChecklist(event.currentTarget);
                });
            } else if (data.status === 'empty') {
                $('.checklists').html('У вас еще нет ни одного чек-листа');
                $('.delete-all-modal').remove();
            }
            preloader.classList.remove('active');
        });

    $checklists.on('submit', '.add-checklist-item', (e) => {
        const name = e.originalEvent.target.elements.item_name.value;
        const checklist_id = e.target.closest('.checklist-form').getAttribute('data-target');

        //перехватим submit, и отменим его
        e.preventDefault();

        checklist.addItem(name, checklist_id);

        //Очистим форму
        e.target.reset();
    });
    const collapsibleElements = document.querySelectorAll('.collapsible');
    let instances = M.Collapsible.init(collapsibleElements, {});

    $('.modal').modal();
});