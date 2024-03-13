<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel='icon' type='image/png' href='icon/donnotec.ico' />
        <title>Clients</title>
    </head>
    <?php
// This file is part of Donnotec System - Small Business, licensed under the MIT License. See the LICENSE file in the project root for full license information.
// Copyright (C) 2023, Donovan R Fourie, Donnotec
// http://donnotec.com
    include '../private/config.php';

    session_start();
    // Check if the user is logged in
    if (!isset($_SESSION['loggedin'])) {
        header("Location: login.php");
        exit();
    }
?>
<body>
<?php if (isset($_SESSION['loggedin'])): ?>

    <h2>Client Information</h2>

    <form action="submit_client.php" method="post">
        <label for="clientName">Client/Company Name:</label>
        <input type="text" id="clientName" name="clientName"><br>

        <!-- Add more fields here as needed -->

        <input type="submit" value="Submit Information">
    </form>
<?php endif; ?>
</body>
</html>
