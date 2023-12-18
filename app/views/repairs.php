<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_header.php');
?>

<nav class="additional_menu">
    <a id="button_back" class="button" href="/"><p>Назад</p></a>
    <div class="filler"></div>
    <div id="button_repair_new" class="button">
        <p>Створити новий запис</p>
    </div>
</nav>

<div id="modal_new_repair" class="modal">
    <div id="modal_repair_new" class="modal_header">
        <p>Створення нової заявки<p>
    </div>
</div>
<div class="overlay"></div>
<div class="container">

</div>

<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/app/views/layouts/_main_footer.php');
?>