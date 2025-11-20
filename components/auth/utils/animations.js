/**
 * Утилита для анимаций
 * Общие функции анимации для всех компонентов
 */

/**
 * Анимация изменения числа в счетчике
 * @param {HTMLElement} element - Элемент со счетчиком
 * @param {number} newValue - Новое значение
 */
function animateCounter(element, newValue) {
    if (!element) return;

    const currentValue = parseInt(element.textContent) || 0;
    if (currentValue === newValue) return;

    element.style.transition = 'transform 0.3s ease';
    element.style.transform = 'scale(1.2)';
    element.textContent = newValue;

    setTimeout(() => {
        element.style.transform = 'scale(1)';
    }, 150);
}

