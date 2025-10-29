<?php
session_start();
require '../function/header.php';

$quiz_id = $_GET['quiz_id'] ?? 0;
if (!$quiz_id || !is_numeric($quiz_id)) {
    alert("آزمون نامعتبر است.");
    redirect('../index.php');
}

$stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();
if (!$quiz) {
    alert("آزمون یافت نشد.");
    redirect('../index.php');
}

$stmt = $conn->prepare("
    SELECT q.id, q.question_text, q.score 
    FROM questions q 
    WHERE q.quiz_id = ? 
    ORDER BY q.id
");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll();

if (!isset($_SESSION['quiz_attempt'])) {
    $_SESSION['quiz_attempt'] = [
        'quiz_id' => $quiz_id,
        'answers' => [],
        'current' => 0,
        'questions' => $questions
    ];
}

if (empty($questions)) {
    alert("این آزمون سوالی ندارد.");
    redirect("quiz/list.php");
}

$attempt = &$_SESSION['quiz_attempt'];
$current_index = $attempt['current'];
$question = $questions[$current_index];

$stmt = $conn->prepare("SELECT id, option_text FROM options WHERE question_id = ? ORDER BY RAND()");
$stmt->execute([$question['id']]);
$options = $stmt->fetchAll();

$total = count($questions);
$hasPrev = $current_index > 0;
$hasNext = $current_index < $total - 1;
?>

<div class="container py-4">
    <div class="card-quiet question-card mx-auto" style="max-width: 800px;">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 text-primary fw-bold"><?= htmlspecialchars($quiz['title']) ?></h4>
                <span class="question-meta">سوال <?= $current_index + 1 ?> از <?= $total ?></span>
            </div>

            <hr class="my-4">

            <p class="question-text fw-semibold mb-4"><?= nl2br(htmlspecialchars($question['question_text'])) . " (" . $question['score'] .")" ?></p>

            <form method="POST" action="process.php" id="answerForm">
                <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                
                <div class="options-container">
                    <?php foreach ($options as $opt){
                        $is_selected = ($attempt['answers'][$question['id']] ?? '') == $opt['id'];
                    ?>
                    <label class="option-card <?= $is_selected ? 'selected' : '' ?>">
                        <input type="radio" name="selected_option" value="<?= $opt['id'] ?>" 
                               <?= $is_selected ? 'checked' : '' ?>>
                        <div class="option-body">
                            <div class="option-label"><?= htmlspecialchars($opt['option_text']) ?></div>
                        </div>
                    </label>
                    <?php } ?>
                </div>

                <div class="action-row d-flex justify-content-between align-items-center mt-5 flex-wrap gap-3">
                    <button type="submit" name="action" value="prev" class="btn btn-outline-secondary px-4" <?= !$hasPrev ? 'disabled' : '' ?>>
                        قبلی
                    </button>

                    <div class="q-pagination">
                        <?php for ($i = 0; $i < $total; $i++){
                            $answered = isset($attempt['answers'][$questions[$i]['id']]);
                        ?>
                        <span class="badge rounded-pill <?= $i == $current_index ? 'bg-primary' : ($answered ? 'bg-success' : 'bg-light text-dark') ?>"
                              onclick="goTo(<?= $i ?>)">
                            <?= $i + 1 ?>
                        </span>
                        <?php } ?>
                    </div>

                    <?php if ($hasNext){ ?>
                    <button type="submit" name="action" value="next" class="btn btn-primary px-4">
                        بعدی
                    </button>
                    <?php } else{ ?>
                    <button type="submit" name="action" value="submit" class="btn btn-success px-5">
                        ثبت نهایی
                    </button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const form = document.getElementById('answerForm');

// وقتی کاربر یکی از گزینه‌ها را انتخاب کرد، فرم ارسال شود
form.querySelectorAll('input[name="selected_option"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // بررسی اینکه آیا دکمه بعدی فعال است یا آخرین سوال
        const nextButton = form.querySelector('button[name="action"][value="next"]');
        const submitButton = form.querySelector('button[name="action"][value="submit"]');

        if (nextButton) {
            // اگر دکمه بعدی وجود داشت، فرم را با action=next ارسال کن
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'action';
            input.value = 'next';
            form.appendChild(input);
            form.submit();
        } else if (submitButton) {
            // اگر آخرین سوال است، فرم را برای ثبت نهایی ارسال کن
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'action';
            input.value = 'submit';
            form.appendChild(input);
            form.submit();
        }
    });
});

// تابع goTo برای پیمایش با شماره سوال بدون تغییر
function goTo(index) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'goto';
    input.value = index;
    form.appendChild(input);
    form.submit();
}
</script>


<?php require '../function/footer.php'; ?>