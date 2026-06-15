<!-- Register Section -->
<div id="register-section" class="tab-section container animate-fade-in" style="display: none;">
    <div class="card mx-auto" style="max-width: 480px; margin-top: 20px;">
        <div class="card-body">
            <h4 class="card-title mb-4 brand-wrap text-center">
                <img class="logo" src="images/logo.webp" alt="PHRMDO Logo" style="max-height: 50px;">
            </h4>
            <h5 class="text-center text-white mb-4" style="font-family: 'Outfit', sans-serif; font-weight: 600;">Create Account</h5>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="form-group">
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                        name="name" value="{{ old('name') }}" placeholder="Complete Name" required autocomplete="name" autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" placeholder="Email Address" required autocomplete="email">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" placeholder="Password" required autocomplete="new-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <input id="password-confirm" type="password" class="form-control"
                            name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
                    </div>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary btn-block shadow-sm"> Register </button>
                </div>
            </form>
        </div>
    </div>
    <p class="text-center mt-4">Have an account? <a href="{{ route('login') }}">Log In</a></p>
</div>
