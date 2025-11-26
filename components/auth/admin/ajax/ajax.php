<?php
/**
 * AJAX обработчик для компонента admin
 * Обрабатывает все AJAX запросы, связанные с управлением пользователями
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

// Загрузка функций компонента admin
require_once __DIR__ . '/../functions.php';

// Включаем буферизацию вывода для предотвращения лишнего вывода
ob_start();

// Отключаем вывод ошибок в браузер (только в лог)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Очищаем буфер перед установкой заголовков
ob_clean();

// Устанавливаем заголовок JSON
header('Content-Type: application/json; charset=utf-8');

// Проверяем, что запрос методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Метод не разрешен']);
    exit;
}

// Проверяем аутентификацию
if (!isAuthenticated()) {
    http_response_code(401);
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Не авторизован']);
    exit;
}

// Проверяем права администратора
if (!hasRole('admin')) {
    http_response_code(403);
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Недостаточно прав']);
    exit;
}

// Получаем данные из запроса
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

// Обрабатываем различные действия
switch ($action) {
    case 'update_user':
        handleUpdateUser($data);
        break;
        
    case 'delete_user':
        handleDeleteUser($data);
        break;
        
    case 'get_users':
        handleGetUsers($data);
        break;
        
    default:
        http_response_code(400);
        ob_end_clean();
        echo json_encode(['success' => false, 'error' => 'Неизвестное действие']);
        exit;
}

/**
 * Обновление пользователя
 */
function handleUpdateUser($data) {
    $user_id = (int)($data['user_id'] ?? 0);
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $role = $data['role'] ?? '';
    $csrf_token = $data['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        http_response_code(403);
        ob_end_clean();
        echo json_encode(['success' => false, 'error' => 'Ошибка безопасности']);
        exit;
    }
    
    $result = updateUserByAdmin($user_id, $username, $email, $role);
    
    if ($result['success']) {
        http_response_code(200);
    } else {
        http_response_code(400);
    }
    
    ob_end_clean();
    echo json_encode($result);
    exit;
}

/**
 * Удаление пользователя
 */
function handleDeleteUser($data) {
    $user_id = (int)($data['user_id'] ?? 0);
    $csrf_token = $data['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        http_response_code(403);
        ob_end_clean();
        echo json_encode(['success' => false, 'error' => 'Ошибка безопасности']);
        exit;
    }
    
    $result = deleteUser($user_id);
    
    if ($result['success']) {
        http_response_code(200);
    } else {
        http_response_code(400);
    }
    
    ob_end_clean();
    echo json_encode($result);
    exit;
}

/**
 * Получение списка пользователей с фильтрами и пагинацией
 */
function handleGetUsers($data) {
    $options = [
        'search' => $data['search'] ?? '',
        'role' => $data['role'] ?? '',
        'sort' => $data['sort'] ?? 'created_desc',
        'limit' => $data['limit'] ?? 50,
        'offset' => $data['offset'] ?? 0,
    ];

    $result = getUsersWithFilters($options);
    $users = $result['users'] ?? [];
    $total = $result['total'] ?? 0;

    $limit = max(1, (int)$options['limit']);
    $offset = max(0, (int)$options['offset']);
    $hasMore = ($offset + $limit) < $total;

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'users' => $users,
        'total' => $total,
        'has_more' => $hasMore,
    ]);
    exit;
}

