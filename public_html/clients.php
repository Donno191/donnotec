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

    $database = new SQLite3('../private/database.db');
    $count = $database->querySingle("SELECT COUNT(*) FROM donnotec_biller WHERE del = 0 and user_id = '".$_SESSION['user_id']."'");
    $result = $database->query("SELECT * FROM donnotec_biller WHERE del = 0 and user_id = '".$_SESSION['user_id']."'");
    $data = array();
    $FAIL = true;
    $option = '';
    if($count != 0){ $FAIL = false; };
    if($count > 1){ $option .= "<option value='0' >All</option>"; };
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) { $option .= "<option value='".$row['id']."' >".$row['name']."</option>"; };
    
    //REQUEST
    if (isset($_REQUEST['FORM'])) {
        if($_REQUEST['FORM'] == "PRIVATE"){
            if (isset($_REQUEST['biller_id'])) {
                if ($_REQUEST['biller_id'] == 0 || $_REQUEST['biller_id'] == '0'){
                    $BillerString = "";    
                }else{
                    $BillerString = " AND biller_id = ".$_REQUEST['biller_id'];
                }    
            }else{
                $BillerString = "";
            }
            // Database configuration
            $database = new SQLite3('../private/database.db');
            $result = $database->query("SELECT * FROM donnotec_client WHERE del = 0 ".$BillerString." AND user_id = '".$_SESSION['user_id']."'");
            $data = array();
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) { // Use fetchArray() instead of fetch()
                if ($row['client_name'] == 'None'){
                    $data[] = ['client_name' => $row['client_name'],'action' => '',];
                }else{
                    $editButton = '1';
                    $data[] = [
                        'client_name' => $row['client_name'],
                        'action' => '<form class="inline_form" action="client_edit.php"><input type="hidden" name="client_id" value="'.$row['id'].'"><input type="submit" value="Edit" /></form>
                        <form class="inline_form" action="clients.php" onSubmit="return confirm(\'Are you sure to delete '.$row['client_name'].'?\')" ><input type="hidden" name="FORM" value="DELCLIENT"><input type="hidden" name="client_id" value="'.$row['id'].'"><input type="submit" value="Delete"></form>',
                    ];
                }
            }
            header('Content-Type: application/json; charset=utf-8');
            echo '{"data": '.json_encode($data).'}';
            die();
        }
        //TEST_URL_STRING : http://localhost:8001/clients.php?FORM=ADDCLIENT&biller_id=4&category_id=4&account=test&client_name=test&extra=asdasd%0D%0Aas%0D%0Ada%0D%0Asda%0D%0Asd%0D%0Aa%0D%0As%0D%0Ad%0D%0Aasdasd
        if($_REQUEST['FORM'] == "ADDCLIENT"){
            $ValidationTest = AddClientValidation($_REQUEST['biller_id'],$_REQUEST['category_id'],$_REQUEST['account'],$_REQUEST['client_name'],$_REQUEST['extra']);
            if ($ValidationTest == "PASS"){
                $ValidationTest = AddClient($_REQUEST['biller_id'],$_REQUEST['category_id'],$_REQUEST['account'],$_REQUEST['client_name'],$_REQUEST['extra']);
                if(str_starts_with($ValidationTest, 'Created client')){
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
        if($_REQUEST['FORM'] == "EDITCLIENT"){
            $ValidationTest = EditClientValidation();
            if ($ValidationTest == "PASS"){
                $output_notification_type = 'success'; 

                $database = new SQLite3('../private/database.db');
                $stmt = $database->prepare("SELECT * FROM donnotec_client WHERE id=? AND del = 0 AND user_id=?");
                $stmt->bindParam(1, $_REQUEST['client_id']);
                $stmt->bindParam(2, $_SESSION['user_id']);
                $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
                if ($result) {  // Check if a record was found in the database
                    $clientName = $result['client_name'];
                }

                $ValidationTest = EditClient();
                if($ValidationTest == "PASS"){
                    $output_notification_type = 'success';
                    $ValidationTest = $clientName." Updated successfully";
                }else{
                    $output_notification_type = 'error';    
                }
            }else{
                $output_notification_type = 'error';
            }
            $output_notification = true;
            if($output_notification_type == 'success'){
                $output_notification_message = $ValidationTest;
            }else{
                $output_notification_message = $ValidationTest."<br><a href=\'".str_replace("biller.php","biller_settings.php",$_SERVER['REQUEST_URI'])."\' >Resubmit form</a>";
            }
        }
        if($_REQUEST['FORM'] == "DELCLIENT"){
            if (isset($_REQUEST['client_id'])) {
                if(is_numeric($_REQUEST['client_id'])) {
                    $database = new SQLite3('../private/database.db');
                    $stmt = $database->prepare("SELECT * FROM donnotec_client WHERE id=? AND del = 0 AND user_id=?");
                    $stmt->bindParam(1, $_REQUEST['client_id']);
                    $stmt->bindParam(2, $_SESSION['user_id']);
                    $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
                    if ($result) {
                        $clientName = $result['client_name'];
                        $database = new SQLite3('../private/database.db');
                        $stmt = $database->prepare("UPDATE donnotec_client SET del = 1 WHERE id = ".$_REQUEST['client_id']);
                        if ($stmt->execute()) {
                            $output_notification_type = 'success';
                            $ValidationTest = $clientName." has been successfully deleted !";
                        } else {
                            $ValidationTest = "An error occurred while updating the database";
                            $output_notification_type = 'error';                            
                        }
                    } else {
                        $ValidationTest = "No matching records were found in the database.";
                        $output_notification_type = 'error';
                    }                                       
                }else{
                    $ValidationTest = "Client ID must be a number";
                    $output_notification_type = 'error';
                }
            } else {
                $ValidationTest = "No Client ID was provided in the request.";
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
            select {
                background-color: #007BFF;
                color: #ffffff;
                border: none;
                cursor: pointer;
                padding: 10px 20px;
                border-radius: 5px;
                font-size: 16px;
                transition: background-color 0.3s;
                
                text-decoration: none;
                margin-left: 5px;
            }    
            .custom-frm{
                display:inline;
            }
            .custom-btn{
                display:inline;
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
                new DataTable('#Clients', {
                    info: false,
                    ordering: false,
                    paging: false,
                    ajax: 'clients.php?FORM=PRIVATE&biller_id='+$("#biller_id").find('option:selected').val(),
                    columns: [
                        { data: 'client_name' },
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
                $("#biller_select").detach().appendTo(".dt-search");

            });
            function BillerID_change(sel){
                var htmlContent = '<div id="biller_select" style="float:left;">'+$("#biller_select").html()+'</div>';
                $('#Clients').text('');
                var table = $('#Clients').DataTable({
                    info: false,
                    ordering: false,
                    paging: false,
                    ajax: 'clients.php?FORM=PRIVATE&biller_id='+sel.value,
                    columns: [
                        { data: 'client_name' },
                        { data: 'action' }
                    ],
                    columnDefs: [
                        {
                            target: 0,
                            searchable: false,
                            orderable: false
                        },{
                            target: 1,
                            searchable: false,
                            orderable: false
                        }
                    ],
                    "bDestroy": true
                });
                $(".dt-search").append(htmlContent);
                $('#biller_id').val(sel.value);
            }
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
            <?php if($FAIL){ echo "<h2 style='color:red;margin:10px;'>Please <a href='biller_add.php' style='text-decoration:none;color:#007BFF;'>create Company/Biller<a> first to the system before managing clients.</h2>";}; ?>
            <div class="content" <?php if($FAIL){echo "style=display:none;";} ?>>
                
                <div style="margin-bottom:10px;padding-bottom:10px;">
                    <form action="client_add.php" method="post" class="custom-frm"  >
                        <input type="submit" name="add_biller" value="Add Client" class="custom-btn">
                    </form>
                    <form action="client_category.php" method="post" class="custom-frm" >
                        <input type="submit" name="add_biller" value="Client Category" class="custom-btn">
                    </form>
                    <form action="client_statements.php" method="post" class="custom-frm" >
                        <input type="submit" name="add_biller" value="Client Statements" class="custom-btn">
                    </form>
                </div><hr>
                <div id="biller_select" style="float:left;">
                    <b>Biller/Company : </b>
                    <select id="biller_id" onchange="BillerID_change(this);">
                        <?php echo $option; ?>
                    </select>
                </div>
                <!-- Assume you have this in your HTML, it could be anywhere before the </body> tag -->
                <table id="Clients" class="display" style="width:100%;" >
                    <thead>
                        <tr>
                            <th>Client Name</th>
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
    function AddClientValidation($biller_id,$category_id,$account,$client_name,$extra){
        $database = new SQLite3('../private/database.db');
        //Validate Biller_id
        $result = $database->prepare("SELECT COUNT(*) as count FROM donnotec_biller WHERE id = ? AND del = 0 AND user_id = ?");
        $result->bindParam(1, $biller_id);
        $result->bindParam(2, $_SESSION['user_id']);
        $row = $result->execute()->fetchArray(SQLITE3_ASSOC);
        if ($row) { if($row['count'] == 0){return "Client can not be linked to Biller/Company, is Biller/Company deleted ?";}}else{return "Biller/Company is an invalid query.";} 
        //Validate Category_id
        $result = $database->prepare("SELECT COUNT(*) as count FROM donnotec_client_category WHERE id = ? AND del = 0 AND biller_id = ? AND user_id = ?");
        $result->bindParam(1, $category_id);
        $result->bindParam(2, $biller_id);
        $result->bindParam(3, $_SESSION['user_id']);
        $row = $result->execute()->fetchArray(SQLITE3_ASSOC);
        if ($row) { if($row['count'] == 0){return "Client can not be linked to Client category, is Client category deleted ?";}}else{return "Client category is an invalid query.";}
        //Validate client account
        $result = $database->prepare("SELECT COUNT(*) as count FROM donnotec_client WHERE account = ? AND del = 0 AND biller_id = ? AND user_id = ?");
        $result->bindParam(1, $account);
        $result->bindParam(2, $biller_id);
        $result->bindParam(3, $_SESSION['user_id']);
        $row = $result->execute()->fetchArray(SQLITE3_ASSOC);
        if ($row) { if($row['count'] != 0){return "Client account already exists !";} };
        $regexp = '/^[A-Za-z0-9]{1,20}$/';
        if (!isset($account)) {return "Client Account Not set !";}            
        if (empty($account)) {return "Client Account cannot be blank.";}
        if (!preg_match($regexp, $account)) {return "Client Account Not valid !<br />characters match a-z A-Z 0-9<br />not more than 20 characters";}
        //Validate client name
        $regexp = "/^(?!\s)(?!.*\s$)(?=.*[a-zA-Z0-9])[a-zA-Z0-9 '~?!]{2,50}$/";
        if (!isset($client_name)) {return "Client name Not set !";}            
        if (empty($client_name)) {return "Client name cannot be blank.";}
        if (!preg_match($regexp, $client_name)) {return "Client Name Not valid !<br />don't start with space<br />don't end with space<br />atleast one alpha or numeric character<br />characters match a-z A-Z 0-9 '~?! <br />minimum 2 characters<br />max 50 characters";}
        return "PASS";
    }
    function AddClient($biller_id,$category_id,$account,$client_name,$extra){
        $database = new SQLite3('../private/database.db');

        //Get biller_name
        $result = $database->prepare("SELECT name FROM donnotec_biller WHERE id = ? AND del = 0 AND user_id = ?");
        $result->bindParam(1, $biller_id);
        $result->bindParam(2, $_SESSION['user_id']);
        $row = $result->execute()->fetchArray(SQLITE3_ASSOC);
        if ($result) { 
            $biller_name = $row['name'];
        }

        //Get account_id for Client_category
        $result = $database->prepare("SELECT account_id FROM donnotec_client_category WHERE id = ? AND del = 0 AND biller_id = ? AND user_id = ?");
        $result->bindParam(1, $category_id);
        $result->bindParam(2, $biller_id);
        $result->bindParam(3, $_SESSION['user_id']);
        $row = $result->execute()->fetchArray(SQLITE3_ASSOC);
        if ($result) { 
            $account_cat_id = $row['account_id'];
        }
        //Add Client account using client_category_account_id
        $account_id = AddBillerAccounts($biller_id,"#C",$account,"Client Account : ".$client_name,0,$account_cat_id,0,"a",0,"p");

        //Add Client using client__account_id
        $stmt_insert = $database->prepare("INSERT INTO donnotec_client (account, client_name, extra, biller_id, user_id, category_id, account_id ) VALUES (:account, :client_name, :extra, :biller_id, :user_id, :category_id, :account_id)");
        $stmt_insert->bindParam(':account', $account);
        $stmt_insert->bindParam(':client_name', $client_name);
        $stmt_insert->bindParam(':extra', $extra);
        $stmt_insert->bindParam(':biller_id', $biller_id);
        $stmt_insert->bindParam(':user_id', $_SESSION['user_id']);
        $stmt_insert->bindParam(':category_id', $category_id);
        $stmt_insert->bindParam(':account_id', $account_id);
        if ($stmt_insert->execute()) {
            return "Created client (".$account.")".$client_name." for ".$biller_name;
        } else {
            return "An error occurred while creating client.";
        }
    }    
    function AddBillerAccounts($biller_id,$num,$account_name,$account_description,$category_id,$sub_id,$system_account_num,$inherit,$isVisible,$sign){
        $database = new SQLite3('../private/database.db');
        $stmt = $database->prepare("INSERT INTO donnotec_accounts (num, account_name, account_description, category_id, biller_id, sub_id, del, user_id, system_account_num, inherit,isVisible,sign)
    VALUES (:num, :account_name, :account_description, :category_id, :biller_id, :sub_id, :del, :user_id, :system_account_num, :inherit, :isVisible, :sign)");
        $stmt->bindValue(':num', $num);
        $stmt->bindValue(':account_name', $account_name);
        $stmt->bindValue(':account_description', $account_description);
        $stmt->bindValue(':category_id', $category_id);
        $stmt->bindValue(':biller_id', $biller_id);
        $stmt->bindValue(':sub_id', $sub_id);
        $stmt->bindValue(':del', 0);
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
        $stmt->bindValue(':system_account_num', $system_account_num);
        $stmt->bindValue(':inherit', $inherit);
        $stmt->bindValue(':isVisible', $isVisible);
        $stmt->bindValue(':sign', $sign);
        if ($stmt->execute()) {
            return $database->lastInsertRowID();
        } else {
            return "An error occurred while creating ".$num." ".$account_name;
        }
    } 
    function EditClientValidation(){

        $database = new SQLite3('../private/database.db');
        if ($_REQUEST['client_id'] == ""){
            return "CLient ID cannot be blank !";
        }

        $query = "SELECT * FROM donnotec_client WHERE id=? and del = 0 and user_id = '".$_SESSION['user_id']."'";
        $stmt = $database->prepare($query);
        $stmt->bindValue(1, $_REQUEST['client_id']);
        $result = $stmt->execute();
        if (!$result) { return "Cannot access Client id in database";}

        if (!isset($_REQUEST['client_name'])){return "Currency symbol Not set !"; }
        if($_REQUEST['client_name'] == ""){ return "Currency symbol cannot be blank !"; }
        $regex = "/^(?!\s)(?!.*\s$)(?=.*[a-zA-Z0-9])[a-zA-Z0-9 '~?!]{2,50}$/";
        if (preg_match($regex, $_REQUEST['client_name']) !== 1){
            return "Client Name Not valid !<br />don't start with space<br />don't end with space<br />atleast one alpha or numeric character<br />characters match a-z A-Z 0-9 '~?! <br />minimum 2 characters<br />max 50 characters";
        }

        return "PASS";
    } 
    function EditClient(){
        $database = new SQLite3('../private/database.db');

        $query = "UPDATE donnotec_client SET client_name = '".$_REQUEST['client_name']."', extra = '".$_REQUEST['extra']."' WHERE id = ".$_REQUEST['client_id'];
        $stmt_insert = $database->prepare($query);
        $stmt_insert->bindValue(':client_name', $_REQUEST['client_name'] );
        $stmt_insert->bindValue(':extra', $_REQUEST['extra'] );
        if ($stmt_insert->execute()) {
            return "PASS";
        } else {
            return "An error occurred while editing Client.";
        }
    }
?>