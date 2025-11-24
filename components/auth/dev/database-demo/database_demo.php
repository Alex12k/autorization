<?php
/**
 * Dev-инструмент: Демонстрация PostgreSQL
 * Показывает возможности работы с базой данных PostgreSQL
 */

// Загрузка зависимостей (если файл вызывается напрямую)
if (!defined('SYSTEM_INITIALIZED')) {
    require_once __DIR__ . '/../../config.php';
    require_once __DIR__ . '/../../functions.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    define('SYSTEM_INITIALIZED', true);
}

/**
 * Функция отображения демонстрации PostgreSQL в модальном окне
 */
function modal_database_demo(): void
{
    ?>
    <div class="box-modal database-demo-modal !w-[95%] lg:!w-[1280px] !max-w-[95%] lg:!max-w-[1280px]">
        <!-- Заголовок модального окна -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="ri-database-2-line text-2xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">PHP + PostgreSQL</h2>
                    <p class="text-sm text-gray-500">Демонстрация работы с базой данных</p>
                </div>
            </div>
            <button class="arcticmodal-close text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-full">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <!-- Содержимое -->
        <div class="space-y-6 max-h-[70vh] overflow-y-auto">
            <!-- Проверка расширений -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="ri-check-line text-3xl text-green-500 mr-3"></i>
                    <h3 class="text-xl font-semibold">Проверка расширений</h3>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <?php
                    $extensions = [
                        'pdo' => 'PDO (PHP Data Objects)',
                        'pdo_pgsql' => 'PDO PostgreSQL',
                        'pgsql' => 'PostgreSQL',
                        'json' => 'JSON',
                        'mbstring' => 'Multibyte String'
                    ];
                    
                    foreach ($extensions as $ext => $name) {
                        $loaded = extension_loaded($ext);
                        $status = $loaded ? 'Подключено' : 'Не подключено';
                        $color = $loaded ? 'text-green-600' : 'text-red-600';
                        $icon = $loaded ? 'ri-check-line' : 'ri-close-line';
                        $iconColor = $loaded ? 'text-green-500' : 'text-red-500';
                        
                        echo "<div class='flex items-center justify-between p-3 bg-gray-50 rounded'>
                                <div class='flex items-center'>
                                    <i class='$icon $iconColor mr-2'></i>
                                    <span class='font-medium'>$name</span>
                                </div>
                                <span class='$color font-semibold'>$status</span>
                              </div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Демонстрация работы с JSON -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="ri-file-text-line text-3xl text-blue-500 mr-3"></i>
                    <h3 class="text-xl font-semibold">Работа с JSON</h3>
                </div>
                <div class="space-y-4">
                    <?php
                    // Создаем JSON данные
                    $userData = [
                        'name' => 'Иван Петров',
                        'age' => 28,
                        'skills' => ['PHP', 'JavaScript', 'PostgreSQL'],
                        'active' => true,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $jsonString = json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    $decodedData = json_decode($jsonString, true);
                    
                    echo "<div class='bg-gray-50 p-4 rounded'>
                            <h4 class='font-semibold mb-2'>Исходные данные:</h4>
                            <pre class='text-xs text-gray-700 overflow-x-auto'>" . htmlspecialchars(print_r($userData, true)) . "</pre>
                          </div>";
                    
                    echo "<div class='bg-gray-50 p-4 rounded'>
                            <h4 class='font-semibold mb-2'>JSON строка:</h4>
                            <pre class='text-xs text-gray-700 overflow-x-auto'>" . htmlspecialchars($jsonString) . "</pre>
                          </div>";
                    
                    echo "<div class='bg-gray-50 p-4 rounded'>
                            <h4 class='font-semibold mb-2'>Декодированные данные:</h4>
                            <pre class='text-xs text-gray-700 overflow-x-auto'>" . htmlspecialchars(print_r($decodedData, true)) . "</pre>
                          </div>";
                    ?>
                </div>
            </div>

            <!-- Демонстрация SQL запросов -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="ri-code-s-slash-line text-3xl text-purple-500 mr-3"></i>
                    <h3 class="text-xl font-semibold">SQL Запросы (Демо)</h3>
                </div>
                <div class="space-y-4">
                    <?php
                    // Демонстрация SQL запросов без реального подключения
                    $queries = [
                        'CREATE TABLE users (id SERIAL PRIMARY KEY, name VARCHAR(100), email VARCHAR(100))',
                        'INSERT INTO users (name, email) VALUES (\'Иван Петров\', \'ivan@example.com\')',
                        'SELECT * FROM users WHERE name LIKE \'%Иван%\'',
                        'UPDATE users SET email = \'new@example.com\' WHERE id = 1',
                        'DELETE FROM users WHERE id = 1'
                    ];
                    
                    foreach ($queries as $index => $query) {
                        $type = match(true) {
                            str_starts_with($query, 'CREATE') => 'DDL',
                            str_starts_with($query, 'INSERT') => 'DML',
                            str_starts_with($query, 'SELECT') => 'DQL',
                            str_starts_with($query, 'UPDATE') => 'DML',
                            str_starts_with($query, 'DELETE') => 'DML',
                            default => 'SQL'
                        };
                        
                        $color = match($type) {
                            'DDL' => 'text-blue-600',
                            'DML' => 'text-green-600',
                            'DQL' => 'text-purple-600',
                            default => 'text-gray-600'
                        };
                        
                        echo "<div class='flex items-start space-x-3 p-3 bg-gray-50 rounded'>
                                <span class='px-2 py-1 text-xs font-semibold bg-white rounded $color'>$type</span>
                                <code class='text-sm flex-1 overflow-x-auto'>" . htmlspecialchars($query) . "</code>
                              </div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

