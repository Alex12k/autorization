<?php
/**
 * AJAX обработчик для компонента change-password
 * Обрабатывает все AJAX запросы, связанные со сменой пароля
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

// Загрузка функций компонента change-password
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

// Обработка запроса на открытие формы смены пароля в модальном окне
if ($action === 'open_modal_change_password_form') {
    header('Content-Type: text/html; charset=utf-8');
    require_once __DIR__ . '/../change_password.php';
    modal_change_password_form();
    exit;
}

// Обработка отправки формы смены пароля
if ($action === 'change-password') {
    // Устанавливаем заголовок для JSON ответов
    header('Content-Type: application/json');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Проверка CSRF токена
    if (!verifyCSRFToken($csrf_token)) {
        echo json_encode([
            'success' => false,
            'error' => 'Ошибка безопасности. Попробуйте еще раз.'
        ]);
        exit;
    }
    
    // Проверка аутентификации
    if (!isAuthenticated()) {
        echo json_encode([
            'success' => false,
            'error' => 'Необходима авторизация'
        ]);
        exit;
    }
    
    // Получение данных формы
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Смена пароля
    $result = changePassword($user_id, $current_password, $new_password, $confirm_password);
    
    if ($result['success']) {
        // Успешная смена пароля
        echo json_encode([
            'success' => true,
            'message' => $result['message']
        ]);
        exit;
    } else {
        // Ошибка
        echo json_encode([
            'success' => false,
            'error' => $result['error']
        ]);
        exit;
    }
}

// Неизвестное действие
echo json_encode([
    'success' => false,
    'error' => 'Неизвестное действие'
]);
exit;

