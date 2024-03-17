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
            $result = $database->query("SELECT biller_id,biller_name FROM donnotec_biller WHERE System_del = 0 and System_user_id = '".$_SESSION['user_id']."'");
            $data = array();
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) { // Use fetchArray() instead of fetch()
                $editButton = '1';//<button type="button" class="btn btn-primary editBtn" data-id="' . $row['biller_id'] . '">Edit</button>
                $data[] = [
                    'biller_id' => $row['biller_id'],
                    'biller_name' => $row['biller_name'],
                    'action' => '<form class="inline_form" action="biller_settings.php"><input type="hidden" name="biller_id" value="'.$row['biller_id'].'"><input type="submit" value="Settings" /></form><form class="inline_form" action="biller.php" onSubmit="return confirm(\'Are you sure to delete '.$row['biller_name'].'?\')" ><input type="hidden" name="FORM" value="DELBILLER"><input type="hidden" name="biller_id" value="'.$row['biller_id'].'"><input type="submit" value="Delete"></form>', // Set this value to 1 since the Edit button is present in all rows
                ];
            }
            header('Content-Type: application/json; charset=utf-8');
            echo '{"data": '.json_encode($data).'}';
            die();
        }
        //TEST_URL_STRING : http://localhost:8001/biller.php?FORM=ADDBILLER&billerName=VRC+Prospects+cc&Currency_symbol=32&Account_type=1
        if($_REQUEST['FORM'] == "ADDBILLER"){
            $ValidationTest = AddBillerValidation($_REQUEST['billerName'],$_REQUEST['Currency_symbol'],$_REQUEST['Account_type']);
            if ($ValidationTest == "PASS"){
                $ValidationTest = AddBiller($_REQUEST['billerName'],$_REQUEST['Currency_symbol'],$_REQUEST['Account_type']);
                if($ValidationTest == "PASS"){
                    $output_notification_type = 'success';
                }else{
                    $output_notification_type = 'error';    
                }
            }else{
                $output_notification_type = 'error';
            }
            $output_notification = true;
            $output_notification_message = $ValidationTest;
        }
        if($_REQUEST['FORM'] == "DELBILLER"){
            if (isset($_REQUEST['biller_id'])) { //TEST is there biller_id
                if(is_numeric($_REQUEST['biller_id'])) { //TEST is biller_id a number 
                    $database = new SQLite3('../private/database.db');
                    $stmt = $database->prepare("SELECT * FROM donnotec_biller WHERE biller_id=? AND system_del = 0 AND system_user_id=?");
                    $stmt->bindParam(1, $_REQUEST['biller_id']);
                    $stmt->bindParam(2, $_SESSION['user_id']);
                    $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
                    if ($result) {  // Check if a record was found in the database
                        $billerName = $result['biller_name'];
                        $database = new SQLite3('../private/database.db');
                        $stmt = $database->prepare("UPDATE donnotec_biller SET system_del = 1 WHERE biller_id = ".$_REQUEST['biller_id']);
                        if ($stmt->execute()) {
                            $output_notification_type = 'success';
                            $ValidationTest = $billerName." has been successfully deleted !";
                        } else {
                            $ValidationTest = "An error occurred while updating the database";
                            $output_notification_type = 'error';                            
                        }
                    } else {
                        $ValidationTest = "No matching records were found in the database.";
                        $output_notification_type = 'error';
                    }                                       
                }else{
                    $ValidationTest = "No Biller/Company ID must be a number";
                    $output_notification_type = 'error';
                }
            } else {
                $ValidationTest = "No Biller/Company ID was provided in the request.";
                $output_notification_type = 'error';
            }
            $output_notification = true;
            $output_notification_message = $ValidationTest;
        }
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
            // Initialize the DataTable with id "example"
            $(document).ready(function() {
                /* <?php if (isset($_SERVER['REQUEST_URI'])) { echo "alert('".$_SERVER['REQUEST_URI']."');"; }; ?> */
                <?php 
                    if ($output_notification){
                        echo "GrowlNotification.notify({title: '".$output_notification_type."!', description: '".$output_notification_message."',image: 'images/danger-outline.svg',type: '".$output_notification_type."',position: 'top-center',closeTimeout: 0});";
                        echo "window.history.replaceState({}, '', window.location.href.split('?')[0]);";
                    }
                ?>
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

<?php
    function Test(){
        return "Hello";
    }
    function AddBillerValidation($billerName,$Currency_symbol,$Account_type){
        if ($billerName == ""){
            return "Biller/Company Name cannot be blank !";
        }
        $regex = "/^(?!\s)(?!.*\s$)(?=.*[a-zA-Z0-9])[a-zA-Z0-9 '~?!]{2,}$/u";
        if (preg_match($regex, $billerName) !== 1){
            return "Biller/Company Name Not valid !<br />- don&apos;t start with space<br />- don&apos;t end with space<br />- atleast one alpha or numeric character<br />- characters match a-z A-Z 0-9 &apos;~?! <br />- minimum 2 characters";
        }
        $pattern = '/^[0-9]+$/'; //digit from 0 to 9 
        if (preg_match($pattern, $Currency_symbol)) { 
            if ($Currency_symbol >= 0 && $Currency_symbol <= 32) {
            } else {
                return "Symbol must be a value between 0 and 32";  
            }
        } else {
            return "Symbol has unknown value";  
        }    
        $pattern = '/^(0|1)$/'; // Regular expression for 0 or 1
        if (preg_match($pattern, $Account_type)) {
        } else {
            return "Business type unknown value ! Value must be 0 or 1"; 
        }           
        return "PASS";
    }
    function AddBiller($billerName,$Currency_symbol,$Account_type){
        $Currency = [
            ["AUD", "AUD$", ".", 2, "", "%s%v", "-%s%v"],
            ["BGN", "лв", ",", 2, "", "%v %s", "-%v %s"],
            ["BRL", "R$", ".", 2, ".","%s%v","-%s%v"],
            ["CAD", "$", ",", 2, "", "%v %s", "-%v %s"],
            ["CHF", "CHF", ".", 2, "'","'%s %v','-%s %v"],
            ["CNY", "¥", ".", 2, ",", "%s %v", "-%s %v"],
            ["CZK", "Kč", ",", 2, "", "%v %s", "-%v %s"],
            ["DKK", "kr.","", 2, ".", "%s%v","-%s%v"],
            ["EUR", "€", ".", 2, "", "%s %v", "-%s %v"],
            ["GBP", "£", ".", 2, "", "%s%v","-%s%v"],
            ["HKD", "HK$", ".", 2, ",","%s%v","-%s%v"],
            ["HRK", "kn", ",", 2, ".","%v %s","-%v %s"],
            ["HUF", "Ft", ",", 2, "", "%v %s","-%v %s"],
            ["IDR", "Rp", ".", 0, "", "%s%v", "-%s%v"],
            ["ILS", "₪", ".", 2, ",","%v %s", "-%v %s"],
            ["INR", "₹", ".", 2, ",", "%s%v", "-%s%v"],
            ["ISK", "kr", ",", 2, ".","%v %s", "-%v %s"],
            ["JPY", "¥", ".", 0, ",", "%s %v", "-%s %v"],
            ["KRW", "₩", ".", 0, ",","%s%v","-%s%v"],
            ["MXN", "Mex$", ".", 2, "", "%s%v","-%s%v"],
            ["MYR", "RM", ".", 2, ",", "%s %v", "-%s %v"],
            ["NOK", "kr", ",", 2, "", "%v %s", "-%v %s"],
            ["NZD", "NZ$", ".", 2, ",","%s%v", "-%s%v"],
            ["PHP", "₱", ".", 2, ",","%s%v", "-%s%v"],
            ["PLN", "zł", ".", 2, ",","%v %s", "-%v %s"],
            ["RON", "lei", ",", 2, ".","%v %s", "-%v %s"],
            ["RUB", "₽.","", 2, "", "%s %v", "-%s %v"],
            ["SEK", "kr", ",", 2, "", "%v %s", "-%v %s"],
            ["SGD", "$", ".", 2, ",","%s %v", "-%s %v"],
            ["THB", "฿", ".", 2, "", "%s %v", "-%s %v"],
            ["TRY", "₺", ",", 2, ".","%s%v", "-%s%v"],
            ["USD", "$", ".", 2, ",","%s%v", "-%s%v"],
            ["ZAR", "R", ".", 2, " ","%s %v", "-%s %v"],
        ];
        // Database connection
        $database = new SQLite3('../private/database.db');
        // Prepare the INSERT statement with parameterized query to prevent SQL injection
        $stmt_insert = $database->prepare("INSERT INTO donnotec_biller (biller_name, Currency_symbol, Currency_decimal_symbol, 
                           Currency_decimal_digit, Currency_digital_grouping, Currency_pos_format, Currency_neg_format, Account_type,System_user_id) VALUES (:billerName, :currencySymbol, 
                            :currencyDecimalSymbol, :currencyDecimalDigit, :currencyDigitalGrouping, :posFormat, :negFormat, :accountType, :user_id)");

        // Set the parameters for the prepared statement
        $stmt_insert->bindParam(':billerName', $billerName);
        $stmt_insert->bindParam(':currencySymbol', $Currency[$Currency_symbol][1]);
        $stmt_insert->bindParam(':currencyDecimalSymbol', $Currency[$Currency_symbol][2]);
        $stmt_insert->bindParam(':currencyDecimalDigit', $Currency[$Currency_symbol][3]);
        $stmt_insert->bindParam(':currencyDigitalGrouping', $Currency[$Currency_symbol][4]);
        $stmt_insert->bindParam(':posFormat', $Currency[$Currency_symbol][5]);
        $stmt_insert->bindParam(':negFormat', $Currency[$Currency_symbol][6]);
        $stmt_insert->bindParam(':accountType', $Account_type);
        $stmt_insert->bindParam(':user_id', $_SESSION['user_id']);

        // Execute the prepared statement with provided URL parameters and get last inserted row id
        if ($stmt_insert->execute()) {
            $billerId = $database->lastInsertRowID(); // Get the ID of the newly inserted record
            return "PASS";
            //echo "The biller id for the newly inserted record is: " . $billerId;
        } else {
            return "An error occurred while creating Biller/Company.";
        }
    }
?>