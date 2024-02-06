function read_repair_details(repair_id) {
    const json_container 
        = JSON.parse(
            document.querySelector("#repair_"+repair_id+" > td:nth-child(11)")
                .innerHTML
        );
    
    return json_container;
}

function fill_modal_editor(data) {
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
    
    document.getElementById('editor_status').value = data['status_id'];
    document.getElementById('repair_editor_registered_datetime').value 
        = data['register_date'];
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
    var seconds = node.value.split(':');
    var dateString =
        m.getFullYear() + "-" +
        ("00" + (m.getMonth()+1)).slice(-2) + "-" +
        ("00" + m.getDate()).slice(-2) + "T" +
        ("00" + m.getHours()).slice(-2) + ":" +
        ("00" + m.getMinutes()).slice(-2) + ":" +
        seconds[2];
    node.value = dateString;
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

const timer_chbox = this.document.getElementById('time_updater_chbox');
var timer;

window.addEventListener('load', function () {
    const overlay = document.getElementById('overlay');
    const button_new_repair_html = this.document.getElementById('new_repair');
    const closeModalButtons = this.document.querySelectorAll('[data-close-button]');
    const list_elements = this.document.querySelectorAll('.list_line');
    const editor_all_inputs = this.document.querySelectorAll("#modal_repair_editor input, #modal_repair_editor select, #modal_repair_editor textarea");

    editor_all_inputs.forEach(element => {
        element.disabled = true;
    });

    this.document.querySelector("#enable_editor").addEventListener('click', ()=>{
        editor_all_inputs.forEach(element => {
            element.disabled = !element.disabled;
        });
    });

    button_new_repair_html.addEventListener('click', ()=>{
        const modal = this.document.getElementById("modal_new_repair_editor");
        openModal(modal);
        timer_chbox.checked = true;
        write_current_datetimer_to_value(this.document.getElementById('registered_datetime'));
        datetimer_for_new_repair();
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
