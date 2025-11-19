<?php
/**
 * AJAX обработчик для компонента логина
 * Обрабатывает все AJAX запросы, связанные с входом в систему
 */

// Загрузка зависимостей
if (!defined('SYSTEM_INITIALIZED')) {
    require_once __DIR__ . '/../../../config.php';
    require_once __DIR__ . '/../../../functions/functions.php';
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

// Обработка запроса на открытие формы логина
if ($action === 'open_login') {
    // Для HTML ответа устанавливаем соответствующий заголовок
    header('Content-Type: text/html; charset=utf-8');
    
    // Загружаем функцию login() для отображения формы
    require_once __DIR__ . '/../login.php';
    
    // Временно очищаем POST данные, чтобы login() не обрабатывал их
    $original_post = $_POST;
    $_POST = [];
    
    // Захватываем вывод функции login()
    ob_start();
    login();
    $form_html = ob_get_clean();
    
    // Восстанавливаем POST данные
    $_POST = $original_post;
    
    // Возвращаем HTML формы
    echo $form_html;
    exit;
}

// Обработка отправки формы логина
if ($action === 'login') {
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
    
    // Получение данных формы
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Аутентификация пользователя
    $result = authenticateUser($username, $password);
    
    if ($result['success']) {
        // Успешный вход
        echo json_encode([
            'success' => true,
            'message' => 'Успешный вход в систему',
            'redirect' => url('dashboard')
        ]);
        exit;
    } else {
        // Ошибка аутентификации
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

