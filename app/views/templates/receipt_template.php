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



require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/tecnickcom/tcpdf/tcpdf.php');

$pdf = new TCPDF();

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('mr. R O B O T');
$pdf->SetTitle('mr. R O B O T | Квитанція');
$pdf->SetSubject('Квитанція');
$pdf->SetKeywords('Service, Report, PDF');

// Вимкнення виводу заголовка сторінки
$pdf->SetPrintHeader(false);

// Додавання нової сторінки
$pdf->AddPage();

// Встановлення шрифта та розміру тексту
$pdf->SetFont('dejavusans', '', 12);

// Отримання розмірів сторінки
$pageWidth = $pdf->getPageWidth();
$pageHeight = $pdf->getPageHeight();

$textTitle = 'Сервісний центр mr. R O B O T';
$receiptNumber = 'Номер квитанції: 12345';

$textWidthTitle = $pdf->GetStringWidth($textTitle);
$textWidthReceiptNumber = $pdf->GetStringWidth($receiptNumber);
$textHeight = 12; // Приблизна висота тексту

// Розрахунок координат для вирівнювання тексту по середині сторінки
$xTitle = ($pageWidth - $textWidthTitle) / 2;
$xReceiptNumber = ($pageWidth - $textWidthReceiptNumber) / 2;
$y = ($pageHeight + $textHeight) / 2;

// Додавання тексту на сторінку
$pdf->Text($xTitle, $y - 10, $textTitle); // Розміщення заголовка вище по вертикалі
$pdf->Text($xReceiptNumber, $y + 10, $receiptNumber); // Розміщення номера квитанції нижче по вертикалі

// Виведення PDF у браузер або збереження файлу
$pdf->Output('service_center.pdf', 'I');

?>
