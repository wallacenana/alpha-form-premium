document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.alpha-form-wrapper');
    if (!form) return;

    const fields = form.querySelectorAll('.alpha-form-field');
    let current = 0;

    const showField = (index) => {
        fields.forEach((field, i) => {
            field.classList.remove('active');
            if (i === index) {
                field.classList.add('active');
            }
        });
    };

    showField(current);

    form.addEventListener('click', function (e) {
        if (e.target.classList.contains('alpha-form-next-button')) {
            current++;
            if (current < fields.length) {
                showField(current);
            }
        }
    });
});


if (document.querySelector('.alpha-form-progress-fill')) {
    const progressBar = document.querySelector('.alpha-form-progress-fill');
    const progressText = document.querySelector('.alpha-form-progress-text');
    const total = fields.length;
    const percent = Math.round(((index + 1) / total) * 100);
    
    progressBar.style.width = percent + '%';
    progressText.textContent = percent + '%';
}
