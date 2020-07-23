export default class ChecklistItem
{

    constructor(props) {
        this.checklistItem_id = props.id;
        this.checklistItem_name = props.name;
        this.checklistItem_extra = props.created_at;
        this.checklistItem_checklistId = props.checklist_id;
    }

    preloader = document.querySelector('.preloader-wrapper');

    ajaxHeaders = {
        'X-CSRF-Token': $('meta[name="csrf-token"]').attr("content")
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

    itemTemplate()
    {
        return ` <label>
                            <input type="checkbox" value="1"/>
                            <span>${this.checklistItem_name}</span>
                            <a href="#!" class="delete_item" data-target="${this.checklistItem_id}">
                                <i class="material-icons">clear</i>
                            </a>
                        </label>`;
    }

    renderItem(target)
    {
        let item = document.createElement('p');
        item.insertAdjacentHTML('beforeend', this.itemTemplate());
        target.insertAdjacentElement('beforeend', item)

        this.afterRenderItem(item);
    }

    afterRenderItem(item)
    {
        item.querySelector('.delete_item').addEventListener('click', () => {
            this.deleteItem(item);
        });
    }
    deleteItem(item) {
        const url = '/checklist/default/delete-checklist-item';
        const props = {"checklist_item_id": this.checklistItem_id};

        this.success = function (data) {
            if (data.status === 'success') {
                this.preloader.classList.remove('active');
                item.remove();
            }
        }
        this.sendAjax(url, props);
    }

}