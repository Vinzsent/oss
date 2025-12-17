<?php
session_start();
include('config.php');
include('includes/nav.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Socio Demographic Data - Micro Online Synthesis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            max-width: 1400px;
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
        
        .barangay-info {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 4px solid #8b5cf6;
        }
        
        .barangay-info h4 {
            color: #8b5cf6;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .content-wrapper {
            display: flex;
            gap: 20px;
        }
        
        .table-section {
            flex: 1;
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .filters-section {
            width: 300px;
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .socio-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .socio-table thead {
            background-color: #8b5cf6;
            color: white;
        }
        
        .socio-table th {
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 1rem;
            border: none;
            white-space: nowrap;
        }
        
        .socio-table tbody tr {
            background-color: white;
            transition: background-color 0.3s ease;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .socio-table tbody tr:hover {
            background-color: #f3f4f6;
            cursor: pointer;
        }
        
        .socio-table td {
            padding: 12px 15px;
            text-align: center;
            border: none;
            font-weight: 500;
            color: #1f2937;
        }
        
        .socio-table td:first-child {
            text-align: left;
            font-weight: 600;
        }
        
        .risk-low {
            background-color: #fef3c7 !important;
            color: #92400e;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            min-width: 80px;
        }
        
        .risk-medium {
            background-color: #fce7f3 !important;
            color: #9f1239;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            min-width: 80px;
        }
        
        .risk-high {
            background-color: #dcfce7 !important;
            color: #166534;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            min-width: 80px;
        }
        
        .filter-card {
            margin-bottom: 25px;
        }
        
        .filter-card h5 {
            color: #8b5cf6;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .filter-select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e5e7eb;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }
        
        .filter-select:focus {
            outline: none;
            border-color: #8b5cf6;
        }
        
        .legend-card {
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .legend-card h6 {
            color: #8b5cf6;
            font-weight: bold;
            margin-bottom: 12px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            margin-right: 10px;
            border: 1px solid #e5e7eb;
        }
        
        .legend-text {
            font-size: 0.9rem;
            color: #4b5563;
        }
        
        .pagination-section {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .pagination-info {
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .pagination-controls {
            display: flex;
            gap: 5px;
        }
        
        .page-btn {
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            background: white;
            color: #6b7280;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .page-btn:hover {
            background: #f3f4f6;
            border-color: #8b5cf6;
            color: #8b5cf6;
        }
        
        .page-btn.active {
            background: #8b5cf6;
            color: white;
            border-color: #8b5cf6;
        }
        
        .total-row {
            background-color: #f8fafc !important;
            font-weight: bold;
            color: #8b5cf6;
        }
        
        .total-row td {
            font-weight: bold !important;
            background-color: #f8fafc !important;
        }
        
        @media (max-width: 1024px) {
            .content-wrapper {
                flex-direction: column;
            }
            
            .filters-section {
                width: 100%;
                order: -1;
            }
        }
        
        @media (max-width: 768px) {
            .main-container {
                padding: 10px;
            }
            
            .page-title {
                font-size: 1.8rem;
            }
            
            .table-section,
            .filters-section {
                padding: 15px;
            }
            
            .socio-table {
                font-size: 0.85rem;
            }
            
            .socio-table th,
            .socio-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-users me-3"></i>Socio Demographic Data
            </h1>
            <p class="page-subtitle">Community Risk Assessment Dashboard</p>
        </div>
        
        <div class="barangay-info">
            <h4><i class="fas fa-map-marker-alt me-2"></i>Barangay: Lizada</h4>
            <p class="mb-0 text-muted">Comprehensive demographic and risk assessment data for community planning and disaster preparedness.</p>
        </div>
        
        <div class="content-wrapper">
            <div class="table-section">
                <h4 class="mb-4">
                    <i class="fas fa-table me-2 text-purple"></i>
                    Demographic Overview
                </h4>
                
                <table class="socio-table">
                    <thead>
                        <tr>
                            <th>Sitio Purok</th>
                            <th>Total Number of Families</th>
                            <th>Total Number of Persons</th>
                            <th>Risk Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Babisa</td>
                            <td>45</td>
                            <td>186</td>
                            <td><span class="risk-low">Low</span></td>
                        </tr>
                        <tr>
                            <td>Camarin</td>
                            <td>38</td>
                            <td>142</td>
                            <td><span class="risk-medium">Medium</span></td>
                        </tr>
                        <tr>
                            <td>Culosa</td>
                            <td>52</td>
                            <td>198</td>
                            <td><span class="risk-high">High</span></td>
                        </tr>
                        <tr>
                            <td>Curvada</td>
                            <td>29</td>
                            <td>87</td>
                            <td><span class="risk-low">Low</span></td>
                        </tr>
                        <tr>
                            <td>Dacudao</td>
                            <td>41</td>
                            <td>156</td>
                            <td><span class="risk-medium">Medium</span></td>
                        </tr>
                        <tr>
                            <td>Doña Rosa</td>
                            <td>35</td>
                            <td>128</td>
                            <td><span class="risk-low">Low</span></td>
                        </tr>
                        <tr>
                            <td>Fisherman</td>
                            <td>48</td>
                            <td>203</td>
                            <td><span class="risk-high">High</span></td>
                        </tr>
                        <tr>
                            <td>Glabaca</td>
                            <td>22</td>
                            <td>76</td>
                            <td><span class="risk-low">Low</span></td>
                        </tr>
                        <tr>
                            <td>Gutierez</td>
                            <td>31</td>
                            <td>114</td>
                            <td><span class="risk-medium">Medium</span></td>
                        </tr>
                        <tr>
                            <td>JV Ferriols</td>
                            <td>27</td>
                            <td>95</td>
                            <td><span class="risk-low">Low</span></td>
                        </tr>
                        <tr>
                            <td>Kasama</td>
                            <td>33</td>
                            <td>121</td>
                            <td><span class="risk-medium">Medium</span></td>
                        </tr>
                        <tr>
                            <td>Lawis</td>
                            <td>44</td>
                            <td>167</td>
                            <td><span class="risk-high">High</span></td>
                        </tr>
                        <tr>
                            <td>Lizada Beach</td>
                            <td>36</td>
                            <td>134</td>
                            <td><span class="risk-medium">Medium</span></td>
                        </tr>
                        <tr>
                            <td>Lizada Proper</td>
                            <td>58</td>
                            <td>245</td>
                            <td><span class="risk-high">High</span></td>
                        </tr>
                        <tr>
                            <td>Maltabis</td>
                            <td>25</td>
                            <td>89</td>
                            <td><span class="risk-low">Low</span></td>
                        </tr>
                        <tr class="total-row">
                            <td>TOTAL</td>
                            <td>564</td>
                            <td>2,141</td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="pagination-section">
                    <div class="pagination-info">
                        Showing 1 to 15 of 15 entries
                    </div>
                    <div class="pagination-controls">
                        <button class="page-btn">Previous</button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn">Next</button>
                    </div>
                </div>
            </div>
            
            <div class="filters-section">
                <div class="filter-card">
                    <h5><i class="fas fa-filter me-2"></i>Search Filters</h5>
                    
                    <label for="barangayFilter" class="form-label">Barangay</label>
                    <select id="barangayFilter" class="filter-select">
                        <option value="">All Barangays</option>
                        <option value="lizada" selected>Lizada</option>
                        <option value="daliao">Daliao</option>
                    </select>
                    
                    <label for="purokFilter" class="form-label">Purok/Sitio</label>
                    <select id="purokFilter" class="filter-select">
                        <option value="">All Purok/Sitio</option>
                        <option value="babisa">Babisa</option>
                        <option value="camarin">Camarin</option>
                        <option value="culosa">Culosa</option>
                        <option value="curvada">Curvada</option>
                        <option value="dacudao">Dacudao</option>
                        <option value="dona-rosa">Doña Rosa</option>
                        <option value="fisherman">Fisherman</option>
                        <option value="glabaca">Glabaca</option>
                        <option value="gutierez">Gutierez</option>
                        <option value="jv-ferriols">JV Ferriols</option>
                        <option value="kasama">Kasama</option>
                        <option value="lawis">Lawis</option>
                        <option value="lizada-beach">Lizada Beach</option>
                        <option value="lizada-proper">Lizada Proper</option>
                        <option value="maltabis">Maltabis</option>
                    </select>
                    
                    <button class="btn btn-primary w-100 mt-3" style="background: #8b5cf6; border-color: #8b5cf6;">
                        <i class="fas fa-search me-2"></i>Apply Filters
                    </button>
                </div>
                
                <div class="legend-card">
                    <h6><i class="fas fa-info-circle me-2"></i>Risk Level Legend</h6>
                    
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #fef3c7;"></div>
                        <span class="legend-text">Low Risk - Minimal vulnerability</span>
                    </div>
                    
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #fce7f3;"></div>
                        <span class="legend-text">Medium Risk - Moderate vulnerability</span>
                    </div>
                    
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #dcfce7;"></div>
                        <span class="legend-text">High Risk - High vulnerability</span>
                    </div>
                </div>
                
                <div class="filter-card mt-4">
                    <h5><i class="fas fa-download me-2"></i>Export Options</h5>
                    <button class="btn btn-outline-primary w-100 mb-2" style="border-color: #8b5cf6; color: #8b5cf6;">
                        <i class="fas fa-file-csv me-2"></i>Export as CSV
                    </button>
                    <button class="btn btn-outline-primary w-100" style="border-color: #8b5cf6; color: #8b5cf6;">
                        <i class="fas fa-file-pdf me-2"></i>Export as PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter functionality
        document.getElementById('barangayFilter').addEventListener('change', function() {
            // Implement barangay filtering logic
            console.log('Barangay filter:', this.value);
        });
        
        document.getElementById('purokFilter').addEventListener('change', function() {
            // Implement purok filtering logic
            console.log('Purok filter:', this.value);
        });
        
        // Row click functionality
        document.querySelectorAll('.socio-table tbody tr:not(.total-row)').forEach(row => {
            row.addEventListener('click', function() {
                // Highlight selected row
                document.querySelectorAll('.socio-table tbody tr:not(.total-row)').forEach(r => {
                    r.style.backgroundColor = '';
                });
                this.style.backgroundColor = '#ede9fe';
            });
        });
        
        // Export functionality
        document.querySelectorAll('.btn-outline-primary').forEach(btn => {
            btn.addEventListener('click', function() {
                const exportType = this.textContent.includes('CSV') ? 'CSV' : 'PDF';
                alert(`Exporting as ${exportType}... (Functionality to be implemented)`);
            });
        });
        
        // Apply filters button
        document.querySelector('.btn-primary').addEventListener('click', function() {
            const barangay = document.getElementById('barangayFilter').value;
            const purok = document.getElementById('purokFilter').value;
            
            console.log('Applying filters:', { barangay, purok });
            
            // Implement filter logic here
            if (barangay || purok) {
                // Filter the table based on selected values
                alert('Filters applied successfully!');
            } else {
                alert('Please select at least one filter option.');
            }
        });
    </script>
</body>
</html>
