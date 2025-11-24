<?php
/**
 * Точка входа компонента auth
 * Инициализирует систему и подключает все необходимые компоненты
 */

// Инициализация системы
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Запуск сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Подключаем компоненты auth
require_once __DIR__ . '/dashboard/dashboard.php';
require_once __DIR__ . '/admin/admin.php';

function auth(){?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-50 to-purple-50">
    <div class="max-w-md w-full space-y-8">
        


        <!-- Контейнер для форм авторизации -->
        <div class="authorization-ajax-container">


        <div class="max-w-md w-full space-y-8">
            <!-- Заголовок -->
            <div class="text-center">
                <div class="gradient-bg w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <i class="ri-account-circle-line text-4xl text-white"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-3">Аккаунт</h1>
                <p class="text-gray-600 text-lg">Выберите действие для продолжения</p>
            </div>

            <!-- Кнопки действий -->
            <div class="bg-white rounded-lg shadow-xl p-8 space-y-4">
                <button 
                    type="button" 
                    class="open_modal_login_form w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-4 px-6 rounded-lg font-semibold text-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-md"
                >
                    <i class="ri-login-box-line mr-2"></i>
                    Войти
                </button>
                
                <button 
                    type="button" 
                    class="open_modal_register_form w-full bg-gradient-to-r from-green-500 to-teal-600 text-white py-4 px-6 rounded-lg font-semibold text-lg hover:from-green-600 hover:to-teal-700 transition-all duration-300 transform hover:scale-105 shadow-md"
                >
                    <i class="ri-user-add-line mr-2"></i>
                    Зарегистрироваться
                </button>
            </div>

            <!-- Демо аккаунты -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                <h3 class="text-sm font-semibold text-blue-800 mb-2">
                    <i class="ri-information-line mr-1"></i>
                    Демо аккаунты:
                </h3>
                <div class="space-y-1 text-xs text-blue-700">
                    <div><strong>Админ:</strong> admin / admin123</div>
                    <div><strong>Пользователь:</strong> user / user123</div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<?php } ?>

<?php
/**
 * Функция для вывода всех скриптов компонента auth
 */
function auth_scripts() {
    ?>
<script src="/components/auth/utils/form-utils.js"></script>
<script src="/components/auth/utils/toast.js"></script>
<script src="/components/auth/utils/animations.js"></script>
<script src="/components/auth/login/script.js"></script>
<script src="/components/auth/register/script.js"></script>
<script src="/components/auth/password-recovery/script.js"></script>
<script src="/components/auth/logout/script.js"></script>
<script src="/components/auth/admin/script.js"></script>
<?php
}