<?php

$data = json_decode($_POST['recipt_data'], true);

$pageWidth = 58;
$pageHeight = 170;

require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/tecnickcom/tcpdf/tcpdf.php');

$pdf = new TCPDF();

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('mr. R O B O T');
$pdf->SetTitle('mr. R O B O T | Квитанція');
$pdf->SetSubject('Квитанція');
$pdf->SetKeywords('Service, Report, PDF');
$pdf->SetPrintHeader(false);
$pdf->SetMargins(5, 0, 5);

$pdf->AddPage('P', array($pageWidth, $pageHeight));

$html = "<head>
    <style>
        *       { margin: 0; padding: 0; }
        body    { font-family: 'DejaVu Sans', sans-serif; font-size: 8pt; }
        h1      { font-size: 8pt; }
        .header { text-align: center; }
    </style>
</head>
<body>
    <div class=\"header\">
        <h1> Сервісний центр </h1>
        <img src=\"/app/assets/images/style/logo_for_recipt.png\">
        #{$data['id']}
    </div>
    <div>
        <p>
            Прізвище Ім`я По батькові:<br>
            {$data['surname']} 
            {$data['first_name']} 
            {$data['last_name']}
        </p>
        <p> Тип пристрою:               <br>{$data['device_name']}   </p>
        <p> Причина зверненя:           <br>{$data['description']}      </p>
        <p> Дата прийняття на ремонт:   <br>{$data['register_date']}    </p>
        <p>
            Графік роботи:<br>ПН - ПТ: 9:00 - 18:00<br>СБ - НД: Вихідні
        </p>
        <p> Наші контакти:<br>тел.: 098 644 50 91 </p>
        <p> Дякуємо, що скористалися нашим сервісом! </p>
    </div>
</body>";
$pdf -> writeHTML($html, false, false, true, false, '', false);

$pdf->Output('mr.ROBOT_recipt_'.$data['id'].'.pdf', 'I');

?>
