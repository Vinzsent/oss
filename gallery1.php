<?php
// Start session
session_start();

// Include database configuration
include('config.php');

// Handle selected barangay and sitio
$selectedBarangay = isset($_POST['barangay']) ? htmlspecialchars($_POST['barangay']) : '';
$selectedSitio = isset($_POST['sitio']) ? htmlspecialchars($_POST['sitio']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Micro OSS App with Geotagging</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }
        thead th {
            background-color: #f8f9fa;
        }
        .card {
            margin-bottom: 20px;
        }
        #map {
            width: 100%;
            height: 400px;
            margin-top: 20px;
        }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include('includes/nav.php'); ?>

    <div class="container mt-5">
        <div class="row">
            <!-- Gallery Section -->
            <div class="col-md-8">
                <h2>Gallery</h2>
                <?php
                if ($selectedBarangay) {
                    $stmt = $conn->prepare("SELECT barangay, sitio_purok, photo, description FROM flood_archive WHERE barangay = ? AND sitio_purok = ?");
                    $stmt->bind_param("ss", $selectedBarangay, $selectedSitio);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $photo = base64_encode($row['photo']);
                            $barangay = htmlspecialchars($row['barangay'], ENT_QUOTES, 'UTF-8');
                            $sitioPurok = htmlspecialchars($row['sitio_purok'], ENT_QUOTES, 'UTF-8');
                            $description = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');
                ?>
                            <div class="card">
                                <img src="data:image/jpeg;base64,<?= $photo ?>" class="card-img-top" alt="<?= $barangay ?> - <?= $sitioPurok ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $barangay ?> - <?= $sitioPurok ?></h5>
                                    <p class="card-text"><?= $description ?></p>
                                </div>
                            </div>
                <?php
                        }
                    } else {
                        echo "<p>No records found for the selected barangay and sitio.</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p>Please select a barangay and sitio to view the flood archive.</p>";
                }
                ?>
                <!-- Map Section -->
                <div id="map"></div>
            </div>

            <!-- Search Section -->
            <div class="col-md-4">
                <h2>Search for Barangay and Sitio</h2>
                <form method="post">
                    <div class="form-group">
                        <label for="barangay">Barangay</label>
                        <select class="form-control" id="barangay" name="barangay" onchange="this.form.submit()">
                            <option value="">--Select Barangay--</option>
                            <option value="daliao" <?= $selectedBarangay === 'daliao' ? 'selected' : '' ?>>Daliao</option>
                            <option value="lizada" <?= $selectedBarangay === 'lizada' ? 'selected' : '' ?>>Lizada</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sitio">Sitio</label>
                        <select class="form-control" id="sitio" name="sitio" onchange="this.form.submit()">
                            <option value="">--Select Sitio--</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 p-4 text-center">
        &copy; 2025 Flood Resilience App. All Rights Reserved.
    </footer>

    <!-- Scripts -->
    <script>
        const sitiosData = {
            daliao: {
                "Nakada": [7.009390752578677, 125.50171445559366],
                "Doña Rosa Phase 1": [7.010321221253498, 125.49893911769212],
                "Kalayaan": [7.015831, 125.509114]
            },
            lizada: {
                "Babisa": [7.0081266390240105, 125.49165857313496],
                "Camarin": [7.006698964659315, 125.5020997443077],
                "Culosa": [7.0278, 125.4951]
            }
        };

        function updateSitioOptions(targetId) {
            const barangay = document.getElementById(targetId.replace('sitio', 'barangay')).value;
            const sitioDropdown = document.getElementById(targetId);
            sitioDropdown.innerHTML = '<option value="">--Select Sitio--</option>';

            if (barangay && sitiosData[barangay]) {
                Object.keys(sitiosData[barangay]).forEach(sitio => {
                    const option = document.createElement('option');
                    option.value = sitio;
                    option.textContent = sitio;
                    sitioDropdown.appendChild(option);
                });
            }
        }

        function initializeMap() {
            const barangay = document.getElementById('barangay').value;
            const sitio = document.getElementById('sitio').value;

            if (barangay && sitio && sitiosData[barangay][sitio]) {
                const [lat, lng] = sitiosData[barangay][sitio];
                const map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat, lng },
                    zoom: 15
                });
                new google.maps.Marker({ position: { lat, lng }, map });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateSitioOptions('sitio');
            document.getElementById('barangay').addEventListener('change', () => updateSitioOptions('sitio'));
            document.getElementById('sitio').addEventListener('change', initializeMap);
        });
    </script>
</body>
</html>
