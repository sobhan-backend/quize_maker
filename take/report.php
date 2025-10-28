<?php
session_start();

require '../function/header.php';
require '../vendor/autoload.php';

use Dompdf\Dompdf;

$attempt_id = $_GET['attempt_id'] ?? 0;
if (!$attempt_id) redirect('../index.php');

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

$dompdf = new Dompdf();
$html = '<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>
    body { font-family: DejaVu Sans, sans-serif; direction: rtl; }
    h1 { color: #007bff; }
    .correct { color: green; }
    .wrong { color: red; }
</style></head><body>
<h1>گزارش آزمون: ' . htmlspecialchars($results[0]['quiz_title']) . '</h1>
<p>نمره: ' . $results[0]['score'] . ' | درصد: ' . $results[0]['percentage'] . '%</p><hr>';

foreach ($results as $r) {
    $icon = (strtolower($r['selected_text']) === strtolower($r['correct_text'])) ? 'درست' : 'غلط';
    $color = $icon === 'درست' ? 'correct' : 'wrong';
    $html .= "<p><strong>سوال:</strong> " . htmlspecialchars($r['question_text']) . "<br>
              <span class='$color'>انتخاب شما: " . htmlspecialchars($r['selected_text']) . " ($icon)</span><br>
              <span class='correct'>پاسخ درست: " . htmlspecialchars($r['correct_text']) . "</span></p><hr>";
}

$html .= '</body></html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("report-$attempt_id.pdf", ['Attachment' => true]);