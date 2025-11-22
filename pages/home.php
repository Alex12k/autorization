<?php
/**
 * Главная страница сайта
 */
function home(){?>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-50 to-purple-50">
        <div class="max-w-md w-full space-y-8">
            <!-- Заголовок -->
            <div class="text-center">
                <div class="gradient-bg w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <i class="ri-shield-user-line text-4xl text-white"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-3">Добро пожаловать</h1>
                <p class="text-gray-600 text-lg mb-2">Это главная страница сайта</p>
                <p class="text-gray-500 text-sm">Выберите действие для продолжения</p>
            </div>

            <!-- Кнопка перехода в аккаунт -->
            <div class="bg-white rounded-lg shadow-xl p-8">
                <a 
                    href="/auth" 
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-4 px-6 rounded-lg font-semibold text-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-md inline-block text-center"
                >
                    <i class="ri-account-circle-line mr-2"></i>
                    Аккаунт
                </a>
            </div>

            <!-- Кнопка открытия модального окна -->
            <div class="bg-white rounded-lg shadow-xl p-8">
                <button 
                    class="open_async-window w-full bg-gradient-to-r from-purple-500 to-pink-600 text-white py-4 px-6 rounded-lg font-semibold text-lg hover:from-purple-600 hover:to-pink-700 transition-all duration-300 transform hover:scale-105 shadow-md"
                >
                    <i class="ri-window-line mr-2"></i>
                    Открыть модальное окно
                </button>
            </div>
        </div>

    </div>


<?php } ?>