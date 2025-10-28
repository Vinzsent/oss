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
        .image-container {
            height: 80vh; /* Adjust height as needed */
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .image-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain; /* Ensure the image scales proportionally */
        }
        .download-controls {
            position: absolute;
            bottom: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
        }
        .download-controls button {
            margin: 5px;
        }
    </style>
</head>
<body>
    <?php include('includes/nav.php'); ?>;
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <h2>Downloadables</h2>
                <div class="image-container bg-light">
                    <img id="hazard-map" src="hazard_toril.jpg" alt="Hazard Map of Toril District, Davao City" />
                    <div class="download-controls">
                        <a id="downloadButton" class="btn btn-primary" href="hazard_toril.jpg" download>Download Map</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <h2>Barangay Maps</h2>
                <div class="alert alert-info">
                    <strong>Search for Barangay:</strong>
                </div>
                <div class="alert-settings">
                    <form>
                        <div class="form-group">
                            <select class="form-control" id="barangaySelect" onchange="updateImage()">
                                <option value="">--Select--</option>
                                <option value="daliao">Daliao</option>
                                <option value="lizada">Lizada</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="purok"><strong>Purok/Sitio</strong></label>
                            <select class="form-control" id="frequency" onchange="focusSitio()">
                                <option>--Select--</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white mt-5 p-4 text-center">
        &copy; 2024 Flood Resilience App. All Rights Reserved.
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/shpjs/3.6.1/shp.min.js"></script>
    <script>
        function updateImage() {
            const select = document.getElementById('barangaySelect');
            const selectedValue = select.value;
            let imageUrl = 'hazard_toril.jpg'; // Default image

            if (selectedValue === 'daliao') {
                imageUrl = 'hazard_daliao.jpg';
            } else if (selectedValue === 'lizada') {
                imageUrl = 'hazard_lizada.jpg';
            }

            document.getElementById('hazard-map').src = imageUrl;
            document.getElementById('downloadButton').href = imageUrl; // Update the download link
        }
    </script>
</body>
</html>
