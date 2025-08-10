document.getElementById('login-form').addEventListener('submit', function (e) {
    const loginInput = document.getElementById('login_input').value.trim();
    const password = document.getElementById('password').value.trim();

    if (!loginInput || !password) {
        e.preventDefault();
        alert('Please fill in both fields.');
    }
});

document.getElementById('language-select').addEventListener('change', function () {
    const lang = this.value;
    window.location.href = `login.php?lang=${lang}`;
});