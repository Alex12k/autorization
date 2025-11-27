


<?php

function async_window(){?>

    <div class="box-modal async-window-modal">
        <!-- Заголовок модального окна -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="ri-window-line text-2xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Асинхронное окно</h2>
                    <p class="text-sm text-gray-500">Информационное модальное окно</p>
                </div>
            </div>
            <button class="arcticmodal-close text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-full">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <!-- Содержимое модального окна -->
        <div class="space-y-6">
            <!-- Основной контент -->
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg p-6 border border-blue-100">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="ri-information-line text-xl text-white"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Добро пожаловать!</h3>
                        <p class="text-gray-700 leading-relaxed">
                            Это пример асинхронного модального окна, загруженного через AJAX. 
                            Окно полностью адаптивно и имеет современный дизайн с использованием 
                            Tailwind CSS и градиентов.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Список возможностей -->
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Возможности:</h4>
                <div class="space-y-2">
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="ri-checkbox-circle-line text-green-500 text-xl"></i>
                        <span class="text-gray-700">Асинхронная загрузка контента</span>
                    </div>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="ri-checkbox-circle-line text-green-500 text-xl"></i>
                        <span class="text-gray-700">Адаптивный дизайн</span>
                    </div>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="ri-checkbox-circle-line text-green-500 text-xl"></i>
                        <span class="text-gray-700">Современный UI/UX</span>
                    </div>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="ri-checkbox-circle-line text-green-500 text-xl"></i>
                        <span class="text-gray-700">Плавные анимации</span>
                    </div>
                </div>
            </div>

            <!-- Кнопки действий -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <button class="arcticmodal-close px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors duration-200">
                    <i class="ri-close-line mr-2"></i>
                    Закрыть
                </button>
                <button class="px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-md">
                    <i class="ri-check-line mr-2"></i>
                    Применить
                </button>
            </div>
        </div>
    </div>

<?php }
