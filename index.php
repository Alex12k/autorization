<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP 8.4 Демонстрация</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="gradient-bg text-white py-8">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <i class="ri-code-s-slash-line text-6xl mb-4"></i>
                <h1 class="text-4xl font-bold mb-2">PHP 8.4 Демонстрация</h1>
                <p class="text-xl opacity-90">Современные возможности PHP</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Информация о системе -->
            <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                <div class="flex items-center mb-4">
                    <i class="ri-server-line text-3xl text-blue-500 mr-3"></i>
                    <h2 class="text-xl font-semibold">Системная информация</h2>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="font-medium">PHP версия:</span>
                        <span class="text-green-600"><?= PHP_VERSION ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Операционная система:</span>
                        <span><?= PHP_OS ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Время сервера:</span>
                        <span><?= date('H:i:s') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Дата:</span>
                        <span><?= date('d.m.Y') ?></span>
                    </div>
                </div>
            </div>

            <!-- Демонстрация новых возможностей PHP 8.4 -->
            <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                <div class="flex items-center mb-4">
                    <i class="ri-flashlight-line text-3xl text-yellow-500 mr-3"></i>
                    <h2 class="text-xl font-semibold">PHP 8.4 Возможности</h2>
                </div>
                <div class="space-y-3">
                    <?php
                    // Демонстрация новых возможностей PHP 8.4
                    $features = [
                        'Named Arguments' => 'Поддерживается',
                        'Constructor Property Promotion' => 'Поддерживается',
                        'Match Expression' => 'Поддерживается',
                        'Nullsafe Operator' => 'Поддерживается',
                        'Attributes' => 'Поддерживается',
                        'Fibers' => 'Поддерживается'
                    ];
                    
                    foreach ($features as $feature => $status) {
                        $color = $status === 'Поддерживается' ? 'text-green-600' : 'text-red-600';
                        echo "<div class='flex justify-between items-center'>
                                <span class='font-medium'>$feature:</span>
                                <span class='$color'>$status</span>
                              </div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Математические вычисления -->
            <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                <div class="flex items-center mb-4">
                    <i class="ri-calculator-line text-3xl text-purple-500 mr-3"></i>
                    <h2 class="text-xl font-semibold">Математика</h2>
                </div>
                <div class="space-y-2 text-sm">
                    <?php
                    $a = 15;
                    $b = 7;
                    $sum = $a + $b;
                    $product = $a * $b;
                    $power = $a ** 2;
                    
                    echo "<div class='flex justify-between'>
                            <span>15 + 7 =</span>
                            <span class='font-bold text-blue-600'>$sum</span>
                          </div>";
                    echo "<div class='flex justify-between'>
                            <span>15 × 7 =</span>
                            <span class='font-bold text-blue-600'>$product</span>
                          </div>";
                    echo "<div class='flex justify-between'>
                            <span>15² =</span>
                            <span class='font-bold text-blue-600'>$power</span>
                          </div>";
                    ?>
                </div>
            </div>

            <!-- Работа с массивами -->
            <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                <div class="flex items-center mb-4">
                    <i class="ri-database-2-line text-3xl text-indigo-500 mr-3"></i>
                    <h2 class="text-xl font-semibold">Работа с данными</h2>
                </div>
                <div class="space-y-2 text-sm">
                    <?php
                    $fruits = ['Яблоко', 'Банан', 'Апельсин', 'Груша'];
                    $count = count($fruits);
                    $first = $fruits[0];
                    $last = end($fruits);
                    
                    echo "<div class='flex justify-between'>
                            <span>Количество фруктов:</span>
                            <span class='font-bold text-green-600'>$count</span>
                          </div>";
                    echo "<div class='flex justify-between'>
                            <span>Первый фрукт:</span>
                            <span class='font-bold text-green-600'>$first</span>
                          </div>";
                    echo "<div class='flex justify-between'>
                            <span>Последний фрукт:</span>
                            <span class='font-bold text-green-600'>$last</span>
                          </div>";
                    ?>
                </div>
            </div>

            <!-- Строковые операции -->
            <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                <div class="flex items-center mb-4">
                    <i class="ri-text-line text-3xl text-pink-500 mr-3"></i>
                    <h2 class="text-xl font-semibold">Строковые операции</h2>
                </div>
                <div class="space-y-2 text-sm">
                    <?php
                    $text = "Привет, мир!";
                    $length = mb_strlen($text);
                    $upper = mb_strtoupper($text);
                    $lower = mb_strtolower($text);
                    
                    echo "<div class='flex justify-between'>
                            <span>Длина строки:</span>
                            <span class='font-bold text-pink-600'>$length</span>
                          </div>";
                    echo "<div class='flex justify-between'>
                            <span>Верхний регистр:</span>
                            <span class='font-bold text-pink-600'>$upper</span>
                          </div>";
                    echo "<div class='flex justify-between'>
                            <span>Нижний регистр:</span>
                            <span class='font-bold text-pink-600'>$lower</span>
                          </div>";
                    ?>
                </div>
            </div>

            <!-- Время выполнения -->
            <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                <div class="flex items-center mb-4">
                    <i class="ri-time-line text-3xl text-orange-500 mr-3"></i>
                    <h2 class="text-xl font-semibold">Время выполнения</h2>
                </div>
                <div class="space-y-2 text-sm">
                    <?php
                    $start = microtime(true);
                    
                    // Имитация работы
                    for ($i = 0; $i < 1000000; $i++) {
                        $result = $i * 2;
                    }
                    
                    $end = microtime(true);
                    $execution_time = round(($end - $start) * 1000, 2);
                    
                    echo "<div class='flex justify-between'>
                            <span>Время выполнения:</span>
                            <span class='font-bold text-orange-600'>{$execution_time} мс</span>
                          </div>";
                    echo "<div class='flex justify-between'>
                            <span>Память использована:</span>
                            <span class='font-bold text-orange-600'>" . round(memory_get_usage() / 1024, 2) . " KB</span>
                          </div>";
                    ?>
                </div>
            </div>
        </div>

        <!-- Кнопки навигации -->
        <div class="text-center mt-8 space-x-4">
            <button onclick="location.reload()" class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                <i class="ri-refresh-line mr-2"></i>
                Обновить страницу
            </button>
            <a href="login.php" class="inline-block bg-gradient-to-r from-green-500 to-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-green-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105">
                <i class="ri-login-box-line mr-2"></i>
                Войти в систему
            </a>
            <a href="register.php" class="inline-block bg-gradient-to-r from-purple-500 to-pink-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-purple-600 hover:to-pink-700 transition-all duration-300 transform hover:scale-105">
                <i class="ri-user-add-line mr-2"></i>
                Регистрация
            </a>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-300">
                <i class="ri-heart-line text-red-500"></i>
                Создано с PHP 8.4 и Tailwind CSS
            </p>
        </div>
    </footer>
</body>
</html> 