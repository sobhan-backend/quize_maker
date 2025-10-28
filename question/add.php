<?php
require '../function/header.php';

$quiz_id = $_GET['quiz_id'] ?? 0;
if (!$quiz_id || !is_numeric($quiz_id)) {
    alert("آزمون نامعتبر است.");
    redirect('../index.php');
}

$stmt = $conn->prepare("SELECT title FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();
if (!$quiz) {
    alert("آزمون یافت نشد.");
    redirect('../index.php');
}
?>

<div class="container py-4" style="max-width: 800px;">
    <h3 class="mb-4 fw-bold text-primary">افزودن سوال به: <?= htmlspecialchars($quiz['title']) ?></h3>

    <form method="POST" id="questionForm" class="card-quiet p-4">
        <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">

        <div class="mb-4">
            <label class="form-label fw-semibold">متن سوال <span class="text-danger">*</span></label>
            <textarea name="question_text" class="form-control" rows="4" required 
                      placeholder="سوال خود را اینجا بنویسید..."><?= old('question_text') ?></textarea>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">امتیاز سوال (پیش‌فرض: 1)</label>
            <input type="number" name="score" class="form-control" min="1" value="<?= old('score', 1) ?>">
        </div>

        <hr class="my-4">

        <h5 class="mb-3">گزینه‌ها <small class="text-muted">(حداقل ۲ مورد انتخاب شود، اولین گزینه = درست)</small></h5>

        <div id="optionsContainer" class="mb-3">
            <?php for ($i = 0; $i < 4; $i++): ?>
            <div class="input-group mb-3 option-row">
                <span class="input-group-text fw-bold int-add"><?= $i + 1 ?></span>
                <input type="text" name="options[]" class="form-control" 
                       placeholder="متن گزینه <?= $i + 1 ?>" value="<?= old("options.$i") ?>">
                <button type="button" class="btn btn-outline-danger remove-option buttom-delete" <?= $i < 2 ? 'disabled' : '' ?>>حذف</button>
            </div>
            <?php endfor; ?>
        </div>

        <button type="button" class="btn btn-outline-secondary btn-pill mb-4" id="addOption">+ افزودن گزینه</button>

        <div class="d-flex gap-3 flex-wrap">
            <button type="submit" class="btn btn-success btn-pill px-5">ذخیره سوال</button>
            <a href="<?= url("question/add.php?quiz_id=$quiz_id") ?>" class="btn btn-primary btn-pill px-4">سوال جدید</a>
            <a href="<?= url("quiz/list.php") ?>" class="btn btn-outline-secondary btn-pill px-4">اتمام</a>
        </div>
    </form>
</div>

<script>
let optionCount = 4;
document.getElementById('addOption').addEventListener('click', () => {
    if (optionCount >= 10) return alert('حداکثر ۱۰ گزینه مجاز است.');
    optionCount++;
    const div = document.createElement('div');
    div.className = 'input-group mb-3 option-row';
    div.innerHTML = `
        <span class="input-group-text fw-bold int-add">${optionCount}</span>
        <input type="text" name="options[]" class="form-control" placeholder="متن گزینه">
        <button type="button" class="btn btn-outline-danger remove-option buttom-delete">حذف</button>
    `;
    document.getElementById('optionsContainer').appendChild(div);
});

document.getElementById('optionsContainer').addEventListener('click', (e) => {
    if (e.target.classList.contains('remove-option')) {
        if (document.querySelectorAll('.option-row').length <= 2) {
            alert('حداقل ۲ گزینه باید باقی بماند.');
            return;
        }
        e.target.parentElement.remove();
        optionCount--;
    }
});
</script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_text = trim($_POST['question_text']);
    $score = (int)($_POST['score'] ?? 1);
    $options = array_filter(array_map('trim', $_POST['options'] ?? []));

    if (!$question_text || count($options) < 2) {
        alert("سوال و حداقل ۲ گزینه الزامی است.");
    } elseif (empty($options[0])) {
        alert("گزینه اول (درست) نمی‌تواند خالی باشد.");
    } else {
        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, score) VALUES (?, ?, ?)");
            $stmt->execute([$quiz_id, $question_text, $score]);
            $question_id = $conn->lastInsertId();

            $correct_set = false;
            foreach ($options as $index => $text) {
                $correct = ($index === 0 && !$correct_set) ? 1 : 0;
                if ($correct) $correct_set = true;
                $stmt = $conn->prepare("INSERT INTO options (question_id, option_text, correct) VALUES (?, ?, ?)");
                $stmt->execute([$question_id, $text, $correct]);
            }

            $conn->commit();
            alert("سوال با موفقیت اضافه شد.", "success");
            redirect("question/add.php?quiz_id=$quiz_id");
        } catch (Exception $e) {
            $conn->rollBack();
            alert("خطا در ذخیره‌سازی: " . $e->getMessage());
        }
    }
}
require '../function/footer.php';
?>