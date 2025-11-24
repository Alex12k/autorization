<?php
/**
 * Dev-инструмент: Демонстрация PHP 8.4
 * Показывает новые возможности PHP 8.4
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
 * Функция отображения демонстрации PHP 8.4 в модальном окне
 */
function modal_php_demo(): void
{
    ?>
    <div class="box-modal php-demo-modal !w-[95%] lg:!w-[1280px] !max-w-[95%] lg:!max-w-[1280px]">
        <!-- Заголовок модального окна -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="ri-code-s-slash-line text-2xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">PHP 8.4</h2>
                    <p class="text-sm text-gray-500">Демонстрация новых возможностей</p>
                </div>
            </div>
            <button class="arcticmodal-close text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-full">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <!-- Содержимое -->
        <div class="space-y-6 max-h-[70vh] overflow-y-auto">
            <!-- Демонстрация PHP 8.4 возможностей -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="ri-flashlight-line text-3xl text-yellow-500 mr-3"></i>
                    <h3 class="text-xl font-semibold">PHP 8.4 Новые возможности</h3>
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
                            <h4 class='font-semibold mb-2'>Match Expression:</h4>
                            <p class='text-sm mb-2'>Статус: <span class='font-bold text-blue-600'>$statusText</span></p>
                            <pre class='text-xs text-gray-700 bg-white p-2 rounded overflow-x-auto'>\$statusText = match(\$status) {
    'active' => 'Активный',
    'inactive' => 'Неактивный',
    default => 'Неизвестно'
};</pre>
                          </div>";
                    
                    // Демонстрация nullsafe operator
                    $user = null;
                    $userName = $user?->name ?? 'Пользователь не найден';
                    
                    echo "<div class='bg-gray-50 p-4 rounded'>
                            <h4 class='font-semibold mb-2'>Nullsafe Operator:</h4>
                            <p class='text-sm mb-2'>Имя пользователя: <span class='font-bold text-green-600'>$userName</span></p>
                            <pre class='text-xs text-gray-700 bg-white p-2 rounded overflow-x-auto'>\$userName = \$user?->name ?? 'Пользователь не найден';</pre>
                          </div>";
                    
                    // Демонстрация named arguments
                    function createUser(string $name, int $age = 25, string $email = '') {
                        return "Пользователь: $name, Возраст: $age, Email: $email";
                    }
                    
                    $userInfo = createUser(name: 'Мария', age: 30, email: 'maria@example.com');
                    
                    echo "<div class='bg-gray-50 p-4 rounded'>
                            <h4 class='font-semibold mb-2'>Named Arguments:</h4>
                            <p class='text-sm mb-2'><span class='font-bold text-purple-600'>$userInfo</span></p>
                            <pre class='text-xs text-gray-700 bg-white p-2 rounded overflow-x-auto'>createUser(name: 'Мария', age: 30, email: 'maria@example.com');</pre>
                          </div>";
                    
                    // Демонстрация typed properties
                    class DemoUser {
                        public string $name;
                        public int $age;
                        
                        public function __construct(string $name, int $age) {
                            $this->name = $name;
                            $this->age = $age;
                        }
                    }
                    
                    $demoUser = new DemoUser('Алексей', 25);
                    
                    echo "<div class='bg-gray-50 p-4 rounded'>
                            <h4 class='font-semibold mb-2'>Typed Properties:</h4>
                            <p class='text-sm mb-2'>Имя: <span class='font-bold text-indigo-600'>{$demoUser->name}</span>, Возраст: <span class='font-bold text-indigo-600'>{$demoUser->age}</span></p>
                            <pre class='text-xs text-gray-700 bg-white p-2 rounded overflow-x-auto'>class DemoUser {
    public string \$name;
    public int \$age;
}</pre>
                          </div>";
                    ?>
                </div>
            </div>

            <!-- Информация о версии PHP -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-800 mb-2">
                    <i class="ri-information-line mr-2"></i>
                    Информация о PHP
                </h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Версия PHP: <strong><?= PHP_VERSION ?></strong></li>
                    <li>• Сервер: <strong><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></strong></li>
                    <li>• SAPI: <strong><?= php_sapi_name() ?></strong></li>
                </ul>
            </div>
        </div>
    </div>
    <?php
}

