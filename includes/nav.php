
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom CSS for navigation -->
<style>
    .dropdown-item.active {
        background-color: #8b5cf6 !important;
        color: white !important;
        font-weight: bold;
    }
    .dropdown-item.active:hover {
        background-color: #6b21a8 !important;
        color: white !important;
    }
    /* Ensure dropdown menu is clickable and properly positioned */
    .dropdown-menu {
        z-index: 1050 !important;
        pointer-events: auto !important;
    }
    .dropdown-toggle {
        cursor: pointer !important;
    }
    .dropdown-item {
        cursor: pointer !important;
    }
    /* Prevent any overlay from blocking dropdown */
    .navbar-nav .dropdown {
        position: relative;
    }
</style>

<!-- Mobile Responsive CSS -->
<link href="assets/css/mobile-responsive.css" rel="stylesheet">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Mobile Navigation Auto-Close Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap dropdowns explicitly to ensure they work
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
    dropdownElementList.forEach(function(dropdownToggleEl) {
        // Initialize dropdown if Bootstrap is available
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            new bootstrap.Dropdown(dropdownToggleEl);
        }
    });
    
    // Auto-close navbar on mobile when clicking a link
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link:not(.dropdown-toggle)');
    const navbarCollapse = document.getElementById('navbarNav');
    
    navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992 && navbarCollapse && navbarCollapse.classList.contains('show')) {
                const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                    toggle: false
                });
                bsCollapse.hide();
            }
        });
    });
    
    // Also close when clicking dropdown items (but allow dropdown to open first)
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    dropdownItems.forEach(function(item) {
        item.addEventListener('click', function() {
            if (window.innerWidth < 992 && navbarCollapse && navbarCollapse.classList.contains('show')) {
                // Small delay to allow navigation to happen first
                setTimeout(function() {
                    if (navbarCollapse.classList.contains('show')) {
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                            toggle: false
                        });
                        bsCollapse.hide();
                    }
                }, 100);
            }
        });
    });
});
</script>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="index.php"><strong>Micro Online Synthesis System</strong></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="home.php">Home</a>
            </li>
			<li class="nav-item">
                <a class="nav-link" href="maps.php">Community Map</a>
            </li>
            <li class="nav-item dropdown">
			<a 
				class="nav-link dropdown-toggle" 
				href="#" 
				id="navbarDropdown" 
				role="button" 
				data-bs-toggle="dropdown" 
				aria-expanded="false">
				Early Warning System
			</a>
    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
        <li><a class="dropdown-item" href="alert.php">Alert Signal</a></li>
        <li><a class="dropdown-item" href="hazard.php">Hazard Map</a></li>
        <li><a class="dropdown-item" href="flood_warning.php">Flood Monitoring</a></li>
    </ul>
</li>
           <li class="nav-item"><a class="nav-link" href="evacuation.php">Evacuation Map</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="socioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Socio Demographic</a>
                    <ul class="dropdown-menu" aria-labelledby="socioDropdown">
                        <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'population.php' ? 'active' : ''; ?>" href="population.php">Population Over Age</a></li>
                        <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'hazard_vul.php' ? 'active' : ''; ?>" href="hazard_vul.php">Hazard Vulnerability</a></li>
                        <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'purok_demographics.php' ? 'active' : ''; ?>" href="purok_demographics.php">Purok Demographics</a></li>
                        <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'purok_evac.php' ? 'active' : ''; ?>" href="purok_evac.php">Purok Evacuation</a></li>
                        <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'socio.php' && isset($_GET['barangay']) && $_GET['barangay'] == 'lizada' ? 'active' : ''; ?>" href="socio.php?barangay=lizada">Lizada</a></li>
                        <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'socio.php' && isset($_GET['barangay']) && $_GET['barangay'] == 'daliao' ? 'active' : ''; ?>" href="socio.php?barangay=daliao">Daliao</a></li>
                    </ul>
                </li>
            <li class="nav-item">
                <a class="nav-link" href="gallery.php">Gallery</a>
            </li>
			<li class="nav-item">
                <a class="nav-link" href="iks.php">IKS</a>
            </li>
			<li class="nav-item">
                <a class="nav-link" href="publications.php">Publications</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="download.php">Downloadables</a>
            </li>
            
            <?php
            // Check if user is logged in
            if (isset($_SESSION['id'])) {
                // User is logged in, show profile picture and logout option
                // Check if profile picture is set in the session
                $profile_picture = isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default-profile.jpg';
                echo '<li class="nav-item">';
                echo '<img src="uploads/' . htmlspecialchars($profile_picture) . '" alt="Profile Picture" class="rounded-circle" style="width: 40px; height: 40px;">'; // Profile picture
                echo '</li>';
                echo '<li class="nav-item">';
                echo '<a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt mr-2"></i>Log Out</a>';
                echo '</li>';
            } else {
                // User is not logged in, show login and sign-up options
                echo '<li class="nav-item">';
                echo '<a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="fas fa-sign-out-alt mr-2"></i>Login</a>';
                echo '</li>';
                echo '<li class="nav-item">';
                echo '<a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#signUpModal"><i class="fas fa-sign-out-alt mr-2"></i>Sign Up</a>';
                echo '</li>';
            }
            ?>
        </ul>
    </div>
</nav>    

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('signUpProvince');
            const citySelect = document.getElementById('signUpCity');
            const barangaySelect = document.getElementById('signUpBarangay');
            const purokSelect = document.getElementById('signUpPurok');

            const provinces = {
                'Davao del Sur': ['Davao City']
            };

            const cities = {
                'Davao City': ['Daliao', 'Lizada']
            };

            const sitios = {
                'Daliao': ['Nakada', 'Doña Rosa Phase 1', 'Kalayaan'],
                'Lizada': ['Babisa', 'Camarin', 'Culosa']
            };

            provinceSelect.addEventListener('change', function() {
                citySelect.innerHTML = `<option value="">Select City/Municipality</option>`;
                if (provinces[this.value]) {
                    provinces[this.value].forEach(city => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                }
            });

            citySelect.addEventListener('change', function() {
                barangaySelect.innerHTML = `<option value="">Select Barangay</option>`;
                if (cities[this.value]) {
                    cities[this.value].forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay;
                        option.textContent = barangay;
                        barangaySelect.appendChild(option);
                    });
                }
            });

            barangaySelect.addEventListener('change', function() {
                purokSelect.innerHTML = `<option value="">Select Purok</option>`;
                if (sitios[this.value]) {
                    sitios[this.value].forEach(purok => {
                        const option = document.createElement('option');
                        option.value = purok;
                        option.textContent = purok;
                        purokSelect.appendChild(option);
                    });
                }
            });
        });
    </script>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('signUpProvince');
            const citySelect = document.getElementById('signUpCity');
            const barangaySelect = document.getElementById('signUpBarangay');
            const purokSelect = document.getElementById('signUpPurok');

            const provinces = {
                'Davao del Sur': ['Davao City']
            };

            const cities = {
                'Davao City': ['Daliao', 'Lizada']
            };

            const barangays = {
                'Davao City': ['Daliao', 'Lizada']
            };

            const sitios = {
                'Daliao': [
                    'Nakada', 'Doña Rosa Phase 1', 'Kalayaan', 'Kalubin-an', 'Kanipaan', 'Lipadas', 'Mcleod', 'Pantalan', 'Pogi Lawis', 'Prudential', 'St. Jude'
                ],
                'Lizada': [
                    'Babisa', 'Camarin', 'Culosa', 'Curvada', 'Dacudao', 'Doña Rosa', 'Fisherman', 'Glabaca', 'Gutierez', 'JV Ferriols', 'Kasama', 'Lawis', 'Lizada Beach', 'Lizada Proper', 'Maltabis'
                ]
            };

            // Populate Provinces
            Object.keys(provinces).forEach(province => {
                const option = document.createElement('option');
                option.value = province;
                option.textContent = province;
                provinceSelect.appendChild(option);
            });

            // When Province is selected
            provinceSelect.addEventListener('change', function() {
                const selectedProvince = this.value;
                citySelect.innerHTML = `<option value="">Select City/Municipality</option>`; // Reset city options
                if (provinces[selectedProvince]) {
                    provinces[selectedProvince].forEach(city => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                }
            });

            // When City is selected
            citySelect.addEventListener('change', function() {
                const selectedCity = this.value;
                barangaySelect.innerHTML = `<option value="">Select Barangay</option>`; // Reset barangay options
                if (cities[selectedCity]) {
                    cities[selectedCity].forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay;
                        option.textContent = barangay;
                        barangaySelect.appendChild(option);
                    });
                }
            });

            // When Barangay is selected
            barangaySelect.addEventListener('change', function() {
                const selectedBarangay = this.value;
                purokSelect.innerHTML = `<option value="">Select Purok/Sitio</option>`; // Reset purok options
                if (sitios[selectedBarangay]) {
                    sitios[selectedBarangay].forEach(purok => {
                        const option = document.createElement('option');
                        option.value = purok;
                        option.textContent = purok;
                        purokSelect.appendChild(option);
                    });
                }
            });
        });
    </script>

    <script>
        function previewPhoto(event) {
            const fileInput = event.target;
            const previewContainer = document.getElementById('photoPreviewContainer');
            const previewImage = document.getElementById('photoPreview');

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.style.display = 'block';
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(fileInput.files[0]);
            } else {
                previewContainer.style.display = 'none';
                previewImage.src = '';
            }
        }
    </script>

    <script>
        // Inline validation to avoid full-page alerts and keep the modal state
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('signUpForm');
            const passwordInput = document.getElementById('signUpPassword');
            const passwordError = document.getElementById('passwordError');
            const submitBtn = document.getElementById('signUpSubmit');
            const provinceSelect = document.getElementById('signUpProvince');
            const citySelect = document.getElementById('signUpCity');
            const barangaySelect = document.getElementById('signUpBarangay');
            const purokSelect = document.getElementById('signUpPurok');
            const provinceError = document.getElementById('provinceError');
            const cityError = document.getElementById('cityError');
            const barangayError = document.getElementById('barangayError');
            const purokError = document.getElementById('purokError');

            function validatePassword() {
                const isValid = (passwordInput.value || '').length >= 6;
                if (!isValid) {
                    passwordInput.classList.add('is-invalid');
                    passwordError.style.display = 'block';
                } else {
                    passwordInput.classList.remove('is-invalid');
                    passwordError.style.display = 'none';
                }
                return isValid;
            }

            function validateSelect(selectEl, errorEl) {
                const isValid = !!(selectEl.value && selectEl.value.trim() !== '');
                if (!isValid) {
                    selectEl.classList.add('is-invalid');
                    errorEl.style.display = 'block';
                } else {
                    selectEl.classList.remove('is-invalid');
                    errorEl.style.display = 'none';
                }
                return isValid;
            }

            passwordInput.addEventListener('input', validatePassword);
            provinceSelect.addEventListener('change', () => validateSelect(provinceSelect, provinceError));
            citySelect.addEventListener('change', () => validateSelect(citySelect, cityError));
            barangaySelect.addEventListener('change', () => validateSelect(barangaySelect, barangayError));
            purokSelect.addEventListener('change', () => validateSelect(purokSelect, purokError));

            form.addEventListener('submit', function (e) {
                const passwordOk = validatePassword();
                const provinceOk = validateSelect(provinceSelect, provinceError);
                const cityOk = validateSelect(citySelect, cityError);
                const barangayOk = validateSelect(barangaySelect, barangayError);
                const purokOk = validateSelect(purokSelect, purokError);

                if (!(passwordOk && provinceOk && cityOk && barangayOk && purokOk)) {
                    e.preventDefault();
                    // If address fields are invalid, switch to Address tab; otherwise keep in Personal
                    const addressInvalid = !(provinceOk && cityOk && barangayOk && purokOk);
                    if (addressInvalid) {
                        const addressTabTrigger = document.getElementById('address-tab');
                        if (addressTabTrigger && !addressTabTrigger.classList.contains('active')) {
                            addressTabTrigger.click();
                        }
                        // Focus the first invalid address field
                        if (!provinceOk) provinceSelect.focus();
                        else if (!cityOk) citySelect.focus();
                        else if (!barangayOk) barangaySelect.focus();
                        else if (!purokOk) purokSelect.focus();
                    } else {
                        const personalTabTrigger = document.getElementById('personal-tab');
                        if (personalTabTrigger && !personalTabTrigger.classList.contains('active')) {
                            personalTabTrigger.click();
                        }
                        passwordInput.focus();
                    }
                }
            });
        });
    </script>

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
                    <a href="logout.php" class="btn btn-primary">Yes</a>
                </div>
            </div>
        </div>
    </div>

