<?php

// This file is part of Donnotec System - Small Business, licensed under the MIT License. See the LICENSE file in the project root for full license information.
// Copyright (C) 2023, Donovan R Fourie, Donnotec
// http://donnotec.com

    include '../private/config.php';
    session_start();
    $database = new SQLite3('../private/database.db');

    if (isset($_POST['submit'])) {
        $user_name = $_POST['user_name'];
        $user_password = $_POST['user_password'];

        $stmt = $database->prepare('SELECT * FROM donnotec_users WHERE user_name = :user_name AND user_password = :user_password');
        $stmt->bindValue(':user_name', $user_name, SQLITE3_TEXT);
        $stmt->bindValue(':user_password', md5($user_password), SQLITE3_TEXT);

        $result = $stmt->execute();
        $user = $result->fetchArray();

        if ($user) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_fullname'] = $user['user_fullname'];
            $_SESSION['user_surename'] = $user['user_surename'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Invalid credentials!";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel='icon' type='image/png' href='icon/donnotec.ico' />
        <title>Login</title>
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
                background-color: #f5f5f5;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .form-card {
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                padding: 20px;
                width: 300px;
            }
            .form-card h2 {
                text-align: center;
                margin-bottom: 10px;  /* adjust this as needed for space between the image and the text */
            }
            /* Style for the login image */
            .login-img {
                display: block;
                margin: 0 auto; /* center the image horizontally */
                height: 100px; /* adjust this according to your image's actual size */
                width: auto;  /* maintain aspect ratio */
            }
            .form-group {
                margin-bottom: 15px;
            }   
            .form-group label {
                display: block;
                margin-bottom: 5px;
            }
            .form-group input {
                width: 90%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 4px;
                transition: border-color 0.3s ease;
            }
            .form-group input:focus {
                border-color: #007bff;
                outline: none;
            }
            .form-group input[type=submit] {
                padding-top:15px;
                width: 98%;
                padding: 10px;
                background-color: #007bff;
                color: #ffffff;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }
            .form-group input[type=submit]:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="form-card" action="login.php" method="post">
            <img src="logo/donnotec_logo.png" alt="Login Icon" class="login-img">
            <h2>Login</h2>
            <form action="#" method="POST">
                <div class="form-group">
                    <label for="user_name">Username:</label>
                    <input type="text" name="user_name" required>
                </div>
                <div class="form-group">
                    <label for="user_password">Password:</label>
                    <input type="password" name="user_password" required>
                </div>
                <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
                <div class="form-group">
                    <input type="submit" name="submit" value="Login">
                </div>
            </form>
        </div>
    </body>
</html>
