document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function (event) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (!form.checkValidity() || password !== confirmPassword) {
            event.preventDefault();
            event.stopPropagation();
            if (password !== confirmPassword) {
                document.getElementById('confirm_password').classList.add('is-invalid');
            }
        }
        form.classList.add('was-validated');
    }, false);
});

function togglePasswordVisibility(passwordId, iconId) {
    const passwordField = document.getElementById(passwordId);
    const icon = document.getElementById(iconId);
    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.name = "eye-off-outline";
    } else {
        passwordField.type = "password";
        icon.name = "eye-outline";
    }
}