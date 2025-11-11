/**
 * Модуль обработки логина
 * Один универсальный AJAX обработчик, который вызывает функцию login()
 */

(function($) {
    'use strict';

    // Проверка наличия общего обработчика
    if (typeof window.AuthAjaxHandler === 'undefined') {
        console.error('AuthAjaxHandler не загружен! Убедитесь, что auth-ajax-handler.js загружен первым.');
        return;
    }

    var handler = window.AuthAjaxHandler;

    // Универсальный AJAX обработчик для логина
    // Обрабатывает и загрузку формы, и отправку данных
    function handleLoginRequest(action, formData) {
        var baseUrl = handler.getBaseUrl();
        var url = baseUrl + '/templates/login/login.php';
        var container = handler.getContainer();
        
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

    // Вызов формы логина по клику
    $(document).on('click', '.open_login', function(e){
        e.preventDefault();
        
        handleLoginRequest('open_login')
            .done(function(response) {
                var htmlResponse = typeof response === 'string' ? response : String(response);
                handler.getContainer().html(htmlResponse.trim());
            })
            .fail(function(xhr, status, error) {
                console.error('Ошибка загрузки формы логина:', error);
                handler.getContainer().html(
                    '<div class="p-4 bg-red-50 border border-red-200 rounded-lg">' +
                    'Ошибка загрузки формы. Попробуйте обновить страницу.' +
                    '</div>'
                );
            });
    });

    // Обработка отправки формы логина через AJAX
    $(document).on('submit', '.authorization-ajax-container form[data-action="login"]', function(e){
        e.preventDefault();
        
        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        var formData = form.serialize() + '&ajax=1';
        
        console.log('Отправка формы логина:', formData);
        
        // Показываем индикатор загрузки
        handler.showLoading(submitButton);
        
        // Отправляем данные через универсальный обработчик
        handleLoginRequest('login', formData)
            .done(function(response, textStatus, xhr) {
                handler.hideLoading(submitButton);
                
                console.log('Ответ сервера (логин):', response);
                
                // Проверяем тип ответа
                var contentType = xhr.getResponseHeader('Content-Type') || '';
                var isJson = contentType.indexOf('application/json') !== -1;
                
                // Пытаемся распарсить JSON ответ
                if (isJson || (typeof response === 'string' && response.trim().startsWith('{'))) {
                    try {
                        var data = JSON.parse(response);
                        console.log('Распарсенный JSON (логин):', data);
                        
                        if (data.success) {
                            // Показываем сообщение об успехе
                            handler.showSuccess(form, data.message || 'Успешный вход в систему');
                            
                            // Перенаправляем на dashboard
                            setTimeout(function() {
                                var redirectUrl = data.redirect || handler.getBaseUrl() + '/dashboard';
                                console.log('Перенаправление на:', redirectUrl);
                                window.location.href = redirectUrl;
                            }, 500);
                        } else {
                            // Показываем ошибку
                            console.error('Ошибка входа:', data.error);
                            handler.showError(form, data.error || 'Ошибка входа');
                        }
                    } catch(e) {
                        console.error('Ошибка парсинга JSON (логин):', e, 'Ответ:', response);
                        handler.getContainer().html(response.trim());
                    }
                } else {
                    // HTML ответ - обновляем контейнер
                    handler.getContainer().html(response.trim());
                }
            })
            .fail(function(xhr, status, error) {
                handler.hideLoading(submitButton);
                console.error('Ошибка AJAX (логин):', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                handler.showError(form, 'Ошибка соединения. Попробуйте еще раз.');
            });
    });

    console.log('Login module initialized');

})(jQuery);
