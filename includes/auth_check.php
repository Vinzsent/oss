<?php
// Authentication check and login required modal
// This file checks if user is logged in and shows modal if not

// Only set variables if they haven't been set already by the including page
if (!isset($is_logged_in)) {
    $is_logged_in = isset($_SESSION['id']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}
if (!isset($user_role_session)) {
    $user_role_session = isset($_SESSION['role']) ? $_SESSION['role'] : null;
}
if (!isset($is_admin)) {
    $is_admin = isset($user_role_session) && $user_role_session === 'admin';
}

// If not logged in, show login modal and hide page content
if (!$is_logged_in) {
    // Hide main content with CSS and show modal
    echo '<style>
        .main-content-protected {
            display: none !important;
        }
        body {
            overflow: hidden;
        }
    </style>';
    ?>
    <!-- Login Required Modal -->
    <div class="modal fade show" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredModalLabel" aria-modal="true" style="display: block; background-color: rgba(0,0,0,0.5);" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="loginRequiredModalLabel">
                        <i class="fas fa-lock me-2"></i>Login Required
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <p class="mb-3">You must be logged in to access this page.</p>
                    <p class="text-muted">Please log in to continue viewing the data.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal" onclick="document.getElementById('loginRequiredModal').classList.remove('show'); document.getElementById('loginRequiredModal').style.display = 'none';">
                        <i class="fas fa-sign-in-alt me-2"></i>Go to Login
                    </button>
                    <a href="home.php" class="btn btn-secondary">
                        <i class="fas fa-home me-2"></i>Go to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Auto-show login modal if login required modal is closed
        document.addEventListener('DOMContentLoaded', function() {
            const loginRequiredModal = document.getElementById('loginRequiredModal');
            if (loginRequiredModal) {
                const bsModal = new bootstrap.Modal(loginRequiredModal, {
                    backdrop: 'static',
                    keyboard: false
                });
                bsModal.show();
                
                // If user clicks "Go to Login", hide this modal and show login modal
                loginRequiredModal.addEventListener('hidden.bs.modal', function() {
                    // Only redirect if modal was closed via backdrop or ESC (not via buttons)
                    if (!document.getElementById('loginModal')) {
                        window.location.href = 'home.php';
                    }
                });
            }
        });
    </script>
    <?php
    // Stop execution - don't show page content
    // We'll wrap the page content in a div that gets hidden by CSS
    return false;
}
return true;
?>

