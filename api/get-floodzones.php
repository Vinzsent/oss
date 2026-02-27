<?php
include('../config/database.php');
$result = mysqli_query($conn,"SELECT * FROM flood_zones");
$data = [];
while($row = mysqli_fetch_assoc($result)){
    $row['polygon'] = json_decode($row['polygon']);
    $data[] = $row;
}
echo json_encode($data);
?>