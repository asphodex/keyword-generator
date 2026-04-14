document.addEventListener('DOMContentLoaded', () => {
    const copyBtn = document.querySelector('.keyword-results__copy-btn');

    if (!copyBtn) {
        return;
    }

    copyBtn.addEventListener('click', () => {
        const items = document.querySelectorAll('.keyword-results__item');
        const text = Array.from(items)
            .map(item => item.textContent)
            .join('\n');

        navigator.clipboard.writeText(text).then(() => {
            const original = copyBtn.textContent;
            copyBtn.textContent = 'Скопировано';
            setTimeout(() => copyBtn.textContent = original, 1500);
        });
    });
});
