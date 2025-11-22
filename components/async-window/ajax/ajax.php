<?php
/**
 * AJAX обработчик для компонента async-window
 * Обрабатывает все AJAX запросы, связанные с асинхронными окнами
 */

// Загрузка зависимостей
// if (!defined('SYSTEM_INITIALIZED')) {
//     require_once __DIR__ . '/../../auth/config.php';
//     require_once __DIR__ . '/../../auth/functions.php';
//     if (session_status() === PHP_SESSION_NONE) {
//         session_start();
//     }
//     define('SYSTEM_INITIALIZED', true);
// }


// Загрузка функций компонента async-window
require_once __DIR__ . '/../index.php';


// Обработка запроса на открытие async-window
if ($_POST['action'] === 'open_async-window') {

    async_window();

   
}

