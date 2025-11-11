<?php
// Конфигурация базы данных

// Настройки для PostgreSQL
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'php_auth_demo');
define('DB_USER', get_current_user()); // Текущий пользователь системы
define('DB_PASS', ''); // Пустой пароль для локальной разработки

// Настройки для SQLite (резервный вариант)
define('SQLITE_PATH', __DIR__ . '/database.sqlite');

// Определяем, какую базу данных использовать
define('USE_POSTGRESQL', false); // Пока используем SQLite для простоты

// Настройки безопасности
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_LIFETIME', 3600); // 1 час

// Настройки приложения
define('APP_NAME', 'PHP 8.4 Auth Demo');
define('APP_VERSION', '1.0.0');

// Функция для получения PDO соединения
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

// Функция для инициализации базы данных
function initializeDatabase(): void
{
    try {
        $pdo = getDatabaseConnection();
        
        if (USE_POSTGRESQL) {
            // SQL для PostgreSQL
            $sql = "
                CREATE TABLE IF NOT EXISTS users (
                    id SERIAL PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    email VARCHAR(100) UNIQUE NOT NULL,
                    password_hash VARCHAR(255) NOT NULL,
                    role VARCHAR(20) DEFAULT 'user' NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
                
                CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
                CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
                CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
            ";
        } else {
            // SQL для SQLite
            $sql = "
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT UNIQUE NOT NULL,
                    email TEXT UNIQUE NOT NULL,
                    password_hash TEXT NOT NULL,
                    role TEXT DEFAULT 'user' NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
                
                CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
                CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
                CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
            ";
        }
        
        $pdo->exec($sql);
        
        // Вставляем демо пользователей, если их еще нет
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            $users = [
                ['admin', 'admin@example.com', password_hash('admin123', PASSWORD_DEFAULT), 'admin'],
                ['user', 'user@example.com', password_hash('user123', PASSWORD_DEFAULT), 'user']
            ];
            
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password_hash, role) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($users as $user) {
                $stmt->execute($user);
            }
        }
        
    } catch (Exception $e) {
        throw new Exception("Ошибка инициализации базы данных: " . $e->getMessage());
    }
}

// Автоматическая инициализация при подключении
try {
    initializeDatabase();
} catch (Exception $e) {
    // Логируем ошибку, но не останавливаем выполнение
    error_log("Database initialization error: " . $e->getMessage());
}
