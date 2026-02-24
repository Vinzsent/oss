<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: pages/home.php");
} else {
    header("Location: pages/home.php"); // Or pages/login.php if you prefer
}
exit;
