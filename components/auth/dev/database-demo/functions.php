<?php
/**
 * Функции для генерации тестовых пользователей
 */

/**
 * Генерация случайного имени пользователя
 */
function generateRandomUsername(int $index): string
{
    $names = ['Иван', 'Петр', 'Мария', 'Анна', 'Алексей', 'Дмитрий', 'Елена', 'Ольга', 'Сергей', 'Наталья'];
    $surnames = ['Иванов', 'Петров', 'Сидоров', 'Козлов', 'Новиков', 'Морозов', 'Попов', 'Соколов', 'Лебедев', 'Козлов'];
    
    $name = $names[array_rand($names)];
    $surname = $surnames[array_rand($surnames)];
    
    return strtolower(transliterate($name . $surname)) . $index;
}

/**
 * Генерация случайного email
 */
function generateRandomEmail(int $index): string
{
    $domains = ['example.com', 'test.com', 'demo.org', 'sample.net', 'mail.ru'];
    $domain = $domains[array_rand($domains)];
    
    return 'user' . $index . '@' . $domain;
}

/**
 * Простая транслитерация для генерации username
 */
function transliterate(string $text): string
{
    $translit = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
    ];
    
    $text = mb_strtolower($text, 'UTF-8');
    $result = '';
    
    for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++) {
        $char = mb_substr($text, $i, 1, 'UTF-8');
        $result .= $translit[$char] ?? $char;
    }
    
    return $result;
}

/**
 * Генерация тестовых пользователей с batch-вставками
 * @param int $amount Количество пользователей для создания
 * @return array Результат операции
 */
function seedUsers(int $amount): array
{
    try {
        // Проверка прав доступа
        if (!isAuthenticated() || !hasRole('admin')) {
            return [
                'success' => false,
                'error' => 'Доступ запрещен. Требуются права администратора.'
            ];
        }
        
        // Валидация количества
        if ($amount < 1 || $amount > 100000) {
            return [
                'success' => false,
                'error' => 'Количество должно быть от 1 до 100,000'
            ];
        }
        
        $pdo = getDB();
        
        // Определяем размер batch (оптимально для SQLite: 100-500)
        $batchSize = min(500, max(100, (int)($amount / 10)));
        
        // Хеш пароля (одинаковый для всех тестовых пользователей)
        $passwordHash = password_hash('password123', PASSWORD_DEFAULT);
        
        // Определяем роли (90% user, 10% admin)
        $roles = ['user', 'user', 'user', 'user', 'user', 'user', 'user', 'user', 'user', 'admin'];
        
        // Получаем начальное количество пользователей
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $initialCount = (int)$stmt->fetchColumn();
        
        // Начинаем транзакцию
        $pdo->beginTransaction();
        
        $errors = 0;
        $batches = ceil($amount / $batchSize);
        
        // Получаем текущее максимальное значение для уникальности
        $stmt = $pdo->query("SELECT COALESCE(MAX(CAST(id AS INTEGER)), 0) FROM users");
        $maxId = (int)$stmt->fetchColumn();
        $startIndex = $maxId + 1;
        
        for ($batch = 0; $batch < $batches; $batch++) {
            $batchStart = $batch * $batchSize;
            $batchEnd = min($batchStart + $batchSize, $amount);
            $batchAmount = $batchEnd - $batchStart;
            
            // Подготавливаем данные для batch
            $values = [];
            $placeholders = [];
            
            for ($i = 0; $i < $batchAmount; $i++) {
                $index = $startIndex + $batchStart + $i;
                $username = generateRandomUsername($index);
                $email = generateRandomEmail($index);
                $role = $roles[array_rand($roles)];
                
                $values[] = $username;
                $values[] = $email;
                $values[] = $passwordHash;
                $values[] = $role;
                
                // SQLite и PostgreSQL используют разные синтаксисы для множественной вставки
                if (USE_POSTGRESQL) {
                    $placeholders[] = "(?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
                } else {
                    $placeholders[] = "(?, ?, ?, ?, datetime('now'), datetime('now'))";
                }
            }
            
            // Формируем SQL запрос
            if (USE_POSTGRESQL) {
                $sql = "INSERT INTO users (username, email, password_hash, role, created_at, updated_at) VALUES " . 
                       implode(', ', $placeholders) . 
                       " ON CONFLICT (username) DO NOTHING";
            } else {
                // SQLite не поддерживает ON CONFLICT DO NOTHING в старых версиях, используем INSERT OR IGNORE
                $sql = "INSERT OR IGNORE INTO users (username, email, password_hash, role, created_at, updated_at) VALUES " . 
                       implode(', ', $placeholders);
            }
            
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute($values);
            } catch (PDOException $e) {
                // Логируем ошибку, но продолжаем
                error_log("Batch insert error: " . $e->getMessage());
                $errors++;
            }
        }
        
        // Коммитим транзакцию
        $pdo->commit();
        
        // Получаем финальное количество пользователей (после commit)
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $finalCount = (int)$stmt->fetchColumn();
        $created = $finalCount - $initialCount;
        
        return [
            'success' => true,
            'created' => $created,
            'requested' => $amount,
            'errors' => $errors,
            'message' => "Успешно создано $created из $amount пользователей"
        ];
        
    } catch (Exception $e) {
        // Откатываем транзакцию в случае ошибки
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        return [
            'success' => false,
            'error' => 'Ошибка при создании пользователей: ' . $e->getMessage()
        ];
    }
}

