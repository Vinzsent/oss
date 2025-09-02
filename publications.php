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
        .image-container {
            height: 80vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .image-container embed {
            width: 100%;
            height: 100%;
            border: none;
        }
        .zoom-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
        }
        .zoom-controls button {
            margin: 5px;
        }
    </style>
</head>
<body>

<?php include('includes/nav.php'); ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h2>Resources</h2>
            <div class="image-container bg-light">
                <embed id="resource-file" src="" type="application/pdf" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="alert alert-info">
                <strong>Search for Resources:</strong>
            </div>
            <form>
                <div class="form-group">
                    <label for="province">Province:</label>
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
                    </select>
                </div>
                <div class="form-group">
                    <label for="resourceSelect"><strong>Resources</strong></label>
                    <select class="form-control" id="resourceSelect" name="resource" onchange="updateResource()">
                        <option value="">-- Select --</option>
                        <option value="uploads\policybrief.pdf">Policy Briefs</option>
                        <option value="uploads\media.pdf">Media Releases</option>
                        <option value="uploads\infographics.pdf">Infographics</option>
                        <option value="uploads\factsheet.pdf">Fact Sheets</option>
                    </select>
                </div>
            </form>
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
    function updateResource() {
        let resourceSelect = document.getElementById("resourceSelect");
        let selectedFile = resourceSelect.value;
        document.getElementById("resource-file").src = selectedFile ? selectedFile : "";
    }

    const BASE_URL = "https://psgc.gitlab.io/api";
    const provinceSelect = document.getElementById("province");
    const municipalitySelect = document.getElementById("municipality");
    const barangaySelect = document.getElementById("barangay");

    function fetchProvinces() {
        fetch(`${BASE_URL}/provinces/`)
            .then(res => res.json())
            .then(data => {
                data.forEach(province => {
                    provinceSelect.appendChild(new Option(province.name, province.code));
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
