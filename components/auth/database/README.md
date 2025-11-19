# Database Directory

Эта директория содержит всё, что связано с базой данных проекта.

## Структура

```
database/
├── config.php          # Конфигурация БД (PostgreSQL/SQLite)
├── connection.php      # Функция подключения к БД
├── init.php           # Инициализация и миграции БД
├── init_database.sql  # SQL схема для ручной установки
├── database.sqlite    # Файл базы данных SQLite (не в git)
└── README.md          # Этот файл
```

## Файлы

### config.php
Содержит настройки подключения к базе данных:
- PostgreSQL (хост, порт, имя БД, пользователь, пароль)
- SQLite (путь к файлу БД)
- Флаг выбора БД (`USE_POSTGRESQL`)

### connection.php
Функция `getDatabaseConnection()` для получения PDO соединения:
- Автоматически выбирает PostgreSQL или SQLite
- Настройки PRAGMA для SQLite (WAL режим, таймауты)
- Обработка ошибок подключения

### init.php
Функция `initializeDatabase()` для инициализации БД:
- Создание таблиц (users, password_reset_tokens)
- Создание индексов
- Вставка демо пользователей (admin/admin123, user/user123)
- Автоматическая инициализация при подключении

### init_database.sql
SQL скрипт для ручного создания структуры БД:
- Можно использовать для PostgreSQL
- Содержит полную схему с комментариями

### database.sqlite
Файл базы данных SQLite (создается автоматически):
- **НЕ коммитится в git** (.gitignore)
- Используется по умолчанию для разработки
- WAL режим для лучшей параллельности

## Использование

### В коде проекта

```php
// Подключение выполняется автоматически через components/auth/config.php
require_once 'components/auth/config.php';
// или если вызывается из компонента auth:
require_once __DIR__ . '/../config.php';

// Получение соединения
$pdo = getDatabaseConnection();

// Использование
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();
```

### Переключение между БД

Отредактируйте `database/config.php`:

```php
// Использовать SQLite (по умолчанию)
define('USE_POSTGRESQL', false);

// Использовать PostgreSQL
define('USE_POSTGRESQL', true);
```

### Сброс базы данных

Для SQLite:
```bash
rm components/auth/database/database.sqlite
# БД пересоздастся автоматически при следующем запросе
```

Для PostgreSQL:
```bash
psql -U your_user -d php_auth_demo -f components/auth/database/init_database.sql
```

## Безопасность

- `components/auth/database/database.sqlite` добавлен в `.gitignore`
- Пароли хешируются через `password_hash()`
- Используются prepared statements для защиты от SQL injection
- CSRF токены для защиты форм

## Миграции

При изменении структуры БД:
1. Обновите `init.php` (функция `initializeDatabase()`)
2. Обновите `init_database.sql` (SQL схема)
3. Создайте миграцию для продакшн БД (вручную)

## Демо пользователи

После инициализации создаются:
- **admin** / admin123 (роль: admin)
- **user** / user123 (роль: user)

