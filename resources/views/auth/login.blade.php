<!DOCTYPE HTML>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="cache-control" content="max-age=604800" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PHRMDO Inventory System - Provincial Government of Surigao del Norte</title>
    <link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <script src="js/jquery-2.0.0.min.js" type="text/javascript"></script>
    <script src="js/bootstrap.bundle.min.js" type="text/javascript"></script>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="fonts/fontawesome/css/all.min.css" type="text/css" rel="stylesheet">
    <link href="css/ui.css" rel="stylesheet" type="text/css" />
    <link href="css/responsive.css" rel="stylesheet" type="text/css" />

    <!-- Google Fonts -->
    <link href="{{ asset('vendor/@fontsource/inter/index.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/@fontsource/outfit/index.css') }}" rel="stylesheet">
    @vite(['resources/css/kiosk.css', 'resources/css/request.css', 'resources/css/login.css'])
    @include('style.style')
</head>

<body class="auth-page" data-auth-has-errors="{{ $errors->any() ? 'true' : 'false' }}">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top shadow-sm">
        <div class="container-fluid px-4 px-md-5">
            <a class="navbar-brand d-flex align-items-center nav-tab-btn" href="#home" data-target="home">
                <img src="images/logo2.webp" alt="PHRMDO Logo" height="35" class="d-inline-block align-top mr-2" style="margin-right: 8px;">
                <span>PHRMDO Inventory System</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-tab-btn" href="#home" data-target="home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-tab-btn kiosk-nav-link" href="#kiosk" data-target="kiosk">
                            <i class="fas fa-box-open" style="margin-right:5px;"></i>Kiosk
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link nav-tab-btn" href="#login" data-target="login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="section-content padding-y" style="min-height: 80vh;">

        @include('auth.home')

        @include('auth.kiosk')



        @include('auth.login-form')

        @include('auth.register-form')

    </section>

    @vite(['resources/js/inventory/script.js', 'resources/js/inventory/kiosk.js', 'resources/js/inventory/login.js'])

    @if($errors->has('email') || $errors->has('password'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const errorAudio = new Audio('{{ asset("sound/credentials.mp3") }}');
                errorAudio.currentTime = 0;
                errorAudio.play().catch(function(e) {
                    console.log("Audio play failed:", e);
                });
            });
        </script>
    @endif
    <footer id="site-footer" style="
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        text-align: center;
        padding: 8px 16px;
        background: rgba(0,0,0,0.45);
        backdrop-filter: blur(6px);
        color: rgba(255,255,255,0.6);
        font-size: 0.7rem;
        letter-spacing: 0.02em;
        z-index: 9999;
    ">
        <small>
            © 2026 <strong style="color:rgba(255,255,255,0.85);">PHRMDO Inventory System</strong>
            &nbsp;·&nbsp;
            Developed by OJT Team: <em>Tudtud Rianne Celone &amp; De La Cruz Romel Charles Nico</em>
        </small>
    </footer>

    <script>
        (function() {
            const footer = document.getElementById('site-footer');
            function updateFooter() {
                const hash = window.location.hash;
                if (hash === '#kiosk' || document.body.classList.contains('kiosk-active')) {
                    footer.style.display = 'none';
                } else {
                    footer.style.display = '';
                }
            }
            // Hide immediately if on kiosk hash
            updateFooter();
            // Listen for tab changes via hash
            window.addEventListener('hashchange', updateFooter);
            // Also watch clicks on the kiosk nav link
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('[data-target]');
                if (!btn) return;
                setTimeout(function() {
                    const active = document.querySelector('.tab-section[style*="block"], .tab-section.active-tab');
                    if (btn.dataset.target === 'kiosk') {
                        footer.style.display = 'none';
                    } else {
                        footer.style.display = '';
                    }
                }, 50);
            });
        })();
    </script>
</body>

</html>

