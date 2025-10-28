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
    <title>Micro OSS App</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.min.js"></script>
    <style>
        /* Mobile-first responsive map */
        #map {
            height: 300px;
            width: 100%;
        }
        
        /* Tablet and larger screens */
        @media (min-width: 768px) {
            #map {
                height: 400px;
            }
        }

        #photoPreview img {
            max-width: 100%;
            max-height: 300px;
            display: none;
        }

        table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        thead th {
            background-color: #f8f9fa;
        }

        .card {
            margin-bottom: 20px;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card-img-top {
            border-radius: 0.5rem 0.5rem 0 0;
            max-height: 400px;
            object-fit: cover;
        }

        /* Sticky search sidebar */
        .sticky-search {
            position: sticky;
            top: 80px;
            z-index: 1000;
        }

        /* Sticky Footer Styles */
        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .content-wrapper {
            flex: 1 0 auto;
        }

        footer {
            flex-shrink: 0;
        }
        
        /* Mobile spacing adjustments */
        @media (max-width: 767px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
            
            h2 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }
            
            h5 {
                font-size: 1.1rem;
            }
            
            .form-group {
                margin-bottom: 1rem;
            }
            
            .btn {
                width: 100%;
                margin-top: 0.5rem;
            }
            
            .card {
                margin-bottom: 1.5rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .card-img-top {
                max-height: 300px;
            }
            
            /* Disable sticky on mobile for better UX */
            .sticky-search {
                position: static;
            }
            
            .mt-5 {
                margin-top: 2rem !important;
            }
            
            /* Modal adjustments for mobile */
            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }
            
            .modal-body {
                padding: 1rem;
            }
            
            .modal-title {
                font-size: 1.25rem;
            }
        }
        
        /* Extra small devices */
        @media (max-width: 576px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            .card-title {
                font-size: 1rem;
            }
            
            .card-text {
                font-size: 0.9rem;
            }
            
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
            
            #map {
                height: 250px;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <?php include('includes/nav.php'); ?>

    <div class="content-wrapper">
        <div class="container mt-5">
            <div class="row">
                <!-- Gallery Section -->
                <div class="col-md-8">
                    <h2>Gallery</h2>
                    <?php
                    // Initialize to avoid undefined variable notices when no filters are selected
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
                            <div class="card">
                                <img src="data:image/jpeg;base64,<?= $photo ?>" class="card-img-top" alt="<?= $barangay ?> - <?= $sitioPurok ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $barangay ?> - <?= $sitioPurok ?></h5>
                                    <p class="card-text"><?= $description ?></p>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        if (!empty($selectedSitio) || !empty($selectedBarangay)) {
                            echo "<p>No records found for the selected filters.</p>";
                        } else {
                            echo "<p>Please select a sitio or barangay to view the flood archive.</p>";
                        }
                    }
                    if ($stmt instanceof mysqli_stmt) {
                        $stmt->close();
                    }
                    ?>
                    <!-- Upload Photo Button -->
                    <button class="btn btn-primary mt-3" data-toggle="modal" data-target="#uploadPhotoModal">Upload Photo</button>
                </div>

                <!-- Search Section -->
                <div class="col-md-4">
                    <div class="sticky-search">
                        <h2>Search for Barangay</h2>
                        <form method="post">
                            <div class="form-group">
                                <label for="barangay">Barangay</label>
                                <select class="form-control" id="barangay" name="barangay">
                                    <option value="">--Select Barangay--</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sitio">Sitio</label>
                                <select class="form-control" id="sitio" name="sitio" onchange="this.form.submit()">
                                    <option value="">--Select Sitio--</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Photo Modal -->
    <div class="modal fade" id="uploadPhotoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Photo</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="upload_photo.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="barangayModal">Barangay</label>
                            <select class="form-control" id="barangayModal" name="barangay" required>
                                <option value="">--Select Barangay--</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sitioModal">Sitio</label>
                            <select class="form-control" id="sitioModal" name="sitio" required>
                                <option value="">--Select Sitio--</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="photo">Photo</label>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required onchange="previewPhoto(event)">
                        </div>
                        <!-- Location Fields -->
                        <label for="latitude">Latitude:</label>
                        <input type="text" name="latitude" id="latitude" placeholder="Select on map" class="form-control" required readonly>
                        <label for="longitude">Longitude:</label>
                        <input type="text" name="longitude" id="longitude" placeholder="Select on map" class="form-control" required readonly><br>
                        <div class="form-group">
                            <label>Preview</label>
                            <div id="photoPreview" style="border: 1px solid #ddd; padding: 10px; text-align: center;">
                                <img id="previewImage" src="#" alt="Photo Preview" style="max-width: 100%; max-height: 300px; display: none;">
                            </div>
                        </div>
                        <h2 class="mt-5">Specify Location on Map</h2>
                        <div id="map"></div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 p-4 text-center">
        &copy; 2024 Flood Resilience App. All Rights Reserved.
    </footer>

    <!-- Optimized Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
            if (file) {
                const reader = new FileReader();
                reader.onload = () => {
                    const previewImage = document.getElementById("previewImage");
                    previewImage.src = reader.result;
                    previewImage.style.display = "block";
                };
                reader.readAsDataURL(file);
            }
        }
    </script>

    <script>
        // Initialize Leaflet map only when modal is visible
        let mapInstance = null;
        let markerInstance = null;

        function initLeafletMap() {
            const defaultLocation = [7.0731, 125.6128];

            if (!mapInstance) {
                mapInstance = L.map("map").setView(defaultLocation, 14);

                // Add OpenStreetMap tiles
                L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                }).addTo(mapInstance);

                // Draggable marker
                markerInstance = L.marker(defaultLocation, { draggable: true }).addTo(mapInstance);
                markerInstance.on("moveend", (event) => {
                    const { lat, lng } = event.target.getLatLng();
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
                mapInstance.invalidateSize();
            }, 200);
        }

        // Bootstrap modal event: initialize/refresh map when shown
        document.addEventListener("DOMContentLoaded", function () {
            const modalEl = document.getElementById('uploadPhotoModal');
            if (modalEl) {
                // For Bootstrap 4 with jQuery available
                if (window.$ && $(modalEl).on) {
                    $(modalEl).on('shown.bs.modal', function () {
                        initLeafletMap();
                    });
                } else {
                    // Fallback: observe attribute changes to detect visibility
                    modalEl.addEventListener('transitionend', () => initLeafletMap());
                }
            }
        });
    </script>

</body>

</html>