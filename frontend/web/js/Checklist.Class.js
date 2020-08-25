export default class ChecklistClass {

    MIN_ITEM_LENGTH = 3;
    MAX_ITEM_COUNT = 50;

    preloader = document.querySelector('.preloader-wrapper');

    ajaxHeaders = {
        'X-CSRF-Token': $('meta[name="csrf-token"]').attr("content")
    }

    checklistTemplate() {
        return `<div class="collapsible-header item">
                    <a href="#!" data-target="${this.checklist_id}" class="item__name">${this.checklist_name}</a>
                    <div class="item__delete">
                        <a href="#!" class="delete_checklist" data-target="${this.checklist_id}"><i class="material-icons">clear</i></a>
                    </div>
                </div>
                <div class="collapsible-body">
                    
                </div>`;
    }

    constructor(props) {
        this.checklist_id = props.id;
        this.checklist_name = props.name;
        this.checklist_createdAt = props.created_at;
        this.checklist_updatedAt = props.updated_at;
        this.checklist_status = props.status;
    }

    sendAjax(url, props, dataType = 'json') {
        $.ajax({
            dataType: dataType,
            headers: this.ajaxHeaders,
            type: 'POST',
            url: url,
            data: props,
            beforeSend: () => this.preloader.classList.add('active'),
            success: (data) => this.success(data)
        });
    }

    renderChecklist(target) {
        const checklistTemplate = this.checklistTemplate();

        let checklist = document.createElement('li');
        checklist.insertAdjacentHTML('beforeend', checklistTemplate);
        target.insertAdjacentElement('beforeend', checklist);

        checklist.querySelector('.delete_checklist').addEventListener('click', () => {
            this.deleteChecklist(checklist);
        });

        this.getChecklistItems(checklist);
        $(checklist).on('submit', '.add-checklist-item', (event) => {
            event.preventDefault();
            this.addItem(event.target.elements.item_name.value);

        });
    }

    /**
     * Удаляет выбранный чек-лист
     */
    deleteChecklist(cl) {
        const url = '/checklist/default/delete-checklist';
        const props = {"checklist_id": this.checklist_id};

        this.success = function (data) {
            if (data.status === 'success') {
                this.preloader.classList.remove('active');
                cl.remove();
                M.toast({html: data.message});
            }
        }
        this.sendAjax(url, props);
    }

    renderAddItemForm() {
        return `<form action="#" class="add-checklist-item">
                    <input class="item-text" type="text" name="item_name" placeholder="введите название">
                    <input type="submit" class="btn add-checklist-item" value="Добавить пункт">
                </form>`;
    }

    /**
     * Получает пункты чек-листа, а так же вешает обработчик на удаление пунктов
     * @param el
     */
    getChecklistItems(el) {
        const url = '/checklist/default/setup-checklist';
        const props = {"checklist_id": this.checklist_id};
        const dataType = 'text';
        this.success = function (data) {
            if (data === 'empty') {
                el.querySelector('.collapsible-body').insertAdjacentHTML('afterbegin', 'Пустой чек-лист'
                    + this.renderAddItemForm());
                this.preloader.classList.remove('active');
            } else {
                el.querySelector('.collapsible-body').insertAdjacentHTML('afterbegin', data
                    + this.renderAddItemForm());
                document.querySelector('.delete_item').addEventListener('click', (event) => {
                    let checklist_item_id = event.currentTarget.getAttribute('data-target');

                    const url = '/checklist/default/delete-checklist-item';
                    const props = {"checklist_item_id": checklist_item_id};

                    this.success = function () {
                        M.toast({html: 'Пункт удален'});
                        this.preloader.classList.remove('active');
                        event.target.closest('label').remove();
                    }

                    this.sendAjax(url, props);
                });
                this.preloader.classList.remove('active');
            }
        }
        this.sendAjax(url, props, dataType);
    }

    addItem(itemName) {
        if (itemName.length <= this.MIN_ITEM_LENGTH) {
            M.toast({html: 'Длина пункта должна быть больше ' + this.MIN_ITEM_LENGTH + ' символов'});
            return false;
        }
        const url = '/checklist/default/add-checklist-item';
        const props = {
            'checklist_id': this.checklist_id,
            'item_name': itemName,
            'item_required': true
        };

        this.success = function (data) {
            if (data.status === 'success') {
                M.toast({html: 'Пункт добавлен'});
                this.preloader.classList.remove('active');
            }
        }

        this.sendAjax(url, props);
    }
    static deleteAllChecklists(csrfToken)
    {
        const url = '/checklist/default/delete-all-checklists';

        $.ajax({
            headers: {
                'X-CSRF-Token': csrfToken
            },
            type: 'POST',
            url: url,
            success: (data) => {
                $('.main-field').html('У вас еще нет ни одного чек-листа');
            }
        });
    }
}