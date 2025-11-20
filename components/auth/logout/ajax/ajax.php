<?php
/**
 * AJAX обработчик для компонента выхода из системы
 * Обрабатывает все AJAX запросы, связанные с выходом из системы
 */

// Загрузка зависимостей
if (!defined('SYSTEM_INITIALIZED')) {
    require_once __DIR__ . '/../../config.php';
    require_once __DIR__ . '/../../functions.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    define('SYSTEM_INITIALIZED', true);
}

// Загрузка функций компонента logout
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

// Обработка выхода из системы
if ($action === 'logout') {
    // Устанавливаем заголовок для JSON ответов
    header('Content-Type: application/json');
    
    // Очищаем все данные сессии перед logout
    $_SESSION = [];
    
    // Выход из системы
    logout();
    
    // Убеждаемся, что сессия полностью очищена (на случай если logout не сработал)
    $_SESSION = [];
    if (isset($_SESSION['authenticated'])) {
        unset($_SESSION['authenticated']);
    }
    if (isset($_SESSION['user_id'])) {
        unset($_SESSION['user_id']);
    }
    if (isset($_SESSION['username'])) {
        unset($_SESSION['username']);
    }
    if (isset($_SESSION['email'])) {
        unset($_SESSION['email']);
    }
    if (isset($_SESSION['role'])) {
        unset($_SESSION['role']);
    }
    if (isset($_SESSION['login_time'])) {
        unset($_SESSION['login_time']);
    }
    
    // Успешный выход - редирект на главную с параметром для предотвращения редиректа
    echo json_encode([
        'success' => true,
        'redirect' => '/?logout=1'
    ]);
    exit;
}

// Неизвестное действие
echo json_encode([
    'success' => false,
    'error' => 'Неизвестное действие'
]);
exit;


