<?php require 'function/header.php'; ?>

<div class="text-center my-5">
    <h1 class="display-5 fw-bold text-primary">به آزمون‌ساز خوش آمدید</h1>
    <p class="text-muted lead">سریع، ساده و زیبا — آزمون بساز، سوال اضافه کن و شرکت‌کنندگان را ارزیابی کن.</p>
</div>

<div class="row g-5 justify-content-center">
    <div class="col-md-5">
        <a href="<?= url('quiz/create.php') ?>" class="text-decoration-none text-reset">
            <div class="card-quiet feature-card p-5 text-center">
                <div class="mb-4">
                    <i class="bi bi-pencil-square text-primary"></i>
                </div>
                <h4 class="mb-3 text-primary fw-bold">طراحی آزمون</h4>
                <p class="text-muted mb-4">ایجاد آزمون جدید، افزودن سوال و گزینه‌ها</p>
                <span class="btn btn-primary btn-pill px-5">شروع طراحی</span>
            </div>
        </a>
    </div>

    <div class="col-md-5">
        <a href="<?= url('quiz/list.php') ?>" class="text-decoration-none text-reset">
            <div class="card-quiet feature-card p-5 text-center">
                <div class="mb-4">
                    <i class="bi bi-check2-square text-success"></i>
                </div>
                <h4 class="mb-3 text-success fw-bold">آزمون‌ها</h4>
                <p class="text-muted mb-4">مشاهده، ویرایش و شرکت در آزمون‌ها</p>
                <span class="btn btn-success btn-pill px-5">مشاهده آزمون‌ها</span>
            </div>
        </a>
    </div>
</div>

<div class="text-center mt-5 text-muted">
    <small>طراحی‌شده با <i class="bi bi-heart-fill text-danger"></i> توسط <a href="https://sobhancv.ir/">سبحان امینی مقدم</a></small>
</div>

<?php require 'function/footer.php'; ?>