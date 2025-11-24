<?php
/**
 * AJAX обработчик для компонента edit-profile
 * Обрабатывает все AJAX запросы, связанные с редактированием профиля
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

// Обработка запроса на открытие формы редактирования профиля в модальном окне
if ($action === 'open_modal_edit_profile_form') {
    header('Content-Type: text/html; charset=utf-8');
    require_once __DIR__ . '/../edit_profile.php';
    modal_edit_profile_form();
    exit;
}

// Обработка отправки формы редактирования профиля
if ($action === 'edit-profile') {
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
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Валидация (переиспользуем логику из updateUserProfile)
    if (empty($username) || empty($email)) {
        echo json_encode([
            'success' => false,
            'error' => 'Все поля обязательны для заполнения'
        ]);
        exit;
    }
    
    if (!validateEmail($email)) {
        echo json_encode([
            'success' => false,
            'error' => 'Некорректный email адрес'
        ]);
        exit;
    }
    
    // Обновление профиля (переиспользуем существующую функцию)
    $result = updateUserProfile($user_id, $username, $email);
    
    if ($result['success']) {
        // Успешное обновление профиля
        echo json_encode([
            'success' => true,
            'message' => 'Профиль успешно обновлен'
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

