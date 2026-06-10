<!DOCTYPE HTML>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="cache-control" content="max-age=604800" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Create Account – PHRMDO Inventory System</title>
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

        .navbar-custom .navbar-brand:hover { opacity: 0.85; }

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

        /* Glassmorphic Card */
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

        .card:hover { transform: translateY(-2px); }

        .card .card-body {
            color: #ffffff;
            padding: 2.5rem;
        }

        .card .form-control,
        .card select.form-control {
            background: #111111;
            color: #ffffff;
            border-color: #222222;
            border-radius: 8px;
            height: 48px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .card .form-control:focus,
        .card select.form-control:focus {
            background: #151515;
            border-color: #444444;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }

        .card .form-control::placeholder { color: #777777; }

        .card select.form-control option {
            background: #111111;
            color: #ffffff;
        }

        .card .invalid-feedback { color: #ff6b6b; font-weight: 500; }

        .card a,
        .card .btn { color: #ffffff; }

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

        .card .custom-control-label {
            color: #aaaaaa;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .form-group label {
            color: #aaaaaa;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.4rem;
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

        .text-center.mt-4 a:hover { opacity: 0.8; text-decoration: none; }

        .role-hint {
            background: rgba(255, 255, 255, 0.06);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            color: #aaaaaa;
            line-height: 1.6;
            margin-top: 0.5rem;
        }

        .role-hint strong { color: #cccccc; }

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
            <a class="navbar-brand d-flex align-items-center" href="{{ route('login') }}">
                <img src="images/logo2.webp" alt="PHRMDO Logo" height="35" class="d-inline-block align-top" style="margin-right: 8px;">
                <span>PHRMDO Inventory</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarRegister" aria-controls="navbarRegister" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarRegister">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}#login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('register') }}">Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="section-content padding-y" style="min-height: 80vh;">
        <div class="container">
            <div class="card mx-auto animate-fade-in" style="max-width: 500px; margin-top: 20px;">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="text-center mb-4">
                        <img class="logo" src="images/logo.webp" alt="PHRMDO Logo" style="max-height: 50px;">
                    </div>
                    <h5 class="text-center mb-4" style="font-family: 'Outfit', sans-serif; font-weight: 700; letter-spacing: -0.5px;">
                        Create Your Account
                    </h5>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Name -->
                        <div class="form-group">
                            <label for="name">Complete Name</label>
                            <input id="name" type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name') }}"
                                placeholder="Enter your full name"
                                required autocomplete="name" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input id="email" type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}"
                                placeholder="Enter your email"
                                required autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Role Dropdown -->
                        <div class="form-group">
                            <label for="role">System Role</label>
                            <select id="role" name="role"
                                class="form-control @error('role') is-invalid @enderror"
                                required>
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select a role...</option>
                                <option value="Admin"   {{ old('role') == 'Admin'   ? 'selected' : '' }}>Admin</option>
                                <option value="Staff"   {{ old('role') == 'Staff'   ? 'selected' : '' }}>Staff</option>
                                <option value="Viewer"  {{ old('role') == 'Viewer'  ? 'selected' : '' }}>Viewer</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <div class="role-hint mt-2">
                                <strong>Roles:</strong><br>
                                • <strong>Admin</strong> — Super user who can Edit, Operate &amp; access all.<br>
                                • <strong>Staff</strong> — Can Operate in the Inventory Management.<br>
                                • <strong>Viewer</strong> — Can only view the Dashboard &amp; Analytics.
                            </div>
                        </div>

                        <!-- Password Row -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="password">Password</label>
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password"
                                    placeholder="Min. 8 characters"
                                    required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="password-confirm">Confirm Password</label>
                                <input id="password-confirm" type="password"
                                    class="form-control"
                                    name="password_confirmation"
                                    placeholder="Repeat password"
                                    required autocomplete="new-password">
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="form-group mt-2 mb-0">
                            <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <p class="text-center mt-4">Already have an account? <a href="{{ route('login') }}">Log In</a></p>
        </div>
    </section>

    <!-- Bottom Floating Link -->
    <a href="{{ route('login') }}" class="btn btn-dark rounded-pill shadow"
        style="font-size: 12px; z-index: 1000; position: fixed; bottom: 15px; right: 15px; font-weight: 600; padding: 0.5rem 1rem;">
        <i class="fas fa-arrow-left" style="margin-right: 4px;"></i> Back to Login
    </a>
</body>

</html>
