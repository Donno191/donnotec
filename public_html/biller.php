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
            $result = $database->query("SELECT id,name FROM donnotec_biller WHERE del = 0 and user_id = '".$_SESSION['user_id']."'");
            $data = array();
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) { // Use fetchArray() instead of fetch()
                $editButton = '1';//<button type="button" class="btn btn-primary editBtn" data-id="' . $row['biller_id'] . '">Edit</button>
                $data[] = [
                    'biller_id' => $row['id'],
                    'biller_name' => $row['name'],
                    'action' => '<form class="inline_form" action="biller_settings.php"><input type="hidden" name="biller_id" value="'.$row['id'].'"><input type="submit" value="Settings" /></form><form class="inline_form" action="biller.php" onSubmit="return confirm(\'Are you sure to delete '.$row['name'].'?\')" ><input type="hidden" name="FORM" value="DELBILLER"><input type="hidden" name="biller_id" value="'.$row['id'].'"><input type="submit" value="Delete"></form>', // Set this value to 1 since the Edit button is present in all rows
                ];
            }
            header('Content-Type: application/json; charset=utf-8');
            echo '{"data": '.json_encode($data).'}';
            die();
        }
        //TEST_URL_STRING : http://localhost:8001/biller.php?FORM=ADDBILLER&billerName=VRC+Prospects+cc&Currency_symbol=32&Account_type=1
        if($_REQUEST['FORM'] == "ADDBILLER"){
            $ValidationTest = AddBillerValidation($_REQUEST['billerName'],$_REQUEST['Currency_symbol']);
            if ($ValidationTest == "PASS"){
                $ValidationTest = AddBiller($_REQUEST['billerName'],$_REQUEST['Currency_symbol']);
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
                    $stmt = $database->prepare("SELECT * FROM donnotec_biller WHERE id=? AND del = 0 AND user_id=?");
                    $stmt->bindParam(1, $_REQUEST['id']);
                    $stmt->bindParam(2, $_SESSION['user_id']);
                    $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
                    if ($result) {  // Check if a record was found in the database
                        $billerName = $result['biller_name'];
                        $database = new SQLite3('../private/database.db');
                        $stmt = $database->prepare("UPDATE donnotec_biller SET del = 1 WHERE id = ".$_REQUEST['biller_id']);
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
        if($_REQUEST['FORM'] == "SETBILLER"){
            // Validate form fields
            $output_notification = true;
            $output_notification_type = 'error'; 
            $output_notification_message = '';
            //Allow Estimate
            $Setting_allow_estimates = 0;
			if(isset($_REQUEST['Setting_allow_estimates'])){
				if($_REQUEST['Setting_allow_estimates'] == '1' || $_REQUEST['Setting_allow_estimates'] == 'on'){
					$Setting_allow_estimates = 1;
                    if (!isset($_REQUEST['Setting_prefix_estimate']) || trim($_REQUEST['Setting_prefix_estimate']) === '') {
                        $output_notification_message = 'Estimate prefix cannot be blank !<br><a href="'.str_replace("biller.php","biller_settings.php",$_SERVER['REQUEST_URI']).'">Back to form</a>';
                        $output_notification_type = 'error'; 
                    }else{
                        if (strlen($_REQUEST['Setting_prefix_estimate']) > 8 || !preg_match('/^[a-zA-Z]{1,8}$/', $_REQUEST['Setting_prefix_estimate'])) {
                            $output_notification_message = 'Estimate prefix Not valid !<br />Must be Alphabetic letters only !<br />Cannot be more than 8 characters<br /><a href="'.str_replace("biller.php","biller_settings.php",$_SERVER['REQUEST_URI']).'">Back to form</a>';
                            $output_notification_type = 'error'; 
                        }
				    }
			    }
            }

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
                /* */
                <?php //if (isset($_SERVER['REQUEST_URI'])) { echo "alert('".$_SERVER['REQUEST_URI']."');"; }; ?>
                <?php 
                    if ($output_notification){
                        echo "GrowlNotification.notify({title: '".$output_notification_type."!', description: '".$output_notification_message."',image: 'images/danger-outline.svg',type: '".$output_notification_type."',position: 'top-center',closeTimeout: 0});";
                        //echo "window.history.replaceState({}, '', window.location.href.split('?')[0]);";
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
    function AddBillerValidation($billerName,$Currency_symbol){
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
        return "PASS";
    }
    function AddBiller($billerName,$Currency_symbol){
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
        $stmt_insert = $database->prepare("INSERT INTO donnotec_biller (name, Currency_id, Currency_symbol, Currency_decimal_symbol, 
                           Currency_decimal_digit, Currency_digital_grouping, Currency_pos_format, Currency_neg_format,user_id) VALUES (:name, :currencyid, :currencySymbol, 
                            :currencyDecimalSymbol, :currencyDecimalDigit, :currencyDigitalGrouping, :posFormat, :negFormat, :user_id)");

        // Set the parameters for the prepared statement
        $stmt_insert->bindParam(':name', $billerName);
        $stmt_insert->bindParam(':currencyid', $Currency_symbol);
        $stmt_insert->bindParam(':currencySymbol', $Currency[$Currency_symbol][1]);
        $stmt_insert->bindParam(':currencyDecimalSymbol', $Currency[$Currency_symbol][2]);
        $stmt_insert->bindParam(':currencyDecimalDigit', $Currency[$Currency_symbol][3]);
        $stmt_insert->bindParam(':currencyDigitalGrouping', $Currency[$Currency_symbol][4]);
        $stmt_insert->bindParam(':posFormat', $Currency[$Currency_symbol][5]);
        $stmt_insert->bindParam(':negFormat', $Currency[$Currency_symbol][6]);
        $stmt_insert->bindParam(':user_id', $_SESSION['user_id']);

        // Execute the prepared statement with provided URL parameters and get last inserted row id
        if ($stmt_insert->execute()) {
            $billerId = $database->lastInsertRowID(); // Get the ID of the newly inserted record
            //function AddBillerAccounts($biller_id,$Account_num,$Account_name,$Account_description,$Category_account_id,$Category_account_name,$Sub_Account,$Sub_Account_Name,$Account_System_num,$Account_inherit,$isVisible,$sign){
            $LastROW = AddBillerAccounts($billerId,"B50","Inventory","In a business accounting context, the word inventory is commonly used to describe the goods and materials that a business holds for the ultimate purpose of resale.",1,0,1,"a",1,"p");
            $LastROW = AddBillerAccounts($billerId,"B60","Cash/Bank Account","Current Asset Account to keep record of all cash and bank accounts",1,0,2,"a",1,"p");
            $LastROW = AddBillerAccounts($billerId,"B70","Account Payable","Accounts payable is money owed by a business to its suppliers and shown on its Balance Sheet as a liability.",3,0,3,"l",1,"p");
            $LastROW = AddBillerAccounts($billerId,"#SC","No Supplier Category","Supplier Account Category None. Description : Suppliers with no account information",0,$LastROW,0,"l",0,"p");
            $LastROW1 = AddSupplierCAT("None","Supplier with no account information",$billerId,$LastROW);
            $LastROW = AddBillerAccounts($billerId,"#S","Supplier with no account information","Supplier Account : Supplier with no account information",0,$LastROW,0,"l",0,"p");
            $LastROW1 = AddSupplier("None","Supplier with no account information",$billerId,$LastROW1,$LastROW); //1331
            $LastROW2 = AddBillerAccounts($billerId,"5200","Retained Earnings","In accounting, retained earnings refers to the portion of net income which is retained by the corporation rather than distributed to its owners as dividends.",5,0,4,"e",1,"p");
            $LastROW = AddBillerAccounts($billerId,"5100","Capital Contrubition","Capital received from investors for stock, equal to capital stock plus contributed capital. also called contributed capital. also called paid-in capital.",5,0,5,"e",1,"p");
            $LastROW = AddBillerAccounts($billerId,"5400","Capital Account","In financial accounting, the capital account is one of the accounts in shareholders' equity. Sole proprietorships have a single capital account in the owner's equity. Partnerships maintain a capital account for each of the partners.",5,0,6,"e",1,"p");
            $LastROW3 = AddBillerAccounts($billerId,"M40","Net Income","In business, Net income also referred to as the bottom line, net profit, or net earnings is an entity's income minus expenses for an accounting period.",0,$LastROW2,7,"e",1,"p");
            $LastROW = AddBillerAccounts($billerId,"I35","Withdraw","Withdraw by business owner(s) of the companies earnings.",0,"",$LastROW2,"Retained Earnings",8,"e",1,"n");
            $LastROW5 = AddBillerAccounts($billerId,"I10","Revenue","In business, revenue or turnover is income that a company receives from its normal business activities, usually from the sale of goods and services to customers.",0,$LastROW3,9,"e",1,"p");
            $LastROW4 = AddBillerAccounts($billerId,"I25","Expenses","Technically, an expense is an event in which an asset is used up or a liability is incurred. In terms of the accounting equation, expenses reduce owners' equity.",0,$LastROW3,10,"e",1,"n");
            $LastROW = AddBillerAccounts($billerId,"I15","Cost of Goods Sold","Cost of goods sold (COGS) refer to the inventory costs of those goods a business has sold during a particular period. Costs are associated with particular goods using one of several formulas, including specific identification, first-in first-out (FIFO), or average cost.",0,$LastROW4,11,"e",1,"n");
            $LastROW = AddBillerAccounts($billerId,"B75","Tax Payable","At its simplest, a company&#39;s tax expense, or tax charge, as it sometimes called, is computed in by multiplying the income before tax number, as reported to shareholders, by the appropriate tax rate. In reality, the computation is typically considerably more complex due to things such as expenses considered not deductible by taxing authorities (&#34;add backs&#34;), the range of tax rates applicable to various levels of income, different tax rates in different jurisdictions, multiple layers of tax on income, and other issues",3,0,12,"l",1,"p");
            $LastROW = AddBillerAccounts($billerId,"B25","Deferred Income Tax","Temporary differences are differences between the carrying amount of an asset or liability recognized in the statements of financial position and the amount attributed to that asset or liability for tax which are temporary differences that will result in taxable amounts in determining taxable profit (tax loss) of future periods when the carrying amount of the asset or liability is recovered or settled; or deductible temporary differences, which are temporary differences that will result in deductible amounts in determining taxable profit (tax loss) of future periods when the carrying amount of the asset or liability is recovered or settled.",4,0,13,"l",1,"p");
            $LastROW = AddBillerAccounts($billerId,"1000","Sales","A sale is the act of selling a product or service in return for money or other compensation. It is an act of completion of a commercial activity.",0,$LastROW5,14,"e",1,"p");
            $LastROW = AddBillerAccounts($billerId,"I20","Allowance Uncollectible Accounts Expense","The allowance account is shown as an offset (contra) to gross accounts receivable in order to arrive at net accounts receivable. The net figure is the realizable value of the receivable",0,$LastROW4,15,"e",1,"n");
            $LastROW6 = AddBillerAccounts($billerId,"B55","Account Receivable","Accounts receivable also known as Debtors, is money owed to a business by its clients (customers) and shown on its balance sheet as an asset. It is one of a series of accounting transactions dealing with the billing of a customer for goods and services that the customer has ordered.",1,0,16,"a",1,"p");
            $LastROW7 = AddBillerAccounts($billerId,"#CC","No Client Category","Client Account Category None. Description : Clients with no account information",0,$LastROW6,0,"a",0,"p");
            $LastROW = AddClientCAT("None","Clients with no account information",$billerId, $LastROW);
            $LastROW8 = AddBillerAccounts($billerId,"#C","Clients with no account information","Client Account : Clients with no account information",0,$LastROW7,0,"a",0,"p");
            $LastROW = AddClient("None","None",$billerId, $LastROW,$LastROW8);
            $LastROW = AddBillerAccounts($billerId,"M13","Unallocated Account/Temporary account","Unallocated Account/Temporary account (one not included in financial statements) created to record disbursements or receipts associated with yet-unconcluded transactions until their conclusion, or discrepancies between totals of other accounts until their rectification or correct classification.",6,0,17,"e",1,"p");
            $LastROW = AddBillerAccounts($billerId,"9500","VAT Payable","Value Added Tax (VAT) is a consumption tax levied in many countries around the world, including member countries of the European Union. VAT is similar to sales tax in the United States; a portion of the sales price of a taxable item or service is charged to the consumer and forwarded to the taxation authority.",3,0,18,"l",1,"p");
            $LastROW = AddBillerAccounts($billerId,"2400","Discount Allowed","Discount Allowed for Clients",0,$LastROW4,19,"e",1,"n");
            $LastROW = AddBillerAccounts($billerId,"2450","Discount Received","Discount Received from Suppliers",0,$LastROW5,20,"e",1,"p");
            $LastROW = AddBillerAccounts($billerId,"3000","Accounting Fees","Expense",0,$LastROW4,0,"e",1,"n");
            $LastROW = AddBillerAccounts($billerId,"3050","Advertising","Expense",0,$LastROW4,0,"e",1,"n"); //2387
            $LastROW = AddBillerAccounts($billerId,"2850","Bad Debts Recovered","Revenue",0,$LastROW5,0,"e",1,"p");
            $LastROW = AddBillerAccounts($billerId,"3200","Bank Charges","Expense",0,$LastROW4,0,"e",1,"n"); 
            $LastROW = AddBillerAccounts($billerId,"5500","Bank Loans","Non-Current Liability",4,0,0,"l",1,"p");
            $LastROW = AddBillerAccounts($billerId,"3300","Computer Expenses","Expense",0,$LastROW4,0,"e",1,"n");  
            $LastROW = AddBillerAccounts($billerId,"3450","Depreciation","Expense",0,$LastROW4,0,"e",1,"n"); 
            $LastROW = AddBillerAccounts($billerId,"3650","Electricity and Water","Expense",0,$LastROW4,0,"e",1,"n");
            $LastROW = AddBillerAccounts($billerId,"6250","Equipment","Non-Current Asset",2,0,0,"a",1,"p"); 
            $LastROW = AddBillerAccounts($billerId,"6300","Furniture and Fittings","Non-Current Asset",2,0,0,"a",1,"p"); 
            $LastROW = AddBillerAccounts($billerId,"3800","General Expenses","Expense",0,$LastROW4,0,"e",1,"n");
            $LastROW = AddBillerAccounts($billerId,"3850","Insurance","Expense",0,$LastROW4,0,"e",1,"n");
            $LastROW = AddBillerAccounts($billerId,"4150","Motor Vehicle Expenses","Expense",0,$LastROW4,0,"e",1,"n");
            $LastROW = AddBillerAccounts($billerId,"1004","Other Sales","Revenue",0,$LastROW5,0,"e",1,"p");
            $LastROW = AddBillerAccounts($billerId,"4200","Printing and Stationery","Expense",0,$LastROW4,0,"e",1,"n");
            $LastROW = AddBillerAccounts($billerId,"4300","Rent Paid","Expense",0,$LastROW4,0,"e",1,"n");
            $LastROW = AddBillerAccounts($billerId,"4350","Repair and Maintenance","Expense",0,$LastROW4,0,"e",1,"n");
            $LastROW = AddBillerAccounts($billerId,"4400","Salaries and Wages","Expense",0,$LastROW4,0,"e",1,"n");
            $LastROW = AddBillerAccounts($billerId,"8600","Staff Loans","Current Asset",1,0,0,"a",1,"p");
            $LastROW = AddBillerAccounts($billerId,"4600","Telephone and Internet","Expense",0,$LastROW4,0,"e",1,"n");
            $LastROW = AddBillerAccounts($billerId,"4650","Travel and Accommodation","Expense",0,$LastROW4,0,"e",1,"n");
            return "PASS";
            //echo "The biller id for the newly inserted record is: " . $billerId;
        } else {
            return "An error occurred while creating Biller/Company.";
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
    function AddClient($account,$client_name,$biller_id, $category_id,$account_id){
        $database = new SQLite3('../private/database.db');
        $stmt = $database->prepare("INSERT INTO donnotec_client (account, client_name, biller_id, user_id, category_id,account_id)
    VALUES (:account, :client_name, :biller_id, :user_id, :category_id,:account_id)");
        $stmt->bindValue(':account', $account);
        $stmt->bindValue(':client_name', $client_name);
        $stmt->bindValue(':biller_id', $biller_id);
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
        $stmt->bindValue(':category_id', $category_id);
        $stmt->bindValue(':account_id', $account_id);
        if ($stmt->execute()) {
            return $database->lastInsertRowID();
        } else {
            return "An error occurred while creating Supplier None";
        }        
    }
    function AddClientCAT($cat_name,$cat_description,$biller_id, $account_id){
        $database = new SQLite3('../private/database.db');
        $stmt = $database->prepare("INSERT INTO donnotec_client_category (cat_name, cat_description, biller_id, user_id, account_id) 
        VALUES (:cat_name, :cat_description, :biller_id, :user_id, :account_id)");
        $stmt->bindValue(':cat_name', $cat_name);
        $stmt->bindValue(':cat_description', $cat_description);
        $stmt->bindValue(':biller_id', $biller_id);
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
        $stmt->bindValue(':account_id', $account_id);
        if ($stmt->execute()) {
            return $database->lastInsertRowID();
        } else {
            return "An error occurred while creating Client Category ".$name;
        }        
    }
    function AddSupplier($account,$supplier_name,$biller_id, $category_id,$account_id){
        $database = new SQLite3('../private/database.db');
        $stmt = $database->prepare("INSERT INTO donnotec_supplier (account, supplier_name, biller_id, user_id, category_id,account_id)
    VALUES (:account, :supplier_name, :biller_id, :user_id, :category_id,:account_id)");
        $stmt->bindValue(':account', $account);
        $stmt->bindValue(':supplier_name', $supplier_name);
        $stmt->bindValue(':biller_id', $biller_id);
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
        $stmt->bindValue(':category_id', $category_id);
        $stmt->bindValue(':account_id', $account_id);
        if ($stmt->execute()) {
            return $database->lastInsertRowID();
        } else {
            return "An error occurred while creating Supplier None";
        }        
    }
    function AddSupplierCAT($cat_name,$cat_description,$biller_id, $account_id){
        $database = new SQLite3('../private/database.db');
        $stmt = $database->prepare("INSERT INTO donnotec_supplier_category (cat_name, cat_description, biller_id, user_id, account_id) 
        VALUES (:cat_name, :cat_description, :biller_id, :user_id, :account_id)");
        $stmt->bindValue(':cat_name', $cat_name);
        $stmt->bindValue(':cat_description', $cat_description);
        $stmt->bindValue(':biller_id', $biller_id);
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
        $stmt->bindValue(':account_id', $account_id);
        if ($stmt->execute()) {
            return $database->lastInsertRowID();
        } else {
            return "An error occurred while creating Supplier Category ".$name;
        }        
    }
    function SetBiller($billerName,$Currency_symbol,$Account_type){
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