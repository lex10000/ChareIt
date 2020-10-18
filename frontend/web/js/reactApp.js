'use strict';

import Feed from "./components/Feed.js";
const types = [/\/feed/, /\/profile\/\d+/, /\/top/];
document.addEventListener('DOMContentLoaded', function () {
    const elem = document.querySelector('.modal');
    const el = document.querySelector('#friends-tabs');
    M.Tabs.init(el, {});
    M.Modal.init(elem, {});
});

if (location.pathname === '/settings') {
    document.querySelector('#delete-user-form').addEventListener('beforeSubmit', () => {
        if (!confirm('Вы точно уверены, что хотите удалить аккаунт?')) {
            return false;
        }
    });
}

types.forEach(type => {
    if (location.pathname.match(type)) {
        let feedType = location.pathname.slice(1);
        const domContainer = document.querySelector('#reactapp');
        ReactDOM.render(React.createElement(Feed, { feedType: feedType }), domContainer);
    }
});