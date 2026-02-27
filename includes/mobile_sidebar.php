<?php
// Modern Mobile Sidebar (Offcanvas)
?>
<div class="offcanvas offcanvas-start mobile-sidebar" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold text-primary" id="mobileSidebarLabel">
            <i class="fas fa-bars-staggered me-2"></i>Micro OSS
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="sidebar-user-info p-3 mb-2 bg-light">
            <?php if (isset($_SESSION['id'])): ?>
                <div class="d-flex align-items-center">
                    <?php
                    $profile_picture = isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default-profile.jpg';
                    ?>
                    <img src="../uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <div>
                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></div>
                        <small class="text-muted"><?php echo htmlspecialchars($_SESSION['role'] ?? 'Member'); ?></small>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex align-items-center text-muted">
                    <i class="fas fa-user-circle fa-3x me-3"></i>
                    <div>
                        <div class="fw-bold">Guest Mode</div>
                        <small>Login to access more features</small>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="list-group list-group-flush sidebar-nav-list">
            <a href="home.php" class="list-group-item list-group-item-action py-3 border-0">
                <i class="fas fa-house me-3 text-primary"></i>Home
            </a>
            <a href="maps.php" class="list-group-item list-group-item-action py-3 border-0">
                <i class="fas fa-map-location-dot me-3 text-success"></i>Community Map
            </a>

            <div class="sidebar-divider p-2 px-3 bg-light text-muted small fw-bold">EARLY WARNING SYSTEM</div>
            <a href="alert.php" class="list-group-item list-group-item-action py-3 border-0">
                <i class="fas fa-triangle-exclamation me-3 text-warning"></i>Alert Signal
            </a>
            <a href="hazard.php" class="list-group-item list-group-item-action py-3 border-0">
                <i class="fas fa-map-area me-3 text-danger"></i>Hazard Map
            </a>
            <a href="flood_warning.php" class="list-group-item list-group-item-action py-3 border-0">
                <i class="fas fa-water me-3 text-info"></i>Flood Monitoring
            </a>
            <a href="evacuation.php" class="list-group-item list-group-item-action py-3 border-0">
                <i class="fas fa-route me-3 text-primary"></i>Evacuation Map
            </a>

            <div class="sidebar-divider p-2 px-3 bg-light text-muted small fw-bold">DEMOGRAPHICS</div>
            <a href="population.php" class="list-group-item list-group-item-action py-3 border-0">
                <i class="fas fa-users me-3 text-indigo"></i>Population Analysis
            </a>
            <a href="hazard_vul.php" class="list-group-item list-group-item-action py-3 border-0">
                <i class="fas fa-shield-halved me-3 text-warning"></i>Vulnerability
            </a>
            <a href="purok_demographics.php" class="list-group-item list-group-item-action py-3 border-0">
                <i class="fas fa-building-user me-3 text-success"></i>Purok Demographics
            </a>

            <div class="sidebar-divider p-2 px-3 bg-light text-muted small fw-bold">RESOURCES</div>
            <a href="gallery.php" class="list-group-item list-group-item-action py-3 border-0">
                <i class="fas fa-images me-3 text-pink"></i>Gallery
            </a>
            <a href="iks.php" class="list-group-item list-group-item-action py-3 border-0">
                <i class="fas fa-brain me-3 text-primary"></i>IKS
            </a>
            <a href="publications.php" class="list-group-item list-group-item-action py-3 border-0">
                <i class="fas fa-book me-3 text-secondary"></i>Publications
            </a>
            <a href="download.php" class="list-group-item list-group-item-action py-3 border-0">
                <i class="fas fa-file-arrow-down me-3 text-success"></i>Downloadables
            </a>

            <div class="sidebar-divider p-2 px-3 bg-light text-muted small fw-bold">ACCOUNT</div>
            <?php if (isset($_SESSION['id'])): ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin.php" class="list-group-item list-group-item-action py-3 border-0">
                        <i class="fas fa-user-shield me-3 text-danger"></i>Admin Panel
                    </a>
                <?php endif; ?>
                <a href="#" class="list-group-item list-group-item-action py-3 border-0 text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-right-from-bracket me-3"></i>Log Out
                </a>
            <?php else: ?>
                <a href="#" class="list-group-item list-group-item-action py-3 border-0" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <i class="fas fa-sign-in-alt me-3 text-primary"></i>Login
                </a>
                <a href="#" class="list-group-item list-group-item-action py-3 border-0" data-bs-toggle="modal" data-bs-target="#signUpModal">
                    <i class="fas fa-user-plus me-3 text-success"></i>Sign Up
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .mobile-sidebar {
        width: 280px !important;
        border-radius: 0 20px 20px 0 !important;
        overflow-y: auto !important;
    }

    .sidebar-nav-list .list-group-item {
        font-weight: 500;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }

    .sidebar-nav-list .list-group-item:hover {
        background-color: #f8fafc;
        transform: translateX(5px);
    }

    .sidebar-nav-list .list-group-item i {
        width: 20px;
        text-align: center;
    }

    .text-indigo {
        color: #6366f1;
    }

    .text-pink {
        color: #ec4899;
    }
</style>