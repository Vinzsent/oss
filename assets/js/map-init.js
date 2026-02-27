let map;
let userMarker = null;
let directionsService;
let directionsRenderer;
let centerMarkers = [];
let watchId = null;
let currentDestination = null;
let updateTimeout = null;
let lastPosition = null;  
let nearestMarker = null; 
const defaultCenter = { lat: 7.028012, lng: 125.447948 };

// Optional temporary markers before DB loads
const tempCenters = [
    { lat: 6.998811, lng: 125.493077, name: "San Vicente Gym", risk: "low" },
        { lat: 7.000569, lng: 125.496536, name: "Flock For Jesus Ministry Church", risk: "low" },
        { lat: 6.998317, lng: 125.498598, name: "Dacudao Basketball Court", risk: "low" },
        { lat: 6.996939, lng: 125.492569, name: "Sr. San Pedro Chapel", risk: "low" },
        { lat: 7.011274, lng: 125.489110, name: "Lizada Barangay Hall", risk: "low" },
        { lat: 7.007139058762269, lng: 125.49217165220169, name: "Babisa Basketball Court", risk: "low"  },
        { lat: 7.069663632868993, lng: 125.4597074378554, name: "Lizada Old Barangay Operations Center", risk: "low"  },
        { lat: 7.003996510580622, lng: 125.5010249102185, name: "Purok Guitierez Basketball Court ", risk: "low"  },
        { lat: 7.004059739493508, lng: 125.49812014554799, name: "Basketball Court", risk: "low"  },
        { lat: 6.995520539418084, lng: 125.48722766414656, name: "Samuel Village", risk: "low"  },
        { lat: 7.002749659549413, lng: 125.49543740901937, name: "JV Ferriols NHS", risk: "low"  },
        { lat: 7.006649292034094, lng: 125.50084929979096, name: "Purok Camarin Basketball Court", risk: "low"  }   
        ];

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: defaultCenter,
        zoom: 12,
        fullscreenControl: true,
        zoomControl: true,
        mapTypeControl: false
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({ suppressMarkers: false });
    directionsRenderer.setMap(map);

    const geocoder = new google.maps.Geocoder();

    // Add temporary markers with click-to-select
    tempCenters.forEach(center => {
        const marker = new google.maps.Marker({
            position: { lat: center.lat, lng: center.lng },
            map: map,
            title: center.name,
            icon: getRiskIcon(center.risk)
        });
        marker.addListener('click', () => selectCenter(marker));
        centerMarkers.push(marker);
    });

    // Show user location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const userLocation = { lat: position.coords.latitude, lng: position.coords.longitude };
            userMarker = new google.maps.Marker({
                position: userLocation,
                map: map,
                title: "Your Location",
                icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
            });
            map.setCenter(userLocation);
            map.setZoom(15);
        });
    }

    // Load DB centers
    fetch('../api/get-centers.php')
        .then(res => res.json())
        .then(data => {
            centerMarkers.forEach(m => m.setMap(null));
            centerMarkers = [];

            const select = document.getElementById('center-select'); // dropdown
            select.innerHTML = `<option value="">-- Select Center --</option>`;

            data.forEach(center => {
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(center.latitude), lng: parseFloat(center.longitude) },
                    map: map,
                    title: center.name,
                    icon: getRiskIcon(center.risk_level || "low"),
                    defaultIcon: getRiskIcon(center.risk_level || "low")
                });

                // click to select center
                marker.addListener('click', () => selectCenter(marker));

                // add to dropdown
                const option = document.createElement('option');
                option.value = `${center.latitude},${center.longitude}`;
                option.textContent = center.name;
                select.appendChild(option);

                centerMarkers.push(marker);
            });

            // dropdown selection
            select.addEventListener('change', () => {
                if (!userMarker) return;
                const [lat, lng] = select.value.split(',').map(Number);
                const chosenMarker = centerMarkers.find(m => m.getPosition().lat() === lat && m.getPosition().lng() === lng);
                if (chosenMarker) selectCenter(chosenMarker);
            });
        })
        .catch(err => console.error("Error loading centers:", err));

    // Map click to get barangay
    map.addListener('click', e => {
        geocoder.geocode({ location: e.latLng }, (results, status) => {
            if (status === "OK" && results[0]) {
                const barangay = parseBarangay(results[0].address_components);
                if (barangay) {
                    const infoPanel = document.getElementById("info-panel");
                    infoPanel.innerHTML += `<br><em>Clicked Barangay:</em> ${barangay}`;
                }
            }
        });
    });
}

// Select a center (marker)
// Select a center (marker)
function selectCenter(marker) {
    currentDestination = marker.getPosition();
    highlightNearestMarker(marker);

    // Get center status from marker object (assume we stored it)
    const status = marker.status || "Vacant"; // default if undefined

    if (userMarker) updateRoute(userMarker.getPosition(), marker.getTitle(), status);
}

// Update route & info panel with center status
function updateRoute(start, centerName, status = "Vacant") {
    if (!currentDestination) return;
    directionsService.route({
        origin: start,
        destination: currentDestination,
        travelMode: google.maps.TravelMode.DRIVING
    }, (response, statusRoute) => {
        if (statusRoute === "OK") {
            directionsRenderer.setDirections(response);
            const leg = response.routes[0].legs[0];
            document.getElementById("info-panel").innerHTML =
                `<strong>Nearest Center:</strong> ${centerName}<br>` +
                `<strong>Status:</strong> ${status}<br>` +
                `<strong>Distance:</strong> ${leg.distance.text} (${(leg.distance.value/1000).toFixed(2)} km)<br>` +
                `<strong>Estimated Time:</strong> ${leg.duration.text}`;
        } else console.error("Directions request failed:", statusRoute);
    });
}

// Helper: flood risk icon
function getRiskIcon(risk) {
    switch(risk.toLowerCase()) {
        case "high": return "http://maps.google.com/mapfiles/ms/icons/red-dot.png";
        case "medium": return "http://maps.google.com/mapfiles/ms/icons/orange-dot.png";
        default: return "http://maps.google.com/mapfiles/ms/icons/green-dot.png";
    }
}

// Parse barangay from address components
function parseBarangay(components) {
    for (const c of components) {
        if (c.types.includes("sublocality_level_1") || c.types.includes("political")) return c.long_name;
    }
    return null;
}

// Reset map
function resetMap() {
    if (userMarker) userMarker.setMap(null);
    directionsRenderer.setDirections({ routes: [] });
    centerMarkers.forEach(m => { m.setMap(map); if (m.defaultIcon) m.setIcon(m.defaultIcon); });
    document.getElementById("info-panel").innerHTML = "";
    lastPosition = null;
    nearestMarker = null;
    currentDestination = null;
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
    }
}

// Haversine distance (km)
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2-lat1) * Math.PI/180;
    const dLon = (lon2-lon1) * Math.PI/180;
    const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLon/2)**2;
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

// Find nearest center and start live tracking
function findNearest() {
    if (!navigator.geolocation) { alert("Geolocation not supported."); return; }

    navigator.geolocation.getCurrentPosition(position => {
        const userLocation = { lat: position.coords.latitude, lng: position.coords.longitude };
        if (!userMarker) {
            userMarker = new google.maps.Marker({
                position: userLocation,
                map: map,
                title: "Your Location",
                icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
            });
        } else userMarker.setPosition(userLocation);

        map.panTo(userLocation);
        map.setZoom(15);

        // find nearest
        let nearest = null;
        let minDist = Infinity;
        centerMarkers.forEach(m => {
            const dist = calculateDistance(userLocation.lat, userLocation.lng, m.getPosition().lat(), m.getPosition().lng());
            if (dist < minDist) { minDist = dist; nearest = m; }
        });

        if (!nearest) { alert("No evacuation centers found."); return; }
        selectCenter(nearest);

        // Start debounced live tracking
        if (watchId === null) {
            watchId = navigator.geolocation.watchPosition(pos => {
                const newLoc = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                // only update if moved >10m
                if (!lastPosition || calculateDistance(lastPosition.lat, lastPosition.lng, newLoc.lat, newLoc.lng) > 0.01) {
                    lastPosition = newLoc;
                    userMarker.setPosition(newLoc);
                    map.panTo(newLoc);
                    if (updateTimeout) clearTimeout(updateTimeout);
                    updateTimeout = setTimeout(() => {
                        if (currentDestination) updateRoute(newLoc, nearestMarker.getTitle());
                    }, 2000);
                }
            }, err => console.error(err), { enableHighAccuracy: true, maximumAge: 0, timeout: 10000 });
        }

    }, () => alert("Please allow location access."), { enableHighAccuracy: true, timeout: 10000 });
}

// Highlight nearest/selected marker
function highlightNearestMarker(marker) {
    if (nearestMarker && nearestMarker !== marker) {
        if (nearestMarker.defaultIcon) nearestMarker.setIcon(nearestMarker.defaultIcon);
    }
    marker.setIcon("http://maps.google.com/mapfiles/ms/icons/blue-dot.png");
    nearestMarker = marker;
}

// Update route & info panel
function updateRoute(start, centerName) {
    if (!currentDestination) return;
    directionsService.route({
        origin: start,
        destination: currentDestination,
        travelMode: google.maps.TravelMode.DRIVING
    }, (response, status) => {
        if (status === "OK") {
            directionsRenderer.setDirections(response);
            const leg = response.routes[0].legs[0];
            document.getElementById("info-panel").innerHTML =
                `<strong>Nearest Center:</strong> ${centerName}<br>` +
                `<strong>Distance:</strong> ${leg.distance.text} (${(leg.distance.value/1000).toFixed(2)} km)<br>` +
                `<strong>Estimated Time:</strong> ${leg.duration.text}`;
        } else console.error("Directions request failed:", status);
    });
}

window.onload = initMap;