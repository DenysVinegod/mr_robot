<?php
$data = json_decode($_POST['recipt_data'], true);
$data['id'] = str_pad($data['id'], 7, '0', STR_PAD_LEFT);

$pageWidth = 58;
$pageHeight = 180;

require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('mr. R O B O T');
$pdf->SetTitle('mr. R O B O T | Квитанція');
$pdf->SetSubject('Квитанція');
$pdf->SetKeywords('Service, Report, PDF');
$pdf->SetPrintHeader(false);
$pdf->SetMargins(5, 0, 5);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->AddPage('P', array($pageWidth, $pageHeight));

// define barcode style
$style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => false,
    'hpadding' => 'auto',
    'vpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
    'stretchtext' => 4
);

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
        <p> Тип пристрою:               <br>{$data['device_name']}      </p>
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

// CODE 128 C
$pdf->write1DBarcode($data['id'], 'EAN8', '12', '', '', 15, '', $style, 'N');

$pdf->Output('mr.ROBOT_recipt_'.$data['id'].'.pdf', 'I');

?>
