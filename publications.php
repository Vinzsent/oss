<?php
session_start(); // Ensure session is started
include('config.php'); // Include database configuration

// ANCHOR: Check user role for admin functionality
$user_role = '';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $role_query = "SELECT role FROM users WHERE id = ?";
    $role_stmt = $conn->prepare($role_query);
    $role_stmt->bind_param("i", $user_id);
    $role_stmt->execute();
    $role_result = $role_stmt->get_result();
    $user_data = $role_result->fetch_assoc();
    if ($user_data && isset($user_data['role'])) {
        $user_role = $user_data['role'];
    }
}

// ANCHOR: Fetch uploaded files from database
$uploads_query = "SELECT * FROM publications WHERE is_active = 1 ORDER BY uploaded_at DESC";
$uploads_result = $conn->query($uploads_query);

// ANCHOR: Check if query failed
if (!$uploads_result) {
    // ANCHOR: If table doesn't exist, create it
    $create_table = "CREATE TABLE IF NOT EXISTS publications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL,
        original_name VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        file_type VARCHAR(100) NOT NULL,
        file_size INT NOT NULL,
        category VARCHAR(100) NOT NULL,
        description TEXT,
        uploaded_by INT NOT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_active BOOLEAN DEFAULT TRUE,
        FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
    )";

    if ($conn->query($create_table)) {
        // ANCHOR: Retry the query after creating table
        $uploads_result = $conn->query($uploads_query);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publications - Micro Online Synthesis System</title>
    <!-- Use FontAwesome 6.4.0 like hazard_vul.php -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Styles from hazard_vul.php */
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            flex: 1;
            width: 100%;
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

        .content-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            height: 100%;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-left: 4px solid #8b5cf6;
        }

        .stats-card h5 {
            color: #8b5cf6;
            font-weight: bold;
            margin-bottom: 15px;
        }

        /* PDF Viewer Styles */
        .image-container {
            height: 80vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .image-container embed {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 8px;
        }

        .zoom-controls {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            z-index: 1000;
        }

        .zoom-controls button {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.2s;
        }

        .zoom-controls button:hover {
            transform: scale(1.1);
        }

        /* File List Styles */
        .file-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .file-item {
            border-bottom: 1px solid #f3f4f6;
            padding: 12px 0;
            transition: all 0.2s ease;
        }

        .file-item:last-child {
            border-bottom: none;
        }

        .file-item:hover {
            background-color: #f9fafb;
            padding-left: 5px;
        }

        .file-icon {
            width: 24px;
            height: 24px;
            margin-right: 10px;
            color: #6b7280;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .btn-gradient:hover {
            color: white;
            opacity: 0.9;
        }

        /* Scrollbar styling */
        .file-list::-webkit-scrollbar {
            width: 6px;
        }

        .file-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .file-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        /* Modal Styles */
        .modal-header {
            background-color: #8b5cf6;
            color: white;
        }

        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>
</head>

<body>

    <?php include('includes/nav.php'); ?>

    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-book me-3"></i>Reference Materials
            </h1>
            <p class="page-subtitle">Publications, Reports, and Policy Briefs</p>
        </div>

        <div class="row">
            <!-- Main Viewer Area -->
            <div class="col-lg-8 mb-4">
                <div class="content-container p-0 overflow-hidden">
                    <div class="image-container">
                        <div id="placeholder-message" class="text-center text-muted p-5">
                            <i class="fas fa-file-pdf fa-4x mb-3 text-light-gray opacity-50"></i>
                            <h5>Select a resource to view</h5>
                            <p>Choose a document from the sidebar to display it here.</p>
                        </div>
                        <embed id="resource-file" src="" type="application/pdf" style="display:none;" />

                        <div class="zoom-controls" style="display:none;">
                            <button type="button" class="btn btn-light" onclick="zoomIn()" title="Zoom In">
                                <i class="fas fa-plus text-primary"></i>
                            </button>
                            <button type="button" class="btn btn-light" onclick="zoomOut()" title="Zoom Out">
                                <i class="fas fa-minus text-primary"></i>
                            </button>
                            <button type="button" class="btn btn-light" onclick="resetZoom()" title="Reset Zoom">
                                <i class="fas fa-expand text-primary"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Admin Actions -->
                <?php if ($user_role === 'admin'): ?>
                    <div class="stats-card mb-4">
                        <h5><i class="fas fa-cog me-2"></i>Administration</h5>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                <i class="fas fa-upload me-2"></i>Upload New Resource
                            </button>
                            <a href="file_manager.php" class="btn btn-outline-primary">
                                <i class="fas fa-folder-open me-2"></i>Manage Files
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Filters -->
                <div class="stats-card mb-4">
                    <h5><i class="fas fa-search me-2"></i>Find Resources</h5>
                    <form>
                        <div class="mb-3">
                            <label for="province" class="form-label text-muted small fw-bold">PROVINCE</label>
                            <select class="form-select form-select-sm" id="province">
                                <option value="">-- Select Province --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="municipality" class="form-label text-muted small fw-bold">MUNICIPALITY/CITY</label>
                            <select class="form-select form-select-sm" id="municipality" disabled>
                                <option value="">-- Select Municipality/City --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="barangay" class="form-label text-muted small fw-bold">BARANGAY</label>
                            <select class="form-select form-select-sm" id="barangay" disabled>
                                <option value="">-- Select Barangay --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="resourceSelect" class="form-label text-muted small fw-bold">AVAILABLE RESOURCES</label>
                            <select class="form-select" id="resourceSelect" name="resource" onchange="updateResource()">
                                <option value="">-- Select Document --</option>
                                <optgroup label="Standard Documents">
                                    <option value="uploads/policybrief.pdf">Policy Briefs</option>
                                    <option value="uploads/media.pdf">Media Releases</option>
                                    <option value="uploads/infographics.pdf">Infographics</option>
                                    <option value="uploads/factsheet.pdf">Fact Sheets</option>
                                </optgroup>
                                <?php if ($uploads_result && $uploads_result->num_rows > 0): ?>
                                    <optgroup label="Uploaded Files">
                                        <?php
                                        $uploads_result->data_seek(0);
                                        while ($upload = $uploads_result->fetch_assoc()):
                                            if ($upload && is_array($upload) && isset($upload['file_path'])):
                                        ?>
                                                <option value="<?php echo htmlspecialchars($upload['file_path']); ?>">
                                                    <?php echo htmlspecialchars($upload['original_name'] ?? 'Unknown File'); ?>
                                                </option>
                                        <?php
                                            endif;
                                        endwhile; ?>
                                    </optgroup>
                                <?php endif; ?>
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Recent Uploads -->
                <div class="stats-card">
                    <h5><i class="fas fa-history me-2"></i>Recent Uploads</h5>
                    <div class="file-list">
                        <?php
                        if ($uploads_result && $uploads_result->num_rows > 0):
                            $uploads_result->data_seek(0);
                            $count = 0;
                            while (($upload = $uploads_result->fetch_assoc()) && $count < 8):
                                $count++;
                                if ($upload && is_array($upload)):
                                    $iconClass = 'fa-file';
                                    if (isset($upload['file_type'])) {
                                        if (strpos($upload['file_type'], 'image') !== false) $iconClass = 'fa-file-image';
                                        elseif (strpos($upload['file_type'], 'pdf') !== false) $iconClass = 'fa-file-pdf';
                                        elseif (strpos($upload['file_type'], 'word') !== false) $iconClass = 'fa-file-word';
                                    }
                        ?>
                                    <div class="file-item d-flex align-items-center cursor-pointer" onclick="selectFile('<?php echo htmlspecialchars($upload['file_path']); ?>')">
                                        <i class="fas <?php echo $iconClass; ?> file-icon text-muted"></i>
                                        <div class="overflow-hidden">
                                            <div class="text-truncate fw-bold text-dark small"><?php echo htmlspecialchars($upload['original_name'] ?? 'Unknown File'); ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                <?php echo ucfirst($upload['category'] ?? 'General'); ?> •
                                                <?php echo isset($upload['uploaded_at']) ? date('M d, Y', strtotime($upload['uploaded_at'])) : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                endif;
                            endwhile;
                        else:
                            ?>
                            <p class="text-muted small text-center my-3">No files uploaded yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <?php if ($user_role === 'admin'): ?>
        <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">
                            <i class="fas fa-upload me-2"></i>Upload New Resource
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="uploadForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="fileInput" class="form-label">Select File</label>
                                <input type="file" class="form-control" id="fileInput" name="file" required>
                                <div class="form-text">
                                    Allowed types: PDF, DOC, Images. Max size: 10MB.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="categorySelect" class="form-label">Category</label>
                                <select class="form-select" id="categorySelect" name="category" required>
                                    <option value="">-- Select Category --</option>
                                    <option value="policy">Policy Briefs</option>
                                    <option value="media">Media Releases</option>
                                    <option value="infographics">Infographics</option>
                                    <option value="factsheet">Fact Sheets</option>
                                    <option value="reports">Reports</option>
                                    <option value="guidelines">Guidelines</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="descriptionInput" class="form-label">Description (Optional)</label>
                                <textarea class="form-control" id="descriptionInput" name="description" rows="3" placeholder="Brief description..."></textarea>
                            </div>

                            <div class="upload-progress mt-3" style="display:none;">
                                <div class="progress mb-2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-purple" role="progressbar" style="width: 0%; background-color: #8b5cf6;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Uploading file...</small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="uploadFile()" style="background-color: #8b5cf6; border-color: #8b5cf6;">
                            <i class="fas fa-upload me-1"></i>Upload File
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <footer class="bg-white text-center py-4 mt-auto border-top">
        <div class="container">
            <p class="mb-0 text-dark">&copy; 2024 Flood Resilience App. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function updateResource() {
            let resourceSelect = document.getElementById("resourceSelect");
            let selectedFile = resourceSelect.value;
            const embed = document.getElementById("resource-file");
            const placeholder = document.getElementById("placeholder-message");
            const zoomControls = document.querySelector(".zoom-controls");

            if (selectedFile) {
                embed.src = selectedFile;
                embed.style.display = 'block';
                placeholder.style.display = 'none';

                // Show zoom controls only for PDF files
                if (selectedFile.toLowerCase().includes('.pdf')) {
                    zoomControls.style.display = 'flex';
                } else {
                    zoomControls.style.display = 'none';
                }
            } else {
                embed.style.display = 'none';
                placeholder.style.display = 'block';
                zoomControls.style.display = 'none';
            }
        }

        function selectFile(filePath) {
            let resourceSelect = document.getElementById("resourceSelect");
            resourceSelect.value = filePath;
            // If the value exists in the dropdown (it should), update; if not, we can force update or add it
            updateResource();
        }

        // Zoom functionality for PDF viewing
        let currentZoom = 1;
        const minZoom = 0.5;
        const maxZoom = 3;

        function zoomIn() {
            if (currentZoom < maxZoom) {
                currentZoom += 0.25;
                applyZoom();
            }
        }

        function zoomOut() {
            if (currentZoom > minZoom) {
                currentZoom -= 0.25;
                applyZoom();
            }
        }

        function resetZoom() {
            currentZoom = 1;
            applyZoom();
        }

        function applyZoom() {
            const embed = document.getElementById("resource-file");
            if (embed && embed.style.display !== 'none') {
                embed.style.transform = `scale(${currentZoom})`;
                embed.style.transformOrigin = 'top center';
            }
        }

        const BASE_URL = "https://psgc.gitlab.io/api";
        const provinceSelect = document.getElementById("province");
        const municipalitySelect = document.getElementById("municipality");
        const barangaySelect = document.getElementById("barangay");

        function fetchProvinces() {
            fetch(`${BASE_URL}/provinces/`)
                .then(res => res.json())
                .then(data => {
                    // Sort appropriately
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    data.forEach(province => {
                        provinceSelect.appendChild(new Option(province.name, province.code));
                    });
                })
                .catch(err => console.error("Error fetching provinces:", err));
        }

        function fetchMunicipalities(provinceCode) {
            municipalitySelect.innerHTML = '<option value="">-- Select Municipality/City --</option>';
            barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
            municipalitySelect.disabled = true;
            barangaySelect.disabled = true;

            if (!provinceCode) return;

            fetch(`${BASE_URL}/provinces/${provinceCode}/cities-municipalities/`)
                .then(res => res.json())
                .then(data => {
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    data.forEach(municipality => {
                        municipalitySelect.appendChild(new Option(municipality.name, municipality.code));
                    });
                    municipalitySelect.disabled = false;
                });
        }

        function fetchBarangays(municipalityCode) {
            barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
            barangaySelect.disabled = true;

            if (!municipalityCode) return;

            fetch(`${BASE_URL}/cities-municipalities/${municipalityCode}/barangays/`)
                .then(res => res.json())
                .then(data => {
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    data.forEach(barangay => {
                        barangaySelect.appendChild(new Option(barangay.name, barangay.code));
                    });
                    barangaySelect.disabled = false;
                });
        }

        provinceSelect.addEventListener("change", () => fetchMunicipalities(provinceSelect.value));
        municipalitySelect.addEventListener("change", () => fetchBarangays(municipalitySelect.value));

        fetchProvinces();

        // File upload functionality
        function uploadFile() {
            const fileInput = document.getElementById('fileInput');
            const categorySelect = document.getElementById('categorySelect');
            const descriptionInput = document.getElementById('descriptionInput');
            const uploadProgress = document.querySelector('.upload-progress');
            const progressBar = document.querySelector('.progress-bar');

            // Validate form
            if (!fileInput.files[0]) {
                alert('Please select a file to upload.');
                return;
            }

            if (!categorySelect.value) {
                alert('Please select a category.');
                return;
            }

            const fileSize = fileInput.files[0].size;
            const maxSize = 10 * 1024 * 1024; // 10MB
            if (fileSize > maxSize) {
                alert('File size exceeds 10MB limit.');
                return;
            }

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('category', categorySelect.value);
            formData.append('description', descriptionInput.value);

            uploadProgress.style.display = 'block';
            progressBar.style.width = '0%';

            // Simulate progress for UX
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += Math.random() * 20;
                if (progress > 85) progress = 85;
                progressBar.style.width = progress + '%';
            }, 300);

            fetch('upload_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Server responded with status: ' + response.status);
                    return response.text();
                })
                .then(text => {
                    clearInterval(progressInterval);
                    progressBar.style.width = '100%';

                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Invalid JSON response:', text);
                        throw new Error('Invalid server response');
                    }
                })
                .then(data => {
                    if (data.success) {
                        alert('File uploaded successfully!');
                        document.getElementById('uploadForm').reset();
                        uploadProgress.style.display = 'none';

                        // Use Bootstrap 5 modal instance to hide if possible, or fallback methods
                        const modalEl = document.getElementById('uploadModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        else {
                            // Fallback for rough hiding if instance retrieval fails (rare in BS5 simple usage)
                            modalEl.classList.remove('show');
                            modalEl.style.display = 'none';
                            document.body.classList.remove('modal-open');
                            const backdrop = document.querySelector('.modal-backdrop');
                            if (backdrop) backdrop.remove();
                        }

                        location.reload();
                    } else {
                        alert('Upload failed: ' + (data.message || 'Unknown error'));
                        uploadProgress.style.display = 'none';
                    }
                })
                .catch(error => {
                    clearInterval(progressInterval);
                    console.error('Upload error:', error);
                    alert('Upload failed: ' + error.message);
                    uploadProgress.style.display = 'none';
                });
        }
    </script>
</body>

</html>