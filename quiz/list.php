<?php require '../function/header.php'; ?>

<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">لیست آزمون‌ها</h2>

    <div class="row g-4">
        <?php
        $stmt = $conn->query("SELECT q.*, COUNT(qu.id) as question_count 
                             FROM quizzes q 
                             LEFT JOIN questions qu ON qu.quiz_id = q.id 
                             GROUP BY q.id 
                             ORDER BY q.created_at DESC");
        $quizzes = $stmt->fetchAll();
        ?>
        <?php foreach ($quizzes as $quiz): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card-quiet p-4 h-100 d-flex flex-column">
                <!-- عنوان و تعداد سوال -->
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <h5 class="mb-0 fw-bold"><?= htmlspecialchars($quiz['title']) ?></h5>
                    <span class="badge bg-light text-dark"><?= $quiz['question_count'] ?> سوال</span>
                </div>

                <!-- توضیحات -->
                <p class="text-muted small mb-4 flex-grow-1">
                    <?= htmlspecialchars($quiz['description'] ?? 'بدون توضیح') ?>
                </p>

                <!-- دکمه‌های اصلی (راست) -->
                <div class="d-flex gap-2 mb-3">
                    <?php if ($quiz['question_count'] > 0): ?>
                    <a href="<?= url("take/start.php?quiz_id={$quiz['id']}") ?>" 
                       class="btn btn-success btn-sm btn-pill flex-grow-1" title="شروع آزمون">
                        <i class="bi bi-play"></i> شروع
                    </a>
                    <?php endif; ?>
                    <a href="<?= url("question/add.php?quiz_id={$quiz['id']}") ?>" 
                       class="btn btn-outline-primary btn-sm btn-pill flex-grow-1" title="افزودن سوال">
                        <i class="bi bi-plus-lg"></i> سوال
                    </a>
                </div>

                <!-- دکمه‌های ویرایش و حذف (چپ، جدا، پایین) -->
                <div class="d-flex gap-2 border-top justify-content-center pt-3 mt-auto">
                    <a href="<?= url("quiz/edit.php?id={$quiz['id']}") ?>" 
                       class="btn btn-outline-warning btn-sm btn-pill" title="ویرایش آزمون">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" action="<?= url("quiz/delete.php") ?>" class="d-inline">
                        <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">
                        <button type="submit" class="btn btn-outline-danger btn-sm btn-pill" 
                                title="حذف آزمون"
                                onclick="return confirm('آیا از حذف «<?= htmlspecialchars($quiz['title']) ?>» مطمئن هستید؟')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- اگر آزمونی وجود نداشت -->
    <?php if (empty($quizzes)): ?>
    <div class="text-center py-5">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <p class="mt-3 text-muted">هنوز آزمونی ایجاد نشده.</p>
        <a href="<?= url('quiz/create.php') ?>" class="btn btn-primary btn-pill px-5">ساخت اولین آزمون</a>
    </div>
    <?php endif; ?>
</div>

<?php require '../function/footer.php'; ?>