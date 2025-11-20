<?php
?>
    <div class="bg-gradient-to-r from-green-500 to-blue-600 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <i class="ri-database-2-line text-6xl mb-4"></i>
                <h1 class="text-4xl font-bold mb-2">PHP + PostgreSQL</h1>
                <p class="text-xl opacity-90">Демонстрация работы с базой данных</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            
            <!-- Проверка расширений -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="ri-check-line text-3xl text-green-500 mr-3"></i>
                    <h2 class="text-xl font-semibold">Проверка расширений</h2>
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
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="ri-file-text-line text-3xl text-blue-500 mr-3"></i>
                    <h2 class="text-xl font-semibold">Работа с JSON</h2>
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
                            <h3 class='font-semibold mb-2'>Исходные данные:</h3>
                            <pre class='text-sm text-gray-700'>" . print_r($userData, true) . "</pre>
                          </div>";
                    
                    echo "<div class='bg-gray-50 p-4 rounded'>
                            <h3 class='font-semibold mb-2'>JSON строка:</h3>
                            <pre class='text-sm text-gray-700'>$jsonString</pre>
                          </div>";
                    
                    echo "<div class='bg-gray-50 p-4 rounded'>
                            <h3 class='font-semibold mb-2'>Декодированные данные:</h3>
                            <pre class='text-sm text-gray-700'>" . print_r($decodedData, true) . "</pre>
                          </div>";
                    ?>
                </div>
            </div>

            <!-- Демонстрация SQL запросов -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="ri-code-s-slash-line text-3xl text-purple-500 mr-3"></i>
                    <h2 class="text-xl font-semibold">SQL Запросы (Демо)</h2>
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
                                <code class='text-sm flex-1'>$query</code>
                              </div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Демонстрация PHP 8.4 возможностей -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="ri-flashlight-line text-3xl text-yellow-500 mr-3"></i>
                    <h2 class="text-xl font-semibold">PHP 8.4 Новые возможности</h2>
                </div>
                <div class="space-y-4">
                    <?php
                    // Демонстрация match expression
                    $status = 'active';
                    $statusText = match($status) {
                        'active' => 'Активный',
                        'inactive' => 'Неактивный',
                        'pending' => 'Ожидает',
                        default => 'Неизвестно'
                    };
                    
                    echo "<div class='bg-gray-50 p-4 rounded'>
                            <h3 class='font-semibold mb-2'>Match Expression:</h3>
                            <p>Статус: <span class='font-bold text-blue-600'>$statusText</span></p>
                          </div>";
                    
                    // Демонстрация nullsafe operator
                    $user = null;
                    $userName = $user?->name ?? 'Пользователь не найден';
                    
                    echo "<div class='bg-gray-50 p-4 rounded'>
                            <h3 class='font-semibold mb-2'>Nullsafe Operator:</h3>
                            <p>Имя пользователя: <span class='font-bold text-green-600'>$userName</span></p>
                          </div>";
                    
                    // Демонстрация named arguments
                    function createUser(string $name, int $age = 25, string $email = '') {
                        return "Пользователь: $name, Возраст: $age, Email: $email";
                    }
                    
                    $userInfo = createUser(name: 'Мария', age: 30, email: 'maria@example.com');
                    
                    echo "<div class='bg-gray-50 p-4 rounded'>
                            <h3 class='font-semibold mb-2'>Named Arguments:</h3>
                            <p><span class='font-bold text-purple-600'>$userInfo</span></p>
                          </div>";
                    ?>
                </div>
            </div>

            <!-- Кнопки навигации -->
            <div class="text-center mt-8 space-x-4">
                <a href="/" class="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition-colors">
                    <i class="ri-home-line mr-2"></i>
                    Главная страница
                </a>
                <button onclick="location.reload()" class="inline-block bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition-colors">
                    <i class="ri-refresh-line mr-2"></i>
                    Обновить
                </button>
            </div>
        </div>
    </div>

