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
    <title>Download Resources - Micro Online Synthesis System</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet css (kept for compatibility if needed) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <style>
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

        /* Content Cards */
        .content-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-left: 4px solid #8b5cf6;
        }

        .stats-card h5 {
            color: #8b5cf6;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Map/Image Container */
        .image-wrapper {
            width: 100%;
            height: 600px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            border: 1px solid #e9ecef;
            position: relative;
        }

        .image-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        /* Buttons */
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            transition: transform 0.2s ease;
        }

        .btn-gradient:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        footer {
            margin-top: auto;
        }
    </style>
</head>

<body>
    <?php include('includes/nav.php'); ?>

    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-download me-3"></i>Download Resources
            </h1>
            <p class="page-subtitle">Access and download hazard maps and barangay data</p>
        </div>

        <div class="row">
            <!-- Map Preview Section -->
            <div class="col-lg-8 mb-4">
                <div class="content-card">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                        <h4 class="mb-0" style="color: #8b5cf6; font-weight: bold;">
                            <i class="fas fa-map me-2"></i>Map Preview
                        </h4>
                        <span class="badge bg-primary rounded-pill">Visual Aid</span>
                    </div>

                    <div class="image-wrapper">
                        <img id="hazard-map" src="hazard_toril.jpg" alt="Hazard Map">
                    </div>
                </div>
            </div>

            <!-- Controls Section -->
            <div class="col-lg-4">
                <div class="stats-card">
                    <h5><i class="fas fa-filter me-2"></i>Map Settings</h5>

                    <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis">
                        <i class="fas fa-info-circle me-2"></i>Select a location to update the map view.
                    </div>

                    <form>
                        <div class="mb-3">
                            <label for="barangaySelect" class="form-label text-muted fw-bold">Barangay</label>
                            <select class="form-select form-select-lg" id="barangaySelect" onchange="updateImage()" style="border-color: #8b5cf6;">
                                <option value="">-- Select Barangay --</option>
                                <option value="daliao">Daliao</option>
                                <option value="lizada">Lizada</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="frequency" class="form-label text-muted fw-bold">Purok/Sitio</label>
                            <select class="form-select" id="frequency">
                                <option>-- Select Purok --</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <a id="downloadButton" class="btn btn-gradient py-3 fw-bold" href="hazard_toril.jpg" download>
                                <i class="fas fa-download me-2"></i>Download Map
                            </a>
                        </div>
                    </form>
                </div>

                <div class="stats-card mt-3">
                    <h5><i class="fas fa-question-circle me-2"></i>Help</h5>
                    <p class="text-muted small mb-0">
                        Choose a specific Barangay to view its detailed hazard map. Use the download button to save the high-resolution image to your device.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-center py-4 border-top">
        <div class="container">
            <p class="mb-0 text-dark">&copy; 2024 Flood Resilience App. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Keeping legacy scripts references if strictly needed, though logic is Vanilla JS below -->
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

            const imgElement = document.getElementById('hazard-map');
            const downloadBtn = document.getElementById('downloadButton');

            // Add simple fade effect
            imgElement.style.opacity = '0.5';

            setTimeout(() => {
                imgElement.src = imageUrl;
                downloadBtn.href = imageUrl;
                downloadBtn.download = imageUrl; // Suggest filename

                imgElement.onload = () => {
                    imgElement.style.opacity = '1';
                };
            }, 200);
        }
    </script>
</body>

</html>