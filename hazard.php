<?php
session_start(); // Ensure session is started
include('config.php'); // Include database configuration
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Micro OSS App</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        /* Mobile-first responsive image container */
        .image-container {
            height: 400px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        
        .image-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.2s;
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
            width: 40px;
            height: 40px;
            padding: 0;
            font-size: 1.2rem;
        }
        
        /* Tablet and larger screens */
        @media (min-width: 768px) {
            .image-container {
                height: 600px;
            }
        }
        
        /* Desktop screens */
        @media (min-width: 992px) {
            .image-container {
                height: 70vh;
            }
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
            
            h3 {
                font-size: 1.25rem;
            }
            
            .form-group {
                margin-bottom: 1rem;
            }
            
            .alert {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

<?php include('includes/nav.php'); ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h2>Hazard Map of Toril District, Davao City</h2>
            <div class="image-container bg-light">
                <img id="hazard-map" src="toril.png" alt="Hazard Map of Toril District, Davao City" />
                <div class="zoom-controls">
                    <button class="btn btn-primary" onclick="zoomIn()">+</button>
                    <button class="btn btn-primary" onclick="zoomOut()">-</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <h2>Barangay Maps</h2>
            <div class="alert alert-info">
                <strong>Search for Barangay:</strong>
            </div>
            <form>
                <div class="form-group">
                    <!-- <label for="province">Province:</label>
                    <select class="form-control" id="province">
                        <option value="">-- Select Province --</option>
                    </select> 

                    <label for="municipality">Municipality/City:</label>
                    <select class="form-control" id="municipality" disabled>
                        <option value="">-- Select Municipality/City --</option>
                    </select>

                    <label for="barangay">Barangay:</label>
                    <select class="form-control" id="barangay" disabled>
                        <option value="">-- Select Barangay --</option>
                    </select>-->
                </div>

                <div class="form-group">
                    <label for="barangaySelect"><strong>Barangay</strong></label>
                    <select class="form-control" id="barangaySelect" onchange="updateImage()">
                        <option value="">--Select--</option>
                        <option value="daliao">Daliao</option>
                        <option value="lizada">Lizada</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="sitioSelect"><strong>Purok/Sitio</strong></label>
                    <select class="form-control" id="sitioSelect" onchange="focusSitio()">
                        <option>--Select--</option>
                    </select>
                </div>
            </form>

            <div class="mt-4">
                <h3>Risk Level</h3>
                <img src="legend.png" alt="Risk Level Legend" class="img-fluid" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white mt-5 p-4 text-center">
    &copy; 2024 Flood Resilience App. All Rights Reserved.
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

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

    function updateImage() {
        const selectedValue = document.getElementById('barangaySelect').value;
        const imageUrl = selectedValue === 'daliao' ? 'daliao.png' : selectedValue === 'lizada' ? 'lizada.png' : 'toril.png';
        document.getElementById('hazard-map').src = imageUrl;
        
        // Reset zoom when changing barangay
        scale = 1;
        document.getElementById('hazard-map').style.transform = `scale(${scale})`;
        document.getElementById('hazard-map').style.transformOrigin = 'center center';
        
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
            
            // Add a visual indicator (optional)
            console.log(`Focusing on ${sitioValue} in ${barangayValue} at position ${focusPoint.x}%, ${focusPoint.y}%`);
        }
    }

    // API for dynamic barangay selection
    const BASE_URL = "https://psgc.gitlab.io/api";
    const provinceSelect = document.getElementById("province");
    const municipalitySelect = document.getElementById("municipality");
    const barangaySelect = document.getElementById("barangay");

    function fetchProvinces() {
        fetch(`${BASE_URL}/provinces/`)
            .then(res => res.json())
            .then(data => {
                data.forEach(province => {
                    const option = new Option(province.name, province.code);
                    provinceSelect.appendChild(option);
                });
            })
            .catch(err => console.error("Error fetching provinces:", err));
    }

    function fetchMunicipalities(provinceCode) {
        municipalitySelect.innerHTML = '<option value="">-- Select Municipality/City --</option>';
        barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
        municipalitySelect.disabled = true;
        barangaySelect.disabled = true;

        fetch(`${BASE_URL}/provinces/${provinceCode}/cities-municipalities/`)
            .then(res => res.json())
            .then(data => {
                data.forEach(municipality => {
                    municipalitySelect.appendChild(new Option(municipality.name, municipality.code));
                });
                municipalitySelect.disabled = false;
            });
    }

    function fetchBarangays(municipalityCode) {
        barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
        barangaySelect.disabled = true;

        fetch(`${BASE_URL}/cities-municipalities/${municipalityCode}/barangays/`)
            .then(res => res.json())
            .then(data => {
                data.forEach(barangay => {
                    barangaySelect.appendChild(new Option(barangay.name, barangay.code));
                });
                barangaySelect.disabled = false;
            });
    }

    provinceSelect.addEventListener("change", () => fetchMunicipalities(provinceSelect.value));
    municipalitySelect.addEventListener("change", () => fetchBarangays(municipalitySelect.value));

    fetchProvinces();
</script>

</body>
</html>
