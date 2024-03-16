<!-- This file is part of Donnotec System - Small Business, licensed under the MIT License. -->
<!-- Copyright (C) <?php echo date("Y"); ?>, Donovan R Fourie, Donnotec -->
<!-- https://github.com/Donno191/donnotec -->
<!-- http://donnotec.com -->
<?php
    // This file is part of Donnotec System - Small Business, licensed under the MIT License. See the LICENSE file in the project root for full license information.
    // Copyright (C) 2024, Donovan R Fourie, Donnotec
    // http://donnotec.com

    // Start PHP session
    session_start();

    // Check if the user is logged in
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        // User is logged in, redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // User is not logged in, redirect to login page
        header("Location: login.php");
        exit();
    }
?>
