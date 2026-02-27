<!-- Modern Mobile Icon Bottom Nav -->
<div class="mobile-bottom-nav" id="mobileBottomNav">
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>
    <a href="home.php" class="nav-item-mobile <?php echo ($current_page == 'home.php') ? 'active' : ''; ?>">
        <i class="fas fa-house"></i>
    </a>
    <a href="../pages/maps.php" class="nav-item-mobile <?php echo ($current_page == 'maps.php') ? 'active' : ''; ?>">
        <i class="fas fa-calendar-alt"></i> <!-- Closest to the calendar/planner icon in image -->
    </a>
    <a href="../pages/evacuation.php" class="nav-item-mobile <?php echo ($current_page == 'evacuation.php') ? 'active' : ''; ?>">
        <i class="fas fa-map-marked"></i>
    </a>
    <a href="../pages/alert.php" class="nav-item-mobile <?php echo ($current_page == 'alert.php') ? 'active' : ''; ?>">
        <i class="fas fa-bell"></i>
    </a>
    <?php if (isset($_SESSION['id'])): ?>
        <a href="#" class="nav-item-mobile <?php echo (in_array($current_page, ['profile.php', 'settings.php'])) ? 'active' : ''; ?>" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <?php
            $profile_pics = isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default-profile.jpg';
            ?>
            <img src="../uploads/<?php echo htmlspecialchars($profile_pics); ?>" alt="Profile" class="nav-profile-img">
        </a>
    <?php else: ?>
        <a href="#" class="nav-item-mobile" data-bs-toggle="modal" data-bs-target="#loginModal">
            <i class="fas fa-user-circle"></i>
        </a>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bottomNav = document.getElementById('mobileBottomNav');
            const sidebar = document.getElementById('mobileSidebar');
            let lastScrollTop = 0;
            let isSidebarOpen = false;

            if (bottomNav && sidebar) {
                // Hiding/Showing when Sidebar is toggled
                sidebar.addEventListener('show.bs.offcanvas', function() {
                    isSidebarOpen = true;
                    bottomNav.classList.add('hide-nav');
                });

                sidebar.addEventListener('hidden.bs.offcanvas', function() {
                    isSidebarOpen = false;
                    bottomNav.classList.remove('hide-nav');
                });

                // Hiding/Showing based on Scroll
                window.addEventListener('scroll', function() {
                    if (isSidebarOpen) return;

                    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                    if (scrollTop > lastScrollTop && scrollTop > 50) {
                        // Scrolling down - hide
                        bottomNav.classList.add('hide-nav');
                    } else if (scrollTop < lastScrollTop) {
                        // Scrolling up - show
                        bottomNav.classList.remove('hide-nav');
                    }

                    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
                }, {
                    passive: true
                });
            }
        });
    </script>
</div>