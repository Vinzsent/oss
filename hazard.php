<?php
session_start(); // Ensure session is started
include('config.php'); // Include database configuration
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hazard Map - Micro OSS App</title>
    <!-- Use Bootstrap 5 for consistency -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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

        .image-wrapper {
            position: relative;
            background-color: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e9ecef;
            height: 600px; /* Fixed height for desktop */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .image-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        
        .zoom-controls {
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 10;
            background: rgba(255,255,255,0.7);
            padding: 5px;
            border-radius: 20px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(0,0,0,0.1);
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
            border: none;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            color: #667eea;
            width: 40px;
            height: 40px;
            transition: all 0.2s;
        }
        
        .zoom-controls button:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
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
            .image-wrapper {
                height: 400px;
            }
        }
    </style>
</head>
<body>

<?php include('includes/nav.php'); ?>

<div class="main-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-exclamation-triangle me-3"></i>Hazard Map
        </h1>
        <p class="page-subtitle">Flood susceptibility and risk assessment map for Toril District</p>
    </div>

    <div class="row">
        <!-- Map Image Section -->
        <div class="col-lg-8 mb-4">
            <div class="content-card">
                <h4 class="section-title">
                    <i class="fas fa-map me-2"></i>Map View
                </h4>
                <div class="image-wrapper">
                    <img id="hazard-map" src="toril.png" alt="Hazard Map of Toril District, Davao City" />
                    <div class="zoom-controls">
                        <button onclick="zoomIn()" title="Zoom In"><i class="fas fa-plus"></i></button>
                        <button onclick="zoomOut()" title="Zoom Out"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controls Section -->
        <div class="col-lg-4 mb-4">
            <div class="content-card">
                <h4 class="section-title">
                    <i class="fas fa-columns me-2"></i>Barangay Details
                </h4>
                
                <div class="alert-info-custom mb-4">
                    <strong><i class="fas fa-search me-1"></i> Search for Barangay:</strong>
                    <p class="mb-0 mt-1 small">Select a barangay to view detailed risk areas.</p>
                </div>
                
                <form>
                    <!-- Uncomment if needed in future
                    <div class="mb-3">
                        <label class="form-label fw-bold">Province</label>
                        <select class="form-select" id="province">
                            <option value="">-- Select Province --</option>
                        </select>
                    </div>
                    -->

                    <div class="mb-3">
                        <label for="barangaySelect" class="form-label fw-bold">Barangay</label>
                        <select class="form-select" id="barangaySelect" onchange="updateImage()">
                            <option value="">-- Select --</option>
                            <option value="daliao">Daliao</option>
                            <option value="lizada">Lizada</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sitioSelect" class="form-label fw-bold">Purok/Sitio</label>
                        <select class="form-select" id="sitioSelect" onchange="focusSitio()">
                            <option value="">-- Select --</option>
                        </select>
                    </div>
                </form>

                <div class="mt-4 border-top pt-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Risk Level Legend</h5>
                    <img src="legend.png" alt="Risk Level Legend" class="img-fluid rounded border" style="width: 100%;">
                </div>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="container">
        &copy; 2024 Flood Resilience App. All Rights Reserved.
    </div>
</footer>


<script>
    let scale = 1;

    function zoomIn() {
        scale += 0.1;
        document.getElementById('hazard-map').style.transform = `scale(${scale})`;
    }

    function zoomOut() {
        scale = Math.max(0.1, scale - 0.1);
        document.getElementById('hazard-map').style.transform = `scale(${scale})`;
    }

    // Keep existing logic for updating images and focusing
    function updateImage() {
        const selectedValue = document.getElementById('barangaySelect').value;
        const imageUrl = selectedValue === 'daliao' ? 'daliao.png' : selectedValue === 'lizada' ? 'lizada.png' : 'toril.png';
        const img = document.getElementById('hazard-map');
        
        // Add fade effect
        img.style.opacity = '0';
        setTimeout(() => {
            img.src = imageUrl;
            img.style.opacity = '1';
        }, 300);
        
        // Reset zoom when changing barangay
        scale = 1;
        img.style.transform = `scale(${scale})`;
        img.style.transformOrigin = 'center center';
        
        // Populate sitio dropdown based on selected barangay
        const sitioSelect = document.getElementById('sitioSelect');
        sitioSelect.innerHTML = '<option value="">--Select--</option>';
        
        if (selectedValue === 'daliao') {
            const daliaoPuroks = [
                'Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5',
                'Purok 6', 'Purok 7', 'Purok 8', 'Purok 9', 'Purok 10',
                'Sitio Crossing', 'Sitio Riverside', 'Sitio Upper Daliao'
            ];
            daliaoPuroks.forEach(purok => {
                const option = document.createElement('option');
                option.value = purok.toLowerCase().replace(/\s+/g, '_');
                option.textContent = purok;
                sitioSelect.appendChild(option);
            });
        } else if (selectedValue === 'lizada') {
            const lizadaPuroks = [
                'Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5',
                'Purok 6', 'Purok 7', 'Purok 8', 'Purok 9', 'Purok 10',
                'Sitio Proper', 'Sitio Lower Lizada', 'Sitio Upper Lizada'
            ];
            lizadaPuroks.forEach(purok => {
                const option = document.createElement('option');
                option.value = purok.toLowerCase().replace(/\s+/g, '_');
                option.textContent = purok;
                sitioSelect.appendChild(option);
            });
        }
    }

    function focusSitio() {
        const barangayValue = document.getElementById('barangaySelect').value;
        const sitioValue = document.getElementById('sitioSelect').value;
        
        if (!sitioValue || sitioValue === '') return;
        
        // Define approximate focus points for each purok/sitio within the image
        // These coordinates represent percentage positions within the image (0-100)
        const focusPoints = {
            daliao: {
                'purok_1': { x: 20, y: 30 },
                'purok_2': { x: 30, y: 25 },
                'purok_3': { x: 40, y: 35 },
                'purok_4': { x: 50, y: 40 },
                'purok_5': { x: 60, y: 30 },
                'purok_6': { x: 25, y: 50 },
                'purok_7': { x: 35, y: 55 },
                'purok_8': { x: 45, y: 60 },
                'purok_9': { x: 55, y: 50 },
                'purok_10': { x: 65, y: 45 },
                'sitio_crossing': { x: 15, y: 70 },
                'sitio_riverside': { x: 70, y: 60 },
                'sitio_upper_daliao': { x: 80, y: 20 }
            },
            lizada: {
                'purok_1': { x: 25, y: 35 },
                'purok_2': { x: 35, y: 30 },
                'purok_3': { x: 45, y: 40 },
                'purok_4': { x: 55, y: 35 },
                'purok_5': { x: 65, y: 45 },
                'purok_6': { x: 30, y: 55 },
                'purok_7': { x: 40, y: 60 },
                'purok_8': { x: 50, y: 55 },
                'purok_9': { x: 60, y: 60 },
                'purok_10': { x: 70, y: 50 },
                'sitio_proper': { x: 50, y: 70 },
                'sitio_lower_lizada': { x: 20, y: 75 },
                'sitio_upper_lizada': { x: 75, y: 25 }
            }
        };
        
        const focusPoint = focusPoints[barangayValue]?.[sitioValue];
        
        if (focusPoint) {
            // Zoom to 2x scale
            scale = 2;
            const img = document.getElementById('hazard-map');
            
            // Calculate transform origin based on focus point
            const originX = focusPoint.x + '%';
            const originY = focusPoint.y + '%';
            
            img.style.transformOrigin = `${originX} ${originY}`;
            img.style.transform = `scale(${scale})`;
        }
    }

    // Keep PSGC API functions in case we enable the province/municipality filters later
    const BASE_URL = "https://psgc.gitlab.io/api";
    // ... existing PSGC code ...
</script>

</body>
</html>
