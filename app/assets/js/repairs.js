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
    const closeModalButtons = document.querySelectorAll('[data-close-button]');

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
});
