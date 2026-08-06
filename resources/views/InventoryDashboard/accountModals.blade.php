@php
    $currentUser ??= auth()->user();
@endphp

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom border-light">
                <h5 class="modal-title fw-bold" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width:44px;height:44px;">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Are you sure you want to logout?</h6>
                        <p class="text-muted mb-0 small">You will need to sign in again to access the inventory dashboard.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-light bg-light">
                <button type="button" class="btn btn-light text-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4" data-action="submit-logout">Logout</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="modal-content border-0 shadow">
            <div class="modal-header border-bottom border-light">
                <h5 class="modal-title fw-bold">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
                <div class="modal-body p-4">
                    @if($errors->profile->any())
                        <div class="alert alert-danger py-2 small">
                            Please check the highlighted profile fields.
                        </div>
                    @endif

                    <div class="mb-4 text-center">
                        <img src="{{ $currentUser->profile_picture ? asset('storage/' . $currentUser->profile_picture) : asset('images/default-avatar.png') }}" alt="Current Profile" class="rounded-circle border border-3 border-light shadow-sm mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                        <div>
                            <label for="profile_picture" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-camera me-1"></i> Change Picture
                            </label>
                            <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/jpeg,image/png,image/jpg,image/gif" onchange="if(this.files[0]) { let reader = new FileReader(); reader.onload = function(e) { document.querySelector('#profileModal img').src = e.target.result; }; reader.readAsDataURL(this.files[0]); }">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Name</label>
                        <input type="text" name="name" class="form-control rounded-3 @error('name', 'profile') is-invalid @enderror" value="{{ old('name', $currentUser->name) }}" required>
                        @error('name', 'profile')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Email</label>
                        <input type="email" class="form-control rounded-3 bg-light" value="{{ $currentUser->email }}" readonly disabled>
                        <div class="form-text">Email cannot be changed here.</div>
                    </div>
                    <div class="border-top pt-3 mt-4">
                        <h6 class="fw-bold text-dark mb-3">Change Password</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">Current Password</label>
                            <input type="password" name="current_password" class="form-control rounded-3 @error('current_password', 'profile') is-invalid @enderror" autocomplete="current-password">
                            @error('current_password', 'profile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">New Password</label>
                            <input type="password" name="password" class="form-control rounded-3 @error('password', 'profile') is-invalid @enderror" autocomplete="new-password">
                            @error('password', 'profile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold text-secondary small">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3" autocomplete="new-password">
                            <div class="form-text">Leave password fields blank to keep your current password.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-light bg-light">
                    <button type="button" class="btn btn-light text-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
            </form>
    </div>
</div>

@if($errors->profile->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const profileModal = document.getElementById('profileModal');

            if (profileModal && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(profileModal).show();
            }
        });
    </script>
@endif
