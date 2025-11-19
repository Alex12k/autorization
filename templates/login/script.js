/**
 * Модуль обработки логина
 * Один универсальный AJAX обработчик, который вызывает функцию login()
 */

/**
 * Переключение видимости пароля
 */
function togglePassword(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('ri-eye-line');
        icon.classList.add('ri-eye-off-line');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('ri-eye-off-line');
        icon.classList.add('ri-eye-line');
    }
}

// Вызов формы логина по клику
$(document).on('click', '.open_login', function(e) {
    e.preventDefault();
    console.log('open_login');
    $.post('/templates/login/ajax/ajax.php', {action: 'open_login', ajax: '1'}, function(res) {
        console.log(res);
        $('.authorization-ajax-container').html(res);
    });
});





// Обработка отправки формы логина через AJAX
$(document).on('submit', '.authorization-ajax-container form[data-action="login"]', function(e){
    e.preventDefault();
    
    let form = $(this);
    let formData = form.serialize() + '&ajax=1';
    
    $.post('/templates/login/ajax/ajax.php', formData, function(res) {
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