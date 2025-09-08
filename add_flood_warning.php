<?php
// Start session to access user data
session_start();

// Database connection
include('config.php');

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $barangay = $_POST['barangay'] ?? '';
    $sitio = $_POST['sitio'] ?? '';
    $warning_level = $_POST['warning_level'] ?? '';
    $status = $_POST['status'] ?? '';
    $recommendation = $_POST['recommendation'] ?? '';
    
    // Validate required fields
    if (!empty($barangay) && !empty($sitio) && !empty($warning_level) && !empty($status)) {
        // Prepare and execute the insert query
        $stmt = $conn->prepare("INSERT INTO FloodWarning 
                              (barangay, sitio, warning_level, status, recommended_action, date_created) 
                              VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $barangay, $sitio, $warning_level, $status, $recommendation);
        
        if ($stmt->execute()) {
            $message = "Flood warning data added successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
        $stmt->close();
    } else {
        $message = "Please fill in all required fields.";
        $messageType = "warning";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Flood Warning - Micro OSS App</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
        }
        .form-container {
            background-color: #f8f9fa;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include('includes/nav.php'); ?>

    <div class="container mt-5 main-content">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2><i class="fas fa-exclamation-triangle"></i> Add Flood Warning</h2>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="form-container mt-4">
                    <form method="POST" action="add_flood_warning-backend.php">
                        <div class="form-group">
                            <label for="barangay">Barangay <span class="text-danger">*</span></label>
                            <select class="form-control" id="barangay" name="barangay" required>
                                <option value="">Select Barangay</option>
                                <option value="lizada">Lizada</option>
                                <option value="daliao">Daliao</option>
                                <option value="toril">Toril</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sitio">Sitio/Purok <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sitio" name="sitio" required>
                        </div>

                        <div class="form-group">
                            <label>Warning Level <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="warning_level" id="level1" value="1" required>
                                <label class="form-check-label" for="level1">
                                    <span class="badge badge-warning p-2">Level 1 - Yellow Alert</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="warning_level" id="level2" value="2">
                                <label class="form-check-label" for="level2">
                                    <span class="badge badge-warning p-2" style="background-color: orange;">Level 2 - Orange Alert</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="warning_level" id="level3" value="3">
                                <label class="form-check-label" for="level3">
                                    <span class="badge badge-danger p-2">Level 3 - Red Alert</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Monitoring">Monitoring</option>
                                <option value="Alert">Alert</option>
                                <option value="Evacuation">Evacuation</option>
                                <option value="Safe">Safe</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="recommendation">Recommendation/Action</label>
                            <textarea class="form-control" id="recommendation" name="recommendation_action" rows="3"></textarea>
                        </div>

                        <div class="form-group text-right">
                            <a href="flood_warning.php" class="btn btn-secondary mr-2">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white mt-5 p-4 text-center">
        &copy; 2024 Flood Resilience App. All Rights Reserved.
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>