export default class ChecklistClass {

    MIN_ITEM_LENGTH = 3;
    MAX_ITEM_COUNT = 255;

    checklistTemplate(checklist_props, checklist_items) {
        return `<div class="collapsible-header item">
                    <a href="#" data-target="${checklist_props.checklist_id}" class="item__name">${checklist_props.name}</a>
                    <div class="item__delete">
                        <a href="#" class="delete_checklist" data-target="${checklist_props.id}"><i class="material-icons">clear</i></a>
                    </div>
                </div>
                <div class="collapsible-body">
                    ${checklist_items}
                </div>`;
    }

    constructor(props) {
        this.ajaxHeaders = {
            'X-CSRF-Token': props.ajaxHeaders,
            'Content-Type': 'application/x-www-form-urlencoded'

        };
        this.preloader = props.preloader;
        this.domTarget = props.domTarget;
    }

    sendAjax(url, props = null) {
        return fetch(url, {
            method: 'POST',
            headers: this.ajaxHeaders,
            body: props
        });
    }

    /**
     * Получает пункты чек-листа
     * @param {int} checklist_id
     * @return {Promise} сверстанные пункты чек-листа
     */
    getChecklistItems(checklist_id) {
        const url = '/checklist/default/setup-checklist';
        const props = "checklist_id="+checklist_id;
        return this.sendAjax(url, props)
            .then((response) => response.text());
    }


    renderChecklist(checklist_props) {
        this.getChecklistItems(checklist_props.id)
            .then((data => {
                let checklist = document.createElement('li');
                checklist.innerHTML = this.checklistTemplate(checklist_props, data);
                this.checklist = checklist;
                this.domTarget.insertAdjacentElement('beforeend', this.checklist);
            }));
    }

    // $(checklist).on('submit', '.add-checklist-item', (event) => {
    //     event.preventDefault();
    //     this.addItem(event.target.elements.item_name.value);
    //     event.target.reset();
    // });


    /**
     Удаляет выбранный чек-лист
     * @param {Object} target
     */
    deleteChecklist(target) {
        console.log(typeof(target));
        const checklist_id = target.getAttribute('data-target');
        const url = '/checklist/default/delete-checklist';
        const props = `checklist_id=${checklist_id}`;
        this.sendAjax(url, props)
            .then((Response) => Response.json())
            .then((data) => {
                target.closest('li').remove();
                if (!this.isAnyChecklists()) {
                    document.querySelector('.checklists').innerHTML = 'У вас еще нет ни одного чек-листа';
                    document.querySelector('.delete-all-modal').remove();
                }
                ChecklistClass.sendToastMessage(data.message);
            })
    }

    /**
     * Возвращает true, если остались чек-лист в рабочем окне, иначе false
     * @returns {boolean}
     */
    isAnyChecklists() {
        const checklistCount = document.querySelector('.checklists').children.length;
        return checklistCount > 0;
    }

    /**
     * Добавляет пункт в чек-лист. Вызывается при submit`е формы.
     * TODO: валидацию вынести в отдельный метод
     * TODO: рендер шаблона вынести в отдельный метод
     * @param {string} itemName
     * @param {int} checklist_id
     * @return {boolean}
     */
    addItem(itemName, checklist_id) {
        if (itemName.length <= this.MIN_ITEM_LENGTH) {
            M.toast({html: 'Длина пункта должна быть больше ' + this.MIN_ITEM_LENGTH + ' символов'});
            return false;
        }
        const url = '/checklist/default/add-checklist-item';
        const props = `checklist_id=${checklist_id}&name=${itemName}&extra=1`;

        this.sendAjax(url, props)
            .then(Response => Response.json())
            .then(data => {
                if (data.status === 'success') {
                    ChecklistClass.sendToastMessage('Пункт добавлен');
                    const targetSelector = $(`.checklist_items[data-target = ${checklist_id}]`);
                    targetSelector.append(`
                    <p>
                         <label>
                            <input type="checkbox" value="1"/>
                            <span>${data.checklist_options.name}</span>
                            <a href="#" class="delete_item" data-target="${data.checklist_options.id}">
                                <i class="material-icons">clear</i>
                            </a>
                        </label>
                    </p>`);
                    if (!targetSelector.find('.empty-checklist').hasClass('empty-checklist-active')) {
                        targetSelector.find('.empty-checklist').addClass('empty-checklist-active');
                    }
                } else if(data.status === 'error') {
                    ChecklistClass.sendToastMessage('Произошла ошибка при добавлении пункта');
                }
            })
    }

    /**
     * Удаляет все чек-листы пользователя
     */
    deleteAllChecklists() {
        const url = '/checklist/default/delete-all-checklists';
        this.sendAjax(url)
            .then((Response) => {
                document.querySelector('.checklists').innerHTML = 'У вас еще нет ни одного чек-листа';
                document.querySelector('.delete-all-modal').remove();
                ChecklistClass.sendToastMessage('Все чек-листы удалены!');
            })
    }

    /**
     * Выводит информационное сообщение на экран (toast из библиотеки Materialize.css, если не находит библиотеку, то
     * alert).
     * @param {string} message
     */
    static sendToastMessage(message) {
        M ? M.toast({html: message}) : alert(message);
    }

    static deleteChecklistItem(target, csrfToken) {
        let checklist_item_id = target.getAttribute('data-target');

        const url = '/checklist/default/delete-checklist-item';
        const props = {"checklist_item_id": checklist_item_id};

        $.ajax({
            headers: {
                'X-CSRF-Token': csrfToken
            },
            type: 'POST',
            url: url,
            data: props,
            success: (data) => {
                const id = data.checklist_id;
                const targetSelector = target.closest(`.checklist-form`);

                if (targetSelector.querySelector('.empty-checklist')) {
                    targetSelector.querySelector('.empty-checklist').classList.remove('empty-checklist-active');
                }
                target.closest('label').remove();
                ChecklistClass.sendToastMessage('Пункт удален');

            }
        });
    }
    ;
}