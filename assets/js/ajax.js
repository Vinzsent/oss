let centers = [];

function loadCenters() {

    fetch("api/get-centers.php")
        .then(response => response.json())
        .then(data => {

            centers = data;

            data.forEach(center => {

                new google.maps.Marker({
                    position: {
                        lat: parseFloat(center.latitude),
                        lng: parseFloat(center.longitude)
                    },
                    map: map,
                    title: center.name,
                    icon: "https://maps.google.com/mapfiles/ms/icons/red-dot.png"
                });

            });

        })
        .catch(error => {
            console.error("Error loading centers:", error);
        });
}