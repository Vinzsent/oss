<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include('config.php');
include('includes/nav.php');

// Get ownership type and households from URL parameters
$ownership_type = isset($_GET['ownership_type']) ? $_GET['ownership_type'] : '';
$households = isset($_GET['households']) ? $_GET['households'] : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_ownership_type = $_POST['ownership_type'];
    $new_households = $_POST['households'];
    
    // Update database
    $sql = "UPDATE household_ownership SET households = ? WHERE ownership_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $new_households, $new_ownership_type);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Household ownership data updated successfully!";
        header('Location: household_materials.php');
        exit();
    } else {
        $error_message = "Error updating data: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Household Ownership Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .edit-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            margin-top: 50px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .page-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .page-header p {
            color: #666;
            font-size: 16px;
        }
        .ownership-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
        }
        .form-icon {
            position: absolute;
            right: 15px;
            top: 42px;
            color: #667eea;
        }
        .input-group {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="edit-container">
            <div class="page-header">
                <h2><i class="fas fa-home me-2"></i>Edit Household Ownership Data</h2>
                <p>Update the household ownership information in the survey data</p>
            </div>
            
            <div class="ownership-badge">
                <i class="fas fa-edit me-2"></i>Ownership Type: <?php echo htmlspecialchars($ownership_type); ?>
            </div>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="ownership_type" class="form-label">Ownership Type</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="ownership_type" name="ownership_type" 
                               value="<?php echo htmlspecialchars($ownership_type); ?>" readonly>
                        <i class="fas fa-home form-icon"></i>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="households" class="form-label">Number of Households</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="households" name="households" 
                               value="<?php echo htmlspecialchars($households); ?>" min="0" required>
                        <i class="fas fa-users form-icon"></i>
                    </div>
                    <div class="form-text">Enter the total number of households for this ownership type</div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="household_materials.php" class="btn btn-secondary me-md-2">
                        <i class="fas fa-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
