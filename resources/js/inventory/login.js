// Attach via addEventListener (Vite ES modules are not in global scope,
// so onclick="fn()" in HTML won't find these functions — use listeners instead)

document.addEventListener('DOMContentLoaded', function () {

    // ── Login password toggle ─────────────────────────────────────────────
    var lpInp    = document.getElementById('lp_password');
    var lpIcon   = document.getElementById('lp_eye_icon');
    var lpEyeBtn = lpIcon ? lpIcon.closest('button') : null;

    if (lpEyeBtn && lpInp && lpIcon) {
        // Remove the old inline onclick so it doesn't fire twice if JS was ever global
        lpEyeBtn.removeAttribute('onclick');

        lpEyeBtn.addEventListener('click', function () {
            if (lpInp.type === 'password') {
                lpInp.type = 'text';
                lpIcon.classList.replace('fa-eye', 'fa-eye-slash');
                lpEyeBtn.style.color = '#3b82f6';
            } else {
                lpInp.type = 'password';
                lpIcon.classList.replace('fa-eye-slash', 'fa-eye');
                lpEyeBtn.style.color = '';
            }
        });
    }

    // ── Register password toggle ──────────────────────────────────────────
    var regInp    = document.getElementById('reg_password');
    var regIcon   = document.getElementById('reg_eye_icon');
    var regEyeBtn = regIcon ? regIcon.closest('button') : null;

    if (regEyeBtn && regInp && regIcon) {
        regEyeBtn.removeAttribute('onclick');

        regEyeBtn.addEventListener('click', function () {
            if (regInp.type === 'password') {
                regInp.type = 'text';
                regIcon.classList.replace('fa-eye', 'fa-eye-slash');
                regEyeBtn.style.color = '#3b82f6';
            } else {
                regInp.type = 'password';
                regIcon.classList.replace('fa-eye-slash', 'fa-eye');
                regEyeBtn.style.color = '';
            }
        });
    }

});
