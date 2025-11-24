/**
 * Модуль обработки регистрации
 */


/**
 * Переключение видимости пароля для формы регистрации
 * Управляет двумя полями пароля одновременно
 */
function togglePasswordRegister(inputId, iconId) {
    const passwordInput = document.getElementById('modal_register_password');
    const confirmPasswordInput = document.getElementById('modal_register_confirm_password');
    const passwordIcon = document.getElementById('toggle-modal-register-password-icon');
    const confirmPasswordIcon = document.getElementById('toggle-modal-register-confirm-password-icon');

    if (passwordInput && passwordInput.type === 'password') {
        passwordInput.type = 'text';
        if (confirmPasswordInput) confirmPasswordInput.type = 'text';
        if (passwordIcon) {
            passwordIcon.classList.remove('ri-eye-line');
            passwordIcon.classList.add('ri-eye-off-line');
        }
        if (confirmPasswordIcon) {
            confirmPasswordIcon.classList.remove('ri-eye-line');
            confirmPasswordIcon.classList.add('ri-eye-off-line');
        }
    } else if (passwordInput) {
        passwordInput.type = 'password';
        if (confirmPasswordInput) confirmPasswordInput.type = 'password';
        if (passwordIcon) {
            passwordIcon.classList.remove('ri-eye-off-line');
            passwordIcon.classList.add('ri-eye-line');
        }
        if (confirmPasswordIcon) {
            confirmPasswordIcon.classList.remove('ri-eye-off-line');
            confirmPasswordIcon.classList.add('ri-eye-line');
        }
    }
}

// Вызов формы регистрации по клику (открывает модальное окно)
$(document).on('click', '.open_modal_register_form', function(e) {
    e.preventDefault();
    
    // Закрываем все открытые модальные окна перед открытием нового
    $.arcticmodal('close');
    
    $.post('/components/auth/register/ajax/ajax.php', {action: 'open_modal_register_form'}, function(res) {
        $(res).arcticmodal({closeOnOverlayClick: false});
    });
});

// Обработка отправки формы регистрации из модального окна
$(document).on('submit', '.register-modal form[data-action="register"]', function(e){
    e.preventDefault();
    
    let form = $(this);
    let formData = form.serialize() + '&ajax=1';
    
    $.post('/components/auth/register/ajax/ajax.php', formData, function(res) {
        console.log('Ответ сервера:', res);
        
        // jQuery автоматически парсит JSON, проверяем тип
        if (typeof res === 'object' && res !== null) {
            // Это уже объект (JSON распарсен автоматически)
            if (res.success) {
                // Закрываем модальное окно
                $('.arcticmodal-container').arcticmodal('close');
                // Сразу открываем форму логина
                $('.open_modal_login_form').trigger('click');
            } else {
                // Ошибка - показываем сообщение
                window.AuthFormUtils.showError(form, res.error || 'Ошибка регистрации');
            }
        }
    });
});

console.log('Register module initialized');
