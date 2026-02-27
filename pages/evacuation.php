<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evacuation Map - Micro OSS App</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/mobile-responsive.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .map-container {
            height: 600px;
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .map-container {
                height: 400px;
            }
        }
    </style>
</head>

<body>

    <!-- Mobile-Only Top Header -->
    <div class="mobile-top-header d-lg-none bg-primary text-white px-3 py-2 d-flex align-items-center justify-content-between shadow-sm">
        <div class="col-2">
            <button class="btn text-white p-2 border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                <i class="fas fa-bars-staggered fa-lg"></i>
            </button>
        </div>
        <div class="mobile-brand fw-bold text-center flex-grow-1 px-2" style="font-size: 1.2rem; white-space: nowrap; letter-spacing: 0.5px;">
            Micro OSS
        </div>
        <div style="width: 40px;"></div> <!-- Balancer to keep text centered -->
    </div>

    <?php include('../includes/mobile_sidebar.php'); ?>
    <?php include('../includes/mobile_bottom_nav.php'); ?>

    <div class="main-container">

        <div class="page-header hidden-mobile text-center">
            <h1><i class="fas fa-map-marked-alt me-2"></i>Evacuation Map</h1>
            <p>Find safe zones and evacuation routes in your area</p>
        </div>

        <div class="row">

            <!-- MAP -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div id="map" class="map-container"></div>
                    </div>
                </div>
            </div>

            <!-- SIDEBAR -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="fw-bold mb-3">Evacuation Controls</h5>

                        <button class="btn btn-primary w-100 mb-2" onclick="resetMap()">Clear Map</button>

                        <button class="btn btn-success w-100 mb-3" onclick="findNearest()">Find Nearest Evacuation Center</button>

                        <hr>
                        <h6 class="fw-bold">Info Panel</h6>
                        <div id="info-panel"></div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="bg-dark text-white text-center p-3 mt-4">
        &copy; 2026 Micro OSS Flood Resilience App
    </footer>

    <!-- GOOGLE MAP SCRIPT -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRkSkfYweTDI09aam31aXA-Vh69J7Ui6Y&libraries=geometry"></script>
    <script src="../assets/js/map-init.js"></script>

    <?php include('../includes/scripts.php'); ?>
</body>

</html>