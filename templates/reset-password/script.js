/**
 * Модуль обработки сброса пароля
 */


// Вызов формы сброса пароля по клику
$(document).on('click', '.open_reset-password', function() {
    let data = { action: 'open_reset-password' };
    let urlParams = new URLSearchParams(window.location.search);
    let token = urlParams.get('token');
    if (token) {
        data.token = token;
    }
    
    $.post('/templates/reset-password/reset_password.php', data, function(res) {
        console.log(res);
        $('.authorization-ajax-container').html(res);
    });
});





// Обработка отправки формы сброса пароля через AJAX
$(document).on('submit', '.authorization-ajax-container form[data-action="reset-password"]', function(e){
    e.preventDefault();
    
    let form = $(this);
    let formData = form.serialize() + '&ajax=1';
    
    $.post('/templates/reset-password/reset_password.php', formData, function(res) {
        console.log('Ответ сервера:', res);
        
        // jQuery автоматически парсит JSON, проверяем тип
        if (typeof res === 'object' && res !== null) {
            // Это уже объект (JSON распарсен автоматически)
            if (res.success) {
                // Показываем сообщение об успехе
                window.AuthAjaxHandler.showSuccess(form, res.message || 'Пароль успешно изменен!');
                
                // Очищаем форму
                form[0].reset();
                
                // Загружаем форму логина через небольшую задержку
                setTimeout(function() {
                    $('.open_login').trigger('click');
                }, 1500);
            } else {
                // Ошибка - показываем сообщение
                window.AuthAjaxHandler.showError(form, res.error || 'Ошибка сброса пароля');
            }
        } else {
            // Это строка (HTML) - обновляем контейнер
            $('.authorization-ajax-container').html(res);
        }
    });
});

console.log('Reset Password module initialized');

