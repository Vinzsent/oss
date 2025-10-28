	<?php
	session_start();
	?>

	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Micro OSS App</title>
		<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
	</head>
	<body class="bg-gray-100">

		<!-- Navigation Menu -->
		  <div class="sticky-header">
			<?php include('includes/nav.php'); ?>
		</div>

		<!-- Home Link Section -->
		<section id="about" class="py-16 bg-white">
			<div class="container mx-auto text-center">
				<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-8">
					<!-- Community Map -->
					<div class="bg-gray-100 p-6 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105" id="alert-signals">
						<a href="maps.php" class="block" style="text-decoration: none;">
							<img src="assets/icons/map.png" alt="Alert Signals Icon" class="w-32 mx-auto mb-4">
							<h3 class="text-xl font-semibold text-blue-500">Community Map</h3>
						</a>
					</div>
					<!-- Alert Signal -->
					<div class="bg-gray-100 p-6 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105" id="alert-signals">
						<a href="alert.php" class="block" style="text-decoration: none;">
							<img src="assets/icons/alert.png" alt="Alert Signals Icon" class="w-32 mx-auto mb-4">
							<h3 class="text-xl font-semibold text-blue-500">Alert Signals</h3>
						</a>
					</div>
					<!-- Hazard Map -->
					<div class="bg-gray-100 p-6 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105" id="hazard-map">
						<a href="hazard.php" class="block" style="text-decoration: none;">
							<img src="assets/icons/hazardmap.png" alt="Hazard Map Icon" class="w-32 mx-auto mb-4">
							<h3 class="text-xl font-semibold text-blue-500">Hazard Map</h3>
						</a>
					</div>
					<!-- Early Warning System -->
					<div class="bg-gray-100 p-6 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105" id="ews">
						<a href="flood_warning.php" class="block" style="text-decoration: none;">
							<img src="assets/icons/ews.png" alt="Early Warning System Icon" class="w-32 mx-auto mb-4">
							<h3 class="text-xl font-semibold text-blue-500">Flood Monitoring</h3>
						</a>
					</div>
					<!-- Evacuation Map -->
					<div class="bg-gray-100 p-6 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105" id="evacuation">
						<a href="evacuation.php" class="block" style="text-decoration: none;">
							<img src="assets/icons/evacuation-map.png" alt="Evacuation Map Icon" class="w-32 mx-auto mb-4">
							<h3 class="text-xl font-semibold text-blue-500">Evacuation Map</h3>
						</a>
					</div>
					<!-- Socio-Demographic Data -->
					<div class="bg-gray-100 p-6 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105" id="socio-data">
						<a href="socio.php" class="block" style="text-decoration: none;">
							<img src="assets/icons/socio-data.png" alt="Socio-Demographic Data Icon" class="w-32 mx-auto mb-4">
							<h3 class="text-xl font-semibold text-blue-500">Socio-Demographic Data</h3>
						</a>
					</div>
					<!-- Media Gallery -->
					<div class="bg-gray-100 p-6 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105" id="media-gallery">
						<a href="gallery.php" class="block" style="text-decoration: none;">
							<img src="assets/icons/media-gallery.png" alt="Media Gallery Icon" class="w-32 mx-auto mb-4">
							<h3 class="text-xl font-semibold text-blue-500">Media Gallery</h3>
						</a>
					</div>
					<!-- Indigenous Knowledge System -->
					<div class="bg-gray-100 p-6 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105" id="iks">
						<a href="iks.php" class="block" style="text-decoration: none;">
							<img src="assets/icons/iks.png" alt="Indigenous Knowledge System Icon" class="w-32 mx-auto mb-4">
							<h3 class="text-xl font-semibold text-blue-500">Indigenous Knowledge System</h3>
						</a>
					</div>
					<!-- Policies & Publications -->
					<div class="bg-gray-100 p-6 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105" id="policies-publications">
						<a href="Publications.php" class="block" style="text-decoration: none;">
							<img src="assets/icons/policy.png" alt="Policies & Publications Icon" class="w-32 mx-auto mb-4">
							<h3 class="text-xl font-semibold text-blue-500">Policies & Publications</h3>
						</a>
					</div>
					<!-- Downloadables -->
					<div class="bg-gray-100 p-6 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105" id="downloadables">
						<a href="download.php" class="block" style="text-decoration: none;">
							<img src="assets/icons/downloadables.png" alt="Downloadables Icon" class="w-32 mx-auto mb-4">
							<h3 class="text-xl font-semibold text-blue-500">Downloadables</h3>
						</a>
					</div>
					<!-- Admin -->
					<div class="bg-gray-100 p-6 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105" id="admin">
						<button type="button" class="block w-full text-left" style="text-decoration: none; background: none; border: none;" data-bs-toggle="modal" data-bs-target="#adminModal">
							<img src="assets/icons/admin.png" alt="Admin Icon" class="w-32 mx-auto mb-4">
							<h3 class="text-xl font-semibold text-blue-500 text-center">Admin</h3>
						</button>
					</div>
				</div>
			</div>
		</section>
		
		<?php include('includes/footer.php'); ?>
		
		<!-- Admin Authentication Modal -->
		<div class="modal fade" id="adminModal" tabindex="-1" aria-labelledby="adminModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header bg-primary text-white">
						<h5 class="modal-title" id="adminModalLabel">
							<i class="fas fa-shield-alt mr-2"></i>Admin Access
						</h5>
						<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="adminLoginForm">
							<div class="form-group">
								<label for="adminPassword" class="font-weight-bold">Enter Admin Password:</label>
								<input type="password" class="form-control" id="adminPassword" placeholder="Password" required autocomplete="off">
								<small>Note: it only has 3 attempts</small>
							</div>
							<div id="errorMessage" class="alert alert-danger" style="display: none;"></div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-primary" id="adminLoginBtn">Access Admin</button>
					</div>
				</div>
			</div>
		</div>

		<!-- jQuery for Admin Modal -->
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="https://kit.fontawesome.com/a076d05399.js"></script>
		
		<script>
		$(document).ready(function() {
			let attemptCount = 0;
			const maxAttempts = 3;
			
			// Handle admin login
			$('#adminLoginBtn').click(function() {
				const password = $('#adminPassword').val();
				const errorDiv = $('#errorMessage');
				
				if (!password) {
					showError('Please enter a password');
					return;
				}
				
				// Client-side validation with obfuscation
				if (validatePassword(password)) {
					// Clear form and close modal
					$('#adminPassword').val('');
					const modal = bootstrap.Modal.getInstance(document.getElementById('adminModal'));
					if (modal) modal.hide();
					errorDiv.hide();
					attemptCount = 0;
					
					// Redirect to admin page
					window.location.href = 'admin.php';
				} else {
					attemptCount++;
					
					if (attemptCount >= maxAttempts) {
						showError('Too many failed attempts. Access denied.');
						$('#adminLoginBtn').prop('disabled', true);
						setTimeout(function() {
							const modal = bootstrap.Modal.getInstance(document.getElementById('adminModal'));
					if (modal) modal.hide();
							$('#adminLoginBtn').prop('disabled', false);
							attemptCount = 0;
						}, 3000);
					} else {
						showError(`Invalid password. ${maxAttempts - attemptCount} attempts remaining.`);
					}
					
					$('#adminPassword').val('').focus();
				}
			});
			
			// Handle Enter key press
			$('#adminPassword').keypress(function(e) {
				if (e.which == 13) {
					$('#adminLoginBtn').click();
				}
			});
			
			// Reset form when modal is closed
			$('#adminModal').on('hidden.bs.modal', function() {
				$('#adminPassword').val('');
				$('#errorMessage').hide();
				attemptCount = 0;
				$('#adminLoginBtn').prop('disabled', false);
			});
			
			// Focus password field when modal opens
			$('#adminModal').on('shown.bs.modal', function() {
				$('#adminPassword').focus();
			});
			
			function showError(message) {
				$('#errorMessage').text(message).show();
			}
			
			// Obfuscated password validation
			function validatePassword(input) {
				const encoded = btoa(input);
				const target = 'bWlzYWRtaW4='; // Base64 encoded "misadmin"
				return encoded === target;
			}
		});
		</script>
	</body>
	</html>
