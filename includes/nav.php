<!-- Custom CSS for navigation -->
<style>
    .dropdown-item.active {
        background-color: #8b5cf6 !important;
        color: white !important;
        font-weight: bold;
    }

    .dropdown-item.active:hover {
        background-color: #6b21a8 !important;
        color: white !important;
    }

    /* Ensure dropdown menu is clickable and properly positioned */
    .dropdown-menu {
        z-index: 1050 !important;
    }

    .dropdown-toggle,
    .dropdown-item {
        cursor: pointer !important;
    }
</style>

<!-- Mobile Responsive CSS -->
<link href="assets/css/mobile-responsive.css" rel="stylesheet">

<!-- Mobile Navigation Auto-Close Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-close navbar on mobile when clicking a link
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link:not(.dropdown-toggle)');
        const navbarCollapse = document.getElementById('navbarNav');

        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992 && navbarCollapse && navbarCollapse.classList.contains('show')) {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                            toggle: false
                        });
                        bsCollapse.hide();
                    }
                }
            });
        });

        // Also close when clicking dropdown items (but allow dropdown to open first)
        const dropdownItems = document.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(function(item) {
            item.addEventListener('click', function() {
                if (window.innerWidth < 992 && navbarCollapse && navbarCollapse.classList.contains('show')) {
                    // Small delay to allow navigation to happen first
                    setTimeout(function() {
                        if (navbarCollapse.classList.contains('show')) {
                            if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                                const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                                    toggle: false
                                });
                                bsCollapse.hide();
                            }
                        }
                    }, 100);
                }
            });
        });
    });
</script>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="index.php">Micro Online Synthesis System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="home.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="maps.php">Community Map</a>
            </li>
            <li class="nav-item dropdown">
                <a
                    class="nav-link dropdown-toggle"
                    href="#"
                    id="navbarDropdown"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Early Warning System
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="alert.php">Alert Signal</a></li>
                    <li><a class="dropdown-item" href="hazard.php">Hazard Map</a></li>
                    <li><a class="dropdown-item" href="flood_warning.php">Flood Monitoring</a></li>
                </ul>
            </li>
            <li class="nav-item"><a class="nav-link" href="evacuation.php">Evacuation Map</a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="socioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Socio Demographic</a>
                <ul class="dropdown-menu" aria-labelledby="socioDropdown">
                    <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'population.php' ? 'active' : ''; ?>" href="population.php">Population Over Age</a></li>
                    <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'hazard_vul.php' ? 'active' : ''; ?>" href="hazard_vul.php">Hazard Vulnerability</a></li>
                    <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'purok_demographics.php' ? 'active' : ''; ?>" href="purok_demographics.php">Purok Demographics</a></li>
                    <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'household_materials.php' ? 'active' : ''; ?>" href="household_materials.php">Household Materials Analysis</a></li>
                    <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'purok_evac.php' ? 'active' : ''; ?>" href="purok_evac.php">Purok Evacuation</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="gallery.php">Gallery</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="iks.php">IKS</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="publications.php">Publications</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="download.php">Downloadables</a>
            </li>

            <?php
            // Check if user is logged in
            if (isset($_SESSION['id'])) {
                // User is logged in, show profile picture and logout option
                // Check if profile picture is set in the session
                $profile_picture = isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default-profile.jpg';
                echo '<li class="nav-item">';
                echo '<img src="uploads/' . htmlspecialchars($profile_picture) . '" alt="Profile Picture" class="rounded-circle" style="width: 40px; height: 40px;">'; // Profile picture
                echo '</li>';
                echo '<li class="nav-item">';
                echo '<a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt mr-2"></i>Log Out</a>';
                echo '</li>';
            } else {
                // User is not logged in, show login and sign-up options
                echo '<li class="nav-item">';
                echo '<a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="fas fa-sign-in-alt mr-2"></i>Login</a>';
                echo '</li>';
                echo '<li class="nav-item">';
                echo '<a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#signUpModal"><i class="fas fa-user-plus mr-2"></i>Sign Up</a>';
                echo '</li>';
            }
            ?>
        </ul>
    </div>
</nav>