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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(rgba(255, 255, 255, 0.88), rgba(244, 246, 249, 0.66)), url('images/BG.webp') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            padding-top: 90px;
            color: #212529;
            transition: background 0.3s ease;
        }
        
        /* Glassmorphic Navbar */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .navbar-custom .navbar-brand {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            color: #111111 !important;
            letter-spacing: -0.5px;
            transition: opacity 0.2s ease;
        }
        
        .navbar-custom .navbar-brand:hover {
            opacity: 0.85;
        }
        
        .navbar-custom .nav-link {
            color: #555555 !important;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.5rem 1.2rem !important;
            margin: 0 0.15rem;
            border-radius: 20px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .navbar-custom .nav-link:hover {
            color: #000000 !important;
            background: rgba(0, 0, 0, 0.05);
        }
        
        .navbar-custom .nav-link.active {
            color: #ffffff !important;
            background: #000000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        /* Section Transition */
        .tab-section {
            transition: opacity 0.3s ease;
        }

        /* Home Hero Section */
        #home-section {
            padding: 3rem 0;
        }
        
        .hero-logo {
            max-height: 160px;
            width: auto;
            filter: drop-shadow(0 15px 30px rgba(0, 0, 0, 0.08));
            transition: transform 0.5s ease;
        }
        
        .hero-logo:hover {
            transform: scale(1.05);
        }
        
        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            color: #111111;
            line-height: 1.25;
            letter-spacing: -1px;
            max-width: 900px;
            margin: 0 auto;
        }
        
        .hero-subtitle {
            font-size: 1.15rem;
            color: #555555;
            max-width: 650px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .btn-premium-cta {
            background-color: #000000;
            color: #ffffff !important;
            border: none;
            font-weight: 600;
            padding: 1rem 2.5rem;
            font-size: 1.05rem;
            letter-spacing: 0.5px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-premium-cta:hover {
            background-color: #222222;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
        }

        /* About Section Card */
        .about-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .about-card-header {
            background: #000000;
            color: #ffffff;
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            text-align: center;
        }
        
        .about-card-body {
            padding: 3rem 2.5rem;
            color: #444444;
            font-size: 1.02rem;
            line-height: 1.8;
        }
        
        .about-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            color: #ffffff;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }
        
        .about-subtitle {
            font-size: 0.95rem;
            opacity: 0.8;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 500;
        }
        
        .about-section-heading {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            color: #111111;
            margin-bottom: 1.25rem;
            font-size: 1.25rem;
        }

        /* Login Card overrides - glassmorphic transparent */
        .card {
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .card .card-body {
            color: #ffffff;
            padding: 2.5rem;
        }
        
        .card .form-control {
            background: #111111;
            color: #ffffff;
            border-color: #222222;
            border-radius: 8px;
            height: 48px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        
        .card .form-control:focus {
            background: #151515;
            border-color: #444444;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }
        
        .card .form-control::placeholder {
            color: #777777;
        }
        
        .card .invalid-feedback {
            color: #ff6b6b;
            font-weight: 500;
        }
        
        .card a,
        .card .btn {
            color: #ffffff;
        }
        
        .card .btn-primary {
            background-color: #ffffff;
            color: #000000 !important;
            border: none;
            height: 48px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.25s ease;
        }
        
        .card .btn-primary:hover {
            background-color: #e5e5e5;
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.1);
        }
        
        .card .btn-facebook,
        .card .btn-google {
            background-color: #111111;
            border: 1px solid #222222;
            color: #ffffff;
            height: 44px;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .card .btn-facebook:hover,
        .card .btn-google:hover {
            background-color: #1a1a1a;
            border-color: #333333;
        }
        
        .card .custom-control-label {
            color: #aaaaaa;
            font-size: 0.9rem;
            cursor: pointer;
        }
        
        .form-group a {
            color: #aaaaaa;
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }
        
        .form-group a:hover {
            color: #ffffff;
            text-decoration: none;
        }
        
        .text-center.mt-4 {
            color: #555555;
            font-size: 0.95rem;
        }
        
        .text-center.mt-4 a {
            color: #000000;
            font-weight: 600;
            transition: opacity 0.2s ease;
        }
        
        .text-center.mt-4 a:hover {
            opacity: 0.8;
            text-decoration: none;
        }

        /* Fade-in Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
    <script src="js/script.js" type="text/javascript"></script>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center nav-tab-btn" href="#home" data-target="home">
                <img src="images/logo2.webp" alt="PHRMDO Logo" height="35" class="d-inline-block align-top mr-2" style="margin-right: 8px;">
                <span>PHRMDO Inventory</span>
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
                        <a class="nav-link nav-tab-btn" href="#about" data-target="about">About</a>
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

        @include('auth.about')

        @include('auth.login-form')

    </section>

    <!-- Bottom Floating Quick-Home Link -->
    <a href="/" class="btn btn-dark rounded-pill shadow"
        style="font-size: 12px; z-index: 1000; position: fixed; bottom: 15px; right: 15px; font-weight: 600; padding: 0.5rem 1rem;">
        <i class="fas fa-home mr-1"></i> Root Home
    </a>

    <!-- Tab Navigation Logic -->
    <script type="text/javascript">
        $(document).ready(function() {
            // Function to change active tab/section
            function showTab(target) {
                // Remove active class from all links, and add to the matching ones
                $('.navbar-nav .nav-link').removeClass('active');
                $('.navbar-nav .nav-link[data-target="' + target + '"]').addClass('active');
                
                // Collapse responsive navbar menu if open
                if ($('.navbar-collapse').hasClass('show')) {
                    $('.navbar-toggler').click();
                }

                // Hide all sections, and fade in target section
                $('.tab-section').hide();
                $('#' + target + '-section').fadeIn(300);

                // Update URL hash
                window.location.hash = target;
            }

            // Click listener for tab buttons
            $('.nav-tab-btn').on('click', function(e) {
                var target = $(this).attr('data-target');
                if (target) {
                    e.preventDefault();
                    showTab(target);
                }
            });

            // Initial hash checking or default load
            var initialHash = window.location.hash.substring(1);
            var hasErrors = {{ $errors->any() ? 'true' : 'false' }};
            
            if (initialHash === 'home' || initialHash === 'about' || initialHash === 'login' || initialHash === 'register') {
                showTab(initialHash);
            } else if (hasErrors) {
                showTab('login');
            } else {
                // Default to home
                showTab('home');
            }
        });
    </script>
</body>

</html>
