<?php
session_start();
include('config.php');
include('includes/nav.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $age_bracket = $_POST['age_bracket'];
    $female = $_POST['female'];
    $male = $_POST['male'];
    $total = $female + $male;
    
    // Check if age bracket already exists
    $check_sql = "SELECT id FROM age_population WHERE age_bracket = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $age_bracket);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing record
        $update_sql = "UPDATE age_population SET female = ?, male = ?, total = ? WHERE age_bracket = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iiis", $female, $male, $total, $age_bracket);
        
        if ($update_stmt->execute()) {
            $success_message = "Age bracket data updated successfully!";
        } else {
            $error_message = "Error updating data: " . $conn->error;
        }
    } else {
        // Insert new record
        $insert_sql = "INSERT INTO age_population (age_bracket, female, male, total) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("siii", $age_bracket, $female, $male, $total);
        
        if ($insert_stmt->execute()) {
            $success_message = "New age bracket data added successfully!";
        } else {
            $error_message = "Error adding data: " . $conn->error;
        }
    }
}
?>

<?php
// Fetch existing age brackets for dropdown
$brackets_sql = "SELECT DISTINCT age_bracket FROM age_population WHERE age_bracket != 'TOTAL' ORDER BY id";
$brackets_result = $conn->query($brackets_sql);
$existing_brackets = array();

if ($brackets_result->num_rows > 0) {
    while($row = $brackets_result->fetch_assoc()) {
        $existing_brackets[] = $row['age_bracket'];
    }
}

// Check if this is an edit request
$is_edit = isset($_GET['edit']) && $_GET['edit'] == '1';
$edit_age_bracket = '';
$edit_female = '';
$edit_male = '';

if ($is_edit) {
    $edit_age_bracket = isset($_GET['age_bracket']) ? $_GET['age_bracket'] : '';
    $edit_female = isset($_GET['female']) ? $_GET['female'] : '';
    $edit_male = isset($_GET['male']) ? $_GET['male'] : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Edit' : 'Add'; ?> Population Data - Micro Online Synthesis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }
        
        .page-subtitle {
            font-size: 1.2rem;
            margin: 10px 0 0 0;
            text-align: center;
            opacity: 0.9;
        }
        
        .form-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-header {
            color: #8b5cf6;
            font-weight: bold;
            margin-bottom: 25px;
            font-size: 1.5rem;
            text-align: center;
        }
        
        .form-label {
            color: #4b5563;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }
        
        .btn-primary {
            background: #8b5cf6;
            border-color: #8b5cf6;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #7c3aed;
            border-color: #7c3aed;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
        }
        
        .btn-secondary {
            background: #6b7280;
            border-color: #6b7280;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
            border-color: #4b5563;
            transform: translateY(-2px);
        }
        
        .alert-success {
            background-color: #dcfce7;
            border-color: #22c55e;
            color: #166534;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: #fef2f2;
            border-color: #ef4444;
            color: #991b1b;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .input-group-text {
            background-color: #f8fafc;
            border-color: #e5e7eb;
            color: #6b7280;
        }
        
        .existing-data {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .existing-data h5 {
            color: #8b5cf6;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .data-table thead {
            background-color: #8b5cf6;
            color: white;
        }
        
        .data-table th {
            padding: 12px;
            text-align: center;
            font-weight: bold;
            border: none;
        }
        
        .data-table tbody tr {
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .data-table tbody tr:hover {
            background-color: #f8fafc;
        }
        
        .data-table td {
            padding: 12px;
            text-align: center;
            border: none;
        }
        
        @media (max-width: 768px) {
            .main-container {
                padding: 10px;
            }
            
            .page-title {
                font-size: 1.8rem;
            }
            
            .form-container {
                padding: 20px;
            }
            
            .btn-primary, .btn-secondary {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-<?php echo $is_edit ? 'edit' : 'plus-circle'; ?> me-3"></i><?php echo $is_edit ? 'Edit' : 'Add'; ?> Population Data
            </h1>
            <p class="page-subtitle"><?php echo $is_edit ? 'Update' : 'Enter'; ?> demographic information for age brackets</p>
        </div>
        
        <div class="form-container">
            <h4 class="form-header">
                <i class="fas fa-users me-2"></i>
                Age Bracket Demographics
            </h4>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="age_bracket" class="form-label">
                            <i class="fas fa-calendar-alt me-2"></i>Age Bracket
                        </label>
                        <select class="form-select" id="age_bracket" name="age_bracket" required <?php echo $is_edit ? 'disabled' : ''; ?>>
                            <option value="">Select Age Bracket</option>
                            <?php
                            if (!empty($existing_brackets)) {
                                foreach ($existing_brackets as $bracket) {
                                    $selected = ($is_edit && $bracket === $edit_age_bracket) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($bracket) . "' $selected>" . htmlspecialchars($bracket) . "</option>";
                                }
                            } else {
                                // Fallback options if no data exists
                                $default_brackets = array('0-4', '5-9', '10-14', '15-19', '20-24', '25-29', '30-34', '35-39', '40-44', '45-49', '50-54', '55-59', '60-64', '65-69', '70-74', '75-79', '80+');
                                foreach ($default_brackets as $bracket) {
                                    $selected = ($is_edit && $bracket === $edit_age_bracket) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($bracket) . "' $selected>" . htmlspecialchars($bracket) . " years</option>";
                                }
                            }
                            ?>
                        </select>
                        <?php if ($is_edit): ?>
                            <input type="hidden" name="age_bracket" value="<?php echo htmlspecialchars($edit_age_bracket); ?>">
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="female" class="form-label">
                            <i class="fas fa-venus me-2"></i>Female Population
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="female" name="female" min="0" required value="<?php echo $is_edit ? htmlspecialchars($edit_female) : ''; ?>">
                            <span class="input-group-text">persons</span>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="male" class="form-label">
                            <i class="fas fa-mars me-2"></i>Male Population
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="male" name="male" min="0" required value="<?php echo $is_edit ? htmlspecialchars($edit_male) : ''; ?>">
                            <span class="input-group-text">persons</span>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="total_display" class="form-label">
                            <i class="fas fa-calculator me-2"></i>Total Population
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="total_display" readonly>
                            <span class="input-group-text">persons</span>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i><?php echo $is_edit ? 'Update' : 'Save'; ?> Data
                            </button>
                            <a href="population.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Population
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="existing-data">
            <h5><i class="fas fa-database me-2"></i>Existing Age Bracket Data</h5>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Age Bracket</th>
                            <th>Female</th>
                            <th>Male</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $existing_sql = "SELECT age_bracket, female, male, total FROM age_population WHERE age_bracket != 'TOTAL' ORDER BY id";
                        $existing_result = $conn->query($existing_sql);
                        
                        if ($existing_result->num_rows > 0) {
                            // Track unique age brackets to avoid duplicates
                            $displayed_brackets = array();
                            while($row = $existing_result->fetch_assoc()) {
                                $bracket = $row["age_bracket"];
                                
                                // Skip if this bracket was already displayed
                                if (in_array($bracket, $displayed_brackets)) {
                                    continue;
                                }
                                
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($bracket) . "</td>";
                                echo "<td>" . number_format($row["female"]) . "</td>";
                                echo "<td>" . number_format($row["male"]) . "</td>";
                                echo "<td>" . number_format($row["total"]) . "</td>";
                                echo "</tr>";
                                
                                // Mark this bracket as displayed
                                $displayed_brackets[] = $bracket;
                            }
                            
                            // Calculate and display totals
                            $total_sql = "SELECT SUM(female) as total_female, SUM(male) as total_male, SUM(total) as total_population FROM age_population WHERE age_bracket != 'TOTAL'";
                            $total_result = $conn->query($total_sql);
                            $totals = $total_result->fetch_assoc();
                            
                            echo "<tr style='background-color: #f8fafc; font-weight: bold;'>";
                            echo "<td>TOTAL</td>";
                            echo "<td>" . number_format($totals["total_female"]) . "</td>";
                            echo "<td>" . number_format($totals["total_male"]) . "</td>";
                            echo "<td>" . number_format($totals["total_population"]) . "</td>";
                            echo "</tr>";
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>No existing data found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-calculate total
        function calculateTotal() {
            const female = parseInt(document.getElementById('female').value) || 0;
            const male = parseInt(document.getElementById('male').value) || 0;
            const total = female + male;
            document.getElementById('total_display').value = total;
        }
        
        // Add event listeners for auto-calculation
        document.getElementById('female').addEventListener('input', calculateTotal);
        document.getElementById('male').addEventListener('input', calculateTotal);
        
        // Initialize total on page load if editing
        <?php if ($is_edit): ?>
            document.addEventListener('DOMContentLoaded', function() {
                calculateTotal();
            });
        <?php endif; ?>
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const ageBracket = document.getElementById('age_bracket').value;
            const female = parseInt(document.getElementById('female').value) || 0;
            const male = parseInt(document.getElementById('male').value) || 0;
            
            if (!ageBracket) {
                e.preventDefault();
                alert('Please select an age bracket.');
                return;
            }
            
            if (female < 0 || male < 0) {
                e.preventDefault();
                alert('Population numbers cannot be negative.');
                return;
            }
            
            if (female === 0 && male === 0) {
                e.preventDefault();
                alert('Please enter at least one person in the population.');
                return;
            }
        });
    </script>
</body>
</html>
