<?php
// Include database connection
include('config.php');

// Query to fetch data from flood_archive table
$sql = "SELECT barangay, sitio_purok, photo, description FROM flood_archive";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Loop through the results and display each card
    while ($row = $result->fetch_assoc()) {
        // Convert binary photo data to base64 for display
        $photo = base64_encode($row['photo']);
        $description = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');
        $barangay = htmlspecialchars($row['barangay'], ENT_QUOTES, 'UTF-8');
        $sitioPurok = htmlspecialchars($row['sitio_purok'], ENT_QUOTES, 'UTF-8');
        ?>
        <div class="card" style="margin-bottom: 20px;">
            <img src="data:image/jpeg;base64,<?= $photo ?>" class="card-img-top" alt="<?= $barangay ?> - <?= $sitioPurok ?>">
            <div class="card-body">
                <h5 class="card-title"><?= $barangay ?> - <?= $sitioPurok ?></h5>
                <p class="card-text"><?= $description ?></p>
            </div>
        </div>
        <?php
    }
} else {
    echo "<p>No records found in the flood archive.</p>";
}

// Close the database connection
$conn->close();
?>
