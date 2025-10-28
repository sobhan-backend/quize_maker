<?php require '../function/header.php'; ?>

<div class="container py-4" style="max-width: 700px;">
    <h2 class="mb-4 fw-bold text-primary">ایجاد آزمون جدید</h2>

    <form method="POST" class="card-quiet p-4">
        <div class="mb-4">
            <label class="form-label fw-semibold">عنوان آزمون <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control form-control-lg" required 
                   value="<?= old('title') ?>" placeholder="مثلاً: آزمون ریاضی پایه دهم">
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">توضیحات (اختیاری)</label>
            <textarea name="description" class="form-control" rows="3" 
                              placeholder="مثلاً: شامل ۲۰ سوال تستی با زمان ۳۰ دقیقه"><?= old('description') ?></textarea>
        </div>
        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-primary btn-pill px-5">ایجاد آزمون</button>
            <a href="<?= url() ?>" class="btn btn-outline-secondary btn-pill px-4">انصراف</a>
        </div>
    </form>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description'] ?? '');

    if ($title) {
        $stmt = $conn->prepare("INSERT INTO quizzes (title, description) VALUES (?, ?)");
        $stmt->execute([$title, $desc]);
        $quiz_id = $conn->lastInsertId();
        alert("آزمون با موفقیت ایجاد شد. حالا می‌تونی سوال اضافه کنی.", "success");
        redirect("question/add.php?quiz_id=$quiz_id");
    } else {
        alert("عنوان الزامی است.");
    }
}
?>

<?php require '../function/footer.php'; ?>