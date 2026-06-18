<!-- Login Section -->
<div id="login-section" class="tab-section container animate-fade-in" style="display: none;">
    <div class="card mx-auto" style="max-width: 380px; margin-top: 20px;">
        <div class="card-body">
            <h4 class="card-title mb-4 brand-wrap text-center">
                <img class="logo" src="images/logo.webp" alt="PHRMDO Logo" style="max-height: 50px;">
            </h4>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <a href="#" class="btn btn-facebook btn-block mb-2"> 
                    <i class="fab fa-facebook-f mr-2" style="margin-right: 6px;"></i> Sign In With Facebook
                </a>
                <a href="#" class="btn btn-google btn-block mb-4"> 
                    <i class="fab fa-google mr-2" style="margin-right: 6px;"></i> Sign In With Google
                </a>
                <div class="form-group">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" placeholder="Email Address" required
                        autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <input id="password" type="password"
                        class="form-control @error('password') is-invalid @enderror" name="password" required
                        autocomplete="current-password" placeholder="Password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group clearfix">
                    <label class="float-left custom-control custom-checkbox mt-1">
                        <input class="custom-control-input" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <div class="custom-control-label">Remember me</div>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="float-right mt-1">Forgot password?</a>
                    @endif
                </div>
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary btn-block shadow-sm"> Login </button>
                </div>
            </form>
        </div>
    </div>
    <p class="text-center mt-4">Don't have an account? <a href="#register" class="nav-tab-btn" data-target="register">Sign up</a></p>
</div>
