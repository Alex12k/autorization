<?php
/**
 * Скрипт для очистки всех cookies и сессий
 * Используйте этот файл для полной очистки сессий и cookies
 */

session_start();

// Очищаем все данные сессии
$_SESSION = [];

// Удаляем cookie сессии, если она существует
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Уничтожаем сессию
session_destroy();

// Удаляем все cookies, связанные с доменом
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time() - 3600, '/');
        setcookie($name, '', time() - 3600, '/', $_SERVER['HTTP_HOST']);
    }
}

// Начинаем новую сессию
session_start();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Очистка сессий и cookies</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .container {
            background: white;
            color: #333;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            text-align: center;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #c3e6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #bee5eb;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .button:hover {
            background: #5568d3;
        }
        .button-container {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Очистка завершена</h1>
        
        <div class="success">
            <strong>Все cookies и сессии успешно очищены!</strong>
        </div>
        
        <div class="info">
            <h3>Что было сделано:</h3>
            <ul>
                <li>✅ Очищены все данные сессии PHP</li>
                <li>✅ Удалены все cookies сессии</li>
                <li>✅ Удалены все cookies домена</li>
                <li>✅ Сессия уничтожена и пересоздана</li>
            </ul>
        </div>
        
        <div class="info">
            <h3>Статус сессии:</h3>
            <p><strong>ID сессии:</strong> <?= session_id() ?></p>
            <p><strong>Данные сессии:</strong> <?= empty($_SESSION) ? 'Пусто' : print_r($_SESSION, true) ?></p>
        </div>
        
        <div class="button-container">
            <a href="/" class="button">Перейти на главную страницу</a>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 12px;">
            <p>После очистки вы будете автоматически разлогинены.</p>
            <p>Для входа используйте демо аккаунты: admin / admin123 или user / user123</p>
        </div>
    </div>
    
    <script>
        // Автоматический редирект на главную через 3 секунды
        setTimeout(function() {
            window.location.href = '/';
        }, 3000);
    </script>
</body>
</html>

