let floodPolygons = [];

function loadFloodZones() {

    fetch("api/get-floodzones.php")
        .then(response => response.json())
        .then(zones => {

            zones.forEach(zone => {

                const polygon = new google.maps.Polygon({
                    paths: zone.polygon,
                    fillColor: getRiskColor(zone.risk_level),
                    fillOpacity: 0.4,
                    strokeColor: "#000",
                    strokeWeight: 1,
                    map: map
                });

                floodPolygons.push({
                    polygon: polygon,
                    risk: zone.risk_level
                });

            });

        });
}

function getRiskColor(level) {
    if (level === "high") return "red";
    if (level === "moderate") return "orange";
    return "green";
}

function checkFloodRisk(location) {

    floodPolygons.forEach(zone => {

        if (google.maps.geometry.poly.containsLocation(
            new google.maps.LatLng(location.lat, location.lng),
            zone.polygon
        )) {

            alert("⚠ WARNING: You are inside a " + zone.risk.toUpperCase() + " flood risk zone!");

        }

    });
}