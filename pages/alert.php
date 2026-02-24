<?php
// Start session to access user data
session_start();

// Include database configuration file
include('../config.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert Signal - Micro OSS App</title>
    <!-- Use Bootstrap 5 for consistency -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            padding: 0;
            margin: 0;
        }

        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            flex: 1;
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

        .content-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            height: 100%;
        }

        .section-title {
            color: #6b21a8;
            font-weight: bold;
            margin-bottom: 20px;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 10px;
        }

        .video-wrapper {
            position: relative;
            width: 100%;
            height: 85vh;
            /* Maximize height for readability */
            background-color: #000;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .video-wrapper video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .zoom-controls {
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 10;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px;
            border-radius: 20px;
            backdrop-filter: blur(5px);
        }

        .zoom-controls button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-weight: bold;
            font-size: 1.2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            transform: translateY(-2px);
        }

        .alert-info-custom {
            background-color: #e0f2fe;
            border-left: 4px solid #0ea5e9;
            color: #0c4a6e;
            padding: 15px;
            border-radius: 6px;
        }

        footer {
            background-color: #1f2937 !important;
            color: white;
            padding: 20px 0;
            margin-top: 40px;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-container {
                padding: 10px;
            }

            .page-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>
    <?php include('../includes/nav.php'); ?>
    <?php include('../includes/mobile_bottom_nav.php'); ?>

    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-satellite-dish me-3"></i>Alert Signal
            </h1>
            <p class="page-subtitle">Monitor real-time alerts and video feeds for your location</p>
        </div>

        <div class="row">
            <!-- Video Section -->
            <div class="col-lg-8 mb-4">
                <div class="content-card">
                    <h4 class="section-title">
                        <i class="fas fa-video me-2"></i>Live Feed
                    </h4>
                    <div class="video-wrapper">
                        <video id="hazard-map" controls>
                            <source src="../alert.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <div class="zoom-controls">
                            <button class="btn btn-primary" onclick="zoomIn()" title="Zoom In"><i class="fas fa-plus"></i></button>
                            <button class="btn btn-primary" onclick="zoomOut()" title="Zoom Out"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controls Section -->
            <div class="col-lg-4 mb-4">
                <div class="content-card">
                    <h4 class="section-title">
                        <i class="fas fa-map-marker-alt me-2"></i>Location Select
                    </h4>

                    <div class="alert-info-custom mb-4">
                        <strong><i class="fas fa-info-circle me-1"></i> Area Selection:</strong>
                        <p class="mb-0 mt-1 small">Choose a location to update the alert feed.</p>
                    </div>

                    <form>
                        <div class="mb-3">
                            <label for="province" class="form-label fw-bold">Province</label>
                            <select id="province" class="form-select">
                                <option value="">-- Select Province --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="municipality" class="form-label fw-bold">Municipality/City</label>
                            <select id="municipality" class="form-select" disabled>
                                <option value="">-- Select Municipality/City --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="barangay" class="form-label fw-bold">Barangay</label>
                            <select id="barangay" class="form-select" disabled>
                                <option value="">-- Select Barangay --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="sitio" class="form-label fw-bold">Sitio <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="text" id="sitio" class="form-control" placeholder="Enter Sitio Name">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            &copy; 2024 Flood Resilience App. All Rights Reserved.
        </div>
    </footer>


    <?php include('../includes/scripts.php'); ?>
</body>

</html>