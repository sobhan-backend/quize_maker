<?php
require '../function/header.php';

$qid = (int)$_POST['question_id'];
if ($qid > 0) {
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->execute([$qid]);
    alert("سوال حذف شد.", "success");
}
redirect("add.php?quiz_id=" . (int)$_POST['quiz_id']);