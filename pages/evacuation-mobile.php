<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Evacuation Map</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        #hazard-map {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        /* Floating action buttons */
        .map-actions {
            position: fixed;
            bottom: 30px;
            left: 15px;
            right: 15px;
            z-index: 1000;
            display: flex;
            flex-direction: row;
            gap: 10px;
            justify-content: center;
        }

        .map-actions .btn {
            flex: 1;
            min-height: 48px;
            font-size: 0.82rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 50px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(6px);
            white-space: nowrap;
            border: none;
            cursor: pointer;
        }

        .btn-nearest {
            background: #16a34a;
            color: #fff;
        }

        .btn-nearest:hover {
            background: #15803d;
            color: #fff;
        }

        .btn-manual {
            background: #4f46e5;
            color: #fff;
        }

        .btn-manual:hover {
            background: #4338ca;
            color: #fff;
        }

        .btn-clear {
            background: rgba(100, 116, 139, 0.9);
            color: #fff;
        }

        .btn-clear:hover {
            background: rgba(71, 85, 105, 0.95);
            color: #fff;
        }
    </style>
</head>

<body>

    <div id="hazard-map"></div>

    <!-- Floating action buttons overlaid on the map -->
    <div class="map-actions">
        <button class="btn btn-nearest" onclick="findNearestEvacuationCenter()">
            <i class="fas fa-location-arrow"></i> Nearest
        </button>
        <a href="evacuation-manual.php" class="btn btn-manual">
            <i class="fas fa-map-marked-alt"></i> Manual
        </a>
        <button class="btn btn-clear" onclick="location.reload()">
            <i class="fas fa-redo"></i> Clear
        </button>
    </div>




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
        var evacuationCenters = [{
                coordinates: [6.998811081095102, 125.49307707152077],
                name: 'Evacuation Area 1'
            },
            {
                coordinates: [7.000568823153534, 125.49653559526544],
                name: 'Evacuation Area 2'
            },
            {
                coordinates: [6.9983168311928585, 125.49859841081346],
                name: 'Evacuation Area 3'
            },
            {
                coordinates: [6.9969388586443495, 125.49256886944907],
                name: 'Evacuation Area 4'
            },
            {
                coordinates: [7.011273893189934, 125.4891101347465],
                name: 'Evacuation Area 5'
            },
            {
                coordinates: [7.007139058762269, 125.49217165220169],
                name: 'Evacuation Area 6'
            },
            {
                coordinates: [7.069663632868993, 125.4597074378554],
                name: 'Evacuation Area 7'
            },
            {
                coordinates: [7.003996510580622, 125.5010249102185],
                name: 'Evacuation Area 8'
            },
            {
                coordinates: [7.004059739493508, 125.49812014554799],
                name: 'Evacuation Area 9'
            },
            {
                coordinates: [6.995520539418084, 125.48722766414656],
                name: 'Evacuation Area 10'
            },
            {
                coordinates: [7.002749659549413, 125.49543740901937],
                name: 'Evacuation Area 11'
            },
            {
                coordinates: [7.006649292034094, 125.50084929979096],
                name: 'Evacuation Area 12'
            }
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
                    styles: [{
                        color: color || 'red',
                        weight: 5,
                        opacity: 0.9
                    }]
                },
                router: L.Routing.osrmv1({
                    serviceUrl: 'https://router.project-osrm.org/route/v1'
                }),
                addWaypoints: false,
                draggableWaypoints: false,
                fitSelectedRoutes: false,
                show: false,
                createMarker: function() {
                    return null;
                } // we already place custom markers
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
                    map.fitBounds(bounds, {
                        padding: [50, 50]
                    });

                    // Scroll back to map on mobile
                    if (window.innerWidth <= 991) {
                        document.getElementById('map').scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
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
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            var d = R * c; // Distance in km
            return d;
        }

        function deg2rad(deg) {
            return deg * (Math.PI / 180);
        }
    </script>

</body>

</html>