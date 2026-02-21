<?php
session_start();
include('config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Evacuation Map - Micro OSS App</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

<?php include('includes/nav.php'); ?>

<div class="main-container">

<div class="page-header text-center">
    <h1><i class="fas fa-map-marked-alt me-2"></i>Evacuation Map</h1>
    <p>Find safe zones and evacuation routes in your area</p>
</div>

<div class="row">

<!-- MAP -->
<div class="col-lg-8 mb-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <div id="hazard-map" class="map-container"></div>
        </div>
    </div>
</div>

<!-- SIDEBAR -->
<div class="col-lg-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="fw-bold mb-3">Barangay Maps</h5>

            <button class="btn btn-primary w-100 mb-2" onclick="resetMap()">
                Clear Map
            </button>

            <button class="btn btn-success w-100 mb-3" onclick="findNearestEvacuationCenter()">
                <i class="fas fa-location-arrow me-1"></i> Find Nearest Evacuation Center
            </button>

            <a href="evacuation-manual.php" class="btn btn-outline-primary w-100 mb-3">
                Manual Evacuation
            </a>

            <hr>

            <h6 class="fw-bold">Legends</h6>
            <p><span class="text-danger fw-bold">●</span> Evacuation Area</p>
            <p><span class="text-primary fw-bold">●</span> Your Location</p>

        </div>
    </div>
</div>

</div>
</div>

<footer class="bg-dark text-white text-center p-3 mt-4">
    &copy; 2026 Micro OSS Flood Resilience App
</footer>

<!-- GOOGLE MAP SCRIPT -->
<script>

let map;
let userMarker = null;
let evacuationMarker = null;
let directionsService;
let directionsRenderer;
let watchId = null;

const defaultCenter = { lat: 7.028012, lng: 125.447948 };

const evacuationCenters = [
    { lat: 6.998811081095102, lng: 125.49307707152077, name: "Evacuation Area 1" },
    { lat: 7.000568823153534, lng: 125.49653559526544, name: "Evacuation Area 2" },
    { lat: 6.9983168311928585, lng: 125.49859841081346, name: "Evacuation Area 3" },
    { lat: 6.9969388586443495, lng: 125.49256886944907, name: "Evacuation Area 4" },
    { lat: 7.011273893189934, lng: 125.4891101347465, name: "Evacuation Area 5" },
    { lat: 7.007139058762269, lng: 125.49217165220169, name: "Evacuation Area 6" },
    { lat: 7.069663632868993, lng: 125.4597074378554, name: "Evacuation Area 7" },
    { lat: 7.003996510580622, lng: 125.5010249102185, name: "Evacuation Area 8" },
    { lat: 7.004059739493508, lng: 125.49812014554799, name: "Evacuation Area 9" },
    { lat: 6.995520539418084, lng: 125.48722766414656, name: "Evacuation Area 10" },
    { lat: 7.002749659549413, lng: 125.49543740901937, name: "Evacuation Area 11" },
    { lat: 7.006649292034094, lng: 125.50084929979096, name: "Evacuation Area 12" }
];

function initMap() {

    map = new google.maps.Map(document.getElementById("hazard-map"), {
        center: defaultCenter,
        zoom: 12
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({ suppressMarkers: true });
    directionsRenderer.setMap(map);

    // --- SHOW CURRENT LOCATION IMMEDIATELY ---
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                userMarker = new google.maps.Marker({
                    position: userLocation,
                    map: map,
                    title: "Your Current Location",
                    icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                });

                map.setCenter(userLocation);
                map.setZoom(15);
            },
            function() {
                alert("Unable to fetch your location. Please allow location access.");
            },
            { enableHighAccuracy: true, timeout: 5000 }
        );
    } else {
        alert("Geolocation is not supported by your browser.");
    }
}

// RESET MAP
function resetMap() {
    if (userMarker) { userMarker.setMap(null); userMarker = null; }
    if (evacuationMarker) { evacuationMarker.setMap(null); evacuationMarker = null; }
    directionsRenderer.setDirections({ routes: [] });
    if (watchId !== null) { navigator.geolocation.clearWatch(watchId); watchId = null; }
    map.setCenter(defaultCenter);
    map.setZoom(12);
}

// FIND & TRACK NEAREST EVACUATION CENTER
function findNearestEvacuationCenter() {

    if (!navigator.geolocation) { alert("Geolocation not supported by your browser."); return; }

    if (watchId !== null) { navigator.geolocation.clearWatch(watchId); }

    watchId = navigator.geolocation.watchPosition(
        function(position) {

            const userLocation = { lat: position.coords.latitude, lng: position.coords.longitude };

            if (!userMarker) {
                userMarker = new google.maps.Marker({
                    position: userLocation,
                    map: map,
                    title: "Your Location",
                    icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                });
            } else { userMarker.setPosition(userLocation); }

            map.panTo(userLocation);

            // Find nearest evacuation center
            let nearest = null; let minDistance = Infinity;
            evacuationCenters.forEach(center => {
                const distance = getDistance(userLocation.lat, userLocation.lng, center.lat, center.lng);
                if (distance < minDistance) { minDistance = distance; nearest = center; }
            });

            if (!nearest) return;

            if (!evacuationMarker) {
                evacuationMarker = new google.maps.Marker({
                    position: { lat: nearest.lat, lng: nearest.lng },
                    map: map,
                    title: nearest.name,
                    icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                });
            }

            // Update route dynamically
            directionsService.route({
                origin: userLocation,
                destination: { lat: nearest.lat, lng: nearest.lng },
                travelMode: google.maps.TravelMode.DRIVING
            }, function(response, status) {
                if (status === "OK") { directionsRenderer.setDirections(response); }
            });

        },
        function(error) { alert("Please enable location access."); },
        { enableHighAccuracy: true, maximumAge: 0, timeout: 5000 }
    );
}

// DISTANCE CALCULATION (HAVERSINE)
function getDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // km
    const dLat = (lat2 - lat1) * Math.PI/180;
    const dLon = (lon2 - lon1) * Math.PI/180;
    const a = Math.sin(dLat/2)*Math.sin(dLat/2) + Math.cos(lat1*Math.PI/180) * Math.cos(lat2*Math.PI/180) * Math.sin(dLon/2)*Math.sin(dLon/2);
    const c = 2*Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

</script>

<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRkSkfYweTDI09aam31aXA-Vh69J7Ui6Y&callback=initMap">
</script>

<?php include('includes/scripts.php'); ?>
</body>
</html>

