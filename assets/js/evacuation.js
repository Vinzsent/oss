let userMarker = null;
let centerMarker = null;

function findNearest() {

    if (!navigator.geolocation) {
        alert("Geolocation not supported.");
        return;
    }

    navigator.geolocation.getCurrentPosition(function (position) {

        const userLocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
        };

        // Show user marker
        if (!userMarker) {
            userMarker = new google.maps.Marker({
                position: userLocation,
                map: map,
                title: "Your Location",
                icon: "https://maps.google.com/mapfiles/ms/icons/blue-dot.png"
            });
        } else {
            userMarker.setPosition(userLocation);
        }

        map.panTo(userLocation);
        map.setZoom(15);

        // Check flood risk
        checkFloodRisk(userLocation);

        if (!centers || centers.length === 0) {
            alert("No evacuation centers found.");
            return;
        }

        let nearestCenter = null;
        let shortestDistance = Infinity;

        centers.forEach(center => {

            const distance = calculateDistance(
                userLocation.lat,
                userLocation.lng,
                parseFloat(center.latitude),
                parseFloat(center.longitude)
            );

            if (distance < shortestDistance) {
                shortestDistance = distance;
                nearestCenter = center;
            }

        });

        if (!nearestCenter) return;

        const destination = {
            lat: parseFloat(nearestCenter.latitude),
            lng: parseFloat(nearestCenter.longitude)
        };

        // Draw route
        directionsService.route({
            origin: userLocation,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING
        }, function (response, status) {

            if (status === "OK") {

                directionsRenderer.setDirections(response);

                const leg = response.routes[0].legs[0];

                document.getElementById("info-panel").innerHTML =
                    "<strong>Nearest Center:</strong> " + nearestCenter.name + "<br>" +
                    "<strong>Barangay:</strong> " + nearestCenter.barangay + "<br>" +
                    "<strong>Distance:</strong> " + leg.distance.text + "<br>" +
                    "<strong>Estimated Time:</strong> " + leg.duration.text + "<br>" +
                    "<strong>Available Capacity:</strong> " +
                    (nearestCenter.capacity - nearestCenter.occupied);

            } else {
                alert("Directions request failed: " + status);
            }

        });

    }, function () {
        alert("Please allow location access.");
    }, {
        enableHighAccuracy: true,
        timeout: 10000
    });
}


function calculateDistance(lat1, lon1, lat2, lon2) {

    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;

    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * Math.PI / 180) *
        Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c;
}