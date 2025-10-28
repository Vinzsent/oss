<?php
// Include your database connection file
include 'config.php'; // Assuming your connection file is named config.php

// Query the database for all flood warnings
$sql = "SELECT * FROM FloodWarning";
$result = $conn->query($sql);

// Check if there are records to display
if ($result->num_rows > 0) {
    // Start the HTML table
    echo '<table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Barangay</th>
                    <th>Sitio</th>
                    <th>Code</th>
                    <th>Warning Level</th>
                    <th>Status</th>
                    <th>Recommended Action</th>
                </tr>
            </thead>
            <tbody>';
    
    // Loop through the records and display them in table rows
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['date_created'] . '</td>';
        echo '<td>' . $row['barangay'] . '</td>';
        echo '<td>' . $row['sitio'] . '</td>';

        // Display warning level as text based on number
        switch ($row['warning_level']) {
            case 1:
                echo '<td style="background-color: yellow;"></td>';
                echo '<td>Yellow Alert</td>';
                break;
            case 2:
                echo '<td style="background-color: orange;"></td>';
                echo '<td>Orange Alert</td>';
                break;
            case 3:
                echo '<td style="background-color: red;"></td>';
                echo '<td>Red Alert</td>';
                break;
            
            default:
                echo '<td>Unknown</td>';
                echo '<td>Unknown</td>';
                break;
        }

        echo '<td>' . $row['status'] . '</td>';
        echo '<td>' . $row['recommended_action'] . '</td>';
        echo '</tr>';
    }

    // End the table
    echo '</tbody></table>';
} else {
    echo 'No records found';
}

// Close the database connection
$conn->close();
?>
