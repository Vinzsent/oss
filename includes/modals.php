<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="loginEmail" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabbed Sign-Up Modal -->
<div class="modal fade" id="signUpModal" tabindex="-1" aria-labelledby="signUpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="signUpModalLabel">Sign Up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Nav Tabs -->
                <ul class="nav nav-tabs" id="signUpTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="personal-tab" data-bs-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="true">Personal Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="address-tab" data-bs-toggle="tab" href="#address" role="tab" aria-controls="address" aria-selected="false">Address</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="upload-tab" data-bs-toggle="tab" href="#upload" role="tab" aria-controls="upload" aria-selected="false">Upload Photo</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3" id="signUpTabContent">
                    <!-- Personal Information Tab -->
                    <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                        <form id="signUpForm" method="POST" action="register.php" novalidate>
                            <div class="mb-3">
                                <label for="signUpFirstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="signUpFirstName" name="first_name" placeholder="Enter your first name" required>
                            </div>
                            <div class="mb-3">
                                <label for="signUpMiddleName" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="signUpMiddleName" name="middle_name" placeholder="Enter your middle name" required>
                            </div>
                            <div class="mb-3">
                                <label for="signUpLastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="signUpLastName" name="last_name" placeholder="Enter your last name" required>
                            </div>
                            <div class="mb-3">
                                <label for="signUpEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="signUpEmail" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="mb-3">
                                <label for="signUpPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="signUpPassword" name="password" placeholder="Enter your password" minlength="6" required>
                                <div id="passwordError" class="invalid-feedback" style="display:none;">Password must be at least 6 characters long.</div>
                            </div>
                    </div>

                    <!-- Address Tab -->
                    <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
                        <div class="mb-3">
                            <label for="signUpProvince" class="form-label">Province</label>
                            <select class="form-select" id="signUpProvince" name="province">
                                <option value="">Select Province</option>
                            </select>
                            <div id="provinceError" class="invalid-feedback" style="display:none;">Province is required.</div>
                        </div>
                        <div class="mb-3">
                            <label for="signUpCity" class="form-label">City/Municipality</label>
                            <select class="form-select" id="signUpCity" name="city_municipality">
                                <option value="">Select City/Municipality</option>
                            </select>
                            <div id="cityError" class="invalid-feedback" style="display:none;">City/Municipality is required.</div>
                        </div>
                        <div class="mb-3">
                            <label for="signUpBarangay" class="form-label">Barangay</label>
                            <select class="form-select" id="signUpBarangay" name="barangay">
                                <option value="">Select Barangay</option>
                            </select>
                            <div id="barangayError" class="invalid-feedback" style="display:none;">Barangay is required.</div>
                        </div>
                        <div class="mb-3">
                            <label for="signUpPurok" class="form-label">Purok</label>
                            <select class="form-select" id="signUpPurok" name="purok">
                                <option value="">Select Purok</option>
                            </select>
                            <div id="purokError" class="invalid-feedback" style="display:none;">Purok is required.</div>
                        </div>
                    </div>

                    <!-- Upload Photo Tab -->
                    <div class="tab-pane fade" id="upload" role="tabpanel" aria-labelledby="upload-tab">
                        <div class="mb-3">
                            <label for="signUpPhoto" class="form-label">Upload Photo</label>
                            <input type="file" class="form-control" id="signUpPhoto" name="photo" onchange="previewPhoto(event)" accept="image/*">
                        </div>
                        <div id="photoPreviewContainer" class="mt-3" style="display: none;">
                            <p><strong>Preview:</strong></p>
                            <img id="photoPreview" src="" alt="Photo Preview" style="max-width: 100%; height: auto; border: 1px solid #ccc;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="signUpSubmit">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="logoutModalLabel">
                    <i class="fas fa-sign-out-alt me-2"></i>Confirm Logout
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-0">Are you sure you want to log out?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <a href="../actions/logout.php" class="btn btn-primary">Yes</a>
            </div>
        </div>
    </div>
</div>