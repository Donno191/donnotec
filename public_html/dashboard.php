<!-- This file is part of Donnotec System - Small Business, licensed under the MIT License. -->
<!-- Copyright (C) <?php echo date("Y"); ?>, Donovan R Fourie, Donnotec -->
<!-- https://github.com/Donno191/donnotec -->
<!-- http://donnotec.com -->
<?php
    // This file is part of Donnotec System - Small Business, licensed under the MIT License. See the LICENSE file in the project root for full license information.
    // Copyright (C) 2024, Donovan R Fourie, Donnotec
    // http://donnotec.com

    include '../private/config.php';
    session_start();
    $database = new SQLite3('../private/database.db');

    if (!isset($_SESSION['loggedin'])) {
        header("Location: login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel='icon' type='image/png' href='icon/donnotec.ico' />
        <title>Dashboard</title>
        <style>
            @font-face {
                font-family: 'Exo';
                src: url('font/Exo-Regular.woff2') format('woff2'),
                    url('font/Exo-Regular.woff') format('woff');
                font-weight: normal;
                font-style: normal;
                font-display: swap;
            }
            body {
                font-family: Exo, Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            <?php include "../private/menu_css.php"; ?>
            /* Dashboard */
            .dashboard {
                display: flex;
                height: 100vh;
                min-height: 930px;
            }
            <?php include "../private/sidebar_css.php"; ?>
            .content {
                flex: 1;
                padding: 20px;
                background-color: #f4f4f4;
            }
            .card {
                background-color: #fff;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
            }
            .card-with-image {
                display: flex;
                align-items: flex-start; /* Align items to the top */
            }
            .card-with-image img {
                max-width: 120px;
                margin-right: 20px;
            }            
        </style>    
    </head>
    <body>
        <!-- BEGIN sidebar INCLUDE -->
        <?php include "../private/menu.php"; ?>
        <!-- END sidebar INCLUDE -->
        <div class="dashboard">
            <!-- BEGIN sidebar INCLUDE -->
            <?php include "../private/sidebar.php"; ?>
            <!-- END sidebar INCLUDE -->

            <div class="content">
                <div class="card card-with-image">
                    <img src="logo/donnotec_logo.png" alt="Description of Image">
                    <div>
                        <h2>Welcome to the Donnotec System - Small Business</h2>
                        <h3>System Features Overview:</h3>
                            <ul>
                                <li>Automatic generation of a complete set of financial statements post each accounting period.</li>
                                <li>Fully customizable accounts management.</li>
                                <li>Multi-account facilities for cash/bank with statement import features.</li>
                                <li>Tailored business settings for individual company needs.</li>
                                <li>Design customized document layouts, including image addition.</li>
                                <li>User and staff management with varying permission levels.</li>
                                <li>Unique report generation with custom criteria.</li>
                                <li>Efficient client management with real-time history and statement creation.</li>
                                <li>Simplified creation of estimates, job cards, and invoices with integration to the inventory system.</li>
                                <li>Streamlined supplier interactions from categorization to order creation and invoice management.</li>
                                <li>Comprehensive inventory system for tracking and categorization.</li>
                            </ul>
                            <p>In essence, our platform offers a holistic solution for businesses, encompassing both management and accounting needs, designed with years of expertise and a commitment to operational excellence.</p>
                            <h3>Current System Release : Version 0.1.2</h3>
                    </div>
                </div>
                <div class="card">
                    <h2>Release notes v0.1.2</h2>
                    <ul>
                        <li>Minor Improvments</li>
                        <li>Added Licensing</li>
                    </ul>                    
                    <p>23 Oct 23</p>
                </div>                
                <div class="card">
                    <h2>Release notes v0.1.1</h2>
                    <ul>
                        <li>Login improved</li>
                        <li>Dashboard improved</li>
                    </ul>                    
                    <p>23 Oct 23</p>
                </div>
                <div class="card">
                    <h2>Released Notes 0.1   -   22 Oct 23</h2>
                    <p>Started Development on Donnotec System - For small business</p>
                </div>
            </div>
        </div>
    </body>
</html>
