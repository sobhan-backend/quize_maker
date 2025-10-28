<?php require '../function/header.php'; ?>

<?php
$quiz_id = $_GET['id'] ?? 0;
if (!$quiz_id || !is_numeric($quiz_id)) {
    alert("آزمون نامعتبر است.");
    redirect('list.php');
}

$stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();
if (!$quiz) {
    alert("آزمون یافت نشد.");
    redirect('list.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description'] ?? '');

    if ($title) {
        $stmt = $conn->prepare("UPDATE quizzes SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $desc, $quiz_id]);
        alert("آزمون با موفقیت ویرایش شد.", "success");
        redirect("list.php");
    } else {
        alert("عنوان الزامی است.");
    }
}
?>

<div class="container py-4" style="max-width: 700px;">
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-pencil-square text-primary me-2" style="font-size: 1.8rem;"></i>
        <h2 class="mb-0 fw-bold text-primary">ویرایش آزمون</h2>
    </div>

    <div class="card-quiet p-4 p-md-5">
        <form method="POST">
            <div class="mb-4">
                <label class="form-label fw-semibold d-block">
                    عنوان آزمون <span class="text-danger">*</span>
                </label>
                <input type="text" name="title" class="form-control form-control-lg" required 
                       value="<?= htmlspecialchars($quiz['title']) ?>" 
                       placeholder="عنوان آزمون را وارد کنید">
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold d-block">توضیحات (اختیاری)</label>
                <textarea name="description" class="form-control" rows="4" 
                          placeholder="توضیح مختصری درباره آزمون..."><?= htmlspecialchars($quiz['description'] ?? '') ?></textarea>
            </div>

            <hr class="my-4">

            <div class="d-flex flex-wrap gap-3">
                <button type="submit" class="btn btn-success btn-pill px-5">
                    ذخیره تغییرات
                </button>
                <a href="<?= url("quiz/list.php") ?>" class="btn btn-outline-secondary btn-pill px-4">
                    انصراف
                </a>
            </div>
        </form>
    </div>

    <div class="text-center mt-4 text-muted small">
        <i class="bi bi-info-circle"></i> پس از ویرایش، به لیست آزمون‌ها بازمی‌گردید.
    </div>
</div>

<?php require '../function/footer.php'; ?>