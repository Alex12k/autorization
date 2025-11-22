/**
 * Утилиты для работы с формами авторизации
 * Содержит общие функции для отображения сообщений, индикаторов загрузки и работы с формами
 */

(function($) {
    'use strict';

    // Получение базового URL (относительно корня сайта)
    function getBaseUrl() {
        var path = window.location.pathname;
        
        // Если путь содержит index.php, берем директорию до него
        var indexPos = path.indexOf('/index.php');
        if (indexPos !== -1) {
            return path.substring(0, indexPos) || '';
        }
        
        // Если путь заканчивается на '/', это корень
        if (path === '/' || path === '') {
            return '';
        }
        
        // Иначе берем директорию (убираем последний сегмент)
        var lastSlash = path.lastIndexOf('/');
        if (lastSlash > 0) {
            return path.substring(0, lastSlash);
        }
        
        return '';
    }

    // Экспорт функций для использования в модулях
    window.AuthFormUtils = {
        getBaseUrl: getBaseUrl,
        
        // Показать сообщение об ошибке
        showError: function(form, message) {
            var errorHtml = '<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg error-message">' +
                '<div class="flex items-center">' +
                '<i class="ri-error-warning-line text-red-500 mr-2"></i>' +
                '<span class="text-red-700">' + message + '</span>' +
                '</div>' +
                '</div>';
            
            // Удаляем старые сообщения об ошибках
            form.find('.error-message').remove();
            form.find('.success-message').remove();
            // Добавляем новое сообщение об ошибке перед формой
            form.prepend(errorHtml);
            
            // Прокрутка к ошибке (если форма видима)
            if (form.is(':visible')) {
                $('html, body').animate({
                    scrollTop: form.offset().top - 100
                }, 300);
            }
        },
        
        // Показать сообщение об успехе
        showSuccess: function(form, message) {
            var successHtml = '<div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg success-message">' +
                '<div class="flex items-center">' +
                '<i class="ri-check-line text-green-500 mr-2"></i>' +
                '<span class="text-green-700">' + message + '</span>' +
                '</div>' +
                '</div>';
            
            // Удаляем старые сообщения
            form.find('.error-message').remove();
            form.find('.success-message').remove();
            // Добавляем новое сообщение об успехе перед формой
            form.prepend(successHtml);
        },
        
        // Показать индикатор загрузки
        showLoading: function(button) {
            var originalText = button.html();
            button.data('original-text', originalText);
            button.prop('disabled', true);
            button.html('<i class="ri-loader-4-line animate-spin mr-2"></i>Загрузка...');
        },
        
        // Скрыть индикатор загрузки
        hideLoading: function(button) {
            var originalText = button.data('original-text');
            if (originalText) {
                button.html(originalText);
            }
            
            button.prop('disabled', false);
        },
        
        // Получить контейнер для форм
        getContainer: function() {
            return $('.authorization-ajax-container');
        }
    };

    // Инициализация при загрузке страницы
    $(document).ready(function() {
        console.log('Auth Form Utils initialized');
    });

})(jQuery);



