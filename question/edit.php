<?php require '../function/header.php'; ?>

<?php
$qid = $_GET['id'] ?? 0;
if (!$qid || !is_numeric($qid)) {
    alert("سوال نامعتبر است.");
    redirect('../quiz/list.php');
}

$stmt = $conn->prepare("SELECT q.*, qu.title as quiz_title FROM questions q JOIN quizzes qu ON q.quiz_id = qu.id WHERE q.id = ?");
$stmt->execute([$qid]);
$question = $stmt->fetch();
if (!$question) {
    alert("سوال یافت نشد.");
    redirect('../quiz/list.php');
}

// گزینه‌ها
$stmt = $conn->prepare("SELECT * FROM options WHERE question_id = ? ORDER BY id");
$stmt->execute([$qid]);
$options = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = trim($_POST['question_text']);
    $score = (int)($_POST['score'] ?? 1);
    $new_options = array_filter(array_map('trim', $_POST['options'] ?? []));

    if ($text && count($new_options) >= 2 && !empty($new_options[0])) {
        $conn->beginTransaction();

        // به‌روزرسانی سوال
        $stmt = $conn->prepare("UPDATE questions SET question_text = ?, score = ? WHERE id = ?");
        $stmt->execute([$text, $score, $qid]);

        // حذف گزینه‌های قدیمی
        $stmt = $conn->prepare("DELETE FROM options WHERE question_id = ?");
        $stmt->execute([$qid]);

        // اضافه کردن گزینه‌های جدید
        $correct_set = false;
        foreach ($new_options as $index => $opt_text) {
            $correct = ($index === 0 && !$correct_set) ? 1 : 0;
            if ($correct) $correct_set = true;
            $stmt = $conn->prepare("INSERT INTO options (question_id, option_text, correct) VALUES (?, ?, ?)");
            $stmt->execute([$qid, $opt_text, $correct]);
        }

        $conn->commit();
        alert("سوال با موفقیت ویرایش شد.", "success");
        redirect("add.php?quiz_id={$question['quiz_id']}");
    } else {
        alert("سوال، حداقل ۲ گزینه و گزینه اول (درست) الزامی است.");
    }
}
?>

<h2>ویرایش سوال در: <?= htmlspecialchars($question['quiz_title']) ?></h2>

<form method="POST" id="editForm">
    <div class="mb-3">
        <label>متن سوال</label>
        <textarea name="question_text" class="form-control" rows="3" required><?= htmlspecialchars($question['question_text']) ?></textarea>
    </div>
    <div class="mb-3">
        <label>امتیاز</label>
        <input type="number" name="score" class="form-control" min="1" value="<?= $question['score'] ?>">
    </div>

    <hr>
    <h5>گزینه‌ها (اولین گزینه = درست)</h5>
    <div id="optionsContainer">
        <?php foreach ($options as $i => $opt){ ?>
        <div class="input-group mb-2">
            <span class="input-group-text"><?= $i + 1 ?></span>
            <input type="text" name="options[]" class="form-control" value="<?= htmlspecialchars($opt['option_text']) ?>">
            <button type="button" class="btn btn-outline-danger remove-option">حذف</button>
        </div>
        <?php } ?>
    </div>
    <button type="button" class="btn btn-outline-secondary" id="addOption">+ افزودن گزینه</button>

    <div class="mt-3">
        <button type="submit" class="btn btn-success">ذخیره</button>
        <a href="<?= url("question/add.php?quiz_id={$question['quiz_id']}") ?>" class="btn btn-secondary">انصراف</a>
    </div>
</form>

<script>
let count = <?= count($options) ?>;
document.getElementById('addOption').onclick = () => {
    if (count >= 10) return;
    count++;
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `<span class="input-group-text">${count}</span>
                     <input type="text" name="options[]" class="form-control">
                     <button type="button" class="btn btn-outline-danger remove-option">حذف</button>`;
    document.getElementById('optionsContainer').appendChild(div);
};

document.getElementById('optionsContainer').onclick = (e) => {
    if (e.target.classList.contains('remove-option') && document.querySelectorAll('#optionsContainer .input-group').length > 2) {
        e.target.parentElement.remove();
        count--;
    }
};
</script>

<?php require '../function/footer.php'; ?>