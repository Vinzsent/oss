<?php
session_start();

include('config.php');

// Handle CRUD operations
if ($_POST) {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
            $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $role = mysqli_real_escape_string($conn, $_POST['role']);
            $province = mysqli_real_escape_string($conn, $_POST['province']);
            $city_municipality = mysqli_real_escape_string($conn, $_POST['city_municipality']);

            $sql = "INSERT INTO users (first_name, last_name, email, role, province, city_municipality) VALUES ('$first_name', '$last_name', '$email', '$role', '$province', '$city_municipality')";
            if (mysqli_query($conn, $sql)) {
                $success_msg = "User created successfully!";
            } else {
                $error_msg = "Error: " . mysqli_error($conn);
            }
            break;

        case 'update':
            $id = (int)$_POST['id'];
            $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
            $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
            $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $role = mysqli_real_escape_string($conn, $_POST['role']);
            $province = mysqli_real_escape_string($conn, $_POST['province']);
            $city_municipality = mysqli_real_escape_string($conn, $_POST['city_municipality']);

            $sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', middle_name='$middle_name', email='$email', role='$role', province='$province', city_municipality='$city_municipality' WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                $success_msg = "User updated successfully!";
            } else {
                $error_msg = "Error: " . mysqli_error($conn);
            }
            break;

        case 'delete':
            $id = (int)$_POST['id'];
            $sql = "DELETE FROM users WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                $success_msg = "User deleted successfully!";
            } else {
                $error_msg = "Error: " . mysqli_error($conn);
            }
            break;
    }
}

// Check for session messages from add-admin.php
if (isset($_SESSION['success_msg'])) {
    $success_msg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}

if (isset($_SESSION['error_msg'])) {
    $error_msg = $_SESSION['error_msg'];
    unset($_SESSION['error_msg']);
}

// Get user statistics and data
$total_users_query = "SELECT COUNT(*) as count FROM users";
$total_users_result = mysqli_query($conn, $total_users_query);
$total_users = mysqli_fetch_assoc($total_users_result);

$user_query = "SELECT * FROM users WHERE email IS NOT NULL AND email != '' ORDER BY created_at DESC";
$user_result = mysqli_query($conn, $user_query);

// Get all users for the table display
$users_query = "SELECT * FROM users";
$users_result = mysqli_query($conn, $users_query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - User Management</title>
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

        .btn-success-custom {
            background: var(--success-green);
            border-color: var(--success-green);
            border-radius: 20px;
            padding: 8px 20px;
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

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: #E8F5E8;
            color: var(--success-green);
        }

        .status-inactive {
            background: #FFF3E0;
            color: var(--warning-orange);
        }

        .role-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .role-admin {
            background: #FFEBEE;
            color: var(--danger-red);
        }

        .role-user {
            background: var(--light-blue);
            color: var(--primary-blue);
        }

        .role-moderator {
            background: #FFF3E0;
            color: var(--warning-orange);
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

        .logout-btn {
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

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }

        .admin-sidebar {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            margin-bottom: 20px;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-menu li {
            margin-bottom: 10px;
        }

        .nav-menu a {
            color: var(--dark-gray);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-menu a:hover {
            color: var(--primary-blue);
        }

        .nav-menu a.active {
            color: var(--primary-blue);
            font-weight: 600;
        }

        .nav-badge {
            background: var(--primary-blue);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="admin-header">
        <div class="container position-relative">
            <button><a href="home.php" class="logout-btn text-decoration-none">
                <i class="fas fa-arrow-left mr-2"></i>Back to Home
           </a></button>
            <h1 class="admin-title">
                <i class="fas fa-users-cog mr-3"></i>Admin Dashboard
            </h1>
            <p class="admin-subtitle">User Management System</p>
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
            <div class="col-md-3">
                <div class="stats-card">
                    <?php
                    $active_users = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role != 'admin' AND status = 'active'");
                    $active_count = ($active_users) ? mysqli_fetch_assoc($active_users)['count'] : 0;
                    ?>
                    <div class="stats-number"><?php echo $active_count; ?></div>
                    <div class="stats-label">Active Users</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <?php 
                    $admin_users = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
                    $admin_count = ($admin_users) ? mysqli_fetch_assoc($admin_users)['count'] : 0;
                    ?>
                    <div class="stats-number"><?php echo $admin_count; ?></div>
                    <div class="stats-label">Administrators</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <?php 
                    $today_users = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE id > 0");
                    $today_count = ($today_users) ? mysqli_fetch_assoc($today_users)['count'] : 0;
                    ?>
                    <div class="stats-number"><?php echo $today_count; ?></div>
                    <div class="stats-label">Total Records</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?php echo $total_users['count']; ?></div>
                    <div class="stats-label">Total Users</div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="row">
            <!-- Navigation Sidebar -->
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="admin-sidebar">
                    <div class="sidebar-header">
                        <h5><i class="fas fa-cogs mr-2"></i>Admin Panel</h5>
                    </div>
                    <ul class="nav-menu">
                        <li>
                            <a href="#" class="active">
                                <i class="fas fa-users"></i>
                                User Management
                                <span class="nav-badge"><?php echo $total_users['count']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="home.php">
                                <i class="fas fa-home"></i>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="barangay_management.php">
                                <i class="fas fa-map-marked-alt"></i>
                                Barangay Management
                            </a>
                        </li>
                        <li>
                            <a href="gallery.php">
                                <i class="fas fa-images"></i>
                                Gallery Management
                            </a>
                        </li>
                        <li>
                            <a href="alert.php">
                                <i class="fas fa-exclamation-triangle"></i>
                                Alert System
                            </a>
                        </li>
                        <li>
                            <a href="flood_warning.php">
                                <i class="fas fa-water"></i>
                                Flood Warnings
                            </a>
                        </li>
                        <li>
                            <a href="evacuation.php">
                                <i class="fas fa-route"></i>
                                Evacuation Routes
                            </a>
                        </li>
                        <li>
                            <a href="hazard.php">
                                <i class="fas fa-radiation"></i>
                                Hazard Maps
                            </a>
                        </li>
                        <li>
                            <a href="publications.php">
                                <i class="fas fa-book"></i>
                                Publications
                            </a>
                        </li>
                        <li>
                            <a href="upload.php">
                                <i class="fas fa-upload"></i>
                                File Upload
                            </a>
                        </li>
                        <li>
                            <a href="maps.php">
                                <i class="fas fa-map"></i>
                                Maps & Resources
                            </a>
                        </li>
                        <li>
                            <a href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- User Management Table -->
            <div class="col-lg-9 col-md-8">
                <div class="content-card">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-users mr-2"></i>User Management</h4>
                        <button class="btn btn-light btn-sm" data-toggle="modal" data-target="#addUserModal">
                            <i class="fas fa-plus mr-2"></i>Add New User
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="overflow-x: hidden;">
                            <table class="table table-custom table-sm" style="font-size: 0.85rem;">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">ID</th>
                                        <th style="width: 90px;">First</th>
                                        <th style="width: 90px;">Middle</th>
                                        <th style="width: 90px;">Last</th>
                                        <th style="width: 150px;">Email</th>
                                        <th style="width: 70px;">Role</th>
                                        <th style="width: 100px;">Province</th>
                                        <th style="width: 100px;">City/Mun</th>
                                        <th style="width: 80px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($users_result && mysqli_num_rows($users_result) > 0) {
                                        while ($user = mysqli_fetch_assoc($users_result)):
                                    ?>
                                        <tr>
                                            <td style="width: 50px;"><strong>#<?php echo $user['id']; ?></strong></td>
                                            <td style="width: 90px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($user['first_name']); ?>"><?php echo htmlspecialchars($user['first_name']); ?></td>
                                            <td style="width: 90px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($user['middle_name']); ?>"><?php echo htmlspecialchars($user['middle_name']); ?></td>
                                            <td style="width: 90px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($user['last_name']); ?>"><?php echo htmlspecialchars($user['last_name']); ?></td>
                                            <td style="width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($user['email']); ?>"><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td style="width: 70px;">
                                                <span class="role-badge role-<?php echo $user['role']; ?>" style="font-size: 0.7rem; padding: 2px 6px;">
                                                    <?php echo strtoupper(substr($user['role'], 0, 4)); ?>
                                                </span>
                                            </td>
                                            <td style="width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($user['province']); ?>"><?php echo htmlspecialchars($user['province']); ?></td>
                                            <td style="width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($user['city_municipality']); ?>"><?php echo htmlspecialchars($user['city_municipality']); ?></td>
                                            <td style="width: 80px;">
                                                <div class="d-flex">
                                                    <button class="btn btn-warning-custom btn-sm mr-1" style="padding: 2px 6px; font-size: 0.7rem;" onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-danger-custom btn-sm" style="padding: 2px 6px; font-size: 0.7rem;" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    } else {
                                        echo '<tr><td colspan="9" class="text-center">No users found</td></tr>';
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

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i>Add New User</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="add-admin.php">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" class="form-control" name="middle_name">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" required>
                            <small>Minimum 6 characters</small>
                        </div>
                        <div class="form-group">
                            <label>Province</label>
                            <input type="text" class="form-control" name="province" required>
                        </div>
                        <div class="form-group">
                            <label>City/Municipality</label>
                            <input type="text" class="form-control" name="city_municipality" required>
                        </div>
                        <div class="form-group">
                            <label>Barangay</label>
                            <input type="text" class="form-control" name="barangay">
                        </div>
                        <div class="form-group">
                            <label>Purok</label>
                            <input type="text" class="form-control" name="purok">
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select class="form-control" name="role" required>
                                <option value="user">User</option>
                                <option value="moderator">Moderator</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-user-edit mr-2"></i>Edit User</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" id="editUserForm" action="edit_user.php">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="first_name" id="edit_first_name" required>
                        </div>
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" class="form-control" name="middle_name" id="edit_middle_name">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="edit_last_name" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" id="edit_password">
                            <small class="text-muted"></small>
                        </div>
                        <div class="form-group">
                            <label>Province</label>
                            <input type="text" class="form-control" name="province" id="edit_province" required>
                        </div>
                        <div class="form-group">
                            <label>City/Municipality</label>
                            <input type="text" class="form-control" name="city_municipality" id="edit_city_municipality" required>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select class="form-control" name="role" id="edit_role" required>
                                <option value="user">User</option>
                                <option value="moderator">Moderator</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <!--<div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status" id="edit_status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete user <strong id="delete_user_name"></strong>?</p>
                    <p class="text-muted">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete_user_id">
                        <button type="submit" class="btn btn-danger">Delete User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function editUser(user) {
            $('#edit_id').val(user.id);
            $('#edit_first_name').val(user.first_name);
            $('#edit_middle_name').val(user.middle_name);
            $('#edit_last_name').val(user.last_name);
            $('#edit_email').val(user.email);
            $('#edit_password').val(user.password);
            $('#edit_role').val(user.role);
            $('#edit_province').val(user.province);
            $('#edit_city_municipality').val(user.city_municipality);
            $('#edit_status').val(user.status);
            $('#editUserModal').modal('show');
        }

        function deleteUser(id, name) {
            $('#delete_user_id').val(id);
            $('#delete_user_name').text(name);
            $('#deleteUserModal').modal('show');
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    </script>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="logoutModalLabel">
                        <i class="fas fa-sign-out-alt mr-2"></i>Confirm Logout
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-0">Are you sure you want to log out?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <a href="logout.php" class="btn btn-primary">Yes</a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>