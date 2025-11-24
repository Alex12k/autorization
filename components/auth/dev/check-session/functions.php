<?php
/**
 * Функции компонента check-session
 * Специфичные функции для диагностики и управления сессией
 */

/**
 * Полная очистка сессии и всех cookies
 * Более агрессивная очистка, чем logout()
 */
function clearSession(): array
{
    try {
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
                if (!empty($name)) {
                    setcookie($name, '', time() - 3600, '/');
                    setcookie($name, '', time() - 3600, '/', $_SERVER['HTTP_HOST']);
                }
            }
        }
        
        // Начинаем новую сессию
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return [
            'success' => true,
            'message' => 'Сессия и все cookies успешно очищены'
        ];
        
    } catch (Exception $e) {
        error_log("Clear session error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Ошибка при очистке сессии: ' . $e->getMessage()
        ];
    }
}

