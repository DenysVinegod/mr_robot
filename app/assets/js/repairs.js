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

window.addEventListener('load', function () {
    const overlay = document.getElementById('overlay');
    const button_new_repair_html = this.document.getElementById('new_repair');
    const closeModalButtons = document.querySelectorAll('[data-close-button]');
    
    button_new_repair_html.addEventListener('click', ()=>{
        const modal = this.document.getElementById("modal_editor");
        openModal(modal);
    });

    closeModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            const modal = button.closest('.modal')
            closeModal(modal)
        })
    });

    overlay.addEventListener('click', () => {
        const modals = document.querySelectorAll('.modal.active')
        modals.forEach(modal => {
            closeModal(modal);
        })
    });
});