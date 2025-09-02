<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Micro OSS App</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <!-- Navigation Menu -->
		<div class="sticky-header">
			<?php include('includes/nav.php'); ?>
		</div>

    <!-- Hero Section -->
    <section class="bg-blue-600 text-white text-center py-20">
        <div class="container mx-auto">
            <h2 class="text-4xl font-bold mb-4">Welcome to Micro Online Synthesis System</h2>
            <p class="text-lg mb-6">A comprehensive tool to enhance community resilience through advanced flood monitoring and early warning systems.</p>
            <button class="bg-yellow-500 text-black px-6 py-2 rounded hover:bg-yellow-400 text-decoration-none" id="loginModal" data-bs-toggle="modal" data-bs-target="#loginModal">Get Started</button>
        </div>
    </section>

    <!-- About Section -->
	<section id="about" class="py-16 bg-white">
		<div class="container mx-auto text-center">
			<h2 class="text-3xl font-semibold text-gray-800 mb-6">About the System</h2>
			<p class="text-lg text-gray-600 mb-6">
				The Micro Online Synthesis System (Micro OSS) enhances community resilience by providing essential tools and data to support local government units and residents in flood-prone areas.
			</p>
		<div class="grid grid-cols-1 md:grid-cols-4 gap-8">
    <!-- Community Map -->
    <div class="bg-blue-50 p-6 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition duration-300">
        <img src="assets/icons/map	.png" alt="Community Map Icon" class="w-32 mx-auto mb-4">
        <h3 class="text-xl font-semibold text-blue-600">Community Map</h3>
        <p class="text-gray-600">Explore the community map to access local hazard zones, infrastructure, and vital locations for better disaster planning.</p>
    </div>

    <!-- Early Warning System (EWS) -->
    <div class="bg-green-50 p-6 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition duration-300">
        <img src="assets/icons/ews.png" alt="Early Warning System Icon" class="w-32 mx-auto mb-4">
        <h3 class="text-xl font-semibold text-green-600">Early Warning System (EWS)</h3>
        <p class="text-gray-600">Receive real-time hazard alerts, flood signals, and hazard maps to stay informed and take timely action.</p>
    </div>

    <!-- Evacuation Map -->
    <div class="bg-yellow-50 p-6 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition duration-300">
        <img src="assets/icons/evacuation-map.png" alt="Evacuation Map Icon" class="w-32 mx-auto mb-4">
        <h3 class="text-xl font-semibold text-yellow-600">Evacuation Map</h3>
        <p class="text-gray-600">Access detailed evacuation routes, safe shelters, and critical locations to ensure safety during emergencies.</p>
    </div>

    <!-- Socio-Demographic Data -->
    <div class="bg-red-50 p-6 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition duration-300">
        <img src="assets/icons/socio-data.png" alt="Socio-Demographic Data Icon" class="w-32 mx-auto mb-4">
        <h3 class="text-xl font-semibold text-red-600">Socio-Demographic Data</h3>
        <p class="text-gray-600">Utilize key demographic and socioeconomic data to improve disaster planning, response, and resource allocation.</p>
    </div>

    <!-- Media Gallery -->
    <div class="bg-indigo-50 p-6 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition duration-300">
        <img src="assets/icons/media-gallery.png" alt="Media Gallery Icon" class="w-32 mx-auto mb-4">
        <h3 class="text-xl font-semibold text-indigo-600">Media Gallery</h3>
        <p class="text-gray-600">Browse a collection of images and videos focused on flood awareness, local events, and emergency response efforts.</p>
    </div>

    <!-- Indigenous Knowledge System -->
    <div class="bg-purple-50 p-6 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition duration-300">
        <img src="assets/icons/iks.png" alt="Indigenous Knowledge System Icon" class="w-32 mx-auto mb-4">
        <h3 class="text-xl font-semibold text-purple-600">Indigenous Knowledge System</h3>
        <p class="text-gray-600">Preserve and integrate traditional disaster response strategies and community wisdom for enhanced preparedness.</p>
    </div>

    <!-- Policies & Publications -->
    <div class="bg-teal-50 p-6 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition duration-300">
        <img src="assets/icons/policy.png" alt="Policies & Publications Icon" class="w-32 mx-auto mb-4">
        <h3 class="text-xl font-semibold text-teal-600">Policies & Publications</h3>
        <p class="text-gray-600">The Policies & Publications section provides key resources like Policy Briefs, Media Releases, Infographics, and Fact Sheets to support decision-making and community engagement.</p>
    </div>

    <!-- Downloadables -->
    <div class="bg-orange-50 p-6 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition duration-300">
        <img src="assets/icons/downloadables.png" alt="Downloadables Icon" class="w-32 mx-auto mb-4">
        <h3 class="text-xl font-semibold text-orange-600">Downloadables</h3>
        <p class="text-gray-600">Access and download critical resources, including maps, reports, and emergency guides, for offline use.</p>
    </div>
</div>
	
		</div>
	</section>




    <!-- Footer Section -->
    <footer class="bg-gray-800 text-white text-center py-4">
        <div class="container mx-auto">
            <p>&copy; 2024 Micro Online Synthesis System. All Rights Reserved.</p>
        </div>
    </footer>

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
                                <label for="signUpContactNo">Contact No.</label>
                                <input type="text" class="form-control" id="signUpContactNo" name="contact_no" placeholder="Enter your contact number" required>
                            </div>
                            <div class="form-group">
                                <label for="signUpAddress">Address</label>
                                <input type="text" class="form-control" id="signUpAddress" name="address" placeholder="Enter your address" required>
                            </div>
                        </div>

                        <!-- Upload Photo Tab -->
                        <div class="tab-pane fade" id="upload" role="tabpanel" aria-labelledby="upload-tab">
                            <div class="form-group">
                                <label for="signUpProfilePhoto">Profile Photo</label>
                                <input type="file" class="form-control" id="signUpProfilePhoto" name="profile_photo" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-3">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap and Tailwind JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
