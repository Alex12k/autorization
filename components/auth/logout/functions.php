<?php
/**
 * Функции компонента logout
 * Специфичные функции для выхода из системы
 */

/**
 * Выход пользователя
 */
function logout(): void
{
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
    
    // Начинаем новую сессию для предотвращения ошибок
    session_start();
}

