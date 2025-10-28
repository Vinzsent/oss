<?php
// Start session to track login status
session_start();

include('config.php');

// Initialize error variables
$email_err = $password_err = "";

// Process the form when it's submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate email
    if (empty($_POST["email"])) {
        $email_err = "Please enter an email.";
    } else {
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Please enter a valid email.";
        }
    }

    // Validate password
    if (empty($_POST["password"])) {
        $password_err = "Please enter a password.";
    } else {
        $password = $_POST["password"];
    }

    // If no errors, check the credentials
    if (empty($email_err) && empty($password_err)) {
        // Use prepared statements to prevent SQL injection
        $sql = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
        $sql->bind_param("s", $email);  // "s" means the parameter is a string
        if ($sql->execute()) {
            $result = $sql->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                // Check hashed password using password_verify()
                if (password_verify($password, $row['password'])) {
                    // Correct credentials, create session
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $row["id"];
                    $_SESSION["email"] = $row["email"];
                    session_regenerate_id(true); // Regenerate session ID to prevent session fixation
                    echo <<<HTML
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Logging in...</title>
                        <style>
                            body { margin: 0; font-family: Arial, sans-serif; }
                            .overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; }
                            .modal { background: #ffffff; border-radius: 8px; padding: 24px 28px; max-width: 360px; width: 90%; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
                            .modal h2 { margin: 0 0 8px; font-size: 20px; color: rgba(59, 130, 246, 0.5); }
                            .modal p { margin: 0 0 16px; color: #333333; }
                            .spinner { width: 28px; height: 28px; border: 3px solid #e5e7eb; border-top-color: rgba(59, 130, 246, 0.5); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto; }
                            @keyframes spin { to { transform: rotate(360deg); } }
                        </style>
                        <script>
                            setTimeout(function () {
                                window.location.href = 'home.php';
                            }, 1500);
                        </script>
                        </head>
                    <body>
                        <div class="overlay">
                            <div class="modal">
                                <h2>Login successful</h2>
                                <p>Redirecting to home...</p>
                                <div class="spinner"></div>
                            </div>
                        </div>
                    </body>
                    </html>
                    HTML;
                    exit();
                } else {
                    $password_err = "Invalid password.";
                    header("Location: index.php");
                }
            } else {
                $email_err = "No user found with that email.";
                header("Location: index.php");
            }
        } else {
            // Log error (for debugging purposes)
            error_log("SQL Error: " . $conn->error);
            $email_err = "An error occurred. Please try again later.";
        }
    }
}

// Close the connection
$conn->close();
