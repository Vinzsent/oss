<?php
session_start();
include('config.php');

// Get ownership ID from URL parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch ownership data from database
if ($id > 0) {
    $sql = "SELECT * FROM household_ownership WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $ownership = $result->fetch_assoc();
        $ownership_type = $ownership['ownership_type'];
        $households = $ownership['households'];
        $survey_year = $ownership['survey_year'];
    } else {
        $_SESSION['error_message'] = "Ownership data not found!";
        header('Location: household_materials.php');
        exit();
    }
} else {
    $_SESSION['error_message'] = "Invalid ownership ID!";
    header('Location: household_materials.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_ownership_type = $_POST['ownership_type'];
    $new_households = $_POST['households'];
    $new_survey_year = $_POST['survey_year'];
    
    // Update database
    $sql = "UPDATE household_ownership SET ownership_type = ?, households = ?, survey_year = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $new_ownership_type, $new_households, $new_survey_year, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Ownership data updated successfully!";
        echo "<script>alert('Ownership data updated successfully!'); window.location.href='household_materials.php';</script>";
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
    <title>Edit Ownership Data</title>
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
            background: #8b5cf6;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 92, 246, 0.4);
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
            background: #8b5cf6;
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
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            pointer-events: none;
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
                <h2><i class="fas fa-key me-2"></i>Edit Ownership Data</h2>
                <p>Update the household ownership information in the survey data</p>
            </div>
            
            <div class="ownership-badge">
                <i class="fas fa-home me-2"></i>Ownership Type: <?php echo htmlspecialchars($ownership_type); ?>
            </div>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="ownership_type" class="form-label"><i class="fas fa-key me-2"></i>Ownership Type</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="ownership_type" name="ownership_type" 
                               value="<?php echo htmlspecialchars($ownership_type); ?>" required>
                        <i class="fas fa-key form-icon"></i>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="households" class="form-label"><i class="fas fa-users me-2"></i>Number of Households</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="households" name="households" 
                               value="<?php echo htmlspecialchars($households); ?>" min="0" required>
                        <i class="fas fa-users form-icon"></i>
                    </div>
                    <div class="form-text">Enter the total number of households for this ownership type</div>
                </div>
                
                <div class="mb-4">
                    <label for="survey_year" class="form-label"><i class="fas fa-calendar me-2"></i>Survey Year</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="survey_year" name="survey_year" 
                               value="<?php echo htmlspecialchars($survey_year); ?>" min="2000" max="2100" required>
                        <i class="fas fa-calendar form-icon"></i>
                    </div>
                    <div class="form-text">Enter the survey year for this data</div>
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
