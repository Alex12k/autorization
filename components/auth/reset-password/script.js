/**
 * Модуль обработки сброса пароля
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

/**
 * Проверка силы пароля
 */
function checkPasswordStrength(password) {
    const strengthBar = document.getElementById('password-strength');
    const strengthText = document.getElementById('strength-text');

    if (!strengthBar || !strengthText) return;

    if (password.length === 0) {
        strengthBar.classList.add('hidden');
        return;
    }

    strengthBar.classList.remove('hidden');

    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z\d]/.test(password)) strength++;

    // Обновление индикатора
    const bars = [1, 2, 3, 4].map(i => document.getElementById(`strength-bar-${i}`));
    bars.forEach(bar => {
        if (bar) bar.className = 'flex-1 h-1 rounded bg-gray-200';
    });

    if (strength <= 2) {
        bars.slice(0, 1).forEach(bar => {
            if (bar) bar.classList.replace('bg-gray-200', 'bg-red-500');
        });
        strengthText.textContent = 'Слабый пароль';
        strengthText.className = 'text-xs mt-1 text-red-600';
    } else if (strength <= 3) {
        bars.slice(0, 2).forEach(bar => {
            if (bar) bar.classList.replace('bg-gray-200', 'bg-yellow-500');
        });
        strengthText.textContent = 'Средний пароль';
        strengthText.className = 'text-xs mt-1 text-yellow-600';
    } else {
        bars.forEach(bar => {
            if (bar) bar.classList.replace('bg-gray-200', 'bg-green-500');
        });
        strengthText.textContent = 'Надежный пароль';
        strengthText.className = 'text-xs mt-1 text-green-600';
    }
}

/**
 * Проверка совпадения паролей
 */
function checkPasswordMatch() {
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    if (!newPasswordInput || !confirmPasswordInput) return;
    
    const newPassword = newPasswordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    const matchMessage = document.getElementById('password-match-message');
    const reqLength = document.getElementById('req-length');
    const reqMatch = document.getElementById('req-match');
    const submitBtn = document.getElementById('submitBtn');

    // Проверка длины
    if (reqLength) {
        if (newPassword.length >= 6) {
            reqLength.innerHTML = '<i class="ri-checkbox-circle-line text-xs mr-2 text-green-600"></i>Минимум 6 символов';
            reqLength.classList.add('text-green-600');
        } else {
            reqLength.innerHTML = '<i class="ri-checkbox-blank-circle-line text-xs mr-2"></i>Минимум 6 символов';
            reqLength.classList.remove('text-green-600');
        }
    }

    // Проверка совпадения
    if (confirmPassword.length > 0 && matchMessage && reqMatch && submitBtn) {
        matchMessage.classList.remove('hidden');

        if (newPassword === confirmPassword) {
            matchMessage.textContent = '✓ Пароли совпадают';
            matchMessage.className = 'text-xs mt-1 text-green-600';
            reqMatch.innerHTML = '<i class="ri-checkbox-circle-line text-xs mr-2 text-green-600"></i>Пароли должны совпадать';
            reqMatch.classList.add('text-green-600');
            submitBtn.disabled = newPassword.length < 6;
        } else {
            matchMessage.textContent = '✗ Пароли не совпадают';
            matchMessage.className = 'text-xs mt-1 text-red-600';
            reqMatch.innerHTML = '<i class="ri-checkbox-blank-circle-line text-xs mr-2"></i>Пароли должны совпадать';
            reqMatch.classList.remove('text-green-600');
            submitBtn.disabled = true;
        }
    } else if (matchMessage && reqMatch && submitBtn) {
        matchMessage.classList.add('hidden');
        if (reqMatch) {
            reqMatch.innerHTML = '<i class="ri-checkbox-blank-circle-line text-xs mr-2"></i>Пароли должны совпадать';
            reqMatch.classList.remove('text-green-600');
        }
        submitBtn.disabled = true;
    }
}

// Инициализация обработчиков событий для формы сброса пароля
function initResetPasswordForm() {
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordMatch();
        });
    }

    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    }
}

// Автоматическая загрузка формы при наличии токена в URL
$(document).ready(function() {
    let urlParams = new URLSearchParams(window.location.search);
    let token = urlParams.get('token');
    if (token && $('.authorization-ajax-container').length) {
        // Автоматически загружаем форму сброса пароля
        let data = { action: 'open_reset-password', token: token, ajax: '1' };
        $.post('/components/auth/reset-password/ajax/ajax.php', data, function(res) {
            console.log('Автоматическая загрузка формы reset-password');
            $('.authorization-ajax-container').html(res);
            // Инициализируем обработчики после загрузки формы
            setTimeout(initResetPasswordForm, 100);
        });
    }
});

// Вызов формы сброса пароля по клику
$(document).on('click', '.open_reset-password', function() {
    let data = { action: 'open_reset-password', ajax: '1' };
    let urlParams = new URLSearchParams(window.location.search);
    let token = urlParams.get('token');
    if (token) {
        data.token = token;
    }
    
    $.post('/components/auth/reset-password/ajax/ajax.php', data, function(res) {
        console.log(res);
        $('.authorization-ajax-container').html(res);
        // Инициализируем обработчики после загрузки формы
        setTimeout(initResetPasswordForm, 100);
    });
});





// Обработка отправки формы сброса пароля через AJAX
$(document).on('submit', '.authorization-ajax-container form[data-action="reset-password"]', function(e){
    e.preventDefault();
    
    let form = $(this);
    let formData = form.serialize() + '&ajax=1';
    
    $.post('/components/auth/reset-password/ajax/ajax.php', formData, function(res) {
        console.log('Ответ сервера:', res);
        
        // jQuery автоматически парсит JSON, проверяем тип
        if (typeof res === 'object' && res !== null) {
            // Это уже объект (JSON распарсен автоматически)
            if (res.success) {
                // Очищаем токен из URL, чтобы избежать повторной загрузки формы сброса
                if (window.history && window.history.replaceState) {
                    let url = new URL(window.location.href);
                    url.searchParams.delete('token');
                    window.history.replaceState({}, '', url.pathname + (url.search ? url.search : ''));
                }
                
                // Заменяем весь контейнер на сообщение об успехе
                let successHtml = '<div class="bg-white rounded-lg shadow-xl p-8">' +
                    '<div class="text-center mb-6">' +
                    '<div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">' +
                    '<i class="ri-check-line text-4xl text-green-600"></i>' +
                    '</div>' +
                    '<h3 class="text-xl font-bold text-gray-900 mb-2">Пароль успешно изменен!</h3>' +
                    '<p class="text-gray-600">Теперь вы можете войти в систему с новым паролем</p>' +
                    '</div>' +
                    '<a href="/" class="open_login block w-full bg-gradient-to-r from-green-500 to-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-green-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 text-center cursor-pointer">' +
                    '<i class="ri-login-box-line mr-2"></i>Войти в систему</a>' +
                    '</div>';
                
                $('.authorization-ajax-container').html(successHtml);
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

