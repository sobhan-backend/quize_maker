<?php
session_start();

require '../function/helper.php';
require '../function/db.php';

if (!isset($_SESSION['quiz_attempt'])) {
    alert("آزمون شروع نشده است.");
    redirect('../index.php');
    exit;
}

$attempt = &$_SESSION['quiz_attempt'];
$quiz_id = $attempt['quiz_id'];
$action = $_POST['action'] ?? '';
$question_id = (int)$_POST['question_id'];
$selected = $_POST['selected_option'] ?? null;

if ($selected !== null) {
    $attempt['answers'][$question_id] = (int)$selected;
}

$total = count($attempt['questions']);

if ($action === 'prev' && $attempt['current'] > 0) {
    $attempt['current']--;
} elseif ($action === 'next' && $attempt['current'] < $total - 1) {
    $attempt['current']++;
} elseif ($action === 'submit') {
    redirect("take/submit.php");
    exit;
} elseif (isset($_POST['goto'])) {
    $goto = (int)$_POST['goto'];
    if ($goto >= 0 && $goto < $total) {
        $attempt['current'] = $goto;
    }
}

redirect("take/start.php?quiz_id=$quiz_id");

exit;