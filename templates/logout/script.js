/**
 * Модуль обработки выхода из системы
 * Один универсальный AJAX обработчик, который вызывает функцию logoutPage()
 */

(function($) {
    'use strict';

    // Проверка наличия общего обработчика
    if (typeof window.AuthAjaxHandler === 'undefined') {
        console.error('AuthAjaxHandler не загружен! Убедитесь, что auth-ajax-handler.js загружен первым.');
        return;
    }

    var handler = window.AuthAjaxHandler;

    // Универсальный AJAX обработчик для выхода из системы
    // Обрабатывает запрос на выход
    function handleLogoutRequest(action, formData) {
        var baseUrl = handler.getBaseUrl();
        var url = baseUrl + '/templates/logout/logout.php';
        
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

    // Вызов выхода из системы по клику
    $(document).on('click', '.open_logout', function(e){
        e.preventDefault();
        
        // Отправляем запрос на выход с action=logout
        handleLogoutRequest('logout', { action: 'logout', ajax: '1' })
            .done(function(response, textStatus, xhr) {
                console.log('Ответ сервера (выход):', response);
                
                // Проверяем тип ответа
                var contentType = xhr.getResponseHeader('Content-Type') || '';
                var isJson = contentType.indexOf('application/json') !== -1;
                
                // Пытаемся распарсить JSON ответ
                if (isJson || (typeof response === 'string' && response.trim().startsWith('{'))) {
                    try {
                        var data = JSON.parse(response);
                        console.log('Распарсенный JSON (выход):', data);
                        
                        if (data.success) {
                            // Перенаправляем на главную через небольшую задержку
                            setTimeout(function() {
                                var redirectUrl = data.redirect || handler.getBaseUrl() + '/';
                                console.log('Перенаправление на:', redirectUrl);
                                window.location.href = redirectUrl;
                            }, 300);
                        } else {
                            console.error('Ошибка выхода:', data.error);
                            // Показываем ошибку, если есть контейнер
                            var container = handler.getContainer();
                            if (container.length) {
                                handler.showError(container, data.error || 'Ошибка выхода из системы');
                            }
                        }
                    } catch(e) {
                        console.error('Ошибка парсинга JSON (выход):', e, 'Ответ:', response);
                        // Если это не JSON, значит обычный редирект произошел
                        // Просто обновляем страницу
                        window.location.reload();
                    }
                } else {
                    // Если это не JSON, значит обычный редирект произошел
                    // Просто обновляем страницу
                    window.location.reload();
                }
            })
            .fail(function(xhr, status, error) {
                console.error('Ошибка AJAX (выход):', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                // При ошибке просто обновляем страницу
                window.location.reload();
            });
    });

    console.log('Logout module initialized');

})(jQuery);

