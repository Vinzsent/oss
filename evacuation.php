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
    <title>Evacuation Map - Micro OSS App</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6.4.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    
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

        .map-container {
            height: 600px;
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e9d5f7;
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .map-container {
                height: 400px;
            }
            .main-container {
                padding: 10px;
            }
            .page-title {
                font-size: 1.8rem;
            }
        }
        
        .section-title {
            color: #6b21a8;
            font-weight: bold;
            margin-bottom: 20px;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 10px;
        }

        .form-label {
            font-weight: 600;
            color: #4b5563;
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
        
        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .legend-item i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        
        footer {
            background-color: #1f2937 !important;
            color: white;
            padding: 20px 0;
            margin-top: 40px;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include('includes/nav.php'); ?>  
    
    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-map-marked-alt me-3"></i>Evacuation Map
            </h1>
            <p class="page-subtitle">Find safe zones and evacuation routes in your area</p>
        </div>

        <div class="row">
            <!-- Map Section -->
            <div class="col-lg-8 mb-4">
                <div class="content-card">
                    <h4 class="section-title">
                        <i class="fas fa-globe-asia me-2"></i>Map View
                    </h4>
                    <div id="map" class="map-container">
                        <div id="hazard-map" style="width: 100%; height: 100%;"></div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Controls -->
            <div class="col-lg-4 mb-4">
                <div class="content-card">
                    <h4 class="section-title">
                        <i class="fas fa-columns me-2"></i>Barangay Maps
                    </h4>
                    
                    <div class="alert-info-custom mb-4">
                        <strong><i class="fas fa-info-circle me-1"></i> Search for Barangay:</strong>
                        <p class="mb-0 mt-1 small">Select a barangay to view specific locations and routes.</p>
                    </div>
                    
                    <div class="alert-settings">
                        <form onsubmit="return false;">

                            <button type="button" class="btn btn-primary w-100" onclick="location.reload()">Clear Map</button>
                            <button type="button" class="btn btn-success w-100 mt-2" onclick="findNearestEvacuationCenter()"><i class="fas fa-location-arrow me-2"></i> Find Nearest Evacuation Center</button>
                        </form>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3 fw-bold text-secondary">Legends</h5>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-map-marker-alt text-danger me-2" style="font-size: 1.2rem;"></i>
                        <span>Evacuation Area</span>
                    </div>
                    <!-- Add more legends here if needed -->
                    <div class="d-flex align-items-center mb-2">
                         <i class="fas fa-map-marker-alt text-primary me-2" style="font-size: 1.2rem;"></i>
                         <span>Barangay Hall</span>
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

    <!-- Scripts -->
    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js"></script>
    <script>
        // Initialize the map
        var map = L.map('hazard-map').setView([7.028012, 125.447948], 12); // Coordinates for Toril, Davao City

        // Add a tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Define safe zones for evacuation (Point B)
        var evacuationCenters = [
            { coordinates: [6.998811081095102, 125.49307707152077], name: 'Evacuation Area 1' },
            { coordinates: [7.000568823153534, 125.49653559526544], name: 'Evacuation Area 2' },
            { coordinates: [6.9983168311928585, 125.49859841081346], name: 'Evacuation Area 3' },
            { coordinates: [6.9969388586443495, 125.49256886944907], name: 'Evacuation Area 4' },
            { coordinates: [7.011273893189934, 125.4891101347465], name: 'Evacuation Area 5' },
            { coordinates: [7.007139058762269, 125.49217165220169], name: 'Evacuation Area 6' },
            { coordinates: [7.069663632868993, 125.4597074378554], name: 'Evacuation Area 7' },
            { coordinates: [7.003996510580622, 125.5010249102185], name: 'Evacuation Area 8' },
            { coordinates: [7.004059739493508, 125.49812014554799], name: 'Evacuation Area 9' },
            { coordinates: [6.995520539418084, 125.48722766414656], name: 'Evacuation Area 10' },
            { coordinates: [7.002749659549413, 125.49543740901937], name: 'Evacuation Area 11' },
            { coordinates: [7.006649292034094, 125.50084929979096], name: 'Evacuation Area 12' }
        ];

        var currentPolygon = null;
        var currentRoutes = [];
        var currentMarkers = [];
        var routingControl = null; // holds the OSRM road-following route control
        var allRoutingControls = []; // holds all routing controls

        function drawEvacuationRoute(start, end, color = 'blue') {
            // Use Leaflet Routing Machine with OSRM to follow the road network
            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(start[0], start[1]),
                    L.latLng(end[0], end[1])
                ],
                lineOptions: {
                    styles: [{ color: color || 'red', weight: 5, opacity: 0.9 }]
                },
                router: L.Routing.osrmv1({
                    serviceUrl: 'https://router.project-osrm.org/route/v1'
                }),
                addWaypoints: false,
                draggableWaypoints: false,
                fitSelectedRoutes: false,
                show: false,
                createMarker: function() { return null; } // we already place custom markers
            }).addTo(map);
            allRoutingControls.push(routingControl); // add routing control to array
            allRoutingControls.push(routingControl); // add routing control to array
        }

        function findNearestEvacuationCenter() {
            if (!navigator.geolocation) {
                alert("Geolocation is not supported by your browser");
                return;
            }

            navigator.geolocation.getCurrentPosition(success, error);

            function success(position) {
                var lat = position.coords.latitude;
                var long = position.coords.longitude;
                var userLocation = [lat, long];

                if (evacuationCenters.length === 0) {
                    alert("No evacuation centers defined.");
                    return;
                }

                // Find nearest safe zone
                var nearest = null;
                var minDistance = Infinity;

                evacuationCenters.forEach(zone => {
                    var dist = getDistance(lat, long, zone.coordinates[0], zone.coordinates[1]);
                    if (dist < minDistance) {
                        minDistance = dist;
                        nearest = zone;
                    }
                });

                if (nearest) {
                    // Clear map
                    if (currentPolygon) map.removeLayer(currentPolygon);
                    currentRoutes.forEach(route => map.removeLayer(route));
                    currentMarkers.forEach(marker => map.removeLayer(marker));
                    allRoutingControls.forEach(control => map.removeControl(control));
                    allRoutingControls = []; // Clear array properly
                    currentRoutes = [];
                    currentMarkers = [];

                    // Add User Marker (Point A)
                    var markerA = L.marker(userLocation).addTo(map).bindPopup("Your Location").openPopup();
                    currentMarkers.push(markerA);

                    // Add Nearest Evacuation Center Marker (Point B) - RED ICON
                    var markerB = L.marker(nearest.coordinates, {
                        icon: L.divIcon({
                            className: 'custom-red-marker',
                            html: '<i class="fas fa-map-marker-alt" style="color: red; font-size: 32px; text-shadow: 1px 1px 2px black;"></i>',
                            iconSize: [32, 32],
                            iconAnchor: [16, 32],
                            popupAnchor: [0, -32]
                        })
                    }).addTo(map).bindPopup("Nearest Evacuation Center: " + nearest.name);
                    currentMarkers.push(markerB);

                    // Draw route
                    drawEvacuationRoute(userLocation, nearest.coordinates, 'red');

                    // Fit bounds
                    var bounds = L.latLngBounds([userLocation, nearest.coordinates]);
                    map.fitBounds(bounds, {padding: [50, 50]});
                }
            }

            function error() {
                alert("Please turn on your location first");
            }
        }

        // Helper to calculate distance (Haversine Formula) in km
        function getDistance(lat1, lon1, lat2, lon2) {
            var R = 6371; // Radius of the earth in km
            var dLat = deg2rad(lat2 - lat1);
            var dLon = deg2rad(lon2 - lon1);
            var a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
                Math.sin(dLon/2) * Math.sin(dLon/2); 
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
            var d = R * c; // Distance in km
            return d;
        }

        function deg2rad(deg) {
            return deg * (Math.PI/180);
        }
    </script>
</body>
</html>
