<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Micro Online Synthesis System</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-lg font-bold">Micro Online Synthesis System</h1>
            <ul class="flex space-x-4">
                <li><a href="#about" class="hover:underline">About</a></li>
                <li><a href="#" data-toggle="modal" data-target="#loginModal" class="hover:underline">Login</a></li>
                <li><a href="#" data-toggle="modal" data-target="#signUpModal"  class="hover:underline">Register</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-blue-600 text-white text-center py-20">
        <div class="container mx-auto">
            <h2 class="text-4xl font-bold mb-4">Welcome to Barangay Online Synthesis System</h2>
            <p class="text-lg mb-6">A platform to manage barangay projects, alerts, and resident information for efficient governance.</p>
            <a href="login.php" class="bg-yellow-500 text-black px-6 py-2 rounded hover:bg-yellow-400">Get Started</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 bg-white">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-semibold text-gray-800 mb-6">About the System</h2>
            <p class="text-lg text-gray-600 mb-6">
                This system aims to improve the management and delivery of public services in barangays. It provides a comprehensive tool for managing residents, projects, alerts, and other important community data.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gray-100 p-6 rounded shadow-md">
                    <h3 class="text-xl font-semibold text-blue-600">Resident Management</h3>
                    <p class="text-gray-600">Easily add, update, and manage resident records within the barangay.</p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-gray-100 p-6 rounded shadow-md">
                    <h3 class="text-xl font-semibold text-blue-600">Project Management</h3>
                    <p class="text-gray-600">Track and manage ongoing projects in the barangay to improve public infrastructure and services.</p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-gray-100 p-6 rounded shadow-md">
                    <h3 class="text-xl font-semibold text-blue-600">Alert System</h3>
                    <p class="text-gray-600">Create and view alerts for residents, keeping them informed on important events and emergencies.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-gray-800 text-white text-center py-4">
        <div class="container mx-auto">
            <p>&copy; 2024 Barangay Online Synthesis System. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="login.php">
                        <div class="form-group">
                            <label for="loginEmail">Email</label>
                            <input type="email" class="form-control" id="loginEmail" name="email" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label for="loginPassword">Password</label>
                            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Sign-Up Modal -->
    <div class="modal fade" id="signUpModal" tabindex="-1" role="dialog" aria-labelledby="signUpModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signUpModalLabel">Sign Up</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs" id="signUpTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="personal-tab" data-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="true">Personal Information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="address-tab" data-toggle="tab" href="#address" role="tab" aria-controls="address" aria-selected="false">Address</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="upload-tab" data-toggle="tab" href="#upload" role="tab" aria-controls="upload" aria-selected="false">Upload Photo</a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-3" id="signUpTabContent">
                        <!-- Personal Information Tab -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                            <form id="signUpForm" method="POST" action="register.php">
                                <div class="form-group">
                                    <label for="signUpFirstName">First Name</label>
                                    <input type="text" class="form-control" id="signUpFirstName" name="first_name" placeholder="Enter your first name" required>
                                </div>
                                <div class="form-group">
                                    <label for="signUpMiddleName">Middle Name</label>
                                    <input type="text" class="form-control" id="signUpMiddleName" name="middle_name" placeholder="Enter your middle name" required>
                                </div>
                                <div class="form-group">
                                    <label for="signUpLastName">Last Name</label>
                                    <input type="text" class="form-control" id="signUpLastName" name="last_name" placeholder="Enter your last name" required>
                                </div>
                                <div class="form-group">
                                    <label for="signUpEmail">Email</label>
                                    <input type="email" class="form-control" id="signUpEmail" name="email" placeholder="Enter your email" required>
                                </div>
                                <div class="form-group">
                                    <label for="signUpPassword">Password</label>
                                    <input type="password" class="form-control" id="signUpPassword" name="password" placeholder="Enter your password" required>
                                </div>
                        </div>

                        <!-- Address Tab -->
                        <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
                            <div class="form-group">
                                <label for="signUpProvince">Province</label>
                                <select class="form-control" id="signUpProvince" name="province" >
                                    <option value="">Select Province</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="signUpCity">City/Municipality</label>
                                <select class="form-control" id="signUpCity" name="city_municipality" >
                                    <option value="">Select City/Municipality</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="signUpBarangay">Barangay</label>
                                <select class="form-control" id="signUpBarangay" name="barangay" >
                                    <option value="">Select Barangay</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="signUpPurok">Purok</label>
                                <select class="form-control" id="signUpPurok" name="purok" >
                                    <option value="">Select Purok</option>
                                </select>
                            </div>
                        </div>

                        <!-- Upload Photo Tab -->
                        <div class="tab-pane fade" id="upload" role="tabpanel" aria-labelledby="upload-tab">
                            <div class="form-group">
                                <label for="signUpPhoto">Upload Photo</label>
                                <input type="file" class="form-control-file" id="signUpPhoto" name="photo" onchange="previewPhoto(event)">
                            </div>
                            <div id="photoPreviewContainer" class="mt-3" style="display: none;">
                                <p><strong>Preview:</strong></p>
                                <img id="photoPreview" src="" alt="Photo Preview" style="max-width: 100%; height: auto; border: 1px solid #ccc;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

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


</body>
</html>
