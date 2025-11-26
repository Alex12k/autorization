<?php
/**
 * AJAX обработчик для компонента seed-users
 * Обрабатывает все AJAX запросы, связанные с генерацией тестовых пользователей
 */

// Загрузка зависимостей
if (!defined('SYSTEM_INITIALIZED')) {
    require_once __DIR__ . '/../../../config.php';
    require_once __DIR__ . '/../../../functions.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    define('SYSTEM_INITIALIZED', true);
}

// Подключаем функции генерации
require_once __DIR__ . '/../functions.php';

// Проверяем, что это AJAX запрос
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$is_ajax = $is_ajax || (isset($_POST['ajax']) && $_POST['ajax'] === '1');

if (!$is_ajax) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Этот endpoint предназначен только для AJAX запросов'
    ]);
    exit;
}

// Получаем действие
$action = $_POST['action'] ?? '';

// Обработка запроса на открытие формы генерации пользователей
if ($action === 'open_modal_seed_users') {
    header('Content-Type: text/html; charset=utf-8');
    require_once __DIR__ . '/../seed_users.php';
    modal_seed_users();
    exit;
}

// Обработка запроса на генерацию пользователей
if ($action === 'seed_users') {
    header('Content-Type: application/json; charset=utf-8');
    
    // Увеличиваем лимиты для больших объемов
    set_time_limit(300); // 5 минут
    ini_set('memory_limit', '512M');
    
    $amount = isset($_POST['amount']) ? (int)$_POST['amount'] : 0;
    
    if ($amount <= 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Неверное количество пользователей'
        ]);
        exit;
    }
    
    $result = seedUsers($amount);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// Неизвестное действие
echo json_encode([
    'success' => false,
    'error' => 'Неизвестное действие'
]);
exit;

