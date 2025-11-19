<?php
/**
 * Роутер приложения
 * Обрабатывает входящие запросы и загружает соответствующие страницы
 */

/**
 * Основная функция роутинга
 * Определяет какую страницу загружать на основе URI
 */
function route(): void
{
    // Получение URI и очистка от параметров
    $request_uri = $_SERVER['REQUEST_URI'];
    $script_name = dirname($_SERVER['SCRIPT_NAME']);
    
    // Убираем базовый путь из URI
    if ($script_name !== '/') {
        $request_uri = substr($request_uri, strlen($script_name));
    }
    
    // Убираем GET параметры
    $request_uri = strtok($request_uri, '?');
    
    // Убираем index.php из начала URI, если он там есть
    if (strpos($request_uri, '/index.php') === 0) {
        $request_uri = substr($request_uri, strlen('/index.php'));
    } elseif (strpos($request_uri, 'index.php/') === 0) {
        $request_uri = substr($request_uri, strlen('index.php/'));
    }
    
    // Убираем начальный и конечный слэш
    $request_uri = trim($request_uri, '/');
    
    // Если пустой URI, это точка входа (auth)
    if (empty($request_uri)) {
        $request_uri = 'auth';
    }
    
    /**
     * Карта роутов для template функций (система авторизации)
     * Ключ - URI роута, Значение - ['file' => путь, 'function' => название функции]
     */
    $template_routes = [
        // Аутентификация
        //'login' => ['file' => 'templates/login/login.php', 'function' => 'login'],
        // 'logout' => ['file' => 'templates/logout/logout.php', 'function' => 'logoutPage'],
        // 'register' => ['file' => 'templates/register/register.php', 'function' => 'register'],
        // 'forgot-password' => ['file' => 'templates/forgot-password/forgot_password.php', 'function' => 'forgotPassword'],
         //'reset-password' => ['file' => 'templates/reset-password/reset_password.php', 'function' => 'resetPassword'],
        
        // Приватные страницы
        'dashboard' => ['file' => 'templates/dashboard.php', 'function' => 'dashboard'],
        'admin' => ['file' => 'templates/admin.php', 'function' => 'admin'],
    ];
    
    /**
     * Карта роутов для обычных страниц
     * Ключ - URI роута, Значение - путь к файлу
     */
    $page_routes = [
        // Точка входа приложения
        'auth' => 'pages/auth.php',
        
        // API
        'api' => 'api.php',
        
        // Дополнительные
        'database-demo' => 'pages/database_demo.php',
        'check-session' => 'pages/check_session.php',
        '404' => 'pages/404.php',
    ];
    
    /**
     * Загрузка страницы
     */
    // Проверяем template routes (приоритет)
    if (isset($template_routes[$request_uri])) {
        $route = $template_routes[$request_uri];
        $template_file = $route['file'];
        $function_name = $route['function'];
        
        // Проверяем существование файла
        if (file_exists($template_file)) {
            require_once $template_file;
            
            // Вызываем функцию
            if (function_exists($function_name)) {
                $function_name();
            } else {
                http_response_code(500);
                echo '<h1>500 - Внутренняя ошибка</h1>';
                echo '<p>Функция ' . htmlspecialchars($function_name) . ' не найдена</p>';
            }
        } else {
            // Страница не найдена - файл не существует
            http_response_code(404);
            echo '<h1>404 - Страница не найдена</h1>';
            echo '<p>Файл ' . htmlspecialchars($template_file) . ' не существует</p>';
            echo '<a href="/">Вернуться на главную</a>';
        }
    }
    // Проверяем обычные page routes
    elseif (isset($page_routes[$request_uri])) {
        $page_file = $page_routes[$request_uri];
        
        // Проверяем существование файла
        if (file_exists($page_file)) {
            require $page_file;
        } else {
            http_response_code(404);
            echo '<h1>404 - Страница не найдена</h1>';
            echo '<p>Файл ' . htmlspecialchars($page_file) . ' не существует</p>';
            echo '<a href="/">Вернуться на главную</a>';
        }
    }
    // Роут не найден
    else {
        http_response_code(404);
        if (file_exists('pages/404.php')) {
            require 'pages/404.php';
        } else {
            echo '<h1>404 - Страница не найдена</h1>';
            echo '<p>Запрашиваемый роут не существует</p>';
            echo '<a href="/">Вернуться на главную</a>';
        }
    }
}

/**
 * Добавление нового template роута
 * @param string $uri URI роута
 * @param string $file_path Путь к файлу template
 * @param string $function_name Название функции
 */
function addTemplateRoute(string $uri, string $file_path, string $function_name): void
{
    global $template_routes;
    $template_routes[$uri] = ['file' => $file_path, 'function' => $function_name];
}

/**
 * Добавление нового page роута
 * @param string $uri URI роута
 * @param string $file_path Путь к файлу страницы
 */
function addPageRoute(string $uri, string $file_path): void
{
    global $page_routes;
    $page_routes[$uri] = $file_path;
}
