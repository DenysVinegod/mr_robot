<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_header.php');
?>

<nav class="additional_menu">
    <a id="button_back" class='tooltip menu_button' href="/">
        <img src='/app/assets/images/style/log_out.png'>
        <span class='tooltiptext'>Назад</span>
    </a>
    <div class="filler"></div>
    <div id="new_repair" 
        class='tooltip menu_button' 
        data-modal-target='#modal_editor'>
        <img src='/app/assets/images/style/pencil.png'>
        <span class='tooltiptext'>Створити</span>
    </div>
</nav>

<div id="modal_editor" class="modal">
    <div class="modal_header">
        <p>Створення нової заявки<p>
    </div>
    <div class="modal_body">
        <form action="">
            <fieldset>
                <legend>Дані про замовника:</legend>
            </fieldset>
            <fieldset>
                <legend>Дані про пристрій:</legend>
            </fieldset>
        </form>
    </div>
</div>

<div class="overlay"></div>

<div class="repairs_table_container">

</div>

<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_footer.php');
?>