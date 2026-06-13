function read_repair_details(repair_id) {
    const json_container 
        = JSON.parse(
            document.querySelector("#repair_"+repair_id+" > td:nth-child(11)")
                .innerHTML
        );
    
    return json_container;
}

function getAllowedStatusesForEditor(currentStatusName) {
    return [
        'Нове замовлення',
        'Діагностика',
        'Очікує узгодження',
        'Узгоджено',
        'Скасовано',
        'Відмовлено',
        'Виконано',
        'Видано',
        'Видано без ремонту'
    ];
}

function fill_modal_editor(data) {
    json_container = data;

    document.getElementById('editor_repair_id').innerHTML = data['id'];

    document.getElementById('repair_editor_surname').value = data['surname'];
    document.getElementById('repair_editor_first_name').value 
        = data['first_name'];
    document.getElementById('repair_editor_last_name').value 
        = data['last_name'];

    document.getElementById('repair_editor_contact_type_id').value 
        = data['contact_type_id'];
    document.getElementById('repair_editor_contact').value = data['contact'];

    document.getElementById('repair_editor_device_type').value 
        = data['device_type_id'];
    document.getElementById('repair_editor_device_description').value
        = data['device_description'] || '';
    document.getElementById('repair_editor_device_color').value
        = data['device_color'] || '';
    document.getElementById('repair_editor_device_cosmetic_condition').value
        = data['device_cosmetic_condition'] || '';
    document.getElementById('repair_editor_device_serial_number').value
        = data['device_serial_number'] || '';
    document.getElementById('repair_editor_device_equipment').value
        = data['device_equipment'] || '';

    const statusSelect = document.getElementById('editor_status');
    const statusValue = data['status_id'];
    const currentStatusName = data['status'];

    const allowedStatusNames = getAllowedStatusesForEditor(currentStatusName);
    const existingOptions = Array.from(statusSelect.options).reduce((map, option) => {
        map[option.text.trim()] = option.value;
        return map;
    }, {});

    statusSelect.innerHTML = '';

    allowedStatusNames.forEach(statusName => {
        if (existingOptions[statusName]) {
            const option = document.createElement('option');
            option.value = existingOptions[statusName];
            option.text = statusName;
            statusSelect.appendChild(option);
        }
    });

    if (statusValue.toString() !== '') {
        const foundOption = Array.from(statusSelect.options).find(option => option.value === statusValue.toString());
        if (foundOption) {
            statusSelect.value = statusValue;
        } else {
            const customStatus = document.createElement('option');
            customStatus.value = statusValue;
            customStatus.text = currentStatusName;
            customStatus.selected = true;
            statusSelect.appendChild(customStatus);
        }
    }

    let registerDateValue = data['register_date'];
    if (registerDateValue && registerDateValue.indexOf(' ') !== -1) {
        registerDateValue = registerDateValue.replace(' ', 'T');
    }
    document.getElementById('repair_editor_registered_datetime').value 
        = registerDateValue;
    document.getElementById('repair_editor_problem_description').value 
        = data['description'];
    document.getElementById('repair_editor_price').value = data['price'];
    document.getElementById('repair_editor_master_conclusion').value 
        = data['master_conclusion'];
    document.getElementById('repair_editor_id').value 
        = data['id'];
}

function openModal(modal) {
    if (modal == null) return
    modal.classList.add('active')
    overlay.classList.add('active')
}

function closeModal(modal) {
    if (modal == null) return
    modal.classList.remove('active')
    overlay.classList.remove('active')
}

function write_current_datetimer_to_value(node){
    var m = new Date();
    if (node.value != '') var seconds = node.value.split(':'); 
    else var seconds = ['', '', '00'];
    var dateString =
        m.getFullYear() + "-" +
        ("00" + (m.getMonth()+1)).slice(-2) + "-" +
        ("00" + m.getDate()).slice(-2) + "T" +
        ("00" + m.getHours()).slice(-2) + ":" +
        ("00" + m.getMinutes()).slice(-2) + ":" +
        seconds[2];
    node.value = dateString;
}

function filterClientsByQuery(query) {
    const normalized = query.trim().toLowerCase();
    if (!normalized) return [];
    return window.mrRobotData.clients.filter(client => {
        const title = [client.surname, client.first_name, client.last_name]
            .join(' ').toLowerCase();
        return title.indexOf(normalized) !== -1;
    });
}

function formatClientLabel(client) {
    return client.surname + ' ' + client.first_name + ' ' + client.last_name;
}

function renderClientSuggestions(matches) {
    customerSuggestions.innerHTML = '';
    if (!matches.length) {
        const item = document.createElement('div');
        item.className = 'suggestion_item';
        item.textContent = 'Немає результатів';
        customerSuggestions.appendChild(item);
        return;
    }
    matches.forEach(client => {
        const item = document.createElement('div');
        item.className = 'suggestion_item';
        item.dataset.clientId = client.id;
        item.textContent = formatClientLabel(client);
        item.addEventListener('click', () => {
            selectClient(client);
        });
        customerSuggestions.appendChild(item);
    });
}

function renderClientContacts(clientId) {
    customerContactsExisting.innerHTML = '';
    const contacts = window.mrRobotData.contacts.filter(contact => Number(contact.client_id) === Number(clientId));
    if (!contacts.length) {
        const empty = document.createElement('div');
        empty.className = 'suggestion_item';
        empty.textContent = 'У клієнта немає контактів';
        customerContactsExisting.appendChild(empty);
        return;
    }
    contacts.forEach(contact => {
        const item = document.createElement('div');
        item.className = 'suggestion_item';
        item.dataset.contactId = contact.id;
        item.dataset.contactTypeId = contact.type_id;
        item.dataset.contactValue = contact.contact;
        item.textContent = contact.contact;
        item.addEventListener('click', () => {
            customerContactId.value = contact.id;
            customerContactTypeId.value = contact.type_id;
            customerContact.value = contact.contact;
            customerContact.classList.add('selected');
        });
        customerContactsExisting.appendChild(item);
    });
}

function renderClientDevices(clientId) {
    customerDeviceSelect.innerHTML = '';
    const placeholderOption = document.createElement('option');
    placeholderOption.value = '0';
    placeholderOption.textContent = 'Оберіть пристрій клієнта';
    customerDeviceSelect.appendChild(placeholderOption);

    const devices = window.mrRobotData.devices.filter(device => Number(device.client_id) === Number(clientId));
    if (devices.length) {
        devices.forEach(device => {
            const option = document.createElement('option');
            option.value = device.id;
            const label = device.description || 'Пристрій #' + device.id;
            const details = [];
            if (device.color) details.push(device.color);
            if (device.serial_number) details.push('SN:' + device.serial_number);
            if (device.cosmetic_condition) details.push(device.cosmetic_condition);
            const fullLabel = details.length ? label + ' [' + details.join(', ') + ']' : label;
            option.textContent = fullLabel;
            option.dataset.deviceData = JSON.stringify(device);
            customerDeviceSelect.appendChild(option);
        });
    }
    const newOption = document.createElement('option');
    newOption.value = 'new';
    newOption.textContent = 'Додати новий пристрій';
    customerDeviceSelect.appendChild(newOption);
    customerDeviceSelect.disabled = false;
}

function updateDeviceSelectState() {
    const selectedClientId = Number(customerClientId.value) > 0;
    const hasCustomerName = [customerSurname, customerFirstName, customerLastName].some(input => input.value.trim() !== '');

    if (selectedClientId) {
        return;
    }

    if (hasCustomerName) {
        customerDeviceSelect.disabled = false;
        customerDeviceSelect.innerHTML = '';
        const placeholderOption = document.createElement('option');
        placeholderOption.value = '0';
        placeholderOption.textContent = 'Оберіть клієнта або додайте новий пристрій';
        customerDeviceSelect.appendChild(placeholderOption);
        const newOption = document.createElement('option');
        newOption.value = 'new';
        newOption.textContent = 'Додати новий пристрій';
        customerDeviceSelect.appendChild(newOption);
        customerNewDeviceFields.classList.add('hidden');
    } else {
        customerDeviceSelect.disabled = true;
        customerDeviceSelect.innerHTML = '<option value="0">Оберіть клієнта для пристрою</option><option value="new">Додати новий пристрій</option>';
        customerNewDeviceFields.classList.add('hidden');
    }
}

function resetNewRepairModal() {
    customerSearch.value = '';
    customerSuggestions.innerHTML = '';
    customerClientId.value = 0;
    customerContactId.value = 0;
    customerSurname.value = '';
    customerFirstName.value = '';
    customerLastName.value = '';
    customerContactTypeId.value = customerContactTypeId.options[0] ? customerContactTypeId.options[0].value : '';
    customerContact.value = '';
    customerContactsExisting.innerHTML = '';
    customerDeviceSelect.innerHTML = '<option value="0">Оберіть клієнта для пристрою</option><option value="new">Додати новий пристрій</option>';
    customerDeviceSelect.disabled = true;
    customerNewDeviceFields.classList.add('hidden');
    customerDeviceType.value = customerDeviceType.options[0] ? customerDeviceType.options[0].value : '';
    customerDeviceDescription.value = '';
    document.getElementById('customer_device_color').value = '';
    document.getElementById('customer_device_cosmetic_condition').value = '';
    document.getElementById('customer_device_serial_number').value = '';
    document.getElementById('customer_device_equipment').value = '';
}

function selectClient(client) {
    customerClientId.value = client.id;
    customerSurname.value = client.surname;
    customerFirstName.value = client.first_name;
    customerLastName.value = client.last_name;
    customerSuggestions.innerHTML = '';
    customerContactId.value = 0;
    customerContact.value = '';
    customerContactTypeId.value = customerContactTypeId.options[0] ? customerContactTypeId.options[0].value : '';
    renderClientContacts(client.id);
    renderClientDevices(client.id);
}

function toggleNewDeviceFields() {
    if (customerDeviceSelect.value === 'new') {
        customerNewDeviceFields.classList.remove('hidden');
        customerDeviceType.required = true;
        customerDeviceDescription.required = false;
    } else {
        customerNewDeviceFields.classList.add('hidden');
        customerDeviceType.required = false;
        customerDeviceDescription.required = false;
    }
}

function datetimer_for_new_repair() {
    if (timer_chbox.checked) {
        write_current_datetimer_to_value(this.document.getElementById('registered_datetime'));
        timer = setInterval(() => {
            write_current_datetimer_to_value(this.document.getElementById('registered_datetime'))
        }, 60000);
    } else {
        clearInterval(timer);
    }
}

function add_button_to_menu() {
    let repair_menu = document.createElement("nav");
    repair_menu.id = "repair_main_menu";
    repair_menu.classList.add("main_menu");
    // 
    let new_repair_button = document.createElement("div");
    new_repair_button.id = "new_repair_button";
    new_repair_button.classList.add("main_menu_item");
    new_repair_button.classList.add("tooltip");
    new_repair_button.setAttribute("data-modal-target", "#modal_editor");
    // 
    let repair_button_image = document.createElement("img");
    repair_button_image.src = "/app/assets/images/style/new_page.png";
    // 
    let tooltip_span = document.createElement("span");
    tooltip_span.classList.add("tooltiptext");
    tooltip_span.classList.add("tooltip_down");
    tooltip_span.innerHTML = "Створити";

    
    repair_menu.appendChild(new_repair_button);
    new_repair_button.appendChild(repair_button_image);
    new_repair_button.appendChild(tooltip_span);

    let parrent_node = document.getElementById("page_header");
    let main_menu = document.querySelector("#page_header > nav.main_menu");
    parrent_node.insertBefore(repair_menu, main_menu);
}

function sendData(data) {
    var jsonData = JSON.stringify(data);

    var form = document.createElement('form');
    form.method = 'post';
    form.action = '/app/views/templates/recipt_template.php';
    form.target = '_blank';

    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'recipt_data';
    input.value = jsonData;

    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
}

let overlay, timer_chbox, timer, json_container;
let customerSearch, customerSuggestions, customerClientId, customerContactId,
    customerSurname, customerFirstName, customerLastName,
    customerContactsExisting, customerNewContactButton,
    customerContactTypeId, customerContact,
    customerDeviceSelect, customerNewDeviceFields,
    customerDeviceType, customerDeviceDescription;

window.addEventListener('load', function () {
    overlay = document.getElementById('overlay');
    timer_chbox = this.document.getElementById('time_updater_chbox');
    add_button_to_menu();

    const button_new_repair_html = this.document.getElementById('new_repair_button');
    const closeModalButtons = this.document.querySelectorAll('[data-close-button]');
    const list_elements = this.document.querySelectorAll('.list_line');
    const editor_all_inputs = this.document.querySelectorAll("#modal_repair_editor input, #modal_repair_editor select, #modal_repair_editor textarea");
    const button_pencil = this.document.querySelector("#enable_editor");
    const button_printer = this.document.querySelector("#print_recipt");

    customerSearch = this.document.getElementById('customer_search');
    customerSuggestions = this.document.getElementById('customer_suggestions');
    customerClientId = this.document.getElementById('customer_client_id');
    customerContactId = this.document.getElementById('customer_contact_id');
    customerSurname = this.document.getElementById('customer_surname');
    customerFirstName = this.document.getElementById('customer_first_name');
    customerLastName = this.document.getElementById('customer_last_name');
    customerContactsExisting = this.document.getElementById('customer_contacts_existing');
    customerNewContactButton = this.document.getElementById('customer_new_contact_button');
    customerContactTypeId = this.document.getElementById('customer_contact_type_id');
    customerContact = this.document.getElementById('customer_contact');
    customerDeviceSelect = this.document.getElementById('customer_device_select');
    customerNewDeviceFields = this.document.getElementById('customer_new_device_fields');
    customerDeviceType = this.document.getElementById('customer_device_type');
    customerDeviceDescription = this.document.getElementById('customer_device_description');

    editor_all_inputs.forEach(element => {
        element.disabled = true;
    });

    if (customerDeviceSelect) {
        customerDeviceSelect.disabled = true;
    }

    if (customerSearch) {
        customerSearch.addEventListener('input', () => {
            const matches = filterClientsByQuery(customerSearch.value);
            renderClientSuggestions(matches.slice(0, 6));
            updateDeviceSelectState();
        });
    }

    [customerSurname, customerFirstName, customerLastName].forEach(input => {
        input.addEventListener('input', updateDeviceSelectState);
    });

    if (customerNewContactButton) {
        customerNewContactButton.addEventListener('click', () => {
            customerContactId.value = 0;
            customerContact.value = '';
            customerContactTypeId.value = customerContactTypeId.options[0] ? customerContactTypeId.options[0].value : '';
            customerContact.focus();
        });
    }

    if (customerDeviceSelect) {
        customerDeviceSelect.addEventListener('change', () => {
            toggleNewDeviceFields();
        });
    }

    if (button_new_repair_html) {
        button_new_repair_html.addEventListener('click', ()=>{
            const modal = this.document.getElementById('modal_new_repair_editor');
            resetNewRepairModal();
            openModal(modal);
            timer_chbox.checked = true;
            write_current_datetimer_to_value(this.document.getElementById('registered_datetime'));
            datetimer_for_new_repair();
        });
    }

    button_pencil.addEventListener('click', ()=>{
        editor_all_inputs.forEach(element => {
            element.disabled = !element.disabled;
        });
    });

    button_printer.addEventListener('click', ()=>{
        sendData(json_container);
    });

    closeModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            const modal = button.closest('.modal');
            closeModal(modal);
            timer_chbox.checked = false;
            datetimer_for_new_repair();
        })
    });

    overlay.addEventListener('click', () => {
        const modals = document.querySelectorAll('.modal.active')
        modals.forEach(modal => {
            closeModal(modal);
            timer_chbox.checked = false;
            datetimer_for_new_repair();
        })
    });

    timer_chbox.onchange = function() {
        datetimer_for_new_repair();
    };

    list_elements.forEach(element => {
        const modal = this.document.getElementById('modal_repair_editor');
        element.addEventListener('click', () => {
            fill_modal_editor(
                read_repair_details(
                    element.children[0].innerHTML
                )
            );
            openModal(modal);
        });
    });
});
