<?php
/**
 * Front Controller - Единая точка входа в приложение
 * Все запросы проходят через этот файл
 */



require_once 'functions/route_air.php';
require_once 'pages/home.php';
require_once 'components/async-window/index.php';


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
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/assets/arcticmodal/style.css">
</head>
<body class="bg-gray-50 min-h-screen">




<?php
// Все запросы обрабатываются через роутер

// Инициализация системы auth
require_once 'components/auth/auth.php';

rt();
?>
<div class="open_modal_login_form">
    <button class="open_modal_login_form_button">
        <i class="ri-login-box-line"></i>
        Войти
    </button>
</div>

<footer class="bg-gray-800 text-white py-6 mt-12">
    <div class="container mx-auto px-4 text-center">
        <p class="text-gray-300">
            <i class="ri-heart-line text-red-500"></i>
            Создано с PHP 8.4 и Tailwind CSS
        </p>
    </div>
</footer>

<?php auth_scripts(); ?>
<script src="/assets/arcticmodal/script.js"></script>
<script src="/components/async-window/script.js"></script>
</body>
</html>
