<?php
// ANCHOR: File management interface for admin users
session_start();
include('config.php');

// ANCHOR: Check if user is logged in and is admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// ANCHOR: Get user role
$user_id = $_SESSION['id'];
$role_query = "SELECT role FROM users WHERE id = ?";
$role_stmt = $conn->prepare($role_query);
$role_stmt->bind_param("i", $user_id);
$role_stmt->execute();
$role_result = $role_stmt->get_result();
$user_role = $role_result->fetch_assoc()['role'];

if ($user_role !== 'admin') {
    header('Location: index.php');
    exit();
}

// ANCHOR: Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $file_id = $_POST['file_id'];
    
    // ANCHOR: Get file info
    $file_query = "SELECT file_path FROM publications WHERE id = ? AND uploaded_by = ?";
    $file_stmt = $conn->prepare($file_query);
    $file_stmt->bind_param("ii", $file_id, $user_id);
    $file_stmt->execute();
    $file_result = $file_stmt->get_result();
    
    if ($file_result->num_rows > 0) {
        $file_info = $file_result->fetch_assoc();
        
        // ANCHOR: Delete file from filesystem
        if (file_exists($file_info['file_path'])) {
            unlink($file_info['file_path']);
        }
        
        // ANCHOR: Delete from database
        $delete_query = "DELETE FROM publications WHERE id = ? AND uploaded_by = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("ii", $file_id, $user_id);
        $delete_stmt->execute();
        
        if ($delete_stmt->affected_rows > 0) {
            $message = "File deleted successfully.";
        } else {
            $error = "Failed to delete file from database.";
        }
    } else {
        $error = "File not found or access denied.";
    }
}

// ANCHOR: Fetch all uploads with user information
$uploads_query = "SELECT u.*, us.first_name FROM publications u 
                  LEFT JOIN users us ON u.uploaded_by = us.id 
                  ORDER BY u.uploaded_at DESC";
$uploads_result = $conn->query($uploads_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager - Micro OSS App</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <style>
        .file-icon {
            width: 32px;
            height: 32px;
            margin-right: 0.75rem;
        }
        
        .file-row {
            transition: background-color 0.2s;
        }
        
        .file-row:hover {
            background-color: #f8f9fa;
        }
        
        .file-actions {
            white-space: nowrap;
        }
        
        .file-size {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .category-badge {
            font-size: 0.75rem;
        }
        
        /* ANCHOR: Mobile responsive improvements */
        @media (max-width: 768px) {
            .file-actions {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .file-actions .btn {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .file-icon {
                width: 24px;
                height: 24px;
            }
        }
        
        @media (max-width: 576px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            .card-body {
                padding: 1rem 0.75rem;
            }
            
            .file-actions {
                flex-direction: row;
                justify-content: center;
            }
            
            .file-actions .btn {
                flex: 1;
                margin: 0 0.125rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include('includes/nav.php'); ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2><i class="fas fa-folder-open"></i> File Manager</h2>
                        <p class="text-muted">Manage uploaded files and resources</p>
                    </div>
                    <div>
                        <a href="publications.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Back to Resources
                        </a>
                    </div>
                </div>

                <!-- ANCHOR: Messages -->
                <?php if (isset($message)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                <?php endif; ?>

                <!-- ANCHOR: Files Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i> All Files 
                            <span class="badge badge-primary"><?php echo $uploads_result->num_rows; ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($uploads_result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>File</th>
                                        <th>Category</th>
                                        <th>Size</th>
                                        <th>Uploaded By</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($file = $uploads_result->fetch_assoc()): ?>
                                    <tr class="file-row">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-<?php echo $file['file_type'] === 'image' ? 'image' : ($file['file_type'] === 'document' ? 'pdf' : 'archive'); ?> file-icon text-primary"></i>
                                                <div>
                                                    <div class="font-weight-bold"><?php echo htmlspecialchars($file['original_name']); ?></div>
                                                    <?php if ($file['description']): ?>
                                                    <small class="text-muted"><?php echo htmlspecialchars(substr($file['description'], 0, 50)) . (strlen($file['description']) > 50 ? '...' : ''); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary category-badge">
                                                <?php echo ucfirst($file['category']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="file-size">
                                                <?php echo formatFileSize($file['file_size']); ?>
                                            </span>
                                        </td>
                                         <td>
                                             <small class="text-muted">
                                                 <?php echo htmlspecialchars($file['first_name'] ?? 'Unknown'); ?>
                                             </small>
                                         </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('M d, Y H:i', strtotime($file['uploaded_at'])); ?>
                                            </small>
                                        </td>
                                        <td class="file-actions">
                                            <a href="<?php echo htmlspecialchars($file['file_path']); ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               target="_blank" 
                                               title="View File">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <a href="<?php echo htmlspecialchars($file['file_path']); ?>" 
                                               class="btn btn-sm btn-outline-success" 
                                               download="<?php echo htmlspecialchars($file['original_name']); ?>"
                                               title="Download File">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            
                                            <?php if ($file['uploaded_by'] == $user_id): ?>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteFile(<?php echo $file['id']; ?>, '<?php echo htmlspecialchars($file['original_name']); ?>')"
                                                    title="Delete File">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No files uploaded yet</h5>
                            <p class="text-muted">Upload your first file to get started.</p>
                            <a href="publications.php" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Files
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ANCHOR: Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning"></i> Confirm Delete
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the file <strong id="fileName"></strong>?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="file_id" id="deleteFileId">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete File
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function deleteFile(fileId, fileName) {
            document.getElementById('fileName').textContent = fileName;
            document.getElementById('deleteFileId').value = fileId;
            $('#deleteModal').modal('show');
        }
    </script>
</body>
</html>

<?php
// ANCHOR: Helper function to format file sizes
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

$conn->close();
?>
