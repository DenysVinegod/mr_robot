<?php

$data = array(
    'recipt_num' => 0123456,
    'client' => [
        'surname' => 'Прізвище', 
        'first_name' => 'Ім\'я',
        'last_name' => 'По батькові'
    ],
    'device' => [
        'type' => 'device_type'
    ],
    'description' => 'причина звернення',
    'register_date' => 'дата прийняття в ремонт'
);

$pageWidth = 48;
$pageHeight = 100;

function get_x_for_center(string $text): int{
    global $pageWidth, $pdf;
    $textWidth = $pdf -> GetStringWidth($text);
    return $xText = ($pageWidth - $textWidth) / 2;
}

require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/tecnickcom/tcpdf/tcpdf.php');

$pdf = new TCPDF();

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('mr. R O B O T');
$pdf->SetTitle('mr. R O B O T | Квитанція');
$pdf->SetSubject('Квитанція');
$pdf->SetKeywords('Service, Report, PDF');
$pdf->SetPrintHeader(false);

$pdf->AddPage('P', array($pageWidth, $pageHeight));

$receiptNumber = 'Квитанція #'.$data['recipt_num'];

// Додавання тексту на сторінку
$pdf->SetFont('dejavusans', '', 6);
$pdf->Text(get_x_for_center('Сервісний центр'), 2, 'Сервісний центр');
$pdf->SetFont('dejavusans', '', 8);
$pdf->Text(get_x_for_center('mr. R O B O T'), 5, 'mr. R O B O T');
$pdf->SetFont('dejavusans', '', 6);

$pdf->Text(get_x_for_center($receiptNumber), 12, $receiptNumber);
$pdf->Text(get_x_for_center('Клієнт:'), 16, 'Клієнт:');

// Виведення PDF у браузер або збереження файлу
$pdf->Output('mr.ROBOT_recipt_'.$data['recipt_num'].'.pdf', 'I');

?>
