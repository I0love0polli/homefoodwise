// Toggle password visibility
function togglePasswordVisibility(passwordId, iconId) {
    var passwordField = document.getElementById(passwordId);
    var icon = document.getElementById(iconId);
    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.name = "eye-off-outline";
    } else {
        passwordField.type = "password";
        icon.name = "eye-outline";
    }
}