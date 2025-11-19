<?php
/**
 * Функции для подключения к базе данных
 */

/**
 * Получение PDO соединения с базой данных
 * @return PDO
 * @throws Exception
 */
function getDatabaseConnection(): PDO
{
    if (USE_POSTGRESQL) {
        // PostgreSQL соединение
        $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            return new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            throw new Exception("Ошибка подключения к PostgreSQL: " . $e->getMessage());
        }
    } else {
        // SQLite соединение
        $dsn = "sqlite:" . SQLITE_PATH;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 30, // Таймаут ожидания блокировки 30 секунд
        ];
        
        try {
            $pdo = new PDO($dsn, null, null, $options);
            
            // Настройки SQLite для предотвращения блокировок
            $pdo->exec('PRAGMA journal_mode = WAL;'); // Write-Ahead Logging для лучшей параллельности
            $pdo->exec('PRAGMA busy_timeout = 30000;'); // 30 секунд ожидания при блокировке
            $pdo->exec('PRAGMA synchronous = NORMAL;'); // Баланс между безопасностью и скоростью
            $pdo->exec('PRAGMA cache_size = 10000;'); // Увеличиваем кеш
            $pdo->exec('PRAGMA temp_store = MEMORY;'); // Временные данные в памяти
            
            return $pdo;
        } catch (PDOException $e) {
            throw new Exception("Ошибка подключения к SQLite: " . $e->getMessage());
        }
    }
}

