<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $purok_id = isset($_POST['purok_id']) ? (int)$_POST['purok_id'] : 0;
    $purok_name = isset($_POST['purok_name']) ? trim($_POST['purok_name']) : '';
    $total_pop_families = isset($_POST['total_pop_families']) ? (int)$_POST['total_pop_families'] : 0;
    $total_pop_persons = isset($_POST['total_pop_persons']) ? (int)$_POST['total_pop_persons'] : 0;
    $risk_pop_families = isset($_POST['risk_pop_families']) ? (int)$_POST['risk_pop_families'] : 0;
    $risk_pop_persons = isset($_POST['risk_pop_persons']) ? (int)$_POST['risk_pop_persons'] : 0;
    $plan_a_center_name = isset($_POST['plan_a_center_name']) ? trim($_POST['plan_a_center_name']) : '';
    $plan_a_center_address = isset($_POST['plan_a_center_address']) ? trim($_POST['plan_a_center_address']) : '';
    $plan_a_capacity_families = isset($_POST['plan_a_capacity_families']) ? (int)$_POST['plan_a_capacity_families'] : 0;
    $plan_a_capacity_persons = isset($_POST['plan_a_capacity_persons']) ? (int)$_POST['plan_a_capacity_persons'] : 0;
    $to_be_accommodated_families = isset($_POST['to_be_accommodated_families']) ? (int)$_POST['to_be_accommodated_families'] : 0;
    $to_be_accommodated_persons = isset($_POST['to_be_accommodated_persons']) ? (int)$_POST['to_be_accommodated_persons'] : 0;
    $not_accommodated_families = isset($_POST['not_accommodated_families']) ? (int)$_POST['not_accommodated_families'] : 0;
    $not_accommodated_persons = isset($_POST['not_accommodated_persons']) ? (int)$_POST['not_accommodated_persons'] : 0;
    $plan_b_center_name = isset($_POST['plan_b_center_name']) ? trim($_POST['plan_b_center_name']) : '';
    $plan_b_center_address = isset($_POST['plan_b_center_address']) ? trim($_POST['plan_b_center_address']) : '';
    $plan_b_capacity_families = isset($_POST['plan_b_capacity_families']) ? (int)$_POST['plan_b_capacity_families'] : 0;
    $plan_b_capacity_persons = isset($_POST['plan_b_capacity_persons']) ? (int)$_POST['plan_b_capacity_persons'] : 0;
    $not_accom_plan_ab_families = isset($_POST['not_accom_plan_ab_families']) ? (int)$_POST['not_accom_plan_ab_families'] : 0;
    $not_accom_plan_ab_persons = isset($_POST['not_accom_plan_ab_persons']) ? (int)$_POST['not_accom_plan_ab_persons'] : 0;
    $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
    
    if ($purok_id > 0) {
        // Update existing record
        $query = "UPDATE purok_evacuation_plan SET 
                  purok_name = ?, 
                  total_pop_families = ?, 
                  total_pop_persons = ?, 
                  risk_pop_families = ?, 
                  risk_pop_persons = ?, 
                  plan_a_center_name = ?, 
                  plan_a_center_address = ?, 
                  plan_a_capacity_families = ?, 
                  plan_a_capacity_persons = ?, 
                  to_be_accommodated_families = ?, 
                  to_be_accommodated_persons = ?, 
                  not_accommodated_families = ?, 
                  not_accommodated_persons = ?, 
                  plan_b_center_name = ?, 
                  plan_b_center_address = ?, 
                  plan_b_capacity_families = ?, 
                  plan_b_capacity_persons = ?, 
                  not_accom_plan_ab_families = ?, 
                  not_accom_plan_ab_persons = ?, 
                  remarks = ? 
                  WHERE purok_id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("siiiissiiiiiissiiiisi", 
            $purok_name, 
            $total_pop_families, 
            $total_pop_persons, 
            $risk_pop_families, 
            $risk_pop_persons, 
            $plan_a_center_name, 
            $plan_a_center_address, 
            $plan_a_capacity_families, 
            $plan_a_capacity_persons, 
            $to_be_accommodated_families, 
            $to_be_accommodated_persons, 
            $not_accommodated_families, 
            $not_accommodated_persons, 
            $plan_b_center_name, 
            $plan_b_center_address, 
            $plan_b_capacity_families, 
            $plan_b_capacity_persons, 
            $not_accom_plan_ab_families, 
            $not_accom_plan_ab_persons, 
            $remarks, 
            $purok_id
        );
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Evacuation plan updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating evacuation plan: " . $conn->error;
        }
        
        $stmt->close();
    } else {
        $_SESSION['error'] = "Invalid evacuation plan ID.";
    }
    
    $conn->close();
    header("Location: purok_evac.php");
    exit();
}
?>
