<?php
// Start session to access user data
session_start();

// Include database configuration file
include('config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Micro OSS App</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/shpjs/3.6.1/shp.min.css" />
    <style>
        /* Sticky Navigation */
        nav {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: #343a40; /* Optional: Ensure background color for sticky navbar */
        }

        .video-container {
            height: 80vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .video-container video {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .zoom-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            z-index: 10;
        }
        .zoom-controls button {
            margin: 5px;
        }
        footer {
            background-color: #343a40;
            color: white;
            padding: 1rem;
            text-align: center;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <?php include('includes/nav.php'); ?>

    <div class="container mt-5">
        <div class="row">
            <!-- Alert Signal Video -->
            <div class="col-md-8">
                <h2>Alert Signal</h2>
                <div class="video-container bg-light">
                    <video id="hazard-map" controls>
                        <source src="alert.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="zoom-controls">
                        <button class="btn btn-primary" onclick="zoomIn()">+</button>
                        <button class="btn btn-primary" onclick="zoomOut()">-</button>
                    </div>
                </div>
            </div>

            <!-- Dropdowns for Province, Municipality, Barangay -->
            <div class="col-md-4">
                <label for="province">Province:</label>
                <select id="province" class="form-control">
                    <option value="">-- Select Province --</option>
                </select>

                <label for="municipality">Municipality/City:</label>
                <select id="municipality" class="form-control" disabled>
                    <option value="">-- Select Municipality/City --</option>
                </select>

                <label for="barangay">Barangay:</label>
                <select id="barangay" class="form-control" disabled>
                    <option value="">-- Select Barangay --</option>
                </select>

                <label for="sitio">Sitio (Optional - Type Manually):</label>
                <input type="text" id="sitio" class="form-control" placeholder="Enter Sitio Name">
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>

    <!-- External Libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/shpjs/3.6.1/shp.min.js"></script>

    <script>
        let scale = 1; // Initial scale for zoom functionality

        // Zoom In Function
        function zoomIn() {
            scale += 0.1;
            document.getElementById('hazard-map').style.transform = `scale(${scale})`;
        }

        // Zoom Out Function
        function zoomOut() {
            scale -= 0.1;
            if (scale < 0.1) scale = 0.1; // Prevent zooming out too much
            document.getElementById('hazard-map').style.transform = `scale(${scale})`;
        }

        // Update video based on selected barangay
        function updateVideo() {
            const select = document.getElementById('barangay');
            const selectedValue = select.value;
            let videoUrl = 'toril.mp4'; // Default video

            // Change video based on selected barangay
            if (selectedValue === 'daliao') {
                videoUrl = 'daliao.mp4';
            } else if (selectedValue === 'lizada') {
                videoUrl = 'lizada.mp4';
            }

            const videoElement = document.getElementById('hazard-map');
            videoElement.src = videoUrl;
            videoElement.load();
        }

        // Fetch and populate provinces
        const BASE_URL = "https://psgc.gitlab.io/api";
        const provinceSelect = document.getElementById("province");
        const municipalitySelect = document.getElementById("municipality");
        const barangaySelect = document.getElementById("barangay");

        function fetchProvinces() {
            fetch(`${BASE_URL}/provinces/`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(province => {
                        const option = document.createElement("option");
                        option.value = province.code;
                        option.textContent = province.name;
                        provinceSelect.appendChild(option);
                    });
                })
                .catch(error => console.error("Error fetching provinces:", error));
        }

        function fetchMunicipalities(provinceCode) {
            municipalitySelect.innerHTML = '<option value="">-- Select Municipality/City --</option>';
            barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
            municipalitySelect.disabled = true;
            barangaySelect.disabled = true;

            fetch(`${BASE_URL}/provinces/${provinceCode}/cities-municipalities/`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(municipality => {
                        const option = document.createElement("option");
                        option.value = municipality.code;
                        option.textContent = municipality.name;
                        municipalitySelect.appendChild(option);
                    });
                    municipalitySelect.disabled = false;
                })
                .catch(error => console.error("Error fetching municipalities:", error));
        }

        function fetchBarangays(municipalityCode) {
            barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
            barangaySelect.disabled = true;

            fetch(`${BASE_URL}/cities-municipalities/${municipalityCode}/barangays/`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(barangay => {
                        const option = document.createElement("option");
                        option.value = barangay.code;
                        option.textContent = barangay.name;
                        barangaySelect.appendChild(option);
                    });
                    barangaySelect.disabled = false;
                })
                .catch(error => console.error("Error fetching barangays:", error));
        }

        // Event Listeners for province and municipality dropdowns
        provinceSelect.addEventListener("change", () => {
            const provinceCode = provinceSelect.value;
            if (provinceCode) {
                fetchMunicipalities(provinceCode);
            } else {
                municipalitySelect.innerHTML = '<option value="">-- Select Municipality/City --</option>';
                barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
                municipalitySelect.disabled = true;
                barangaySelect.disabled = true;
            }
        });

        municipalitySelect.addEventListener("change", () => {
            const municipalityCode = municipalitySelect.value;
            if (municipalityCode) {
                fetchBarangays(municipalityCode);
            } else {
                barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
                barangaySelect.disabled = true;
            }
        });

        // Initialize provinces on page load
        fetchProvinces();
    </script>
</body>
</html>
