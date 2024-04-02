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
    $output_notification = false;
    //REQUEST
    if (isset($_REQUEST['FORM'])) {
        if($_REQUEST['FORM'] == "PRIVATE"){
            // Database configuration
            $database = new SQLite3('../private/database.db');
            $result = $database->query("SELECT * FROM donnotec_accounts WHERE del = 0 and isVisible = 1 and biller_id = 2 and user_id = '".$_SESSION['user_id']."'");
            $data = array();
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) { // Use fetchArray() instead of fetch()
                $editButton = '1';//<button type="button" class="btn btn-primary editBtn" data-id="' . $row['biller_id'] . '">Edit</button>
                $data[] = [
                    'account_des' => "(".$row['num'].") ".$row['account_name'],
                    'action' => '<form class="inline_form" action="biller_settings.php"><input type="hidden" name="biller_id" value="'.$row['id'].'"><input type="submit" value="Settings" /></form><form class="inline_form" action="biller.php" onSubmit="return confirm(\'Are you sure to delete '.$row['account_name'].'?\')" ><input type="hidden" name="FORM" value="DELBILLER"><input type="hidden" name="biller_id" value="'.$row['id'].'"><input type="submit" value="Delete"></form>', // Set this value to 1 since the Edit button is present in all rows
                ];
            }
            header('Content-Type: application/json; charset=utf-8');
            echo '{"data": '.json_encode($data).'}';
            die();
        }
        //TEST_URL_STRING : http://localhost:8001/biller.php?FORM=ADDBILLER&billerName=VRC+Prospects+cc&Currency_symbol=32&Account_type=1
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel='icon' type='image/png' href='icon/donnotec.ico'>
        <title>Biller/Company</title>
        <link rel="stylesheet" href="css/datatables.min.css">
        <link href="css/light-theme.min.css" rel="stylesheet">
        <link href="css/dark-theme.min.css" rel="stylesheet">
        <link href="css/colored-theme.min.css" rel="stylesheet">
        <script src="javascript/jquery-3.7.1.min.js"></script>
        <script src="javascript/datatables.min.js?v=123123123"></script>
        <script src="javascript/growl-notification.min.js"></script>
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
            .table_action{
                width:230px;
            }
            .inline_form{
                display: inline-block;
                margin:5px;
            }
            .inline_form input{
                background-color: #007BFF;
                color: #ffffff;
                border: none;
                cursor: pointer;
                padding: 10px 20px;
                border-radius: 5px;
                font-size: 16px; 
                transition: background-color 0.3s; 
            }            
        </style>
        <script>
            $(document).ready(function() {
                /* */
                <?php //if (isset($_SERVER['REQUEST_URI'])) { echo "alert('".$_SERVER['REQUEST_URI']."');"; }; ?>
                <?php 
                    if ($output_notification){
                        echo "GrowlNotification.notify({title: '".$output_notification_type."!', description: '".$output_notification_message."',image: 'images/danger-outline.svg',type: '".$output_notification_type."',position: 'top-center',closeTimeout: 0});";
                        echo "window.history.replaceState({}, '', window.location.href.split('?')[0]);";
                    }
                ?>
                new DataTable('#Account', {
                    info: false,
                    ordering: false,
                    paging: false,
                    ajax: 'accounts.php?FORM=PRIVATE',
                    columns: [
                        { data: 'account_des' },
                        { data: 'action' }
                    ],
                    columnDefs: [
                        {
                            target: 0,
                            searchable: false,
                            orderable: false
                        },
                        {
                            target: 1,
                            searchable: false,
                            orderable: false
                        }
                    ]
                });
            });
        </script>
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
                <div style="margin-bottom:10px;padding-bottom:10px;">
                    <form action="biller_add.php" method="post">
                        <input type="submit" name="add_biller" value="Add Biller/Company" class="custom-btn">
                    </form>
                </div><hr>
                <!-- Assume you have this in your HTML, it could be anywhere before the </body> tag -->
                <table id="Account" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Account Name</th>
                            <th class="table_action">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>