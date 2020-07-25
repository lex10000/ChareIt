'use strict';
import Checklist from '../js/Checklist.Class.js'

$(document).ready(function () {
    const preloader = document.querySelector('.preloader-wrapper');
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    let checklists = [];
    let startInit = () => {
        $.ajax({
            dataType: 'json',
            headers: {
                'X-CSRF-Token': csrfToken
            },
            type: 'POST',
            url: '/checklist/default/get-all-checklists',
            beforeSend: () => preloader.classList.add('active'),
            success: (data) => {
                checklists = data.checklists;
                const target = document.querySelector('.checklists');
                target.innerHTML = null;
                checklists.forEach((item)=>{
                    let checklist = new Checklist(item);
                    checklist.renderChecklist(target);
                })
                preloader.classList.remove('active');
            }
        });
    }
    startInit();

    $(document).on('submit', '.forma1', (e) => {
        setTimeout(() => {
            startInit();
        }, 300)
        M.toast({html: 'Чек-лист успешно добавлен!'});
    });

});