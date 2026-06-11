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

                <div class="form-group">
                    <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required style="background: #111111; color: #ffffff; border-color: #222222; height: 48px;">
                        <option value="" disabled selected>Select System Role...</option>
                        <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin (Super User)</option>
                        <option value="Staff" {{ old('role') == 'Staff' ? 'selected' : '' }}>Staff (Inventory Operator)</option>
                        <option value="Viewer" {{ old('role') == 'Viewer' ? 'selected' : '' }}>Viewer (Read-only Dashboard & Analytics)</option>
                    </select>
                    @error('role')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <div class="mt-2 p-2 rounded" style="background: rgba(255, 255, 255, 0.05); font-size: 0.8rem; color: #bbbbbb; line-height: 1.4;">
                        <strong>Role Descriptions:</strong><br>
                        • <strong>Admin:</strong> Super user who can Edit, Operate, and access all.<br>
                        • <strong>Staff:</strong> Can Operate in the inventory management.<br>
                        • <strong>Viewer:</strong> Can only view the Dashboard and Analytics.
                    </div>
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
