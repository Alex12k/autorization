/**
 * Модуль обработки сброса пароля
 * Один универсальный AJAX обработчик, который вызывает функцию resetPassword()
 */

(function($) {
    'use strict';

    // Проверка наличия общего обработчика
    if (typeof window.AuthAjaxHandler === 'undefined') {
        console.error('AuthAjaxHandler не загружен! Убедитесь, что auth-ajax-handler.js загружен первым.');
        return;
    }

    var handler = window.AuthAjaxHandler;

    // Универсальный AJAX обработчик для сброса пароля
    // Обрабатывает и загрузку формы, и отправку данных
    function handleResetPasswordRequest(action, formData) {
        var baseUrl = handler.getBaseUrl();
        var url = baseUrl + '/templates/reset-password/reset_password.php';
        
        // Данные для отправки
        var data = formData || { action: action };
        
        return $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'text',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
    }

    // Вызов формы сброса пароля по клику (если нужно загрузить форму асинхронно)
    $(document).on('click', '.open_reset-password', function(e){
        e.preventDefault();
        
        var token = $(this).data('token') || '';
        var data = { action: 'open_reset-password' };
        if (token) {
            // Если есть токен в URL, добавляем его
            var urlParams = new URLSearchParams(window.location.search);
            token = urlParams.get('token') || token;
        }
        
        handleResetPasswordRequest('open_reset-password', data)
            .done(function(response) {
                var htmlResponse = typeof response === 'string' ? response : String(response);
                handler.getContainer().html(htmlResponse.trim());
            })
            .fail(function(xhr, status, error) {
                console.error('Ошибка загрузки формы сброса пароля:', error);
                handler.getContainer().html(
                    '<div class="p-4 bg-red-50 border border-red-200 rounded-lg">' +
                    'Ошибка загрузки формы. Попробуйте обновить страницу.' +
                    '</div>'
                );
            });
    });

    // Обработка отправки формы сброса пароля через AJAX
    $(document).on('submit', '.authorization-ajax-container form[data-action="reset-password"]', function(e){
        e.preventDefault();
        
        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        var formData = form.serialize() + '&ajax=1';
        
        console.log('Отправка формы сброса пароля:', formData);
        
        // Показываем индикатор загрузки
        handler.showLoading(submitButton);
        
        // Отправляем данные через универсальный обработчик
        handleResetPasswordRequest('reset-password', formData)
            .done(function(response, textStatus, xhr) {
                handler.hideLoading(submitButton);
                
                console.log('Ответ сервера (сброс пароля):', response);
                
                // Проверяем тип ответа
                var contentType = xhr.getResponseHeader('Content-Type') || '';
                var isJson = contentType.indexOf('application/json') !== -1;
                
                // Пытаемся распарсить JSON ответ
                if (isJson || (typeof response === 'string' && response.trim().startsWith('{'))) {
                    try {
                        var data = JSON.parse(response);
                        console.log('Распарсенный JSON (сброс пароля):', data);
                        
                        if (data.success) {
                            // Показываем сообщение об успехе
                            handler.showSuccess(form, data.message || 'Пароль успешно изменен!');
                            
                            // Очищаем форму
                            form[0].reset();
                            
                            // Загружаем форму логина через небольшую задержку
                            setTimeout(function() {
                                $('.open_login').trigger('click');
                            }, 1500);
                        } else {
                            // Показываем ошибку
                            console.error('Ошибка сброса пароля:', data.error);
                            handler.showError(form, data.error || 'Ошибка сброса пароля');
                        }
                    } catch(e) {
                        console.error('Ошибка парсинга JSON (сброс пароля):', e, 'Ответ:', response);
                        handler.getContainer().html(response.trim());
                    }
                } else {
                    // HTML ответ - обновляем контейнер
                    handler.getContainer().html(response.trim());
                }
            })
            .fail(function(xhr, status, error) {
                handler.hideLoading(submitButton);
                console.error('Ошибка AJAX (сброс пароля):', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                handler.showError(form, 'Ошибка соединения. Попробуйте еще раз.');
            });
    });

    console.log('Reset Password module initialized');

})(jQuery);

