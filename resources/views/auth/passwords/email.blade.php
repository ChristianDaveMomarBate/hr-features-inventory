<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Forgot Password - PHRMDO Inventory System</title>
    <link href="{{ asset('images/favicon.ico') }}" rel="shortcut icon" type="image/x-icon">
    <script src="{{ asset('js/jquery-2.0.0.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('fonts/fontawesome/css/all.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/ui.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet" type="text/css" />

    <!-- Google Fonts -->
    <link href="{{ asset('vendor/@fontsource/inter/index.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/@fontsource/outfit/index.css') }}" rel="stylesheet">
    @vite(['resources/css/login.css'])
    @include('style.style')
</head>

<body class="auth-page" style="overflow: hidden;">

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top shadow-sm">
        <div class="container-fluid px-4 px-md-5">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('login') }}#home">
                <img src="{{ asset('images/logo2.webp') }}" alt="PHRMDO Logo" height="35" class="d-inline-block align-top mr-2" style="margin-right: 8px;">
                <span>PHRMDO Inventory System</span>
            </a>
            <div class="ml-auto">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}#login">Back to Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Forgot Password Section — Full-viewport split-panel overlay -->
    <div id="login-section" class="tab-section tab-active" style="display: flex;">
        <div class="lp-overlay">
        
        <!-- ══════════════ LEFT PANEL ══════════════ -->
        <div class="lp-left" style="position: relative; z-index: 2;">
            <div class="lp-curve-accent"></div>
            <div class="lp-logo-block">
                <div class="lp-seal-ring">
                    <img src="{{ asset('images/logo2.webp') }}" alt="Province Seal">
                </div>
                <h1 class="lp-office-name">
                    PROVINCIAL HUMAN RESOURCE<br>
                    MANAGEMENT AND<br>
                    DEVELOPMENT OFFICE
                </h1>
                <div class="lp-title-divider"></div>
            </div>
            <div class="lp-mission">
                <div class="lp-mission-icon">
                    <i class="fas fa-key"></i>
                </div>
                <div class="lp-mission-body">
                    <h3>Account Recovery</h3>
                    <p>Enter your email address and we will send you a link to reset your password. If you encounter any issues, please contact the system administrator.</p>
                </div>
            </div>
            <!-- Core values -->
            <div class="lp-values">
                <div class="lp-value">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <span class="lp-val-title">LIDERATONG</span>
                        <span class="lp-val-sub">Tapat</span>
                    </div>
                </div>
                <div class="lp-sep"></div>
                <div class="lp-value">
                    <i class="fas fa-heart"></i>
                    <div>
                        <span class="lp-val-title">RESPONSABLE</span>
                        <span class="lp-val-sub">Mapagkalinga</span>
                    </div>
                </div>
                <div class="lp-sep"></div>
                <div class="lp-value">
                    <i class="fas fa-hands-helping"></i>
                    <div>
                        <span class="lp-val-title">LIG-ON</span>
                        <span class="lp-val-sub">Nagkakaisa</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════ RIGHT PANEL ══════════════ -->
        <div class="lp-right" style="position: relative; z-index: 2;">
            <div class="lp-blob lp-blob-1"></div>
            <div class="lp-blob lp-blob-2"></div>
            
            <div class="lp-card" style="margin-top: 80px;">
                <div class="lp-card-head">
                    <div class="lp-shield-icon">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <h2 class="lp-card-title">RESET PASSWORD</h2>
                    <p class="lp-card-sub">Request a password reset link</p>
                    <div class="lp-card-divider"></div>
                </div>

                <div class="lp-card-body">
                    @if (session('status'))
                        <div style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; padding: 12px; margin-bottom: 20px; color: #a7f3d0; border-radius: 4px; font-size: 0.9rem;">
                            <i class="fas fa-check-circle" style="margin-right: 8px;"></i> {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="lp-field">
                            <label class="lp-lbl" for="lp_email">Email Address</label>
                            <div class="lp-inp-wrap">
                                <i class="fas fa-envelope lp-inp-icon"></i>
                                <input id="lp_email" type="email" name="email"
                                    class="lp-inp @error('email') lp-inp--err @enderror"
                                    value="{{ old('email') }}"
                                    placeholder="Enter your registered email"
                                    required autocomplete="email" autofocus>
                            </div>
                            @error('email')
                                <p class="lp-err">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="lp-submit" style="margin-top: 24px;">
                            SEND RESET LINK <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                    
                    <div class="lp-or" style="margin-top: 24px;"><span>OR</span></div>

                    <p class="lp-no-account">
                        Remember your password?
                        <a href="{{ route('login') }}#login" style="color: #60a5fa; font-weight: 500; text-decoration: none;">Log in</a>
                    </p>
                </div>
                
                <div class="lp-card-img-footer">
                    <img src="{{ asset('images/footer.png') }}" alt="Footer Banner">
                </div>
            </div>
        </div>
    </div>
    </div>

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
            © 2026 All Rights Reserved </br>
            <strong style="color:rgba(255,255,255,0.85);">
                Provincial Human Resource Management and Development Office – StockWise
            </strong>
            &nbsp;&nbsp;
            <span style="color:yellow;">CDB.HR_v1r_wojt</span>
        </small>
    </footer>
</body>
</html>
