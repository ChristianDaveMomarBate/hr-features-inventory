/**
 * Login / Register — password visibility toggle helpers
 * Extracted from login-form.blade.php
 */

function lpTogglePwd() {
    var inp  = document.getElementById('lp_password');
    var icon = document.getElementById('lp_eye_icon');
    if (!inp) return;
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        inp.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function regTogglePwd() {
    var inp  = document.getElementById('reg_password');
    var icon = document.getElementById('reg_eye_icon');
    if (!inp) return;
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        inp.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
