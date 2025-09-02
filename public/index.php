<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Micro Online Synthesis System</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="#"><strong>Micro Online Synthesis System</strong></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Early Warning System</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="alert.php">Alert Signal</a></li>
                        <li><a class="dropdown-item" href="hazard.php">Hazard Map</a></li>
                        <li><a class="dropdown-item" href="flood_warning.php">Flood Monitoring</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="evacuation.php">Evacuation Map</a></li>
                <li class="nav-item"><a class="nav-link" href="socio.php">Socio Demographic</a></li>
                <li class="nav-item"><a class="nav-link" href="gallery.php">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="download.php">Downloadables</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#signUpModal">Sign Up</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content Section -->
    <div class="container mt-4">
        <h1>Welcome to the Micro Online Synthesis System</h1>
        <p>This system aims to provide an online synthesis system that helps the barangay in monitoring early warning systems, hazard mapping, evacuation routes, socio-demographic data, and more.</p>

        <section id="early-warning">
            <h2>Early Warning System</h2>
            <p>The Early Warning System provides alerts, hazard mapping, and flood monitoring to ensure that the community is prepared for any potential natural disasters.</p>
            <a href="alert.php" class="btn btn-primary">View Alert Signals</a>
            <a href="hazard.php" class="btn btn-secondary">View Hazard Map</a>
            <a href="flood_warning.php" class="btn btn-success">View Flood Monitoring</a>
        </section>

        <section id="evacuation">
            <h2>Evacuation Map</h2>
            <p>This feature allows you to view evacuation routes to ensure the safety of the residents during emergency situations.</p>
            <a href="evacuation.php" class="btn btn-info">View Evacuation Map</a>
        </section>

        <section id="socio-demographic">
            <h2>Socio-Demographic Data</h2>
            <p>Learn more about the socio-demographic information of the barangays, including population, economic status, and other important data.</p>
            <a href="socio.php" class="btn btn-warning">View Socio Demographic Data</a>
        </section>

        <section id="gallery">
            <h2>Gallery</h2>
            <p>Explore images, videos, and other media from various community activities and events.</p>
            <a href="gallery.php" class="btn btn-dark">View Gallery</a>
        </section>

        <section id="downloads">
            <h2>Downloadables</h2>
            <p>Download important documents, maps, and other resources related to community safety.</p>
            <a href="download.php" class="btn btn-danger">Download Resources</a>
        </section>
    </div>
    
    </div>

    <!-- Modal for Sign Up -->
    <div class="modal fade" id="signUpModal" tabindex="-1" aria-labelledby="signUpModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signUpModalLabel">Sign Up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="register.php">
                        <div class="mb-3">
                            <label for="signUpFirstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="signUpFirstName" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="signUpLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="signUpLastName" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="signUpEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="signUpEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="signUpPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="signUpPassword" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>
</html>
