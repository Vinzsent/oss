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
    <title>Micro OSS App</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        /* ANCHOR: Base styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .image-container {
            height: 80vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .image-container embed {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 0.5rem;
        }
        
        .zoom-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }
        
        .zoom-controls button {
            margin: 5px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* ANCHOR: Upload modal styles */
        .upload-progress {
            display: none;
            margin-top: 10px;
        }
        
        .file-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .file-item {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background-color: #f8f9fa;
            transition: all 0.2s ease;
        }
        
        .file-item:hover {
            background-color: #e9ecef;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .file-icon {
            width: 24px;
            height: 24px;
            margin-right: 0.5rem;
        }
        
        .upload-btn {
            margin-bottom: 1rem;
        }
        
        /* ANCHOR: Responsive design improvements */
        @media (max-width: 768px) {
            .image-container {
                height: 400px;
                margin-bottom: 1rem;
            }
            
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            .zoom-controls {
                top: 5px;
                right: 5px;
            }
            
            .zoom-controls button {
                width: 35px;
                height: 35px;
                margin: 3px;
            }
            
            /* ANCHOR: Stack columns on mobile */
            .col-md-8, .col-md-4 {
                margin-bottom: 1rem;
            }
            
            /* ANCHOR: Full-width buttons on mobile */
            .btn-block {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            /* ANCHOR: Adjust form controls */
            .form-control {
                font-size: 1rem; /* Prevents zoom on iOS */
            }
            
            /* ANCHOR: Better mobile spacing */
            .card {
                margin-bottom: 1rem;
            }
            
            .alert {
                padding: 0.75rem;
                margin-bottom: 1rem;
            }
            
            h2 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }
            
            h6 {
                font-size: 1rem;
            }
            
            .mt-5 {
                margin-top: 2rem !important;
            }
        }
        
        @media (max-width: 576px) {
            .image-container {
                height: 350px;
            }
            
            .zoom-controls {
                display: none; /* Hide zoom controls on very small screens */
            }
            
            /* ANCHOR: Adjust button sizes */
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
            
            /* ANCHOR: Better file list on mobile */
            .file-list {
                max-height: 250px;
            }
            
            .file-item {
                padding: 0.5rem;
            }
            
            /* ANCHOR: Modal improvements */
            .modal-dialog {
                margin: 0.5rem;
            }
            
            .modal-body {
                padding: 1rem 0.75rem;
            }
            
            h2 {
                font-size: 1.35rem;
            }
        }
        
        /* ANCHOR: Tablet specific styles */
        @media (min-width: 769px) and (max-width: 1024px) {
            .image-container {
                height: 70vh;
            }
            
            .col-md-8 {
                margin-bottom: 1rem;
            }
        }
        
        /* ANCHOR: High DPI display improvements */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .file-icon, .zoom-controls button {
                image-rendering: -webkit-optimize-contrast;
                image-rendering: crisp-edges;
            }
        }
        
        /* ANCHOR: Accessibility improvements */
        @media (prefers-reduced-motion: reduce) {
            .file-item {
                transition: none;
            }
            
            .file-item:hover {
                transform: none;
            }
        }
        
        /* ANCHOR: Dark mode support */
        @media (prefers-color-scheme: dark) {
            .file-item {
                background-color: #343a40;
                border-color: #495057;
                color: #ffffff;
            }
            
            .file-item:hover {
                background-color: #495057;
            }
        }
        
        /* ANCHOR: Print styles */
        @media print {
            .zoom-controls, .upload-btn, .btn {
                display: none !important;
            }
            
            .image-container {
                height: auto;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<?php include('includes/nav.php'); ?>

<div class="container mt-5">
    <div class="row">
        <!-- ANCHOR: Main content area - responsive column sizing -->
        <div class="col-lg-8 col-md-12 mb-4">
            <h2><i class="fas fa-book"></i> Resources</h2>
            <div class="image-container">
                <embed id="resource-file" src="" type="application/pdf" />
                <!-- ANCHOR: Zoom controls for PDF viewing -->
                <div class="zoom-controls">
                    <button type="button" class="btn btn-light btn-sm" onclick="zoomIn()" title="Zoom In">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-light btn-sm" onclick="zoomOut()" title="Zoom Out">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-light btn-sm" onclick="resetZoom()" title="Reset Zoom">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- ANCHOR: Sidebar - responsive column sizing -->
        <div class="col-lg-4 col-md-12">
            <!-- ANCHOR: Admin upload section -->
            <?php if ($user_role === 'admin'): ?>
            <div class="upload-btn">
                <button type="button" class="btn btn-success btn-block mb-2" data-toggle="modal" data-target="#uploadModal">
                    <i class="fas fa-upload"></i> Upload New Resource
                </button>
                <a href="file_manager.php" class="btn btn-info btn-block">
                    <i class="fas fa-folder-open"></i> Manage Files
                </a>
            </div>
            <?php endif; ?>
            
            <div class="alert alert-info">
                <strong>Search for Resources:</strong>
            </div>
            <form>
                <div class="form-group">
                    <label for="province">Province:</label>
                    <select class="form-control" id="province">
                        <option value="">-- Select Province --</option>
                    </select>

                    <label for="municipality">Municipality/City:</label>
                    <select class="form-control" id="municipality" disabled>
                        <option value="">-- Select Municipality/City --</option>
                    </select>

                    <label for="barangay">Barangay:</label>
                    <select class="form-control" id="barangay" disabled>
                        <option value="">-- Select Barangay --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="resourceSelect"><strong>Resources</strong></label>
                    <select class="form-control" id="resourceSelect" name="resource" onchange="updateResource()">
                        <option value="">-- Select --</option>
                        <!-- ANCHOR: Static options -->
                        <option value="uploads/policybrief.pdf">Policy Briefs</option>
                        <option value="uploads/media.pdf">Media Releases</option>
                        <option value="uploads/infographics.pdf">Infographics</option>
                        <option value="uploads/factsheet.pdf">Fact Sheets</option>
                        <!-- ANCHOR: Dynamic options from database -->
                        <?php if ($uploads_result && $uploads_result->num_rows > 0): ?>
                            <?php 
                            $uploads_result->data_seek(0); // Reset result pointer
                            while ($upload = $uploads_result->fetch_assoc()): 
                                if ($upload && is_array($upload) && isset($upload['file_path'])):
                            ?>
                                <option value="<?php echo htmlspecialchars($upload['file_path']); ?>">
                                    <?php echo htmlspecialchars($upload['original_name'] ?? 'Unknown File'); ?> 
                                    (<?php echo ucfirst($upload['category'] ?? 'Unknown'); ?>)
                                </option>
                            <?php 
                                endif;
                            endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </form>
            
            <!-- ANCHOR: Recent uploads section -->
            <?php if ($uploads_result && $uploads_result->num_rows > 0): ?>
            <div class="mt-4">
                <h6><i class="fas fa-clock"></i> Recent Uploads</h6>
                <div class="file-list">
                    <?php 
                    $uploads_result->data_seek(0); // Reset result pointer
                    $count = 0;
                    while (($upload = $uploads_result->fetch_assoc()) && $count < 5): 
                        $count++;
                        // ANCHOR: Validate upload data before using it
                        if ($upload && is_array($upload)):
                    ?>
                    <div class="file-item">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-<?php echo isset($upload['file_type']) && $upload['file_type'] === 'image' ? 'image' : (isset($upload['file_type']) && $upload['file_type'] === 'document' ? 'pdf' : 'archive'); ?> file-icon"></i>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold"><?php echo htmlspecialchars($upload['original_name'] ?? 'Unknown File'); ?></div>
                                <small class="text-muted">
                                    <?php echo ucfirst($upload['category'] ?? 'Unknown'); ?> • 
                                    <?php echo isset($upload['uploaded_at']) ? date('M d, Y', strtotime($upload['uploaded_at'])) : 'Unknown Date'; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endif;
                    endwhile; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ANCHOR: Upload Modal -->
<?php if ($user_role === 'admin'): ?>
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">
                    <i class="fas fa-upload"></i> Upload New Resource
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="fileInput"><strong>Select File</strong></label>
                        <input type="file" class="form-control-file" id="fileInput" name="file" required>
                        <small class="form-text text-muted">
                            Allowed types: Images (JPG, PNG, GIF, WebP), Documents (PDF, DOC, DOCX, TXT), Archives (ZIP, RAR, 7Z)
                            <br>Maximum file size: 10MB
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="categorySelect"><strong>Category</strong></label>
                        <select class="form-control" id="categorySelect" name="category" required>
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
                    
                    <div class="form-group">
                        <label for="descriptionInput"><strong>Description (Optional)</strong></label>
                        <textarea class="form-control" id="descriptionInput" name="description" rows="3" 
                                  placeholder="Brief description of the file..."></textarea>
                    </div>
                    
                    <div class="upload-progress">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted">Uploading file...</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="uploadFile()">
                    <i class="fas fa-upload"></i> Upload File
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<footer class="bg-dark text-white mt-5 p-4 text-center">
    &copy; 2024 Flood Resilience App. All Rights Reserved.
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    function updateResource() {
        let resourceSelect = document.getElementById("resourceSelect");
        let selectedFile = resourceSelect.value;
        document.getElementById("resource-file").src = selectedFile ? selectedFile : "";
        
        // ANCHOR: Show zoom controls only for PDF files
        const embed = document.getElementById("resource-file");
        const zoomControls = document.querySelector(".zoom-controls");
        if (selectedFile && selectedFile.toLowerCase().includes('.pdf')) {
            zoomControls.style.display = 'flex';
        } else {
            zoomControls.style.display = 'none';
        }
    }

    // ANCHOR: Zoom functionality for PDF viewing
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
        if (embed && embed.src) {
            embed.style.transform = `scale(${currentZoom})`;
            embed.style.transformOrigin = 'top left';
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

        fetch(`${BASE_URL}/provinces/${provinceCode}/cities-municipalities/`)
            .then(res => res.json())
            .then(data => {
                data.forEach(municipality => {
                    municipalitySelect.appendChild(new Option(municipality.name, municipality.code));
                });
                municipalitySelect.disabled = false;
            });
    }

    function fetchBarangays(municipalityCode) {
        barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
        barangaySelect.disabled = true;

        fetch(`${BASE_URL}/cities-municipalities/${municipalityCode}/barangays/`)
            .then(res => res.json())
            .then(data => {
                data.forEach(barangay => {
                    barangaySelect.appendChild(new Option(barangay.name, barangay.code));
                });
                barangaySelect.disabled = false;
            });
    }

    provinceSelect.addEventListener("change", () => fetchMunicipalities(provinceSelect.value));
    municipalitySelect.addEventListener("change", () => fetchBarangays(municipalitySelect.value));

    fetchProvinces();

    // ANCHOR: File upload functionality
    function uploadFile() {
        const fileInput = document.getElementById('fileInput');
        const categorySelect = document.getElementById('categorySelect');
        const descriptionInput = document.getElementById('descriptionInput');
        const uploadProgress = document.querySelector('.upload-progress');
        const progressBar = document.querySelector('.progress-bar');
        
        // ANCHOR: Validate form
        if (!fileInput.files[0]) {
            alert('Please select a file to upload.');
            return;
        }
        
        if (!categorySelect.value) {
            alert('Please select a category.');
            return;
        }
        
        // ANCHOR: Check file size (10MB limit)
        const fileSize = fileInput.files[0].size;
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (fileSize > maxSize) {
            alert('File size exceeds 10MB limit.');
            return;
        }
        
        // ANCHOR: Create form data
        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('category', categorySelect.value);
        formData.append('description', descriptionInput.value);
        
        // ANCHOR: Show progress bar
        uploadProgress.style.display = 'block';
        progressBar.style.width = '0%';
        
        // ANCHOR: Upload file using fetch API
        fetch('upload_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // ANCHOR: Check if response is ok
            if (!response.ok) {
                throw new Error('Server responded with status: ' + response.status);
            }
            return response.text(); // Get as text first to handle malformed JSON
        })
        .then(text => {
            // ANCHOR: Try to parse JSON
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON response:', text);
                throw new Error('Invalid server response');
            }
        })
        .then(data => {
            if (data.success) {
                // ANCHOR: Show success message
                alert('File uploaded successfully!');
                
                // ANCHOR: Reset form
                document.getElementById('uploadForm').reset();
                uploadProgress.style.display = 'none';
                
                // ANCHOR: Close modal
                $('#uploadModal').modal('hide');
                
                // ANCHOR: Reload page to show new file
                location.reload();
            } else {
                alert('Upload failed: ' + (data.message || 'Unknown error'));
                uploadProgress.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            alert('Upload failed: ' + error.message);
            uploadProgress.style.display = 'none';
        });
        
        // ANCHOR: Simulate progress bar
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 30;
            if (progress > 90) progress = 90;
            progressBar.style.width = progress + '%';
            
            if (progress >= 90) {
                clearInterval(progressInterval);
            }
        }, 200);
    }
</script>
</body>
</html>
