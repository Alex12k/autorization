/**
 * Модуль обработки восстановления пароля
 * Один универсальный AJAX обработчик, который вызывает функцию forgotPassword()
 */

(function($) {
    'use strict';

    // Проверка наличия общего обработчика
    if (typeof window.AuthAjaxHandler === 'undefined') {
        console.error('AuthAjaxHandler не загружен! Убедитесь, что auth-ajax-handler.js загружен первым.');
        return;
    }

    var handler = window.AuthAjaxHandler;

    // Универсальный AJAX обработчик для восстановления пароля
    // Обрабатывает и загрузку формы, и отправку данных
    function handleForgotPasswordRequest(action, formData) {
        var baseUrl = handler.getBaseUrl();
        var url = baseUrl + '/templates/forgot-password/forgot_password.php';
        
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

    // Вызов формы восстановления пароля по клику
    $(document).on('click', '.open_forgot-password, .open_forgot_password', function(e){
        e.preventDefault();
        
        handleForgotPasswordRequest('open_forgot-password')
            .done(function(response) {
                var htmlResponse = typeof response === 'string' ? response : String(response);
                handler.getContainer().html(htmlResponse.trim());
            })
            .fail(function(xhr, status, error) {
                console.error('Ошибка загрузки формы восстановления пароля:', error);
                handler.getContainer().html(
                    '<div class="p-4 bg-red-50 border border-red-200 rounded-lg">' +
                    'Ошибка загрузки формы. Попробуйте обновить страницу.' +
                    '</div>'
                );
            });
    });

    // Обработка отправки формы восстановления пароля через AJAX
    $(document).on('submit', '.authorization-ajax-container form[data-action="forgot-password"]', function(e){
        e.preventDefault();
        
        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        var formData = form.serialize() + '&ajax=1';
        
        console.log('Отправка формы восстановления пароля:', formData);
        
        // Показываем индикатор загрузки
        handler.showLoading(submitButton);
        
        // Отправляем данные через универсальный обработчик
        handleForgotPasswordRequest('forgot-password', formData)
            .done(function(response, textStatus, xhr) {
                handler.hideLoading(submitButton);
                
                console.log('Ответ сервера (восстановление пароля):', response);
                
                // Проверяем тип ответа
                var contentType = xhr.getResponseHeader('Content-Type') || '';
                var isJson = contentType.indexOf('application/json') !== -1;
                
                // Пытаемся распарсить JSON ответ
                if (isJson || (typeof response === 'string' && response.trim().startsWith('{'))) {
                    try {
                        var data = JSON.parse(response);
                        console.log('Распарсенный JSON (восстановление пароля):', data);
                        
                        if (data.success) {
                            // Находим контейнер и заменяем только форму на блок успеха
                            var container = handler.getContainer();
                            
                            // Создаем блок успеха с демо-информацией
                            var successHtml = '<div class="bg-white rounded-lg shadow-xl p-8">' +
                                '<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">' +
                                '<div class="flex items-start">' +
                                '<i class="ri-check-line text-green-500 text-xl mr-2 mt-0.5"></i>' +
                                '<div>' +
                                '<span class="text-green-700 font-medium">' + (data.message || 'Ссылка для восстановления пароля отправлена на ваш email') + '</span>' +
                                '<p class="text-sm text-green-600 mt-1">' +
                                'В реальной системе ссылка будет отправлена на ' + (data.email || 'ваш email') +
                                '</p>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                            
                            // Если есть токен, добавляем демо-блок
                            if (data.token) {
                                var resetUrl = window.location.origin + handler.getBaseUrl() + '/reset-password?token=' + data.token;
                                successHtml += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">' +
                                    '<h3 class="text-sm font-semibold text-blue-800 mb-2 flex items-center">' +
                                    '<i class="ri-information-line mr-2"></i>Демо режим</h3>' +
                                    '<p class="text-xs text-blue-700 mb-3">В production система отправит email со ссылкой. Для демонстрации используйте эту ссылку:</p>' +
                                    '<div class="bg-white rounded p-3 mb-3">' +
                                    '<code class="text-xs break-all text-blue-900">' + resetUrl + '</code>' +
                                    '</div>' +
                                    '<a href="' + handler.getBaseUrl() + '/reset-password?token=' + data.token + '" ' +
                                    'class="block w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors text-center font-semibold">' +
                                    '<i class="ri-key-line mr-2"></i>Сбросить пароль</a>' +
                                    '</div>';
                            }
                            
                            successHtml += '<div class="text-sm text-gray-600">' +
                                '<i class="ri-time-line mr-1"></i>Срок действия ссылки: 1 час</div>' +
                                '</div>';
                            
                            // Заменяем только форму, сохраняя структуру с заголовком
                            // Ищем форму по data-action или по классу form-focus
                            var formElement = form.closest('.form-focus');
                            if (!formElement.length) {
                                // Если не нашли по form-focus, ищем родительский div с bg-white
                                formElement = form.closest('div.bg-white');
                            }
                            
                            if (formElement.length) {
                                // Заменяем форму на блок успеха
                                formElement.replaceWith(successHtml);
                            } else {
                                // Если не нашли форму, ищем контейнер max-w-md и заменяем содержимое после заголовка
                                var contentWrapper = container.find('.max-w-md').first();
                                if (contentWrapper.length) {
                                    // Сохраняем заголовок
                                    var header = contentWrapper.find('.text-center').first();
                                    var headerHtml = header.length ? header[0].outerHTML : '';
                                    
                                    // Заменяем все после заголовка на блок успеха
                                    contentWrapper.html(headerHtml + successHtml);
                                } else {
                                    // Если структура не найдена, заменяем весь контейнер
                                    container.html(successHtml);
                                }
                            }
                        } else {
                            // Показываем ошибку
                            console.error('Ошибка восстановления пароля:', data.error);
                            handler.showError(form, data.error || 'Ошибка восстановления пароля');
                        }
                    } catch(e) {
                        console.error('Ошибка парсинга JSON (восстановление пароля):', e, 'Ответ:', response);
                        handler.getContainer().html(response.trim());
                    }
                } else {
                    // HTML ответ - обновляем контейнер
                    handler.getContainer().html(response.trim());
                }
            })
            .fail(function(xhr, status, error) {
                handler.hideLoading(submitButton);
                console.error('Ошибка AJAX (восстановление пароля):', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                handler.showError(form, 'Ошибка соединения. Попробуйте еще раз.');
            });
    });

    console.log('Forgot Password module initialized');

})(jQuery);

