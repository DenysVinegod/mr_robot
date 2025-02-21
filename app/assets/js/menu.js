function openMainMenuModal(modal) {
    if (modal == null) return
    modal.classList.add('active')
    overlay.classList.add('active')
}

function closeMainMenuModal(modal) {
    if (modal == null) return
    modal.classList.remove('active')
    overlay.classList.remove('active')
    
}

function fill_items_into_main_menu_window(info, category) {
    const main_menu_window_container = this.document
        .querySelector('#main_menu_window #items_container');

    main_menu_window_container.innerHTML = '';
    
    for (let item of info) {
        if (item.category == category) {
            if (item.type == 'tag_a') {
                const menu_item = document.createElement('div');
                menu_item.classList.add('item');
                menu_item.id = item.id;
                
                const a_link = document.createElement('a');
                a_link.href = item.action;
                
                const image = document.createElement('img');
                image.src = item.img_path;

                const label = document.createElement('div');
                label.classList.add('label');
                label.innerHTML = item.label;

                a_link.appendChild(image);
                a_link.appendChild(label);

                menu_item.appendChild(a_link);

                main_menu_window_container.appendChild(menu_item);
            }
        }
    }
}

window.addEventListener('load', () => {
    const overlay = this.document.getElementById('overlay');
    const main_menu_items = {
        apps:       this.document.getElementById('apps_menu'),
        account:    this.document.getElementById('account_menu')
    }
    const modals = {
        main_menu: this.document.getElementById('main_menu_window')
    }

    let info = JSON.parse(this.document
        .querySelector('#main_menu_window #hiden_info').innerHTML);

    main_menu_items.apps.addEventListener('click', () => {
        console.log('apps');
        fill_items_into_main_menu_window(info, 'apps');
        openMainMenuModal(modals.main_menu);
    })

    main_menu_items.account.addEventListener('click', () => {
        console.log('account');
        fill_items_into_main_menu_window(info, 'account');
        openMainMenuModal(modals.main_menu);
    })

    overlay.addEventListener('click', () => {
        const modals = document.querySelectorAll('#main_menu_window.active');
        modals.forEach(modal => {
            closeMainMenuModal(modal);
        })
    });
});