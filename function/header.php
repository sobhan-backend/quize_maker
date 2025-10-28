<?php
session_start();
require_once 'db.php';
require_once 'helper.php';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>آزمون‌ساز</title>
    <link rel="icon" type="image/png" href="<?= assets('img/ico.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.0/Vazirmatn.css" rel="stylesheet">
    <link href="<?= assets('css/style.css') ?>" rel="stylesheet">
    
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm" style="backdrop-filter: blur(10px); margin-bottom: 16px;">
    <div class="container position-relative">
        <!-- لوگو -->
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= url() ?>">
            آزمون‌ساز
        </a>

        <!-- دکمه‌های ناوبری -->
        <div class="d-flex gap-3">
            <a href="<?= url() ?>" 
               class="btn btn-outline-light btn-sm rounded-pill px-4 py-2 d-flex align-items-center gap-2 transition-all">
               <span class="d-none d-md-inline">خانه</span>
                <i class="bi bi-house"></i>
            </a>
            <a href="<?= url('quiz/list.php') ?>" 
               class="btn btn-outline-light btn-sm rounded-pill px-4 py-2 d-flex align-items-center gap-2 transition-all">
               <span class="d-none d-md-inline">آزمون‌ها</span>
               <i class="bi bi-journal-text"></i>
            </a>
        </div>
    </div>
</nav>
    <div class="container">
        <?php showAlert(); ?>