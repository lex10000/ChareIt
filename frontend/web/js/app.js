'use strict';
import Checklist from '../js/Checklist.Class.js'

$(document).ready(function () {
    const $preloader = $('.preloader-wrapper');
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    let checklists = [];
    $.ajax({
        dataType: 'json',
        headers: {
            'X-CSRF-Token': csrfToken
        },
        type: 'POST',
        url: '/checklist/default/get-all-checklists',
        beforeSend: () => $preloader.addClass('active'),
        success: (data) => {
            checklists = data.checklists;
            const target = document.querySelector('.checklists');
            checklists.forEach((item)=>{
                let checklist = new Checklist(item);
                checklist.renderChecklist(target);
            })
            $preloader.removeClass('active');
        }
    });
});