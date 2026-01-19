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
    <title>File Manager - Micro Online Synthesis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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

        .table-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .demographic-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .demographic-table thead {
            background-color: #8b5cf6;
            color: white;
        }

        .demographic-table th {
            padding: 15px;
            text-align: left;
            /* Aligned left for files */
            font-weight: bold;
            font-size: 1.1rem;
            border: none;
        }

        .demographic-table tbody tr {
            background-color: #ffffff;
            /* White background for files for better contrast */
            transition: background-color 0.3s ease;
        }

        .demographic-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .demographic-table tbody tr:hover {
            background-color: #f3e8ff;
            /* Light purple hover */
        }

        .demographic-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
            color: #1f2937;
            vertical-align: middle;
        }

        .file-icon {
            font-size: 1.5rem;
            margin-right: 10px;
            width: 30px;
            text-align: center;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .btn-gradient:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .back-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <?php include('includes/nav.php'); ?>

    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-folder-open me-3"></i>File Manager
            </h1>
            <p class="page-subtitle">Manage uploaded files and resources</p>
        </div>

        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 text-purple" style="color: #8b5cf6; font-weight: bold;">
                    <i class="fas fa-list me-2"></i>All Files
                    <span class="badge rounded-pill bg-primary ms-2"><?php echo $uploads_result->num_rows; ?></span>
                </h4>
                <div>
                    <a href="publications.php" class="btn btn-outline-primary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Resources
                    </a>
                    <button class="btn btn-success" onclick="exportTable()">
                        <i class="fas fa-file-excel me-2"></i>Export to Excel
                    </button>
                </div>
            </div>

            <!-- ANCHOR: Messages -->
            <?php if (isset($message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- ANCHOR: Files Table -->
            <?php if ($uploads_result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="demographic-table" id="filesTable">
                        <thead>
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
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-<?php echo $file['file_type'] === 'image' ? 'image' : ($file['file_type'] === 'document' ? 'pdf' : 'archive'); ?> file-icon text-primary"></i>
                                            <div>
                                                <div class="fw-bold"><?php echo htmlspecialchars($file['original_name']); ?></div>
                                                <?php if ($file['description']): ?>
                                                    <small class="text-muted"><?php echo htmlspecialchars(substr($file['description'], 0, 50)) . (strlen($file['description']) > 50 ? '...' : ''); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo ucfirst($file['category']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted small">
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
                                    <td>
                                        <div class="btn-group" role="group">
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
                                        </div>
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
                    <a href="publications.php" class="btn btn-gradient text-white">
                        <i class="fas fa-upload me-2"></i>Upload Files
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ANCHOR: Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the file <strong id="fileName"></strong>?</p>
                    <p class="text-danger mb-0"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="file_id" id="deleteFileId">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete File
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function deleteFile(fileId, fileName) {
            document.getElementById('fileName').textContent = fileName;
            document.getElementById('deleteFileId').value = fileId;

            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        function exportTable() {
            const table = document.getElementById('filesTable');
            let csv = [];

            // Get headers (exclude Action column)
            const headers = [];
            table.querySelectorAll('thead th').forEach((th, index) => {
                if (index < 5) { // Only include first 5 columns, exclude Actions
                    headers.push('"' + th.textContent.trim().replace(/"/g, '""') + '"');
                }
            });
            csv.push(headers.join(','));

            // Get data rows
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach((td, index) => {
                    if (index < 5) { // Only include first 5 columns
                        // Get text and replace newlines with separator for cleaner CSV
                        let text = td.innerText.trim().replace(/\n/g, ' - ');
                        row.push('"' + text.replace(/"/g, '""') + '"');
                    }
                });
                csv.push(row.join(','));
            });

            // Create download link with BOM for Excel character encoding support
            const csvContent = '\uFEFF' + csv.join('\n');
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'file_list_' + new Date().toISOString().split('T')[0] + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>

</html>

<?php
// ANCHOR: Helper function to format file sizes
function formatFileSize($bytes)
{
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