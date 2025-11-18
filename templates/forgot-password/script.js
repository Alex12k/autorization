/**
 * Модуль обработки восстановления пароля
 */


// Вызов формы восстановления пароля по клику
$(document).on('click', '.open_forgot-password, .open_forgot_password', function(e) {
    e.preventDefault();
    $.post('/templates/forgot-password/forgot_password.php', {action: 'open_forgot-password'}, function(res) {
        console.log(res);
        $('.authorization-ajax-container').html(res);
    });
});



// Обработка отправки формы восстановления пароля через AJAX
$(document).on('submit', '.authorization-ajax-container form[data-action="forgot-password"]', function(e){
    e.preventDefault();
    
    let form = $(this);
    let formData = form.serialize() + '&ajax=1';
    
    $.post('/templates/forgot-password/forgot_password.php', formData, function(res) {
        console.log('Ответ сервера:', res);
        
        // jQuery автоматически парсит JSON, проверяем тип
        if (typeof res === 'object' && res !== null) {
            // Это уже объект (JSON распарсен автоматически)
            if (res.success) {
                // Находим контейнер формы и заменяем его на блок успеха
                let formContainer = form.closest('.form-focus') || form.closest('div.bg-white');
                
                if (formContainer.length) {
                    // Создаем блок успеха с демо-информацией
                    let successHtml = '<div class="bg-white rounded-lg shadow-xl p-8 form-focus transition-all duration-300">' +
                        '<div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">' +
                        '<h3 class="text-sm font-semibold text-green-800 mb-2 flex items-center">' +
                        '<i class="ri-check-line mr-2"></i>Демо режим</h3>' +
                        '<p class="text-xs text-green-700 mb-3">' + (res.message || 'Ссылка для восстановления пароля отправлена на ваш email') + '</p>';
                    
                    if (res.token) {
                        let resetUrl = window.location.origin + '/reset-password?token=' + res.token;
                        successHtml += '<div class="bg-white rounded p-3 mb-3">' +
                            '<code class="text-xs break-all text-green-900">' + resetUrl + '</code>' +
                            '</div>' +
                            '<a href="/reset-password?token=' + res.token + '" ' +
                            'class="block w-full bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition-colors text-center font-semibold">' +
                            '<i class="ri-key-line mr-2"></i>Сбросить пароль</a>';
                    }
                    
                    successHtml += '</div></div>';
                    
                    formContainer.replaceWith(successHtml);
                } else {
                    // Если не нашли контейнер, просто показываем сообщение
                    window.AuthAjaxHandler.showSuccess(form, res.message || 'Ссылка для восстановления пароля отправлена на ваш email');
                }
            } else {
                // Ошибка - показываем сообщение
                window.AuthAjaxHandler.showError(form, res.error || 'Ошибка восстановления пароля');
            }
        } else {
            // Это строка (HTML) - обновляем контейнер
            $('.authorization-ajax-container').html(res);
        }
    });
});

console.log('Forgot Password module initialized');

