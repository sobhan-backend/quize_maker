<?php
session_start();
require '../function/db.php';
require '../function/helper.php';
require '../vendor/autoload.php';

use Mpdf\Mpdf;

// دریافت attempt_id
$attempt_id = $_GET['attempt_id'] ?? 0;
if (!$attempt_id) redirect('../index.php');

// دریافت نتایج از دیتابیس
$stmt = $conn->prepare("
    SELECT qa.score, qa.percentage, q.title as quiz_title, qq.question_text, 
           o.option_text as selected_text, 
           oc.option_text as correct_text
    FROM quiz_attempts qa
    JOIN quizzes q ON qa.quiz_id = q.id
    JOIN attempt_answers aa ON aa.attempt_id = qa.id
    JOIN questions qq ON aa.question_id = qq.id
    JOIN options o ON aa.selected_option_id = o.id
    JOIN options oc ON qq.id = oc.question_id AND oc.correct = 1
    WHERE qa.id = ?
");
$stmt->execute([$attempt_id]);
$results = $stmt->fetchAll();

if (empty($results)) redirect('../index.php');

// مسیر فونت Sahel
$fontPath = '../assets/fonts/sahel-400.ttf';
if (!file_exists($fontPath)) {
    die("فونت یافت نشد در مسیر: $fontPath");
}

// ایجاد mPDF با پشتیبانی RTL و UTF-8
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font' => 'Sahel',
    'autoLangToFont' => true,
    'autoScriptToLang' => true,
    'directionality' => 'rtl',
]);

// ثبت فونت Sahel
$mpdf->fontdata['Sahel'] = [
    'R' => $fontPath,
    'useOTL' => 0xFF,
    'useKashida' => 75
];

// ساخت HTML
$html = '<!DOCTYPE html>
<html lang="fa">
<head>
<meta charset="UTF-8">
<style>
body {
    font-family: "Sahel", sans-serif;
    direction: rtl;
    text-align: right;
    font-size: 13px;
    line-height: 1.8;
}
p, div, h1, h2, h3, span, strong {
    direction: rtl;
    text-align: right;
}
h1 {
    color: #007bff;
    margin-bottom: 10px;
}
.correct { color: green; }
.wrong { color: red; }
hr { margin: 10px 0; border: 0; border-top: 1px solid #ccc; }
</style>
</head>
<body>';

$html .= '<h1>گزارش آزمون: ' . htmlspecialchars($results[0]['quiz_title']) . '</h1>';
$html .= '<p>نمره: ' . $results[0]['score'] . ' / درصد: ' . $results[0]['percentage'] . '%</p><hr>';

foreach ($results as $r) {
    $is_correct = (trim($r['selected_text']) === trim($r['correct_text']));
    $icon = $is_correct ? 'درست' : 'غلط';
    $color = $is_correct ? 'green' : 'red';

    $html .= '<div>
        <strong>سوال:</strong> ' . htmlspecialchars($r['question_text']) . '<br>
        <span style="color:' . $color . '">انتخاب شما: ' . htmlspecialchars($r['selected_text']) . ' (' . $icon . ')</span><br>
        <span style="color:green">پاسخ درست: ' . htmlspecialchars($r['correct_text']) . '</span>
    </div><hr>';
}

$html .= '</body></html>';

// نوشتن و خروجی PDF
$mpdf->WriteHTML($html);
$mpdf->Output("report-$attempt_id.pdf", 'D');
exit;
