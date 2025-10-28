<?php
session_start();
require '../function/helper.php';
require '../function/db.php';

if (!isset($_SESSION['quiz_attempt'])) {
    alert("آزمون شروع نشده است.");
    redirect('../index.php');
    exit;
}

$attempt = $_SESSION['quiz_attempt'];
$quiz_id = $attempt['quiz_id'];
$answers = $attempt['answers'];
$questions = $attempt['questions'] ?? [];

$earned = 0;
$correct_count = 0;
$total_score = 0;

foreach ($questions as $q) {
    $total_score += $q['score'];
    $user_opt = $answers[$q['id']] ?? null;
    if (!$user_opt) continue;

    $stmt = $conn->prepare("SELECT correct FROM options WHERE id = ? AND question_id = ?");
    $stmt->execute([$user_opt, $q['id']]);
    $opt = $stmt->fetch();
    if ($opt && $opt['correct']) {
        $earned += $q['score'];
        $correct_count++;
    }
}

$percentage = $total_score > 0 ? round(($earned / $total_score) * 100, 2) : 0;

$stmt = $conn->prepare("INSERT INTO quiz_attempts (quiz_id, score, percentage) VALUES (?, ?, ?)");
$stmt->execute([$quiz_id, $earned, $percentage]);
$attempt_id = $conn->lastInsertId();

foreach ($answers as $qid => $oid) {
    $stmt = $conn->prepare("INSERT INTO attempt_answers (attempt_id, question_id, selected_option_id) VALUES (?, ?, ?)");
    $stmt->execute([$attempt_id, $qid, $oid]);
}

unset($_SESSION['quiz_attempt']);

require '../function/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <i class="bi bi-trophy display-1 text-warning"></i>
        <h2 class="mt-3 fw-bold text-primary">نتایج آزمون</h2>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="card-ghost p-4 text-center">
                <h5 class="text-success">نمره</h5>
                <h3 class="fw-bold"><?= $earned ?> / <?= $total_score ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-ghost p-4 text-center">
                <h5 class="text-info">درصد</h5>
                <h3 class="fw-bold"><?= $percentage ?>%</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-ghost p-4 text-center">
                <h5 class="text-primary">درست</h5>
                <h3 class="fw-bold"><?= $correct_count ?> / <?= count($questions) ?></h3>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="report.php?attempt_id=<?= $attempt_id ?>" class="btn btn-primary btn-pill px-5 me-3">
            دانلود گزارش PDF
        </a>
        <a href="<?= url() ?>" class="btn btn-outline-secondary btn-pill px-5">
            بازگشت به خانه
        </a>
    </div>
</div>

<?php require '../function/footer.php'; ?>