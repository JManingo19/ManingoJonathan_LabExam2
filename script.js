(function () {
    'use strict';

    // Show / hide password
    document.querySelectorAll('.toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.target);
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.setAttribute('aria-pressed', String(show));
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    });

    var form = document.querySelector('form[data-form]');
    if (!form) return;

    var errorBox = document.getElementById('form-error');
    var isRegister = form.dataset.form === 'register';

    function fail(message, field) {
        errorBox.textContent = message;
        errorBox.hidden = false;
        form.querySelectorAll('.invalid').forEach(function (el) { el.classList.remove('invalid'); });
        if (field) {
            field.classList.add('invalid');
            field.focus();
        }
    }

    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var phonePattern = /^\+?\d{7,15}$/;

    form.addEventListener('submit', function (event) {
        var f = form.elements;

        if (isRegister) {
            if (!f.first_name.value.trim()) { event.preventDefault(); return fail('Enter your first name.', f.first_name); }
            if (!f.last_name.value.trim())  { event.preventDefault(); return fail('Enter your last name.', f.last_name); }

            var id = f.identifier.value.trim();
            var phone = id.replace(/[\s\-().]/g, '');
            if (!emailPattern.test(id) && !phonePattern.test(phone)) {
                event.preventDefault();
                return fail('Enter a valid email address or contact number.', f.identifier);
            }
            if (f.password.value.length < 8) {
                event.preventDefault();
                return fail('Use a password with at least 8 characters.', f.password);
            }
        } else {
            if (!f.username.value.trim()) { event.preventDefault(); return fail('Enter your username.', f.username); }
            if (!f.password.value)        { event.preventDefault(); return fail('Enter your password.', f.password); }
        }
    });

    // Google sign-in is not connected yet. Wire this to your OAuth endpoint.
    var google = document.getElementById('google-btn');
    if (google) {
        google.addEventListener('click', function () {
            fail('Google sign-in is not set up yet.');
        });
    }
})();
