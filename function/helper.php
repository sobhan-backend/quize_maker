<?php
define('BASE_URL', 'http://localhost/quize_maker-main');

function assets($file) {
    return rtrim(BASE_URL, '/') . '/assets/' . ltrim($file, '/');
}

function url($path = '') {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function redirect($path) {
    header('Location: ' . url($path));
    exit;
}

function dd($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    exit;
}

function old($key, $default = '') {
    return $_POST[$key] ?? $default;
}

function alert($msg, $type = 'danger') {
    $_SESSION['alert'] = ['msg' => $msg, 'type' => $type];
}

function showAlert() {
    if (isset($_SESSION['alert'])) {
        $a = $_SESSION['alert'];
        echo "<div class='alert alert-{$a['type']} alert-dismissible fade show' role='alert'>
                {$a['msg']}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
        unset($_SESSION['alert']);
    }
}