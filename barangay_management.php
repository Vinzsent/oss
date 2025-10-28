<?php
session_start();
include('config.php');

// Handle CRUD operations
if ($_POST) {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create_barangay':
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $district = mysqli_real_escape_string($conn, $_POST['district']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);

            $sql = "INSERT INTO barangays (name, district, description) VALUES ('$name', '$district', '$description')";
            if (mysqli_query($conn, $sql)) {
                $success_msg = "Barangay created successfully!";
            } else {
                $error_msg = "Error: " . mysqli_error($conn);
            }
            break;

        case 'create_purok':
            $barangay_id = (int)$_POST['barangay_id'];
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $type = mysqli_real_escape_string($conn, $_POST['type']);
            $coordinates_x = (float)$_POST['coordinates_x'];
            $coordinates_y = (float)$_POST['coordinates_y'];

            $sql = "INSERT INTO puroks_sitios (barangay_id, name, type, coordinates_x, coordinates_y) VALUES ('$barangay_id', '$name', '$type', '$coordinates_x', '$coordinates_y')";
            if (mysqli_query($conn, $sql)) {
                $success_msg = "Purok/Sitio created successfully!";
            } else {
                $error_msg = "Error: " . mysqli_error($conn);
            }
            break;

        case 'update_barangay':
            $id = (int)$_POST['id'];
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $district = mysqli_real_escape_string($conn, $_POST['district']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);

            $sql = "UPDATE barangays SET name='$name', district='$district', description='$description' WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                $success_msg = "Barangay updated successfully!";
            } else {
                $error_msg = "Error: " . mysqli_error($conn);
            }
            break;

        case 'delete_barangay':
            $id = (int)$_POST['id'];
            $sql = "DELETE FROM barangays WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                $success_msg = "Barangay deleted successfully!";
            } else {
                $error_msg = "Error: " . mysqli_error($conn);
            }
            break;

        case 'delete_purok':
            $id = (int)$_POST['id'];
            $sql = "DELETE FROM puroks_sitios WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                $success_msg = "Purok/Sitio deleted successfully!";
            } else {
                $error_msg = "Error: " . mysqli_error($conn);
            }
            break;
    }
}

// Get data for display
$barangays_query = "SELECT * FROM barangays ORDER BY name";
$barangays_result = mysqli_query($conn, $barangays_query);

$puroks_query = "SELECT ps.*, b.name as barangay_name FROM puroks_sitios ps LEFT JOIN barangays b ON ps.barangay_id = b.id ORDER BY b.name, ps.name";
$puroks_result = mysqli_query($conn, $puroks_query);

$total_barangays = mysqli_num_rows($barangays_result);
$total_puroks = mysqli_query($conn, "SELECT COUNT(*) as count FROM puroks_sitios");
$total_puroks_count = mysqli_fetch_assoc($total_puroks)['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Management - Admin Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #4A90E2;
            --light-blue: #E3F2FD;
            --dark-blue: #2E5C8A;
            --accent-blue: #1976D2;
            --success-green: #4CAF50;
            --warning-orange: #FF9800;
            --danger-red: #F44336;
            --light-gray: #F5F5F5;
            --dark-gray: #333333;
        }

        body {
            background-color: var(--light-gray);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-blue));
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .admin-title {
            font-size: 2rem;
            font-weight: 600;
            margin: 0;
        }

        .admin-subtitle {
            opacity: 0.9;
            margin: 0;
        }

        .main-content {
            padding: 30px 0;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-left: 4px solid var(--primary-blue);
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-blue);
        }

        .stats-label {
            color: var(--dark-gray);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .content-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card-header-custom {
            background: var(--primary-blue);
            color: white;
            padding: 20px;
            border: none;
        }

        .btn-primary-custom {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background: var(--dark-blue);
            border-color: var(--dark-blue);
            transform: translateY(-2px);
        }

        .btn-warning-custom {
            background: var(--warning-orange);
            border-color: var(--warning-orange);
            border-radius: 20px;
            padding: 8px 20px;
        }

        .btn-danger-custom {
            background: var(--danger-red);
            border-color: var(--danger-red);
            border-radius: 20px;
            padding: 8px 20px;
        }

        .table-custom {
            margin: 0;
        }

        .table-custom thead th {
            background: var(--light-blue);
            color: var(--dark-blue);
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            padding: 15px;
        }

        .table-custom tbody td {
            padding: 15px;
            vertical-align: middle;
            border-color: #f0f0f0;
        }

        .type-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .type-purok {
            background: #E8F5E8;
            color: var(--success-green);
        }

        .type-sitio {
            background: var(--light-blue);
            color: var(--primary-blue);
        }

        .modal-header-custom {
            background: var(--primary-blue);
            color: white;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        }

        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 25px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            color: var(--dark-gray);
            font-weight: 500;
            padding: 15px 25px;
        }

        .nav-tabs-custom .nav-link.active {
            background: var(--primary-blue);
            color: white;
            border-radius: 10px 10px 0 0;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="admin-header">
        <div class="container position-relative">
            <button><a href="admin.php" class="back-btn text-decoration-none">
                <i class="fas fa-arrow-left mr-2"></i>Back to Admin
            </a></button>
            <h1 class="admin-title">
                <i class="fas fa-map-marked-alt mr-3"></i>Barangay Management
            </h1>
            <p class="admin-subtitle">Manage Barangays, Puroks & Sitios</p>
        </div>
    </div>

    <div class="container main-content">
        <!-- Success/Error Messages -->
        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success alert-custom alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i><?php echo $success_msg; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger alert-custom alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error_msg; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?php echo $total_barangays; ?></div>
                    <div class="stats-label">Total Barangays</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?php echo $total_puroks_count; ?></div>
                    <div class="stats-label">Total Puroks/Sitios</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <?php 
                    $sitio_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM puroks_sitios WHERE type = 'sitio'");
                    $sitio_count_result = mysqli_fetch_assoc($sitio_count)['count'];
                    ?>
                    <div class="stats-number"><?php echo $sitio_count_result; ?></div>
                    <div class="stats-label">Total Sitios</div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs nav-tabs-custom mb-4">
            <li class="nav-item">
                <a class="nav-link active" id="barangays-tab" data-toggle="tab" href="#barangays">
                    <i class="fas fa-building mr-2"></i>Barangays
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="puroks-tab" data-toggle="tab" href="#puroks">
                    <i class="fas fa-map-pin mr-2"></i>Puroks & Sitios
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Barangays Tab -->
            <div class="tab-pane fade show active" id="barangays">
                <div class="content-card">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-building mr-2"></i>Barangay Management</h4>
                        <button class="btn btn-light btn-sm" data-toggle="modal" data-target="#addBarangayModal">
                            <i class="fas fa-plus mr-2"></i>Add Barangay
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>District</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    mysqli_data_seek($barangays_result, 0);
                                    if ($barangays_result && mysqli_num_rows($barangays_result) > 0) {
                                        while ($barangay = mysqli_fetch_assoc($barangays_result)):
                                    ?>
                                        <tr>
                                            <td><strong>#<?php echo $barangay['id']; ?></strong></td>
                                            <td><?php echo htmlspecialchars($barangay['name']); ?></td>
                                            <td><?php echo htmlspecialchars($barangay['district']); ?></td>
                                            <td><?php echo htmlspecialchars($barangay['description']); ?></td>
                                            <td>
                                                <div class="d-flex">
                                                    <button class="btn btn-warning-custom btn-sm mr-1" onclick="editBarangay(<?php echo htmlspecialchars(json_encode($barangay)); ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-danger-custom btn-sm" onclick="deleteBarangay(<?php echo $barangay['id']; ?>, '<?php echo htmlspecialchars($barangay['name']); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center">No barangays found</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Puroks & Sitios Tab -->
            <div class="tab-pane fade" id="puroks">
                <div class="content-card">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-map-pin mr-2"></i>Puroks & Sitios Management</h4>
                        <button class="btn btn-light btn-sm" data-toggle="modal" data-target="#addPurokModal">
                            <i class="fas fa-plus mr-2"></i>Add Purok/Sitio
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Barangay</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Coordinates</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($puroks_result && mysqli_num_rows($puroks_result) > 0) {
                                        while ($purok = mysqli_fetch_assoc($puroks_result)):
                                    ?>
                                        <tr>
                                            <td><strong>#<?php echo $purok['id']; ?></strong></td>
                                            <td><?php echo htmlspecialchars($purok['barangay_name']); ?></td>
                                            <td><?php echo htmlspecialchars($purok['name']); ?></td>
                                            <td>
                                                <span class="type-badge type-<?php echo $purok['type']; ?>">
                                                    <?php echo ucfirst($purok['type']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $purok['coordinates_x']; ?>, <?php echo $purok['coordinates_y']; ?></td>
                                            <td>
                                                <div class="d-flex">
                                                    <button class="btn btn-warning-custom btn-sm mr-1" onclick="editPurok(<?php echo htmlspecialchars(json_encode($purok)); ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-danger-custom btn-sm" onclick="deletePurok(<?php echo $purok['id']; ?>, '<?php echo htmlspecialchars($purok['name']); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    } else {
                                        echo '<tr><td colspan="6" class="text-center">No puroks/sitios found</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Barangay Modal -->
    <div class="modal fade" id="addBarangayModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-building mr-2"></i>Add New Barangay</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create_barangay">
                        <div class="form-group">
                            <label>Barangay Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>District</label>
                            <input type="text" class="form-control" name="district" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">Create Barangay</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Purok/Sitio Modal -->
    <div class="modal fade" id="addPurokModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-map-pin mr-2"></i>Add New Purok/Sitio</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create_purok">
                        <div class="form-group">
                            <label>Barangay</label>
                            <select class="form-control" name="barangay_id" required>
                                <option value="">-- Select Barangay --</option>
                                <?php
                                mysqli_data_seek($barangays_result, 0);
                                while ($barangay = mysqli_fetch_assoc($barangays_result)):
                                ?>
                                    <option value="<?php echo $barangay['id']; ?>"><?php echo htmlspecialchars($barangay['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" name="type" required>
                                <option value="purok">Purok</option>
                                <option value="sitio">Sitio</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>X Coordinate (%)</label>
                                    <input type="number" class="form-control" name="coordinates_x" min="0" max="100" step="0.1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Y Coordinate (%)</label>
                                    <input type="number" class="form-control" name="coordinates_y" min="0" max="100" step="0.1" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">Create Purok/Sitio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Barangay Modal -->
    <div class="modal fade" id="editBarangayModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-building mr-2"></i>Edit Barangay</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_barangay">
                        <input type="hidden" name="id" id="edit_barangay_id">
                        <div class="form-group">
                            <label>Barangay Name</label>
                            <input type="text" class="form-control" name="name" id="edit_barangay_name" required>
                        </div>
                        <div class="form-group">
                            <label>District</label>
                            <input type="text" class="form-control" name="district" id="edit_barangay_district" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="edit_barangay_description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">Update Barangay</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Purok/Sitio Modal -->
    <div class="modal fade" id="editPurokModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-map-pin mr-2"></i>Edit Purok/Sitio</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_purok">
                        <input type="hidden" name="id" id="edit_purok_id">
                        <div class="form-group">
                            <label>Barangay</label>
                            <select class="form-control" name="barangay_id" id="edit_purok_barangay_id" required>
                                <option value="">-- Select Barangay --</option>
                                <?php
                                mysqli_data_seek($barangays_result, 0);
                                while ($barangay = mysqli_fetch_assoc($barangays_result)):
                                ?>
                                    <option value="<?php echo $barangay['id']; ?>"><?php echo htmlspecialchars($barangay['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" id="edit_purok_name" required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" name="type" id="edit_purok_type" required>
                                <option value="purok">Purok</option>
                                <option value="sitio">Sitio</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>X Coordinate (%)</label>
                                    <input type="number" class="form-control" name="coordinates_x" id="edit_purok_x" min="0" max="100" step="0.1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Y Coordinate (%)</label>
                                    <input type="number" class="form-control" name="coordinates_y" id="edit_purok_y" min="0" max="100" step="0.1" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">Update Purok/Sitio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modals -->
    <div class="modal fade" id="deleteBarangayModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete barangay <strong id="delete_barangay_name"></strong>?</p>
                    <p class="text-muted">This action cannot be undone and will also delete all associated puroks/sitios.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete_barangay">
                        <input type="hidden" name="id" id="delete_barangay_id">
                        <button type="submit" class="btn btn-danger">Delete Barangay</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deletePurokModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="delete_purok_name"></strong>?</p>
                    <p class="text-muted">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete_purok">
                        <input type="hidden" name="id" id="delete_purok_id">
                        <button type="submit" class="btn btn-danger">Delete Purok/Sitio</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function editBarangay(barangay) {
            $('#edit_barangay_id').val(barangay.id);
            $('#edit_barangay_name').val(barangay.name);
            $('#edit_barangay_district').val(barangay.district);
            $('#edit_barangay_description').val(barangay.description);
            $('#editBarangayModal').modal('show');
        }

        function editPurok(purok) {
            $('#edit_purok_id').val(purok.id);
            $('#edit_purok_barangay_id').val(purok.barangay_id);
            $('#edit_purok_name').val(purok.name);
            $('#edit_purok_type').val(purok.type);
            $('#edit_purok_x').val(purok.coordinates_x);
            $('#edit_purok_y').val(purok.coordinates_y);
            $('#editPurokModal').modal('show');
        }

        function deleteBarangay(id, name) {
            $('#delete_barangay_id').val(id);
            $('#delete_barangay_name').text(name);
            $('#deleteBarangayModal').modal('show');
        }

        function deletePurok(id, name) {
            $('#delete_purok_id').val(id);
            $('#delete_purok_name').text(name);
            $('#deletePurokModal').modal('show');
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    </script>

</body>
</html>