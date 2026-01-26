<?php
// Start session to access user data
session_start();
// Include authentication check if needed, consistent with other pages
// include('includes/auth_check.php'); 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indigenous Knowledge Systems - Micro Online Synthesis System</title>
    <!-- FontAwesome 6.4.0 -->
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
            color: #fff;
        }

        .content-container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .section-title {
            color: #8b5cf6;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 20px;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 10px;
        }

        .section-subtitle {
            font-weight: 600;
            color: #4b5563;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        p {
            color: #4b5563;
            line-height: 1.8;
            margin-bottom: 1.5rem;
            text-align: justify;
        }

        .img-card {
            background: #fff;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            text-align: center;
        }

        .medium-img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            max-height: 300px;
        }

        ul.feature-list {
            list-style: none;
            padding-left: 0;
        }

        ul.feature-list li {
            position: relative;
            padding-left: 30px;
            margin-bottom: 15px;
            color: #4b5563;
        }

        ul.feature-list li::before {
            content: "\f00c";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #10b981;
            position: absolute;
            left: 0;
            top: 2px;
        }

        strong {
            color: #6b21a8;
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
                <i class="fas fa-feather-alt me-3"></i>Indigenous Knowledge Systems
            </h1>
            <p class="page-subtitle">Traditional Wisdom for Flood Prediction & Prevention</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="content-container">
                    <div class="text-center mb-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="lead">
                            Indigenous communities in the Philippines have developed rich and detailed systems for understanding and predicting floods. These systems are rooted in centuries of observation of the natural environment, reflecting a profound connection between people and nature.
                        </p>
                    </div>

                    <h3 class="section-title"><i class="fas fa-paw me-2"></i>Flood Prediction through Animal Behavior</h3>
                    <div class="row align-items-center mb-4">
                        <div class="col-md-8">
                            <p>A striking feature of Indigenous flood prediction is the use of animal behavior as an early warning system. The Bagobo-Tagabawa tribe observes the movement of crabs inland as a signal of an impending flood. When crabs move to higher ground, it is interpreted as a sign that rivers are likely to overflow soon.</p>
                            <h5 class="section-subtitle">Context and Significance</h5>
                            <p>The movement of crabs is a pattern refined over generations. This knowledge highlights the tribe's deep connection with nature and their ability to interpret small environmental changes as indicators of impending disasters.</p>
                        </div>
                        <div class="col-md-4">
                            <div class="img-card">
                                <img src="assets/icons/crab.png" alt="Crab moving inland" class="medium-img img-fluid">
                                <p class="small text-muted mt-2 mb-0">Crab migration indicators</p>
                            </div>
                        </div>
                    </div>

                    <h3 class="section-title"><i class="fas fa-cloud-sun me-2"></i>Understanding Weather Patterns</h3>
                    <p>Indigenous communities also rely on various natural indicators to forecast weather patterns, demonstrating a holistic approach to environmental awareness:</p>

                    <div class="row mt-4">
                        <div class="col-md-4 mb-4">
                            <div class="img-card h-100">
                                <img src="assets/icons/animal.png" alt="Animal behavior" class="medium-img img-fluid mb-3">
                                <h5>Animal Behavior</h5>
                                <p class="small">Some animals take shelter or behave differently before a storm, sensing atmospheric changes.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="img-card h-100">
                                <img src="assets/icons/cloud.png" alt="Cloud formations" class="medium-img img-fluid mb-3">
                                <h5>Cloud Formations</h5>
                                <p class="small">Specific dark cloud formations and texturing are identified as precursors to approaching heavy storms.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="img-card h-100">
                                <img src="assets/icons/plant.png" alt="Tree conditions" class="medium-img img-fluid mb-3">
                                <h5>Natural Vegetation</h5>
                                <p class="small">Changes in tree leaves and plant rigidity can signal moisture level shifts and incoming rain.</p>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 shadow-sm mt-2 mb-5" role="alert" style="background-color: #e0e7ff; color: #3730a3;">
                        <i class="fas fa-lightbulb me-2"></i><strong>Holistic Insight:</strong> By observing all elements of the environment, Indigenous communities gain a deeper understanding of weather changes without relying on modern technology.
                    </div>

                    <h3 class="section-title"><i class="fas fa-tree me-2"></i>Flood Prevention Practices</h3>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-3">
                                    <div class="img-card p-2" style="width: 120px;">
                                        <img src="assets/icons/bamboo.png" alt="Bamboo planting" class="img-fluid rounded">
                                    </div>
                                </div>
                                <div>
                                    <h5 class="section-subtitle mt-0">Planting Bamboo along Riverbanks</h5>
                                    <p class="mb-0">Bamboo helps stabilize soil with its complex root system and prevent erosion, significantly reducing flood impact and bank collapse during high waters.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-3">
                                    <div class="img-card p-2" style="width: 120px;">
                                        <img src="assets/icons/treeplanting.png" alt="Tree planting" class="img-fluid rounded">
                                    </div>
                                </div>
                                <div>
                                    <h5 class="section-subtitle mt-0">Reforestation Efforts</h5>
                                    <p class="mb-0">Protecting forests and active reforestation reduces flood risks by absorbing rainfall, slowing runoff, and maintaining soil integrity across watersheds.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-top text-center">
                        <h4 class="text-purple mb-3" style="color: #8b5cf6;">Conclusion</h4>
                        <p class="lead mx-auto" style="max-width: 800px;">
                            Indigenous knowledge provides valuable insights into flood prediction and prevention. These practices emphasize a deep connection to nature and highlight sustainable ways to mitigate environmental risks.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-center py-4 mt-auto border-top">
        <div class="container">
            <p class="mb-0 text-dark text-center">&copy; 2024 Flood Resilience App. All Rights Reserved.</p>
        </div>
    </footer>
</body>

</html>