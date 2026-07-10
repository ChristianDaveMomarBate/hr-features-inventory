<!-- Login Section — Full-viewport split-panel overlay -->
<div id="login-section" class="tab-section" style="display:none;">
<div class="lp-overlay">

    <!-- ══════════════ LEFT PANEL ══════════════ -->
    <div class="lp-left">

        <!-- Glowing curved accent -->
        <div class="lp-curve-accent"></div>

        <!-- Seal + Office title -->
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

        <!-- Mission card -->
        <div class="lp-mission">
            <div class="lp-mission-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="lp-mission-body">
                <h3>Our Mission</h3>
                <p>To enhance operational efficiency and accountability by providing a reliable, secure, and real-time inventory management system that streamlines the tracking, monitoring, and distribution of office supplies and assets within the Provincial Human Resource Management and Development Office.</p>
            </div>
        </div>

        <!-- Building illustration (SVG low-opacity line-art) -->
        <div class="lp-building" aria-hidden="true">
            <svg viewBox="0 0 320 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Ground -->
                <line x1="0" y1="155" x2="320" y2="155" stroke="rgba(255,255,255,0.15)" stroke-width="1.5"/>
                <!-- Main body -->
                <rect x="60" y="70" width="200" height="85" stroke="rgba(255,255,255,0.12)" stroke-width="1.2" fill="none"/>
                <!-- Roof / Pediment -->
                <polyline points="40,70 160,20 280,70" stroke="rgba(255,255,255,0.15)" stroke-width="1.2" fill="none"/>
                <!-- Columns -->
                <line x1="95"  y1="70" x2="95"  y2="155" stroke="rgba(255,255,255,0.10)" stroke-width="1"/>
                <line x1="130" y1="70" x2="130" y2="155" stroke="rgba(255,255,255,0.10)" stroke-width="1"/>
                <line x1="165" y1="70" x2="165" y2="155" stroke="rgba(255,255,255,0.10)" stroke-width="1"/>
                <line x1="200" y1="70" x2="200" y2="155" stroke="rgba(255,255,255,0.10)" stroke-width="1"/>
                <line x1="235" y1="70" x2="235" y2="155" stroke="rgba(255,255,255,0.10)" stroke-width="1"/>
                <!-- Door -->
                <rect x="145" y="110" width="30" height="45" stroke="rgba(255,255,255,0.13)" stroke-width="1" fill="none"/>
                <!-- Windows -->
                <rect x="78"  y="85" width="22" height="18" stroke="rgba(255,255,255,0.10)" stroke-width="1" fill="none"/>
                <rect x="113" y="85" width="22" height="18" stroke="rgba(255,255,255,0.10)" stroke-width="1" fill="none"/>
                <rect x="185" y="85" width="22" height="18" stroke="rgba(255,255,255,0.10)" stroke-width="1" fill="none"/>
                <rect x="220" y="85" width="22" height="18" stroke="rgba(255,255,255,0.10)" stroke-width="1" fill="none"/>
                <!-- Flag pole -->
                <line x1="160" y1="5" x2="160" y2="22" stroke="rgba(255,255,255,0.18)" stroke-width="1.2"/>
                <polyline points="160,5 176,11 160,17" stroke="rgba(255,255,255,0.15)" stroke-width="1" fill="none"/>
            </svg>
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

    </div><!-- /lp-left -->

    <!-- ══════════════ RIGHT PANEL ══════════════ -->
    <div class="lp-right">

        <!-- Ambient glow blobs -->
        <div class="lp-blob lp-blob-1"></div>
        <div class="lp-blob lp-blob-2"></div>

        <!-- Glass card -->
        <div class="lp-card">

            <!-- Card header (dark top section) -->
            <div class="lp-card-head">
                <div class="lp-shield-icon">
                    <i class="fas fa-shield-alt"></i>
                    <span class="lp-lock-badge"><i class="fas fa-lock"></i></span>
                </div>
                <h2 class="lp-card-title">WELCOME TO</h2>
                <p class="lp-card-sub">PHRMDO INVENTORY MANAGEMENT SYSTEM</p>
                <div class="lp-card-divider"></div>
            </div>

            <!-- Card form body -->
            <div class="lp-card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="lp-field">
                        <label class="lp-lbl" for="lp_email">Email Address</label>
                        <div class="lp-inp-wrap">
                            <i class="fas fa-envelope lp-inp-icon"></i>
                            <input id="lp_email" type="email" name="email"
                                class="lp-inp @error('email') lp-inp--err @enderror"
                                value="{{ old('email') }}"
                                placeholder="Enter your email address"
                                required autocomplete="email" autofocus>
                        </div>
                        @error('email')
                            <p class="lp-err">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="lp-field">
                        <label class="lp-lbl" for="lp_password">Password</label>
                        <div class="lp-inp-wrap">
                            <i class="fas fa-lock lp-inp-icon"></i>
                            <input id="lp_password" type="password" name="password"
                                class="lp-inp @error('password') lp-inp--err @enderror"
                                placeholder="Enter your password"
                                required autocomplete="current-password">
                            <button type="button" class="lp-eye" onclick="lpTogglePwd()" aria-label="Toggle password">
                                <i class="fas fa-eye" id="lp_eye_icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="lp-err">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Options row --}}
                    <div class="lp-opts">
                        <label class="lp-remember">
                            <input type="checkbox" name="remember" id="lp_remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Remember me</span>
                        </label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="lp-forgot">Forgot Password?</a>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="lp-submit">
                        LOGIN <i class="fas fa-arrow-right"></i>
                    </button>

                </form>

                <div class="lp-or"><span>OR</span></div>

                <p class="lp-no-account">
                    Don't have an account?
                    <a href="#register" class="nav-tab-btn" data-target="register">Sign up</a>
                </p>
            </div>

            <!-- Custom Image Footer Inside Card -->
            <div class="lp-card-img-footer">
                <img src="{{ asset('images/footer.png') }}" alt="Footer Banner">
            </div>
        </div><!-- /lp-card -->

    </div><!-- /lp-right -->

</div><!-- /lp-overlay -->

</div><!-- /login-section -->


