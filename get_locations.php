<?php
header('Content-Type: application/json');
include('config.php');

$action = $_GET['action'] ?? '';

if ($action === 'barangays') {
    // Get barangays from the barangays table
    $query = "SELECT name FROM barangays ORDER BY name";
    
    $result = $conn->query($query);
    $barangays = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $barangays[] = $row['name'];
        }
    }
    
    echo json_encode($barangays);
    
} elseif ($action === 'sitios' && isset($_GET['barangay'])) {
    $barangay = $_GET['barangay'];
    
    // Get puroks/sitios for the selected barangay using JOIN
    $query = "
        SELECT ps.name 
        FROM puroks_sitios ps 
        JOIN barangays b ON ps.barangay_id = b.id 
        WHERE b.name = ? 
        ORDER BY ps.name ASC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $barangay);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sitios = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sitios[] = $row['name'];
        }
    }
    
    echo json_encode($sitios);
    $stmt->close();
    
} else {
    echo json_encode(['error' => 'Invalid action or missing parameters']);
}

$conn->close();
?>
