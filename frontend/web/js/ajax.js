'use strict';
$(document).ready(function () {

    const $preloader = $('.preloader-wrapper');
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    class Checklist {

        ajaxHeaders = {
            'X-CSRF-Token': csrfToken,
        }

        itemTemplate(checklist_options)
        {
            return `<p>
                       <label>
                            <input type="checkbox" value="1"/>
                            <span>${checklist_options.name}</span>
                            <a href="#!" class="delete_item" data-target="${checklist_options.id}">
                                <i class="material-icons">clear</i>
                            </a>
                        </label>
                    </p>`;
        }

        checklistTemplate()
        {
            return `<div class="item">
                        <a href="#!" data-target="${this.checklist_id}" class="item__name">${this.checklist_name}</a>
                        <div class="item__created_at">${this.checklist_createdAt}</div>
                        <div class="item__updated_at">${this.checklist_updatedAt}</div>
                        <div class="item__status">${this.checklist_status}</div>
                        <div class="item__delete">
                            <a href="#!" class="delete_checklist" data-target="${this.checklist_id}"><i class="material-icons">clear</i></a>
                        </div>
                    </div>`;
        }
        constructor(props) {
            this.checklist_id = props.id;
            this.checklist_name = props.name;
            this.checklist_createdAt = props.created_at;
            this.checklist_updatedAt = props.updated_at;
            this.checklist_status = props.status;
        }

        sendAjax(url, props) {
            $.ajax({
                dataType: 'json',
                headers: this.ajaxHeaders,
                type: 'POST',
                url: url,
                data: props,
                beforeSend: () => $preloader.addClass('active'),
                success: (data) => this.success(data)
            });
        }

        deleteChecklist(checklist) {
            const url = '/checklist/default/delete-checklist';
            const props = {"checklist_id": this.checklist_id};

            this.success = function (data) {
                if (data.status === 'success') {
                    $preloader.removeClass('active');
                    checklist.remove();
                }
            }
            this.sendAjax(url, props);
        }

        renderChecklist(target)
        {
            let checklistTemplate = this.checklistTemplate()
            target.insertAdjacentHTML('beforeend', checklistTemplate);

            this.afterRenderChecklist();
        }
        afterRenderChecklist()
        {
            $('.item__name').on('click', () => {
                this.getChecklistItems();
            });
        }
        getChecklistItems() {
            const url = '/checklist/default/setup-checklist';
            const props = {"checklist_id": this.checklist_id};

            this.success = function (data) {
                let items = '';
                data.checklist_options.forEach((item, i) => {
                    items += this.itemTemplate(item);
                });
                $preloader.removeClass('active');
                const text = `<form action="#" class="checklist-form" data-target="${this.checklist_id}">
                        ${items}
                      </form>
                      <input class="item-text" type="text" placeholder="введите название">                       
                       <button class="checklist-form-add btn">Добавить пункт</button>`;
                $('.main-field').html(text);
            }
            this.sendAjax(url, props);
        }
        deleteItem(checklist_item) {
            const url = '/checklist/default/delete-checklist-item';
            const props = {"checklist_item_id": checklist_item.getAttribute('data-target')};

            this.success = function (data) {
                if (data.status === 'success') {
                    $preloader.removeClass('active');
                    checklist_item.closest('p').remove();
                }
            }
            this.sendAjax(url, props);
        }

        addItem(itemName) {
            const url = '/checklist/default/add-checklist-item';
            const props = {
                'checklist_id': this.checklist_id,
                'item_name': itemName,
                'item_required': true
            };
            this.success = function (data) {
                if (data.status === 'success') {
                    $preloader.removeClass('active');
                    const newItem = this.itemTemplate(data.checklist_options);
                    const target = document.querySelector('.checklist-form');
                    target.insertAdjacentHTML('beforeend', newItem);
                }
            }
            this.sendAjax(url, props);
        }


    }

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
                $preloader.removeClass('active');
            })
        }
    });

    // $('.item__name').on('click', function () {
    //     console.log(123123);
    //     const checklist_id = $(this).attr('data-target');
    //     let CheckList = new Checklist(checklist_id);
    //     CheckList.getChecklistItems();
    // });

    $('.main-field').on('click', '.delete_item', (e) => {
        const checklist_id = document.querySelector('.checklist-form').getAttribute('data-target');
        const checklist_item = e.target.closest('.delete_item');
        let CheckList = new Checklist(checklist_id);
        CheckList.deleteItem(checklist_item);
    });

    $('.main-field').on('click', '.checklist-form-add', (e) => {
        const $itemName = $('.item-text');
        const checklist_id = document.querySelector('.checklist-form').getAttribute('data-target');
        let CheckList = new Checklist(checklist_id);
        CheckList.addItem($itemName.val());
        $itemName.val(null);
    });

    $('.checklists').on('click', '.delete_checklist', (e) => {
        const checklist_id = e.currentTarget.getAttribute('data-target');
        let CheckList = new Checklist(checklist_id);
        CheckList.deleteChecklist(e.currentTarget.closest('.item'));
    });
});