<?php
/**
 * Главная страница сайта
 */
function home(){
    // Подключаем header
    if (!function_exists('renderHeader')) {
        require_once __DIR__ . '/../components/header.php';
    }
    renderHeader();
    ?>
    <!-- Кнопка переключения темы -->
    <button 
        id="theme-toggle"
        class="fixed top-20 right-4 sm:right-8 z-50 w-12 h-12 rounded-full bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm shadow-lg hover:shadow-xl flex items-center justify-center text-gray-700 dark:text-gray-200 hover:scale-110 transition-all duration-300 border border-gray-200 dark:border-gray-700"
        aria-label="Переключить тему"
    >
        <i id="theme-icon" class="ri-moon-line text-2xl"></i>
    </button>

    <!-- Анимированный градиентный фон -->
    <div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-blue-50 via-indigo-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 animate-gradient transition-colors duration-500">
        <!-- Декоративные элементы -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400 dark:bg-blue-600 rounded-full mix-blend-multiply filter blur-xl opacity-30 dark:opacity-20 animate-float"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-400 dark:bg-purple-600 rounded-full mix-blend-multiply filter blur-xl opacity-30 dark:opacity-20 animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-400 dark:bg-pink-600 rounded-full mix-blend-multiply filter blur-xl opacity-20 dark:opacity-10 animate-float" style="animation-delay: 4s;"></div>
        </div>

        <div class="relative z-10 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
                <!-- Hero-блок с логотипом заказчика -->
                <div class="grid gap-12 lg:grid-cols-2 items-center animate-fade-in-up">
                    <div class="space-y-8">
                        <!-- Логотип и бейдж -->
                        <div class="flex items-center space-x-4 animate-fade-in-up" style="animation-delay: 0.1s;">
                            <div class="relative w-20 h-20 rounded-2xl bg-white dark:bg-gray-800 shadow-2xl flex items-center justify-center glow-effect hover-glow transition-all duration-300">
                                <img src="/assets/dx-logo.svg" alt="DX logo" class="w-14 h-14 object-contain flip-img">
                            </div>
                            <div>
                                <div class="inline-flex items-center px-3 py-1 rounded-full bg-gradient-to-r from-blue-500/10 to-purple-500/10 dark:from-blue-500/20 dark:to-purple-500/20 border border-blue-200/50 dark:border-blue-700/50">
                                    <span class="text-xs font-bold tracking-widest text-blue-600 dark:text-blue-400 uppercase">Модуль аутентификации</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Готовое решение для безопасного входа и управления пользователями</p>
                            </div>
                        </div>

            <!-- Заголовок -->
                        <div class="space-y-4 animate-fade-in-up" style="animation-delay: 0.2s;">
                            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white leading-tight">
                                Современный модуль
                                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 dark:from-blue-400 dark:via-purple-400 dark:to-pink-400 animate-gradient">
                                    авторизации для Diagix
                                </span>
                            </h1>
                            <p class="text-lg sm:text-xl text-gray-700 dark:text-gray-300 leading-relaxed max-w-xl">
                                Полнофункциональный модуль демонстрирует весь цикл работы с пользователем: от входа и регистрации до личного кабинета, админ‑панели и dev‑инструментов.
                            </p>
                        </div>

                        <!-- Статистика в виде карточек -->
                        <div class="grid grid-cols-3 gap-4 animate-fade-in-up" style="animation-delay: 0.3s;">
                            <div class="glass-effect dark:bg-gray-800/70 rounded-xl p-4 text-center hover-glow transition-all duration-300 hover:scale-105">
                                <div class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">10K+</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 font-medium mt-1">Пользователей</div>
                            </div>
                            <div class="glass-effect dark:bg-gray-800/70 rounded-xl p-4 text-center hover-glow transition-all duration-300 hover:scale-105">
                                <div class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-400 dark:to-pink-400">PHP 8.4</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 font-medium mt-1">Технология</div>
                            </div>
                            <div class="glass-effect dark:bg-gray-800/70 rounded-xl p-4 text-center hover-glow transition-all duration-300 hover:scale-105">
                                <div class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-pink-600 to-red-600 dark:from-pink-400 dark:to-red-400">100%</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 font-medium mt-1">Готовность</div>
                            </div>
                        </div>

                        <!-- Ключевые преимущества с улучшенным дизайном -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 animate-fade-in-up" style="animation-delay: 0.4s;">
                            <div class="glass-effect dark:bg-gray-800/70 rounded-xl p-4 flex items-start space-x-3 hover-glow transition-all duration-300 hover:scale-[1.02]">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-lg flex-shrink-0">
                                    <i class="ri-shield-check-line text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white text-sm mb-1">Безопасная аутентификация</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Хэширование паролей, CSRF‑защита, строгая проверка ролей</p>
                                </div>
                            </div>
                            <div class="glass-effect dark:bg-gray-800/70 rounded-xl p-4 flex items-start space-x-3 hover-glow transition-all duration-300 hover:scale-[1.02]">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white shadow-lg flex-shrink-0">
                                    <i class="ri-dashboard-2-line text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white text-sm mb-1">Личный кабинет + админка</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Современный dashboard и масштабируемая панель администратора</p>
                                </div>
                            </div>
                            <div class="glass-effect dark:bg-gray-800/70 rounded-xl p-4 flex items-start space-x-3 hover-glow transition-all duration-300 hover:scale-[1.02]">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-lg flex-shrink-0">
                                    <i class="ri-database-2-line text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white text-sm mb-1">Работа с большими данными</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Генерация до 10&nbsp;000 пользователей и умная загрузка</p>
                                </div>
                            </div>
                            <div class="glass-effect dark:bg-gray-800/70 rounded-xl p-4 flex items-start space-x-3 hover-glow transition-all duration-300 hover:scale-[1.02]">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-pink-500 to-pink-600 flex items-center justify-center text-white shadow-lg flex-shrink-0">
                                    <i class="ri-bug-line text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white text-sm mb-1">Диагностика и dev‑инструменты</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Модальные окна для проверки сессий и dev‑панель</p>
                                </div>
                </div>
            </div>

                        <!-- Живое демо: кнопка -->
                        <div class="pt-2 animate-fade-in-up" style="animation-delay: 0.5s;">
                            <button 
                                type="button"
                                class="open_modal_login_form group relative inline-flex items-center justify-center px-8 py-4 rounded-xl text-white font-bold text-base bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 shadow-2xl hover:shadow-blue-500/50 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 overflow-hidden"
                            >
                                <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-white/0 via-white/20 to-white/0 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></span>
                                <i class="ri-account-circle-line mr-2 text-2xl relative z-10"></i>
                                <span class="relative z-10">Войти и посмотреть в работе</span>
                            </button>
            </div>
        </div>

                    <!-- Визуальный блок о возможностях модуля с glassmorphism -->
                    <div class="space-y-6 animate-fade-in-up" style="animation-delay: 0.6s;">
                        <div class="glass-effect dark:bg-gray-800/70 rounded-3xl shadow-2xl p-8 hover-glow transition-all duration-300 hover:scale-[1.02]">
                            <div class="flex items-center mb-6">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white shadow-lg mr-3">
                                    <i class="ri-presentation-line text-2xl"></i>
                                </div>
                                <h2 class="text-2xl font-black text-gray-900 dark:text-white">
                                    Ключевые возможности
                                </h2>
                            </div>
                            <ul class="space-y-4 text-sm text-gray-700 dark:text-gray-300">
                                <li class="flex items-start group">
                                    <span class="mt-1 mr-3 text-blue-500 text-xl group-hover:scale-125 transition-transform duration-300">
                                        <i class="ri-checkbox-circle-fill"></i>
                                    </span>
                                    <span class="font-medium">Процесс аутентификации: вход, регистрация, смена пароля и редактирование профиля</span>
                                </li>
                                <li class="flex items-start group">
                                    <span class="mt-1 mr-3 text-purple-500 text-xl group-hover:scale-125 transition-transform duration-300">
                                        <i class="ri-checkbox-circle-fill"></i>
                                    </span>
                                    <span class="font-medium">Современный личный кабинет пользователя с реальным временем сессии и аналитикой</span>
                                </li>
                                <li class="flex items-start group">
                                    <span class="mt-1 mr-3 text-pink-500 text-xl group-hover:scale-125 transition-transform duration-300">
                                        <i class="ri-checkbox-circle-fill"></i>
                                    </span>
                                    <span class="font-medium">Админ‑панель с фильтрами, поиском и бесконечной прокруткой для 10&nbsp;000+ пользователей</span>
                                </li>
                                <li class="flex items-start group">
                                    <span class="mt-1 mr-3 text-emerald-500 text-xl group-hover:scale-125 transition-transform duration-300">
                                        <i class="ri-checkbox-circle-fill"></i>
                                    </span>
                                    <span class="font-medium">Dev‑инструменты: диагностика сессии и генератор тестовых аккаунтов под нагрузку</span>
                                </li>
                            </ul>
                        </div>

                        <div class="glass-effect dark:bg-gray-800/70 rounded-3xl shadow-xl p-8 hover-glow transition-all duration-300">
                            <div class="flex items-center mb-6">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white shadow-lg mr-3">
                                    <i class="ri-stack-line text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white">
                                    Технологический стек модуля
                                </h3>
                            </div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <div class="space-y-1">
                                    <dt class="font-black text-gray-900 dark:text-white text-base">Back‑end</dt>
                                    <dd class="text-gray-600 dark:text-gray-400">PHP&nbsp;8.4, PDO, поддержка SQLite и PostgreSQL</dd>
                                </div>
                                <div class="space-y-1">
                                    <dt class="font-black text-gray-900 dark:text-white text-base">Front‑end</dt>
                                    <dd class="text-gray-600 dark:text-gray-400">Tailwind CSS, аккуратный vanilla JS и jQuery</dd>
                                </div>
                                <div class="space-y-1">
                                    <dt class="font-black text-gray-900 dark:text-white text-base">Архитектура</dt>
                                    <dd class="text-gray-600 dark:text-gray-400">Компонентный подход, AJAX‑коммуникация, SSR‑компоненты</dd>
                                </div>
                                <div class="space-y-1">
                                    <dt class="font-black text-gray-900 dark:text-white text-base">Масштабирование</dt>
                                    <dd class="text-gray-600 dark:text-gray-400">Инструменты для нагрузки, подготовка к выносу в отдельный сервис</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Переключение темы для презентации
        (function() {
            // Ждем загрузки DOM
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTheme);
            } else {
                initTheme();
            }
            
            function initTheme() {
                const themeToggle = document.getElementById('theme-toggle');
                const themeIcon = document.getElementById('theme-icon');
                const html = document.documentElement;
                
                if (!themeToggle || !themeIcon) {
                    console.error('Theme toggle elements not found');
                    return;
                }
                
                // Проверяем сохраненную тему или системные настройки
                const savedTheme = localStorage.getItem('presentation-theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const isDark = savedTheme === 'dark' || (!savedTheme && prefersDark);
                
                // Применяем тему
                function applyTheme(dark) {
                    if (dark) {
                        html.classList.add('dark');
                        html.setAttribute('data-theme', 'dark');
                        themeIcon.className = 'ri-sun-line text-2xl';
                        console.log('Dark theme applied, class:', html.className);
                    } else {
                        html.classList.remove('dark');
                        html.setAttribute('data-theme', 'light');
                        themeIcon.className = 'ri-moon-line text-2xl';
                        console.log('Light theme applied, class:', html.className);
                    }
                    // Принудительно обновляем стили
                    document.body.style.display = 'none';
                    document.body.offsetHeight; // Trigger reflow
                    document.body.style.display = '';
                }
                
                // Инициализация
                applyTheme(isDark);
                
                // Обработчик клика
                themeToggle.addEventListener('click', function() {
                    const isDarkNow = html.classList.contains('dark');
                    const newTheme = !isDarkNow;
                    applyTheme(newTheme);
                    localStorage.setItem('presentation-theme', newTheme ? 'dark' : 'light');
                });
            }
        })();
    </script>
<?php } ?>
