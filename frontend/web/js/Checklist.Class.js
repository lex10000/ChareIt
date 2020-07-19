
import ChecklistItem from '../js/ChecklistItem.Class.js'

export default class ChecklistClass {
    preloader = $('.preloader-wrapper');

    ajaxHeaders = {
        'X-CSRF-Token': $('meta[name="csrf-token"]').attr("content")
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
            beforeSend: () => this.preloader.addClass('active'),
            success: (data) => this.success(data)
        });
    }

    deleteChecklist(cl) {
        const url = '/checklist/default/delete-checklist';
        const props = {"checklist_id": this.checklist_id};

        this.success = function (data) {
            if (data.status === 'success') {
                this.preloader.removeClass('active');
                cl.remove();
            }
        }
        this.sendAjax(url, props);
    }

    renderChecklist(target)
    {
        let checklistTemplate = this.checklistTemplate();
        let cl = document.createElement('div');
        cl.insertAdjacentHTML('beforeend', checklistTemplate);
        target.insertAdjacentElement('beforeend', cl);

        this.afterRenderChecklist(cl);
    }

    afterRenderChecklist(cl)
    {
        cl.querySelector('.item__name').addEventListener('click', () => {
            this.getChecklistItems();
        });
        cl.querySelector('.delete_checklist').addEventListener('click',() => {
            this.deleteChecklist(cl);
        });
    }

    getChecklistItems() {
        const url = '/checklist/default/setup-checklist';
        const props = {"checklist_id": this.checklist_id};

        this.success = function (data) {
            const text = `<form action="#" class="checklist-form" data-target="${this.checklist_id}"></form>
                      <input class="item-text" type="text" placeholder="введите название">                       
                      <button class="checklist-form-add btn">Добавить пункт</button>`;
            $('.main-field').html(text);

            data.checklist_options.forEach((item, i) => {
                let checklistItem = new ChecklistItem(item);
                checklistItem.renderItem(document.querySelector('.checklist-form'));
            });
            $('.checklist-form-add').on('click', () => {
                this.addItem($('.item-text').val());
            })

            this.preloader.removeClass('active');

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
                const newItem = new ChecklistItem(data.checklist_options);
                const target = document.querySelector('.checklist-form');
                newItem.renderItem(target);
                $('.item-text').val(null);
                this.preloader.removeClass('active');
            }
        }
        this.sendAjax(url, props);
    }
}