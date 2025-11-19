<?php
/**
 * AJAX обработчик для компонента выхода из системы
 * Обрабатывает все AJAX запросы, связанные с выходом из системы
 */

// Загрузка зависимостей
if (!defined('SYSTEM_INITIALIZED')) {
    require_once __DIR__ . '/../../../config.php';
    require_once __DIR__ . '/../../functions.php';
    require_once __DIR__ . '/../../../functions/layout.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    define('SYSTEM_INITIALIZED', true);
}

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

// Обработка выхода из системы
if ($action === 'logout') {
    // Устанавливаем заголовок для JSON ответов
    header('Content-Type: application/json');
    
    // Выход из системы
    logout();
    
    // Успешный выход
    echo json_encode([
        'success' => true,
        'redirect' => url()
    ]);
    exit;
}

// Неизвестное действие
echo json_encode([
    'success' => false,
    'error' => 'Неизвестное действие'
]);
exit;


