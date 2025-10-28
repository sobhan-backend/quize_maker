<?php
require '../function/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_id = (int)$_POST['quiz_id'];
    if ($quiz_id > 0) {
        $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->execute([$quiz_id]);
        alert("آزمون با موفقیت حذف شد.", "success");
    }
}
redirect('list.php');