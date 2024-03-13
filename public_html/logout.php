<?php

// This file is part of DDonnotec System - Small Business, licensed under the MIT License. See the LICENSE file in the project root for full license information.
// Copyright (C) 2023, Donovan R Fourie, Donnotec
// http://donnotec.com

session_start();

if (isset($_POST['logout'])) {
    // Destroy the session and redirect the user to a different page (like the homepage or login page)
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

