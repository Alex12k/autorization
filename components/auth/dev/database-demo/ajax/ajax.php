<?php
/**
 * AJAX обработчик для компонента database-demo
 * Обрабатывает все AJAX запросы, связанные с демонстрацией PostgreSQL
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

// Обработка запроса на открытие демонстрации PostgreSQL в модальном окне
if ($action === 'open_modal_database_demo') {
    header('Content-Type: text/html; charset=utf-8');
    require_once __DIR__ . '/../database_demo.php';
    modal_database_demo();
    exit;
}

// Неизвестное действие
echo json_encode([
    'success' => false,
    'error' => 'Неизвестное действие'
]);
exit;

