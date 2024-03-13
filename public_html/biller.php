<?php

// This file is part of Donnotec System - Small Business, licensed under the MIT License. See the LICENSE file in the project root for full license information.
// Copyright (C) 2023, Donovan R Fourie, Donnotec
// http://donnotec.com

    include '../private/config.php';
    session_start();
    $database = new SQLite3('../private/database.db');

    if (!isset($_SESSION['loggedin'])) {
        header("Location: login.php");
        exit();
    }
    //REQUEST
    if (isset($_REQUEST['FORM'])) {
        if($_REQUEST['FORM'] == "PRIVATE"){
            // Database configuration
            $database = new SQLite3('../private/database.db');
            $result = $database->query("SELECT biller_id,biller_name FROM donnotec_biller");
            $data = array();
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) { // Use fetchArray() instead of fetch()
                $editButton = '1';//<button type="button" class="btn btn-primary editBtn" data-id="' . $row['biller_id'] . '">Edit</button>
                $data[] = [
                    'biller_id' => $row['biller_id'],
                    'biller_name' => $row['biller_name'],
                    'action' => '<form action="biller_settings.php?biller_id='.$row['biller_id'].'"><input type="submit" value="Settings" /></form>', // Set this value to 1 since the Edit button is present in all rows
                ];
            }
            header('Content-Type: application/json; charset=utf-8');
            echo '{"data": '.json_encode($data).'}';
            die();
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel='icon' type='image/png' href='icon/donnotec.ico' />
        <title>Biller/Company</title>
        <link rel="stylesheet" href="css/datatables.min.css?v=123123123"/>
        <script src="javascript/jquery-3.7.1.min.js"></script>
        <script src="javascript/datatables.min.js?v=123123123"></script>
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

                width:100px;
            }
        </style>
        <script type="text/javascript">
            // Initialize the DataTable with id "example"
            $(document).ready(function() {
                new DataTable('#example', {
                    info: false,
                    ordering: true,
                    paging: false,
                    ajax: 'biller.php?FORM=PRIVATE',
                    columns: [
                        { data: 'biller_id' },
                        { data: 'biller_name' },
                        { data: 'action' }
                    ],
                    columnDefs: [
                        {
                            target: 0,
                            visible: false,
                            searchable: false,
                            orderable: false
                        },
                        {
                            target: 2,
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
                <table id="example" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>biller_id</th>
                            <th>Biller/Company Name</th>
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
