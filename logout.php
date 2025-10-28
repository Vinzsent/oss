<?php
// Start the session
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page or home page after logout
header("Location: index.php");
exit();
?>
