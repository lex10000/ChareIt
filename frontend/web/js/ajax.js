'use strict';
$(document).ready(function () {

    const $preloader = $('.preloader-wrapper');
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    class Checklist {

        itemTemplate(checklist_options) {
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

        constructor(checklist_id) {
            this.checklist_id = checklist_id;
            this.ajaxHeaders = {
                'X-CSRF-Token': csrfToken,
            }
        }


        beforeSendAjax() {
            $preloader.addClass('active');
        }

        sendAjax(url, props) {
            $.ajax({
                dataType: 'json',
                headers: this.ajaxHeaders,
                type: 'POST',
                url: url,
                data: props,
                beforeSend: () => this.beforeSendAjax(),
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
    }


    $('.item__name').on('click', function () {
        const checklist_id = $(this).attr('data-target');

        let CheckList = new Checklist(checklist_id);
        CheckList.getChecklistItems();
    });

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