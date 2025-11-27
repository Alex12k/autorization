<?php
/**
 * Front Controller - Единая точка входа в приложение
 * Все запросы проходят через этот файл
 */

// ВАЖНО: Инициализация auth ДО любого вывода HTML
// Инициализация системы auth (сессия должна запуститься первой!)
require_once __DIR__ . '/components/auth/auth.php';

// Подключаем остальные файлы
require_once __DIR__ . '/functions/route_air.php';
require_once __DIR__ . '/pages/home.php';
require_once __DIR__ . '/components/async-window/index.php';
require_once __DIR__ . '/components/header.php';

// Отрисовка header для обычных страниц
?>
<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP 8.4 Система</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        // Конфигурация Tailwind должна быть ДО загрузки библиотеки
        window.tailwindConfig = {
            darkMode: 'class'
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Применяем конфигурацию после загрузки
        if (window.tailwind && window.tailwindConfig) {
            tailwind.config = window.tailwindConfig;
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/assets/arcticmodal/style.css">
</head>
<body class="min-h-screen">




<?php
// Все запросы обрабатываются через роутер
rt();
?>

<footer class="bg-gray-800 text-white py-6">
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
