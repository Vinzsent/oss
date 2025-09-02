<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload with Camera, Location, and Preview</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.min.js"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
        #photoPreview img {
            max-width: 100%;
            max-height: 300px;
            display: none;
        }
        #video {
            width: 100%;
            max-width: 400px;
            display: none;
        }
        #captureBtn {
            display: none;
        }
    </style>
</head>
<body>
<?php include('includes/nav.php'); ?>
<div class="container">
    <h3 class="mt-4">Upload Photo</h3>
    <div class="row d-flex align-items-stretch">
        <!-- Left Column: Form -->
        <div class="col-md-6 d-flex flex-column">
            <form action="upload.php" method="POST" enctype="multipart/form-data" class="flex-grow-1">
               
				<button type="button" id="startCamera" class="btn btn-info">Start Camera</button>
				<button type="button" id="captureBtn" class="btn btn-success" style="display: none;">Capture</button>
				<input type="hidden" name="imageData" id="imageData">

				<br>
				<input type="file" id="uploadBtn" accept="image/*" class="btn btn-primary">
				 <video id="video" autoplay class="w-100" style="display: block;"></video>
				<br><br>

                <label for="province">Province:</label>
                <select id="province" class="form-control">
                    <option value="">-- Select Province --</option>
                </select>

                <label for="municipality">Municipality/City:</label>
                <select id="municipality" class="form-control" disabled>
                    <option value="">-- Select Municipality/City --</option>
                </select>

                <label for="barangay">Barangay:</label>
                <select id="barangay" class="form-control" disabled>
                    <option value="">-- Select Barangay --</option>
                </select>

                <label for="sitio">Sitio:</label>
                <input type="text" id="sitio" class="form-control" placeholder="Enter Sitio Name">

                <label for="notes">Description:</label>
                <textarea name="notes" id="notes" rows="4" class="form-control"></textarea><br>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

        <!-- Right Column: Map -->
        <div class="col-md-6 d-flex flex-column">
		
				<canvas id="canvas" style="display: none;"></canvas>
				<div id="photoPreview">
					<img id="previewImage" src="#" alt="Image Preview" class="img-fluid" style="display: none;">
				</div>

				<br>
            <div class="flex-grow-1">
                <div id="map" style="height: 500px;"></div>
            </div>
			<div class="row">
    <div class="col-md-6">
        <label for="latitude">Latitude:</label>
        <input type="text" name="latitude" id="latitude" class="form-control" required readonly>
    </div>
    <div class="col-md-6">
        <label for="longitude">Longitude:</label>
        <input type="text" name="longitude" id="longitude" class="form-control" required readonly>
    </div>
</div>
            </div>
        </div>
    </div>
</div>



    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const defaultLocation = [7.0731, 125.6128];
            const map = L.map("map").setView(defaultLocation, 14);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            }).addTo(map);

            const marker = L.marker(defaultLocation, { draggable: true }).addTo(map);
            marker.on("moveend", function (event) {
                const position = event.target.getLatLng();
                document.getElementById("latitude").value = position.lat.toFixed(6);
                document.getElementById("longitude").value = position.lng.toFixed(6);
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userLocation = [position.coords.latitude, position.coords.longitude];
                        map.setView(userLocation, 14);
                        marker.setLatLng(userLocation);
                        document.getElementById("latitude").value = userLocation[0].toFixed(6);
                        document.getElementById("longitude").value = userLocation[1].toFixed(6);
                    },
                    (error) => console.error("Geolocation error:", error)
                );
            }

            // Camera Capture Logic
            const video = document.getElementById("video");
            const canvas = document.getElementById("canvas");
            const captureBtn = document.getElementById("captureBtn");
            const startCamera = document.getElementById("startCamera");
            const previewImage = document.getElementById("previewImage");
            let stream = null;

            startCamera.addEventListener("click", async function () {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    video.srcObject = stream;
                    video.style.display = "block";
                    captureBtn.style.display = "inline-block";
                    startCamera.style.display = "none";
                } catch (err) {
                    console.error("Error accessing camera:", err);
                }
            });

            captureBtn.addEventListener("click", function () {
                const context = canvas.getContext("2d");
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                // Convert canvas to image data URL
                const imageData = canvas.toDataURL("image/png");
                document.getElementById("imageData").value = imageData;
                
                // Show preview
                previewImage.src = imageData;
                previewImage.style.display = "block";

                // Stop camera
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                video.style.display = "none";
                captureBtn.style.display = "none";
                startCamera.style.display = "inline-block";
            });
        });
    </script>
	<script>
document.getElementById('uploadBtn').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('imageData').value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
<script>
    // API Base URL
    const BASE_URL = "https://psgc.gitlab.io/api";

    // DOM Elements
    const provinceSelect = document.getElementById("province");
    const municipalitySelect = document.getElementById("municipality");
    const barangaySelect = document.getElementById("barangay");

    // Fetch Provinces
    function fetchProvinces() {
      fetch(`${BASE_URL}/provinces/`)
        .then(response => response.json())
        .then(data => {
          data.forEach(province => {
            const option = document.createElement("option");
            option.value = province.code;
            option.textContent = province.name;
            provinceSelect.appendChild(option);
          });
        })
        .catch(error => console.error("Error fetching provinces:", error));
    }

    // Fetch Municipalities for a Selected Province
    function fetchMunicipalities(provinceCode) {
      municipalitySelect.innerHTML = '<option value="">-- Select Municipality/City --</option>';
      barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
      municipalitySelect.disabled = true;
      barangaySelect.disabled = true;

      fetch(`${BASE_URL}/provinces/${provinceCode}/cities-municipalities/`)
        .then(response => response.json())
        .then(data => {
          data.forEach(municipality => {
            const option = document.createElement("option");
            option.value = municipality.code;
            option.textContent = municipality.name;
            municipalitySelect.appendChild(option);
          });
          municipalitySelect.disabled = false;
        })
        .catch(error => console.error("Error fetching municipalities:", error));
    }

    // Fetch Barangays for a Selected Municipality
    function fetchBarangays(municipalityCode) {
      barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
      barangaySelect.disabled = true;

      fetch(`${BASE_URL}/cities-municipalities/${municipalityCode}/barangays/`)
        .then(response => response.json())
        .then(data => {
          data.forEach(barangay => {
            const option = document.createElement("option");
            option.value = barangay.code;
            option.textContent = barangay.name;
            barangaySelect.appendChild(option);
          });
          barangaySelect.disabled = false;
        })
        .catch(error => console.error("Error fetching barangays:", error));
    }

    // Event Listeners
    provinceSelect.addEventListener("change", () => {
      const provinceCode = provinceSelect.value;
      if (provinceCode) {
        fetchMunicipalities(provinceCode);
      } else {
        municipalitySelect.innerHTML = '<option value="">-- Select Municipality/City --</option>';
        barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
        municipalitySelect.disabled = true;
        barangaySelect.disabled = true;
      }
    });

    municipalitySelect.addEventListener("change", () => {
      const municipalityCode = municipalitySelect.value;
      if (municipalityCode) {
        fetchBarangays(municipalityCode);
      } else {
        barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
        barangaySelect.disabled = true;
      }
    });

    // Initialize Provinces on Page Load
    fetchProvinces();
  </script>
  
  <script>
    document.addEventListener("DOMContentLoaded", function () {
        const video = document.getElementById("video");
        const canvas = document.getElementById("canvas");
        const captureBtn = document.getElementById("captureBtn");
        const startCamera = document.getElementById("startCamera");
        const previewImage = document.getElementById("previewImage");
        const uploadBtn = document.getElementById("uploadBtn");
        let stream = null;

        startCamera.addEventListener("click", async function () {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
                video.style.display = "block";
                captureBtn.style.display = "inline-block";
                startCamera.style.display = "none";
            } catch (err) {
                console.error("Error accessing camera:", err);
            }
        });

        captureBtn.addEventListener("click", function () {
            const context = canvas.getContext("2d");
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert canvas to image data URL
            const imageData = canvas.toDataURL("image/png");
            document.getElementById("imageData").value = imageData;

            // Show preview
            previewImage.src = imageData;
            previewImage.style.display = "block";

            // Stop camera
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            video.style.display = "none";
            captureBtn.style.display = "none";
            startCamera.style.display = "inline-block";
        });

        // Handle image upload
        uploadBtn.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = "block";
                    document.getElementById('imageData').value = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>

</body>
</html>
