<?php
// Start session to access user data
session_start();

include('config.php');

$user_role = '';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $user_query = "SELECT role FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $user_id);
    if ($user_stmt->execute()) {
        $user_result = $user_stmt->get_result();
        if ($user_result && $user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $user_role = $user['role'];
            // Debug: Show the role in the input field
            $debug_role = htmlspecialchars($user_role);
        }
    }
    $user_stmt->close();
}

// Get list of barangays for dropdown
$barangays = ['Lizada', 'Daliao']; // Add more barangays as needed

// Process form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barangay = $_POST['barangay'] ?? '';
    $sitio_purok = trim($_POST['sitio_purok'] ?? '');
    $total_families = (int)($_POST['total_families'] ?? 0);
    $total_persons = (int)($_POST['total_persons'] ?? 0);
    $risk_level = (int)($_POST['risk_level'] ?? 0);

    // Validate input
    if (empty($sitio_purok) || $total_families <= 0 || $total_persons <= 0) {
        $message = 'Please fill in all required fields with valid data.';
        $message_type = 'danger';
    } else {
        // Check if sitio/purok already exists
        $check_sql = "SELECT id FROM socio_data WHERE barangay = ? AND sitio_purok = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $barangay, $sitio_purok);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = 'This sitio/purok already exists in the selected barangay.';
            $message_type = 'warning';
        } else {
            // Insert new record
            $insert_sql = "INSERT INTO socio_data (barangay, sitio_purok, total_families, total_persons, risk_level) 
                          VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssiii", $barangay, $sitio_purok, $total_families, $total_persons, $risk_level);
            
            if ($insert_stmt->execute()) {
                $message = 'Socio-demographic data added successfully!';
                $message_type = 'success';
                // Clear form
                $_POST = [];
            } else {
                $message = 'Error adding data: ' . $conn->error;
                $message_type = 'danger';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Socio-Demographic Data - Micro OSS App</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content-wrapper {
            flex: 1 0 auto;
            padding-bottom: 60px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include('includes/nav.php'); ?>

    <div class="content-wrapper">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Add New Socio-Demographic Data</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                                    <?php echo $message; ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <form method="post" action="add_socio.php">
                                <div class="form-group">
                                    <label for="barangay">Barangay <span class="text-danger">*</span></label>
                                    <select class="form-control" id="barangay" name="barangay" required>
                                        <option value="">Select Barangay</option>
                                        <?php foreach ($barangays as $brgy): ?>
                                            <option value="<?php echo $brgy; ?>" <?php echo (isset($_POST['barangay']) && $_POST['barangay'] === $brgy) ? 'selected' : ''; ?>>
                                                <?php echo ucfirst($brgy); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="sitio_purok">Sitio/Purok Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="sitio_purok" name="sitio_purok" 
                                           value="<?php echo htmlspecialchars($_POST['sitio_purok'] ?? ''); ?>" 
                                           required>
                                </div>

                                <div class="form-group">
                                    <label for="total_families">Total Number of Families <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="total_families" name="total_families" 
                                           min="1" value="<?php echo $_POST['total_families'] ?? ''; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="total_persons">Total Number of Persons <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="total_persons" name="total_persons" 
                                           min="1" value="<?php echo $_POST['total_persons'] ?? ''; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="risk_level">Risk Level</label>
                                    <select class="form-control" id="risk_level" name="risk_level">
                                        <option value="0" <?php echo (isset($_POST['risk_level']) && $_POST['risk_level'] == 0) ? 'selected' : ''; ?>>None</option>
                                        <option value="1" <?php echo (isset($_POST['risk_level']) && $_POST['risk_level'] == 1) ? 'selected' : ''; ?>>Low</option>
                                        <option value="2" <?php echo (isset($_POST['risk_level']) && $_POST['risk_level'] == 2) ? 'selected' : ''; ?>>Medium</option>
                                        <option value="3" <?php echo (isset($_POST['risk_level']) && $_POST['risk_level'] == 3) ? 'selected' : ''; ?>>High</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Risk Level Colors: 
                                        <span class="badge" style="background-color: white; color: black; border: 1px solid #dee2e6;">None</span>
                                        <span class="badge badge-warning">Low</span>
                                        <span class="badge" style="background-color: orange; color: white;">Medium</span>
                                        <span class="badge badge-danger">High</span>
                                    </small>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Data
                                    </button>
                                    <a href="socio.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white p-4 text-center">
        <div class="container">
            &copy; 2024 Flood Resilience App. All Rights Reserved.
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>