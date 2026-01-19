<?php
// Start session
session_start();

// Include database configuration
include('config.php');

// Handle selected barangay and sitio
$selectedBarangay = isset($_POST['barangay']) ? htmlspecialchars($_POST['barangay']) : '';
$selectedSitio = isset($_POST['sitio']) ? htmlspecialchars($_POST['sitio']) : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Micro Online Synthesis System</title>
    <!-- Use FontAwesome 6.4.0 like hazard_vul.php -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        /* Styles from hazard_vul.php */
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            flex: 1;
            width: 100%;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }

        .page-subtitle {
            font-size: 1.2rem;
            margin: 10px 0 0 0;
            text-align: center;
            opacity: 0.9;
        }

        /* Container for content sections (replacing plain cards) */
        .content-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-left: 4px solid #8b5cf6;
        }

        .stats-card h5,
        .stats-card h2 {
            color: #8b5cf6;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .section-title {
            color: #8b5cf6;
            font-weight: bold;
            margin-bottom: 20px;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 10px;
        }

        /* Button Gradient Style */
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            transition: transform 0.2s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white;
        }

        /* Gallery Card Styles */
        .gallery-card {
            margin-bottom: 20px;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
            border: none;
            background: #fff;
            overflow: hidden;
        }

        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .card-img-top {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        /* Map Styles */
        #map {
            height: 300px;
            width: 100%;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        @media (min-width: 768px) {
            #map {
                height: 400px;
            }
        }

        /* Sticky Search */
        .sticky-search {
            position: sticky;
            top: 20px;
            z-index: 900;
        }

        /* Modal Styles matching hazard_vul.php */
        .modal-header {
            background-color: #8b5cf6;
            color: white;
        }

        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<body>
    <!-- Navigation Bar -->
    <?php include('includes/nav.php'); ?>

    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-images me-3"></i>Community Gallery
            </h1>
            <p class="page-subtitle">Visual Archive of Barangay Conditions</p>
        </div>

        <div class="row">
            <!-- Gallery Section -->
            <div class="col-lg-8">
                <div class="content-container">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                        <h4 class="mb-0 text-purple" style="color: #8b5cf6; font-weight: bold;">
                            <i class="fas fa-photo-video me-2"></i>Photo Stream
                        </h4>
                        <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                            <i class="fas fa-upload me-2"></i>Upload Photo
                        </button>
                    </div>

                    <div class="row">
                        <?php
                        $stmt = null;
                        $result = null;

                        if (!empty($selectedSitio)) {
                            // If sitio is selected, filter by sitio and optionally by barangay if provided
                            if (!empty($selectedBarangay)) {
                                $stmt = $conn->prepare("SELECT barangay, sitio_purok, photo, description FROM flood_archive WHERE sitio_purok = ? AND barangay = ?");
                                $stmt->bind_param("ss", $selectedSitio, $selectedBarangay);
                            } else {
                                $stmt = $conn->prepare("SELECT barangay, sitio_purok, photo, description FROM flood_archive WHERE sitio_purok = ?");
                                $stmt->bind_param("s", $selectedSitio);
                            }
                            $stmt->execute();
                            $result = $stmt->get_result();
                        } elseif (!empty($selectedBarangay)) {
                            // Fallback: filter by barangay only
                            $stmt = $conn->prepare("SELECT barangay, sitio_purok, photo, description FROM flood_archive WHERE barangay = ?");
                            $stmt->bind_param("s", $selectedBarangay);
                            $stmt->execute();
                            $result = $stmt->get_result();
                        }

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $photo = base64_encode($row['photo']);
                                $barangay = htmlspecialchars($row['barangay'], ENT_QUOTES, 'UTF-8');
                                $sitioPurok = htmlspecialchars($row['sitio_purok'], ENT_QUOTES, 'UTF-8');
                                $description = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');
                        ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card gallery-card h-100">
                                        <img src="data:image/jpeg;base64,<?= $photo ?>" class="card-img-top" alt="<?= $barangay ?> - <?= $sitioPurok ?>">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-2 text-muted"><?= $barangay ?> - <?= $sitioPurok ?></h6>
                                            <p class="card-text"><?= $description ?></p>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo '<div class="col-12 text-center py-5">';
                            echo '<div class="text-muted"><i class="fas fa-images fa-3x mb-3"></i><p class="lead">';
                            if (!empty($selectedSitio) || !empty($selectedBarangay)) {
                                echo "No records found for the selected filters.";
                            } else {
                                echo "Select a location to view photos.";
                            }
                            echo '</p></div></div>';
                        }
                        if ($stmt instanceof mysqli_stmt) {
                            $stmt->close();
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Search Section -->
            <div class="col-lg-4">
                <div class="sticky-search">
                    <div class="stats-card">
                        <h5><i class="fas fa-search me-2"></i>Filter Gallery</h5>
                        <form method="post">
                            <div class="mb-3">
                                <label for="barangay" class="form-label text-muted">Barangay</label>
                                <select class="form-select" id="barangay" name="barangay">
                                    <option value="">--Select Barangay--</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="sitio" class="form-label text-muted">Sitio</label>
                                <select class="form-select" id="sitio" name="sitio" onchange="this.form.submit()">
                                    <option value="">--Select Sitio--</option>
                                </select>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-primary btn-sm">Apply Filters</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Photo Modal -->
    <div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadPhotoModalLabel"><i class="fas fa-upload me-2"></i>Upload Photo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="upload_photo.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="barangayModal" class="form-label">Barangay</label>
                                <select class="form-select" id="barangayModal" name="barangay" required>
                                    <option value="">--Select Barangay--</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sitioModal" class="form-label">Sitio</label>
                                <select class="form-select" id="sitioModal" name="sitio" required>
                                    <option value="">--Select Sitio--</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="photo" class="form-label">Photo</label>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required onchange="previewPhoto(event)">
                        </div>
                        <!-- Location Fields -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" name="latitude" id="latitude" placeholder="Select on map" class="form-control" required readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" name="longitude" id="longitude" placeholder="Select on map" class="form-control" required readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Preview</label>
                            <div id="photoPreview" class="text-center p-2 bg-light rounded border">
                                <img id="previewImage" src="#" alt="Photo Preview" style="max-width: 100%; max-height: 300px; display: none; margin: 0 auto;">
                                <span id="previewPlaceholder" class="text-muted">No image selected</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Specify Location on Map</label>
                            <div id="map"></div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" style="background: #8b5cf6; border-color: #8b5cf6;">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-center py-4 mt-auto border-top">
        <div class="container">
            <p class="mb-0 text-dark">&copy; 2024 Flood Resilience App. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Dynamic data fetching functions
        async function fetchBarangays() {
            try {
                const response = await fetch('get_locations.php?action=barangays');
                return await response.json();
            } catch (error) {
                console.error('Error fetching barangays:', error);
                return [];
            }
        }

        async function fetchSitios(barangay) {
            try {
                const response = await fetch(`get_locations.php?action=sitios&barangay=${encodeURIComponent(barangay)}`);
                return await response.json();
            } catch (error) {
                console.error('Error fetching sitios:', error);
                return [];
            }
        }

        // Populate barangay dropdown
        async function populateBarangayDropdown(selectId) {
            const barangays = await fetchBarangays();
            const selectElement = document.getElementById(selectId);

            // Clear existing options except the first one
            selectElement.innerHTML = '<option value="">--Select Barangay--</option>';

            barangays.forEach(barangay => {
                const option = document.createElement('option');
                option.value = barangay.toLowerCase();
                option.textContent = barangay;
                selectElement.appendChild(option);
            });
        }

        // Update sitio dropdown based on selected barangay
        async function updateSitioOptions(barangayId, sitioId) {
            const barangaySelect = document.getElementById(barangayId);
            const sitioSelect = document.getElementById(sitioId);
            const selectedBarangay = barangaySelect.value;

            // Clear sitio dropdown
            sitioSelect.innerHTML = '<option value="">--Select Sitio--</option>';

            if (selectedBarangay) {
                // Find the actual barangay name from the selected option text
                const selectedOption = barangaySelect.options[barangaySelect.selectedIndex];
                const barangayName = selectedOption.textContent;

                const sitios = await fetchSitios(barangayName);

                sitios.forEach(sitio => {
                    const option = document.createElement('option');
                    option.value = sitio;
                    option.textContent = sitio;
                    sitioSelect.appendChild(option);
                });
            }
        }

        // Initialize event listeners
        document.addEventListener("DOMContentLoaded", async () => {
            // Populate barangay dropdowns
            await populateBarangayDropdown('barangay');
            await populateBarangayDropdown('barangayModal');

            // Set up change event listeners
            ["barangay", "barangayModal"].forEach((barangayId) => {
                const sitioId = barangayId.replace("barangay", "sitio");
                document.getElementById(barangayId).addEventListener("change", () =>
                    updateSitioOptions(barangayId, sitioId)
                );
            });
        });

        // Preview uploaded photo
        function previewPhoto(event) {
            const file = event.target.files[0];
            const previewImage = document.getElementById("previewImage");
            const placeholder = document.getElementById("previewPlaceholder");

            if (file) {
                const reader = new FileReader();
                reader.onload = () => {
                    previewImage.src = reader.result;
                    previewImage.style.display = "block";
                    if (placeholder) placeholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                previewImage.style.display = "none";
                if (placeholder) placeholder.style.display = 'block';
            }
        }
    </script>

    <script>
        // Initialize Leaflet map only when modal is visible
        let mapInstance = null;
        let markerInstance = null;

        function initLeafletMap() {
            const defaultLocation = [7.0731, 125.6128];
            const mapContainer = document.getElementById("map");

            if (!mapInstance && mapContainer) {
                mapInstance = L.map("map").setView(defaultLocation, 14);

                // Add OpenStreetMap tiles
                L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                }).addTo(mapInstance);

                // Draggable marker
                markerInstance = L.marker(defaultLocation, {
                    draggable: true
                }).addTo(mapInstance);
                markerInstance.on("moveend", (event) => {
                    const {
                        lat,
                        lng
                    } = event.target.getLatLng();
                    document.getElementById("latitude").value = lat.toFixed(6);
                    document.getElementById("longitude").value = lng.toFixed(6);
                });

                // Geolocation
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const userLocation = [position.coords.latitude, position.coords.longitude];
                            mapInstance.setView(userLocation, 14);
                            markerInstance.setLatLng(userLocation);
                            document.getElementById("latitude").value = userLocation[0].toFixed(6);
                            document.getElementById("longitude").value = userLocation[1].toFixed(6);
                        },
                        (error) => console.error("Geolocation error:", error)
                    );
                }
            }

            // Important: fix rendering after modal becomes visible
            setTimeout(() => {
                if (mapInstance) mapInstance.invalidateSize();
            }, 300);
        }

        // Bootstrap 5 modal event
        document.addEventListener("DOMContentLoaded", function() {
            const modalEl = document.getElementById('uploadPhotoModal');
            if (modalEl) {
                modalEl.addEventListener('shown.bs.modal', function() {
                    initLeafletMap();
                });
            }
        });
    </script>
</body>

</html>