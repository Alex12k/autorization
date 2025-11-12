/**
 * Модуль обработки логина
 * Один универсальный AJAX обработчик, который вызывает функцию login()
 */


// Вызов формы логина по клику
$(document).on('click', '.open_login', function() {
    $.post('/templates/login/login.php', {action: 'open_login'}, function(res) {
        console.log(res);
        $('.authorization-ajax-container').html(res);
    });
});



// Обработка отправки формы логина через AJAX
$(document).on('submit', '.authorization-ajax-container form[data-action="login"]', function(e){
    e.preventDefault();
    
    let form = $(this);
    let formData = form.serialize() + '&ajax=1';
    
    $.post('/templates/login/login.php', formData, function(res) {
        console.log('Ответ сервера:', res);
        
        // jQuery автоматически парсит JSON, проверяем тип
        if (typeof res === 'object' && res !== null) {
            // Это уже объект (JSON распарсен автоматически)
            if (res.success) {
                // Успешный вход - редирект
                let redirectUrl = res.redirect || '/dashboard';
                console.log('Редирект на:', redirectUrl);
                window.location.href = redirectUrl;
            } else {
                // Ошибка - показываем сообщение
                window.AuthAjaxHandler.showError(form, res.error || 'Ошибка входа');
            }
        } else {
            // Это строка (HTML) - обновляем контейнер
            $('.authorization-ajax-container').html(res);
        }
    });
});

console.log('Login module initialized');