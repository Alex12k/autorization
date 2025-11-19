<?php
/**
 * Front Controller - Единая точка входа в приложение
 * Все запросы проходят через этот файл
 */

// Инициализация системы
require_once 'components/auth/init.php';
require_once 'functions/route.php';

// Определяем, является ли запрос API запросом
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
$script_name = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
if ($script_name !== '/') {
    $request_uri = substr($request_uri, strlen($script_name));
}
$request_uri = strtok($request_uri, '?');
$request_uri = trim($request_uri, '/');

// Если это API запрос, не выводим HTML layout
if ($request_uri === 'api') {
    route();
    exit;
}

// Отрисовка header для обычных страниц
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP 8.4 Система</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('style.css') ?>">
</head>
<body class="bg-gray-50 min-h-screen">

<?php
// Все запросы обрабатываются через роутер
route();
?>

<footer class="bg-gray-800 text-white py-6 mt-12">
    <div class="container mx-auto px-4 text-center">
        <p class="text-gray-300">
            <i class="ri-heart-line text-red-500"></i>
            Создано с PHP 8.4 и Tailwind CSS
        </p>
    </div>
</footer>


<script src="<?= url('components/auth/auth-ajax-handler.js') ?>"></script>
<script src="<?= url('components/auth/login/script.js') ?>"></script>
<script src="<?= url('components/auth/register/script.js') ?>"></script>
<script src="<?= url('components/auth/forgot-password/script.js') ?>"></script>
<script src="<?= url('components/auth/reset-password/script.js') ?>"></script>
<script src="<?= url('components/auth/logout/script.js') ?>"></script>
</body>
</html>
