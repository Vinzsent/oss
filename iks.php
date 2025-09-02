<?php
// Start session to access user data
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indigenous Knowledge Systems for Flood Prediction & Prevention</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            padding: 20px;
            background: white;
            margin: auto;
        }
        h1, h2, h3, h4 {
            color: #333;
        }
        ul {
            margin-left: 20px;
        }
        /* Sticky Header */
        .sticky-header {
            position: fixed;
            top: 0;
            width: 100%;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 10px 0;
        }
        .content {
            margin-top: 80px; /* Prevent content from being hidden under sticky header */
        }
        .medium-img {
            display: block;
            margin: 10px auto;
            max-width: 300px; /* Adjust as needed */
            height: auto;
        }
    </style>
</head>
<body>

    <div class="sticky-header">
        <?php include('includes/nav.php'); ?>
    </div>

    <div class="container content">
        <h3>Indigenous Knowledge Systems for Flood Prediction & Prevention</h3>
        <p>Indigenous communities in the Philippines have developed rich and detailed systems for understanding and predicting floods. These systems are rooted in centuries of observation of the natural environment, reflecting a profound connection between people and nature.</p>
        
        <h4>Flood Prediction through Animal Behavior</h4>
        <p>A striking feature of Indigenous flood prediction is the use of animal behavior as an early warning system. The Bagobo-Tagabawa tribe observes the movement of crabs inland as a signal of an impending flood. When crabs move to higher ground, it is interpreted as a sign that rivers are likely to overflow soon.</p>
        
        <h4>Context and Significance</h4>
        <p>The movement of crabs is a pattern refined over generations. This knowledge highlights the tribe's deep connection with nature and their ability to interpret small environmental changes as indicators of impending disasters.</p>
        <img src="assets/icons/crab.png" alt="Crab moving inland" class="medium-img">
        
        <h4>Understanding Weather Patterns through Natural Indicators</h4>
        <p>Indigenous communities also rely on various natural indicators to forecast weather patterns:</p>
        <ul>
            <li><strong>Animal behavior:</strong> Some animals take shelter or behave differently before a storm.</li>
            <img src="assets/icons/animal.png" alt="Animal behavior" class="medium-img">
        
            <li><strong>Cloud formations:</strong> Dark clouds may indicate an approaching storm.</li>
            <img src="assets/icons/cloud.png" alt="Cloud formations" class="medium-img">
        
            <li><strong>Tree conditions:</strong> Changes in trees and plants may signal moisture level shifts.</li>
            <img src="assets/icons/plant.png" alt="Tree conditions" class="medium-img">
        </ul>
        
        <h4>Holistic Approach to Weather Prediction</h4>
        <p>By observing all elements of the environment, Indigenous communities gain a deeper understanding of weather changes without relying on modern technology.</p>
        
        <h3>Flood Prevention Practices</h3>
        <h4>Planting Bamboo along Riverbanks</h4>
        <p>Bamboo helps stabilize soil and prevent erosion, reducing flood impact.</p>
        <img src="assets/icons/bamboo.png" alt="Bamboo planting" class="medium-img">
        
        <h4>Protecting Forests and Reforestation Efforts</h4>
        <p>Reforestation reduces flood risks by absorbing rainfall, slowing runoff, and preventing soil erosion.</p>
        <img src="assets/icons/treeplanting.png" alt="Tree planting" class="medium-img">
       
        <h3>Conclusion</h3>
        <p>Indigenous knowledge provides valuable insights into flood prediction and prevention. These practices emphasize a deep connection to nature and highlight sustainable ways to mitigate environmental risks.</p>
    </div>
	
	    
</body>
</html>