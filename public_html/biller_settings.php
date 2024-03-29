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

    $Fail = False;
    $ERROR = "";
    if (isset($_REQUEST['biller_id'])) { //TEST is there biller_id
        if(is_numeric($_REQUEST['biller_id'])) { //TEST is biller_id a number 
            $database = new SQLite3('../private/database.db');
            $stmt = $database->prepare("SELECT * FROM donnotec_biller WHERE id=? AND del = 0 AND user_id=?");
            $stmt->bindParam(1, $_REQUEST['biller_id']);
            $stmt->bindParam(2, $_SESSION['user_id']);
            $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
            if ($result) {  // Check if a record was found in the database
                $billerName = $result['name'];
                if(isset($_REQUEST['Currency_id'])){$Currency_id = $_REQUEST['Currency_id'];}else{$Currency_id = $result['Currency_id'];}
                if(isset($_REQUEST['Time_zone'])){$Time_zone = $_REQUEST['Time_zone'];}else{$Time_zone = $result['Time_zone'];}
                //SLAVE Numbers
                if(isset($_REQUEST['Setting_request_slave_number'])){$Setting_request_slave_number = $_REQUEST['Setting_request_slave_number'];}else{$Setting_request_slave_number = $result['Setting_request_slave_number'];}
                if(isset($_REQUEST['Setting_job_slave_number'])){$Setting_job_slave_number = $_REQUEST['Setting_job_slave_number'];}else{$Setting_job_slave_number = $result['Setting_job_slave_number'];}
                if(isset($_REQUEST['Setting_invoice_slave_number'])){$Setting_invoice_slave_number = $_REQUEST['Setting_invoice_slave_number'];}else{$Setting_invoice_slave_number = $result['Setting_invoice_slave_number'];}                
                if(isset($_REQUEST['Setting_order_slave_number'])){$Setting_order_slave_number = $_REQUEST['Setting_order_slave_number'];}else{$Setting_order_slave_number = $result['Setting_order_slave_number'];}
                if(isset($_REQUEST['Setting_sinvoice_slave_number'])){$Setting_sinvoice_slave_number = $_REQUEST['Setting_sinvoice_slave_number'];}else{$Setting_sinvoice_slave_number = $result['Setting_sinvoice_slave_number'];}
                //ALLOW
                if(isset($_REQUEST['Setting_allow_estimates'])){$Setting_allow_estimates = $_REQUEST['Setting_allow_estimates'];}else{$Setting_allow_estimates = $result['Setting_allow_estimates'];}
                if(isset($_REQUEST['Setting_allow_proforma'])){$Setting_allow_proforma = $_REQUEST['Setting_allow_proforma'];}else{$Setting_allow_proforma = $result['Setting_allow_proforma'];}
                if(isset($_REQUEST['Setting_allow_quotation'])){$Setting_allow_quotation = $_REQUEST['Setting_allow_quotation'];}else{$Setting_allow_quotation = $result['Setting_allow_quotation'];}
                if(isset($_REQUEST['Setting_allow_delnote'])){$Setting_allow_delnote = $_REQUEST['Setting_allow_delnote'];}else{$Setting_allow_delnote = $result['Setting_allow_delnote'];}
                if(isset($_REQUEST['Setting_allow_jobcard'])){$Setting_allow_jobcard = $_REQUEST['Setting_allow_jobcard'];}else{$Setting_allow_jobcard = $result['Setting_allow_jobcard'];}
                if(isset($_REQUEST['Setting_allow_orders'])){$Setting_allow_orders = $_REQUEST['Setting_allow_orders'];}else{$Setting_allow_orders = $result['Setting_allow_orders'];}
                //Prefix
                if(isset($_REQUEST['Setting_prefix_estimate'])){$Setting_prefix_estimate = $_REQUEST['Setting_prefix_estimate'];}else{$Setting_prefix_estimate = $result['Setting_prefix_estimate'];}
                if(isset($_REQUEST['Setting_prefix_proforma'])){$Setting_prefix_proforma = $_REQUEST['Setting_prefix_proforma'];}else{$Setting_prefix_proforma = $result['Setting_prefix_proforma'];}
                if(isset($_REQUEST['Setting_prefix_quotation'])){$Setting_prefix_quotation = $_REQUEST['Setting_prefix_quotation'];}else{$Setting_prefix_quotation = $result['Setting_prefix_quotation'];}
                if(isset($_REQUEST['Setting_prefix_delnote'])){$Setting_prefix_delnote = $_REQUEST['Setting_prefix_delnote'];}else{$Setting_prefix_delnote = $result['Setting_prefix_delnote'];}
                if(isset($_REQUEST['Setting_prefix_jobcard'])){$Setting_prefix_jobcard = $_REQUEST['Setting_prefix_jobcard'];}else{$Setting_prefix_jobcard = $result['Setting_prefix_jobcard'];}
                if(isset($_REQUEST['Setting_prefix_invoice'])){$Setting_prefix_invoice = $_REQUEST['Setting_prefix_invoice'];}else{$Setting_prefix_invoice = $result['Setting_prefix_invoice'];}
                if(isset($_REQUEST['Setting_prefix_orders'])){$Setting_prefix_orders = $_REQUEST['Setting_prefix_orders'];}else{$Setting_prefix_orders = $result['Setting_prefix_orders'];}
                if(isset($_REQUEST['Setting_prefix_sinvoice'])){$Setting_prefix_sinvoice = $_REQUEST['Setting_prefix_sinvoice'];}else{$Setting_prefix_sinvoice = $result['Setting_prefix_sinvoice'];}
                //JSON
                if(isset($_REQUEST['Vat'])){$Vat = $_REQUEST['Vat'];}else{$Vat = $result['Setting_vat_list'];}
                if(isset($_REQUEST['Equity'])){$Equity = $_REQUEST['Equity'];}else{$Equity = $result['Setting_owner_list'];}
            } else {
                $Fail = True;
                $ERROR = "<b style='color:red;'>ERROR No matching records were found in the database.</b>";
            }                                       
        }else{
            $Fail = True;
            $ERROR = "<b style='color:red;'>ERROR No Biller/Company ID must be a number</b>";
        }
    } else {
        $Fail = True;
        $ERROR = "<b style='color:red;'>ERROR No Biller/Company ID was provided in the request.</b>";
    }      
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel='icon' type='image/png' href='icon/donnotec.ico'>
        <title>Dashboard</title>
        <link rel="stylesheet" href="css/datatables.min.css">
        <link href="css/light-theme.min.css" rel="stylesheet">
        <link href="css/dark-theme.min.css" rel="stylesheet">
        <link href="css/colored-theme.min.css" rel="stylesheet">
        <link href="css/jquery.modal.min.css" rel="stylesheet">
        <script src="javascript/jquery-3.7.1.min.js"></script>
        <script src="javascript/accounting.min.js"></script>
        <script src="javascript/datatables.min.js?v=123123123"></script>
        <script src="javascript/growl-notification.min.js"></script>
        <script src="javascript/jquery.modal.min.js.js"></script>
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
                width:100%;
                text-align: center;
            }
            .content_header{
                width:100%;
            }
            .content_header h2,.content_header a{
                display:inline-block;
            }  
            .content_header h2{
                margin-left:15px;
            }          
            .content_header a {
                background-color: #007BFF;
                color: #ffffff;
                border: none;
                cursor: pointer;
                padding: 10px 20px;
                border-radius: 5px; 
                font-size: 16px;
                transition: background-color 0.3s;
                text-align: center;
                text-decoration: none;
                cursor: pointer;
                margin-top:15px;
                margin-right:15px;
                float:right;
            } 
            /* Form */
            .content form{
                width:950px;
                margin: auto;
            }
            .content form h3{
                text-align:center;
                width:100%;
            }
            .content form .form-group{
                width:100%;
            }
            .content form .form-group label,.content form .form-group input{
                display:inline-block;
            }    
            .content form .form-group label{
                width:200px;
                text-align:right;
                margin-right: 10px;
            }  
            .content form .form-group p{
                text-align:Left;
                margin-left: 250px;
                display:block;
            } 
            .content form .form-group input, .content form .form-group select{
                width:auto;
                min-width: 700px;
                box-sizing: border-box;
                padding: 5px;
                vertical-align: middle;
            }     
            .content form .form-submit{
                margin:20px;  
                text-align: right;  
            }    
            .content form .form-submit input{
                background-color: #007BFF;
                color: #ffffff;
                border: none;
                cursor: pointer;
                padding: 10px 20px;
                border-radius: 5px;
                font-size: 16px; 
                transition: background-color 0.3s;  
            } 
            /* Table Buttons */
            a.add, a.edit, a.del{
                background-color: #007BFF;
                color: #ffffff;
                border: none;
                cursor: pointer;
                padding: 10px 20px;
                border-radius: 5px;
                font-size: 16px; 
                transition: background-color 0.3s;   
                float:left;     
                text-decoration: none;  
                margin-left:5px;        
            } 
            /* Validation */
            .invalid{
                color:red;
            }  
            /* Modal Dialog */
            .modal .form-group label, .modal .form-group input{
                display:inline-block;
            }   
            .modal .form-group label{
                width:150px;
                text-align:right;
                margin-right: 10px;
            }
            .modal .form-group input{
                width:auto;
                min-width: 250px;
                box-sizing: border-box;
                padding: 5px;
                vertical-align: middle;
            }  
            .modal button{
                background-color: #007BFF;
                color: #ffffff;
                border: none;
                cursor: pointer;
                padding: 10px 20px;
                border-radius: 5px;
                font-size: 16px; 
                transition: background-color 0.3s;   
                float:left;             
            }   
            .modal a.close {
                background-color: #C70039 ;
                color: #ffffff;
                border: none;
                cursor: pointer;
                padding: 10px 20px;
                border-radius: 5px;
                font-size: 16px; 
                transition: background-color 0.3s;   
                float:right; 
                text-decoration: none;            
            }                          
        </style>
        <script>  
            $(document).ready(function() {
                //Initialize Variables
                window.history.replaceState({}, '', window.location.href.split('?')[0]);
                <?php
                if($Setting_allow_estimates == "on" || $Setting_allow_estimates == "1"){
                    echo '$("input[name=Setting_allow_estimates]").prop("checked", true);';
                    echo '$("input[name=Setting_prefix_estimate]").prop("disabled", false);';
                    echo '$("input[name=Setting_prefix_estimate]").val("'.$Setting_prefix_estimate.'");';
                    echo '$("input[name=Setting_request_slave_number]").prop("disabled", false);';
                    echo '$("input[name=Setting_request_slave_number]").val("'.$Setting_request_slave_number.'");';
                }else{
                    echo '$("input[name=Setting_allow_estimates]").prop("checked", false);';
                    echo '$("input[name=Setting_prefix_estimate]").prop("disabled", true);';
                    echo '$("input[name=Setting_request_slave_number]").prop("disabled", true);';
                }
                if($Setting_allow_proforma == "on" || $Setting_allow_proforma == "1"){
                    echo '$("input[name=Setting_allow_proforma]").prop("checked", true);';
                    echo '$("input[name=Setting_prefix_proforma]").prop("disabled", false);';
                    echo '$("input[name=Setting_prefix_proforma]").val("'.$Setting_prefix_proforma.'");';
                    echo '$("input[name=Setting_request_slave_number]").prop("disabled", false);';
                    echo '$("input[name=Setting_request_slave_number]").val("'.$Setting_request_slave_number.'");';
                }else{
                    echo '$("input[name=Setting_allow_proforma]").prop("checked", false);';
                    echo '$("input[name=Setting_prefix_proforma]").prop("disabled", true);';
                    echo '$("input[name=Setting_request_slave_number]").prop("disabled", true);';
                }
                if($Setting_allow_quotation == "on" || $Setting_allow_quotation == "1"){
                    echo '$("input[name=Setting_allow_quotation]").prop("checked", true);';
                    echo '$("input[name=Setting_prefix_quotation]").prop("disabled", false);';
                    echo '$("input[name=Setting_prefix_quotation]").val("'.$Setting_prefix_quotation.'");';
                    echo '$("input[name=Setting_request_slave_number]").prop("disabled", false);';
                    echo '$("input[name=Setting_request_slave_number]").val("'.$Setting_request_slave_number.'");';
                }else{
                    echo '$("input[name=Setting_allow_quotation]").prop("checked", false);';
                    echo '$("input[name=Setting_prefix_quotation]").prop("disabled", true);';
                    echo '$("input[name=Setting_request_slave_number]").prop("disabled", true);';
                } 
                if($Setting_allow_delnote == "on" || $Setting_allow_delnote == "1"){
                    echo '$("input[name=Setting_allow_delnote]").prop("checked", true);';
                    echo '$("input[name=Setting_prefix_delnote]").prop("disabled", false);';
                    echo '$("input[name=Setting_prefix_delnote]").val("'.$Setting_prefix_delnote.'");';
                    echo '$("input[name=Setting_job_slave_number]").prop("disabled", false);';
                    echo '$("input[name=Setting_job_slave_number]").val("'.$Setting_job_slave_number.'");';
                }else{
                    echo '$("input[name=Setting_allow_delnote]").prop("checked", false);';
                    echo '$("input[name=Setting_prefix_delnote]").prop("disabled", true);';
                    echo '$("input[name=Setting_job_slave_number]").prop("disabled", true);';
                }   
                if($Setting_allow_jobcard == "on" || $Setting_allow_jobcard == "1"){
                    echo '$("input[name=Setting_allow_jobcard]").prop("checked", true);';
                    echo '$("input[name=Setting_prefix_jobcard]").prop("disabled", false);';
                    echo '$("input[name=Setting_prefix_jobcard]").val("'.$Setting_prefix_jobcard.'");';
                    echo '$("input[name=Setting_job_slave_number]").prop("disabled", false);';
                    echo '$("input[name=Setting_job_slave_number]").val("'.$Setting_job_slave_number.'");';
                }else{
                    echo '$("input[name=Setting_allow_jobcard]").prop("checked", false);';
                    echo '$("input[name=Setting_prefix_jobcard]").prop("disabled", true);';
                    echo '$("input[name=Setting_job_slave_number]").prop("disabled", true);';
                }   
                echo '$("input[name=Setting_prefix_invoice]").val("'.$Setting_prefix_invoice.'");';  
                echo '$("input[name=Setting_invoice_slave_number]").val("'.$Setting_invoice_slave_number.'");'; 
                if($Setting_allow_orders == "on" || $Setting_allow_orders == "1"){
                    echo '$("input[name=Setting_allow_orders]").prop("checked", true);';
                    echo '$("input[name=Setting_prefix_orders]").prop("disabled", false);';
                    echo '$("input[name=Setting_prefix_orders]").val("'.$Setting_prefix_orders.'");';
                    echo '$("input[name=Setting_order_slave_number]").prop("disabled", false);';
                    echo '$("input[name=Setting_order_slave_number]").val("'.$Setting_order_slave_number.'");';
                }else{
                    echo '$("input[name=Setting_allow_orders]").prop("checked", false);';
                    echo '$("input[name=Setting_prefix_orders]").prop("disabled", true);';
                    echo '$("input[name=Setting_order_slave_number]").prop("disabled", true);';
                }     
                echo '$("input[name=Setting_prefix_sinvoice]").val("'.$Setting_prefix_sinvoice.'");';  
                echo '$("input[name=Setting_sinvoice_slave_number]").val("'.$Setting_sinvoice_slave_number.'");';    
                echo '$("#Currency_symbol").prop("selectedIndex", '.$Currency_id.');';   
                echo '$("#System_time_zone").val("'.$Time_zone.'");';   
                echo "var JSON_VAT = JSON.parse('".$Vat."');";  
                echo "var JSON_Equity = JSON.parse('".$Equity."');";          
                ?>
                //ALLOW ESTIMATES
                if($("input[name='Setting_allow_estimates']").is(":checked")) { $("input[name='Setting_prefix_estimate']").prop("disabled", false);}else{ $("input[name='Setting_prefix_estimate']").prop("disabled", true);};
                $("input[name='Setting_allow_estimates']").on("change", function() {
                    if($("input[name='Setting_allow_estimates']").is(":checked")) {$("input[name='Setting_prefix_estimate']").prop("disabled", false);}else{$("input[name='Setting_prefix_estimate']").prop("disabled", true);};
                });
                //ALLOW PRO FORMA
                if($("input[name='Setting_allow_proforma']").is(":checked")) { $("input[name='Setting_prefix_proforma']").prop("disabled", false);}else{ $("input[name='Setting_prefix_proforma']").prop("disabled", true);};
                $("input[name='Setting_allow_proforma']").on("change", function() {
                    if($("input[name='Setting_allow_proforma']").is(":checked")) {$("input[name='Setting_prefix_proforma']").prop("disabled", false);}else{$("input[name='Setting_prefix_proforma']").prop("disabled", true);};
                });
                //ALLOW Quotation
                if($("input[name='Setting_allow_quotation']").is(":checked")) { $("input[name='Setting_prefix_quotation']").prop("disabled", false);}else{ $("input[name='Setting_prefix_quotation']").prop("disabled", true);};
                $("input[name='Setting_allow_quotation']").on("change", function() {
                    if($("input[name='Setting_allow_quotation']").is(":checked")) {$("input[name='Setting_prefix_quotation']").prop("disabled", false);}else{$("input[name='Setting_prefix_quotation']").prop("disabled", true);};
                });
                //ALLOW REQUEST AUTOINC
                if(!$("input[name='Setting_allow_estimates']").is(":checked") && !$("input[name='Setting_allow_proforma']").is(":checked") && !$("input[name='Setting_allow_quotation']").is(":checked")) {
                    $("input[name='Setting_request_slave_number']").prop("disabled", true);
                }else{
                    $("input[name='Setting_request_slave_number']").prop("disabled", false);
                }
                $("input[name='Setting_allow_estimates'], input[name='Setting_allow_proforma'], input[name='Setting_allow_quotation'] ").on("change", function() {
                    if(!$("input[name='Setting_allow_estimates']").is(":checked") && !$("input[name='Setting_allow_proforma']").is(":checked") && !$("input[name='Setting_allow_quotation']").is(":checked")) {
                        $("input[name='Setting_request_slave_number']").prop("disabled", true);
                    }else{
                        $("input[name='Setting_request_slave_number']").prop("disabled", false);
                    }
                }); 
                //ALLOW DELNOTE
                if($("input[name='Setting_allow_delnote']").is(":checked")) { $("input[name='Setting_prefix_delnote']").prop("disabled", false);}else{ $("input[name='Setting_prefix_delnote']").prop("disabled", true);};
                $("input[name='Setting_allow_delnote']").on("change", function() {
                    if($("input[name='Setting_allow_delnote']").is(":checked")) {$("input[name='Setting_prefix_delnote']").prop("disabled", false);}else{$("input[name='Setting_prefix_delnote']").prop("disabled", true);};
                });
                //ALLOW JOBCARD
                if($("input[name='Setting_allow_jobcard']").is(":checked")) { $("input[name='Setting_prefix_jobcard']").prop("disabled", false);}else{ $("input[name='Setting_prefix_jobcard']").prop("disabled", true);};
                $("input[name='Setting_allow_jobcard']").on("change", function() {
                    if($("input[name='Setting_allow_jobcard']").is(":checked")) {$("input[name='Setting_prefix_jobcard']").prop("disabled", false);}else{$("input[name='Setting_prefix_jobcard']").prop("disabled", true);};
                });
                //ALLOW DEL/JOB AUTOINC
                if(!$("input[name='Setting_allow_delnote']").is(":checked") && !$("input[name='Setting_allow_jobcard']").is(":checked") ) {
                    $("input[name='Setting_job_slave_number']").prop("disabled", true);
                }else{
                    $("input[name='Setting_job_slave_number']").prop("disabled", false);
                }
                $("input[name='Setting_allow_delnote'], input[name='Setting_allow_jobcard']").on("change", function() {
                    if(!$("input[name='Setting_allow_delnote']").is(":checked") && !$("input[name='Setting_allow_jobcard']").is(":checked") ) {
                        $("input[name='Setting_job_slave_number']").prop("disabled", true);
                    }else{
                        $("input[name='Setting_job_slave_number']").prop("disabled", false);
                    }
                }); 
                //ALLOW sOrders
                if($("input[name='Setting_allow_orders']").is(":checked")) { $("input[name='Setting_prefix_orders'], input[name='Setting_order_slave_number']").prop("disabled", false);}else{ $("input[name='Setting_prefix_orders'], input[name='Setting_order_slave_number']").prop("disabled", true);};
                $("input[name='Setting_allow_orders']").on("change", function() {
                    if($("input[name='Setting_allow_orders']").is(":checked")) {$("input[name='Setting_prefix_orders'], input[name='Setting_order_slave_number']").prop("disabled", false);}else{$("input[name='Setting_prefix_orders'], input[name='Setting_order_slave_number']").prop("disabled", true);};
                }); 

                let Currency = [
                    ["AUD","AUD$",".",2," ","%s%v","-%s%v"],
                    ["BGN","лв",",",2," ","%v %s","-%v %s"],
                    ["BRL","R$",".",2,".","%s%v","-%s%v"],
                    ["CAD","$",",",2," ","%v %s","-%v %s"],
                    ["CHF","CHF",".",2,"'","'%s %v','-%s %v"],
                    ["CNY","¥",".",2,",", "%s %v", "-%s %v"],
                    ["CZK","Kč",",",2," ","%v %s","-%v %s"],
                    ["DKK","kr.","",2,".", "%s%v","-%s%v"],
                    ["EUR","€",".",2," ","%s %v", "-%s %v"],
                    ["GBP","£",".",2," ","%s%v","-%s%v"],
                    ["HKD","HK$",".",2,",","%s%v","-%s%v"],
                    ["HRK","kn",",",2,".","%v %s","-%v %s"],
                    ["HUF","Ft",",",2," ","%v %s","-%v %s"],
                    ["IDR","Rp",".",0,".", "%s%v", "-%s%v"],
                    ["ILS","₪",".",2,",","%v %s", "-%v %s"],
                    ["INR","₹",".",2,",", "%s%v", "-%s%v"],
                    ["ISK","kr",",",2,".","%v %s", "-%v %s"],
                    ["JPY","¥",".",0,",", "%s %v", "-%s %v"],
                    ["KRW","₩",".",0,",","%s%v","-%s%v"],
                    ["MXN","Mex$",".",2," ","%s%v","-%s%v"],
                    ["MYR","RM",".",2,",", "%s %v", "-%s %v"],
                    ["NOK","kr",",",2," ","%v %s", "-%v %s"],
                    ["NZD","NZ$",".",2,",","%s%v", "-%s%v"],
                    ["PHP","₱",".",2,",","%s%v", "-%s%v"],
                    ["PLN","zł",".",2,",","%v %s", "-%v %s"],
                    ["RON","lei",",",2,".","%v %s", "-%v %s"],
                    ["RUB","₽.","",2," ","%s %v", "-%s %v"],
                    ["SEK","kr",",",2,"", "%v %s", "-%v %s"],
                    ["SGD","$",".",2,",","%s %v", "-%s %v"],
                    ["THB","฿",".",2," ","%s %v", "-%s %v"],
                    ["TRY","₺",",",2,".","%s%v", "-%s%v"],
                    ["USD","$",".",2,",","%s%v", "-%s%v"],
                    ["ZAR","R",".",2," ","%s %v", "-%s %v"]
                ];
                /*Apply Default Currency Format*/
                accounting.settings = {currency: {symbol : "$",format: "%s%v",decimal : ".",thousand: ",",precision : 2},number: {precision : 0,thousand: ",",decimal : "."}};
                accounting.settings.currency.format = {pos : "%s %v",neg : "%s (%v)",zero: "%s  -- "};
                document.getElementById('Example_cur_pos').value = accounting.formatMoney(1234.578);
                document.getElementById('Example_cur_neg').value = accounting.formatMoney(-1234.578);

                

                /*Apply onchange Currency Format*/
                document.getElementById('Currency_symbol').onchange=function(){
                    accounting.settings.currency.symbol = Currency[document.getElementById('Currency_symbol').selectedIndex][1];
                    accounting.settings.currency.decimal = Currency[document.getElementById('Currency_symbol').selectedIndex][2];
                    accounting.settings.currency.precision = Currency[document.getElementById('Currency_symbol').selectedIndex][3];
                    accounting.settings.currency.thousand = Currency[document.getElementById('Currency_symbol').selectedIndex][4];
                    accounting.settings.currency.format.pos = Currency[document.getElementById('Currency_symbol').selectedIndex][5];
                    accounting.settings.currency.format.zero = Currency[document.getElementById('Currency_symbol').selectedIndex][5];
                    accounting.settings.currency.format.neg = Currency[document.getElementById('Currency_symbol').selectedIndex][6];
	                
	                document.getElementById('Example_cur_pos').value = accounting.formatMoney(1234.578);
	                document.getElementById('Example_cur_neg').value = accounting.formatMoney(-1234.578);
                };

                window.table_vat = Table_VAT(JSON_VAT);
                $("#Vat_del_model_submit").click(function(){
                    ResetForm(); 
                    GrowlNotification.notify({title: 'Done', description: 'Tax type '+JSON_VAT[document.getElementById('Vat_del_index').value].tax_des+" has been deleted!",image: 'images/danger-outline.svg',type: 'success',position: 'top-center',closeTimeout: 5000});
                    JSON_VAT.splice(document.getElementById('Vat_del_index').value, 1);
                    $('#Vat_tag').text(JSON.stringify(JSON_VAT));
                    JSON_VAT_processed = processJsonData(JSON.stringify(JSON_VAT));
                    window.table_vat = Table_VAT(JSON_VAT);
                    $.modal.close();
                });
                $("#Vat_edit_model_submit").click(function(){
                    ResetForm(); 
                    var RegEXP_amount = /^(?:\d*\.\d{1,2}|\d+)$/;
                    if (document.getElementById('Vat_edit_amount').value == "" ){
                        GrowlNotification.notify({title: 'Warning!', description: 'Tax amount field cannot be blank!',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 8000});    
                        return false;
                    }else if(!RegEXP_amount.test(document.getElementById('Vat_edit_amount').value)){
                        GrowlNotification.notify({title: 'Warning!', description: 'Tax amount value invalid!<br>Must be valid number<br>Can be up to two decimal places<br>Ex. 123, 123.45, 123.50',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 5000});    
                        return false;
                    }else{
                        GrowlNotification.notify({title: 'Done', description: 'Tax type '+document.getElementById('Vat_edit_tax_des').value+" has been Edited!",image: 'images/danger-outline.svg',type: 'success',position: 'top-center',closeTimeout: 5000});
                        JSON_VAT[document.getElementById('Vat_edit_index').value].tax_per = document.getElementById('Vat_edit_amount').value;
                        $('#Vat_tag').text(JSON.stringify(JSON_VAT));
                        JSON_VAT_processed = processJsonData(JSON.stringify(JSON_VAT));
                        window.table_vat = Table_VAT(JSON_VAT);
                        document.getElementById('Vat_edit_tax_des').value = "";
                        document.getElementById('Vat_edit_amount').value = "";
                    }
                    $.modal.close();
                });   
                $("#Vat_add_model_submit").click(function(){
                    ResetForm(); 
                    var RegEXP_description = /^[- ':;,\./@\%\(\)a-zA-Z0-9]*$/;
                    var RegEXP_amount = /^(?:\d*\.\d{1,2}|\d+)$/;
                    function Duplication(Value){
                        if(JSON_VAT.length !=0){
                            for(var i=0; i<JSON_VAT.length; i++){
                                if(JSON_VAT[i]["tax_des"] == Value){
                                    return true;
                                }
                            }
                        }
                        return false;                      
                    }
                    if(document.getElementById('Vat_add_tax_des').value == ""){
                        GrowlNotification.notify({title: 'Warning!', description: 'Tax description field cannot be blank!',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 5000});    
                        return false;
                    }else if(Duplication(document.getElementById('Vat_add_tax_des').value)){
                        GrowlNotification.notify({title: 'Warning!', description: 'Tax description already exist!',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 5000});    
                        return false;
                    }else if(!RegEXP_description.test(document.getElementById('Vat_add_tax_des').value)){
                        GrowlNotification.notify({title: 'Warning!', description: 'Tax description value invalid!<br>Tax Description match with the following set: Dash (-)<br>Single quote (&apos;)<br>Colon (:)<br>Semicolon (;)<br>Comma (,)<br>Period / full stop (.)<br>Slash (/)<br>At symbol (@)<br>Percentage sign (%)<br>Left parenthesis (()<br>Right parenthesis ())<br>Uppercase and lowercase alphabets (A-Z and a-z)<br>Digits (0-9)<br>',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 8000});    
                        return false;
                    }else if (document.getElementById('Vat_add_amount').value == "" ){
                        GrowlNotification.notify({title: 'Warning!', description: 'Tax amount field cannot be blank!',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 8000});    
                        return false;
                    }else if(!RegEXP_amount.test(document.getElementById('Vat_add_amount').value)){
                        GrowlNotification.notify({title: 'Warning!', description: 'Tax amount value invalid!<br>Must be valid number<br>Can be up to two decimal places<br>Ex. 123, 123.45, 123.50',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 5000});    
                        return false;
                    }else{
                        GrowlNotification.notify({title: 'Done', description: 'Tax type '+document.getElementById('Vat_add_tax_des').value+" has been added!",image: 'images/danger-outline.svg',type: 'success',position: 'top-center',closeTimeout: 5000});
                        JSON_VAT.push({
                            "tax_des": document.getElementById('Vat_add_tax_des').value,
                            "tax_per": document.getElementById('Vat_add_amount').value
                        });
                        $('#Vat_tag').text(JSON.stringify(JSON_VAT));
                        JSON_VAT_processed = processJsonData(JSON.stringify(JSON_VAT));
                        window.table_vat = Table_VAT(JSON_VAT);
                        document.getElementById('Vat_add_tax_des').value = "";
                        document.getElementById('Vat_add_amount').value = "";
                    }
                    $.modal.close();
                });               
                window.table_equity = Table_Equity(JSON_Equity);

                $("#Equity_del_model_submit").click(function(){
                    GrowlNotification.notify({title: 'Done', description: 'Owner '+JSON_Equity[document.getElementById('Equity_del_index').value].equity_name+" has been deleted!",image: 'images/danger-outline.svg',type: 'success',position: 'top-center',closeTimeout: 5000});
                    JSON_Equity.splice(document.getElementById('Equity_del_index').value, 1);
                    $('#Equity_tag').text(JSON.stringify(JSON_Equity));
                    JSON_Equity_processed = processJsonData(JSON.stringify(JSON_Equity),"Equity");
                    window.table_equity = Table_Equity(JSON_Equity);
                    $.modal.close();
                });
                $("#Equity_edit_model_submit").click(function(){
                    var RegEXP_interest = /^(?:(?!.*\b\d{3}\b)(?:0\.\d{2}|[1-9]\d*(?:\.\d{1,2})?)|100)$/;
                    if (document.getElementById('Equity_edit_equity_int').value == "" ){
                        GrowlNotification.notify({title: 'Warning!', description: 'Interest value field cannot be blank!',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 8000});    
                        return false;
                    }else if(!RegEXP_interest.test(document.getElementById('Equity_edit_equity_int').value)){
                        GrowlNotification.notify({title: 'Warning!', description: 'Interest value invalid!<br>Interest value match with the following set:<br>Must be valid number<br>Can be up to two decimal places<br>Cannot be more that 100<br>Ex. 100, 75.45, 45.50',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 5000});    
                        return false;
                    }else{
                        GrowlNotification.notify({title: 'Done', description: 'Tax type '+document.getElementById('Equity_edit_equity_name').value+" has been Edited!",image: 'images/danger-outline.svg',type: 'success',position: 'top-center',closeTimeout: 5000});
                        JSON_Equity[document.getElementById('Equity_edit_index').value].equity_int = document.getElementById('Equity_edit_equity_int').value;
                        $('#Equity_tag').text(JSON.stringify(JSON_Equity));
                        JSON_Equity_processed = processJsonData(JSON.stringify(JSON_Equity),"Equity");
                        window.table_equity = Table_Equity(JSON_Equity);
                        document.getElementById('Equity_edit_equity_name').value = "";
                        document.getElementById('Equity_edit_equity_int').value = "";
                    }
                    $.modal.close();
                }); 
                $("#Equity_add_model_submit").click(function(){
                    var RegEXP_name = /^[a-z ,.'-]+$/i;
                    var RegEXP_interest = /^(?:(?!.*\b\d{3}\b)(?:0\.\d{2}|[1-9]\d*(?:\.\d{1,2})?)|100)$/;
                    function Duplication(Value){
                        if(JSON_Equity.length !=0){
                            for(var i=0; i<JSON_Equity.length; i++){
                                if(JSON_Equity[i]["equity_name"] == Value){
                                    return true;
                                }
                            }
                        }
                        return false;                      
                    }
                    if(document.getElementById('Equity_add_equity_name').value == ""){
                        GrowlNotification.notify({title: 'Warning!', description: 'Owner name field cannot be blank!',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 5000});    
                        return false;
                    }else if(Duplication(document.getElementById('Equity_add_equity_name').value)){
                        GrowlNotification.notify({title: 'Warning!', description: 'Owner name already exist!',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 5000});    
                        return false;
                    }else if(!RegEXP_name.test(document.getElementById('Equity_add_equity_name').value)){
                        GrowlNotification.notify({title: 'Warning!', description: 'Owners name is invalid!<br>Owwner name match with the following set: <br>Comma (,)<br>Period / full stop (.)<br>Single quote (&apos;)<br>Dash (-)<br>Single quote (&apos;)<br>Uppercase and lowercase alphabets (A-Z and a-z)<br>',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 8000});    
                        return false;
                    }else if (document.getElementById('Equaty_add_equity_int').value == "" ){
                        GrowlNotification.notify({title: 'Warning!', description: 'Interest field cannot be blank!',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 8000});    
                        return false;
                    }else if(!RegEXP_interest.test(document.getElementById('Equaty_add_equity_int').value)){
                        GrowlNotification.notify({title: 'Warning!', description: 'Interest value invalid!<br>Interest value match with the following set:<br>Must be valid number<br>Can be up to two decimal places<br>Cannot be 0<br>Cannot be more that 100<br>Ex. 100, 75.45, 45.50',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 5000});    
                        return false;
                    }else{
                        GrowlNotification.notify({title: 'Done', description: 'Owner '+document.getElementById('Equity_add_equity_name').value+" with interest "+document.getElementById('Equaty_add_equity_int').value+" has been added!",image: 'images/danger-outline.svg',type: 'success',position: 'top-center',closeTimeout: 5000});
                        JSON_Equity.push({
                            "equity_name": document.getElementById('Equity_add_equity_name').value,
                            "equity_int": document.getElementById('Equaty_add_equity_int').value
                        });
                        $('#Equity_tag').text(JSON.stringify(JSON_Equity));
                        JSON_Equity_processed = processJsonData(JSON.stringify(JSON_Equity));
                        window.table_equity = Table_Equity(JSON_Equity);
                        document.getElementById('Equity_add_equity_name').value = "";
                        document.getElementById('Equaty_add_equity_int').value = "";
                    }
                    $.modal.close();
                }); 
                /* Prevent Form Submit with Validation attribute*/
                $('#SETBILLER').submit(function(e) {
		            var target = "FormValidation";
		            if (typeof window[target] == 'function') {
			            if(!window[target]()){
				            e.preventDefault();
				            return false;
			            }
		            }
	            }); 
                $("#SETBILLER input:checkbox").change(function() {
                    ResetForm();                  
                });                
                $("#SETBILLER input").keydown(function() {
	                if($(this).parent().hasClass("state-success")){
		                $(this).parent().removeClass("state-success");
	                }
	                if($(this).parent().hasClass("state-error")){
		                $(this).parent().removeClass("state-error");
		                $(this).parent().next().remove();
	                }
                });
                $("#SETBILLER input").change(function() {
	                if($(this).parent().hasClass("state-success")){
		                $(this).parent().removeClass("state-success");
	                }
	                if($(this).parent().hasClass("state-error")){
		                $(this).parent().removeClass("state-error");
		                $(this).parent().next().remove();
	                }
                });                            
            });
            function ResetForm(){
                $("#SETBILLER em.invalid").each(function() {
                    $(this).remove();
                }); 
                $('.state-error').each(function() {
                    $(this).removeClass('state-error');
                });  
            }
            FormValidation = function(){
	            var ValidationVarible = true;
                var TestError = false;
	            $("#SETBILLER em").each(function() {
		            if ( $(this).addClass("invalid") ){	
			            TestError = true;
		            }
	            });
	            if (TestError){
                    $('html, body').animate({ scrollTop: $('.state-error:first').offset().top}, 1000);
		            GrowlNotification.notify({title: 'Warning!', description: 'Please clear errors before submitting form.',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 5000});
		            return false;
	            }

                var regex = '';
                // Estimate | Pro Forma | Quotation
                if ($("input[name=Setting_allow_estimates]").is(':checked')){
                    $('input[name=Setting_prefix_estimate]').parent().addClass("state-success");  
                    if ($('input[name=Setting_prefix_estimate]').val() == '' ){ ValidationVarible = CreateError('Setting_prefix_estimate',"Estimate prefix cannot be blank !" ,ValidationVarible); }; 
                    regex = /^[a-zA-Z]{1,8}$/;
                    if (!regex.test($('input[name=Setting_prefix_estimate]').val())){ValidationVarible = CreateError('Setting_prefix_estimate',"Estimate prefix Not valid !<br />Must be Alphabetic letters only !<br />Cannot be more than 8 characters<br />" ,ValidationVarible);}
                }
                if ($("input[name=Setting_allow_proforma]").is(":checked")){
                    $('input[name=Setting_prefix_proforma]').parent().addClass("state-success");  
                    if ($('input[name=Setting_prefix_proforma]').val() == '' ){ ValidationVarible = CreateError('Setting_prefix_proforma',"Pro forma prefix cannot be blank !" ,ValidationVarible); }; 
                    regex = /^[a-zA-Z]{1,8}$/;
                    if (!regex.test($('input[name=Setting_prefix_proforma]').val())){ValidationVarible = CreateError('Setting_prefix_proforma',"Pro forma prefix Not valid !<br />Must be Alphabetic letters only !<br />Cannot be more than 8 characters<br />" ,ValidationVarible);}
                }
                if ($("input[name=Setting_allow_quotation]").is(":checked")){
                    $('input[name=Setting_prefix_quotation]').parent().addClass("state-success");  
                    if ($('input[name=Setting_prefix_quotation]').val() == '' ){ ValidationVarible = CreateError('Setting_prefix_quotation',"Quotation prefix cannot be blank !" ,ValidationVarible); }; 
                    regex = /^[a-zA-Z]{1,8}$/;
                    if (!regex.test($('input[name=Setting_prefix_quotation]').val())){ValidationVarible = CreateError('Setting_prefix_quotation',"Quotation prefix Not valid !<br />Must be Alphabetic letters only !<br />Cannot be more than 8 characters<br />" ,ValidationVarible);}
                }                
                if ($("input[name=Setting_allow_estimates]").is(':checked') || $("input[name=Setting_allow_proforma]").is(":checked") || $("input[name=Setting_allow_quotation]").is(":checked")){
                    $('input[name=Setting_request_slave_number]').parent().addClass("state-success");  
                    if ($('input[name=Setting_request_slave_number]').val() == '' ){ ValidationVarible = CreateError('Setting_request_slave_number',"Request AUTOINCREMENT cannot be blank !" ,ValidationVarible); }; 
                    regex = /^[0-9]{1,6}$/;
                    if (!regex.test($('input[name=Setting_request_slave_number]').val())){ValidationVarible = CreateError('Setting_request_slave_number',"Request AUTOINCREMENT Not valid !<br />Must be numbers only !<br />Number from 0 to 999999<br />" ,ValidationVarible);}
                
                }
                //Delivery Note | Job Card
                if ($("input[name=Setting_allow_delnote]").is(':checked')){
                    $('input[name=Setting_prefix_delnote]').parent().addClass("state-success");  
                    if ($('input[name=Setting_prefix_delnote]').val() == '' ){ ValidationVarible = CreateError('Setting_prefix_delnote',"Delivery Note prefix cannot be blank !" ,ValidationVarible); }; 
                    regex = /^[a-zA-Z]{1,8}$/;
                    if (!regex.test($('input[name=Setting_prefix_delnote]').val())){ValidationVarible = CreateError('Setting_prefix_delnote',"Delivery Note prefix Not valid !<br />Must be Alphabetic letters only !<br />Cannot be more than 8 characters<br />" ,ValidationVarible);}
                }
                if ($("input[name=Setting_allow_jobcard]").is(":checked")){
                    $('input[name=Setting_prefix_jobcard]').parent().addClass("state-success");  
                    if ($('input[name=Setting_prefix_jobcard]').val() == '' ){ ValidationVarible = CreateError('Setting_prefix_jobcard',"Job Card prefix cannot be blank !" ,ValidationVarible); }; 
                    regex = /^[a-zA-Z]{1,8}$/;
                    if (!regex.test($('input[name=Setting_prefix_jobcard]').val())){ValidationVarible = CreateError('Setting_prefix_jobcard',"Job Card prefix Not valid !<br />Must be Alphabetic letters only !<br />Cannot be more than 8 characters<br />" ,ValidationVarible);}
                }                
                if ($("input[name=Setting_allow_delnote]").is(':checked') || $("input[name=Setting_allow_jobcard]").is(":checked")){
                    $('input[name=Setting_job_slave_number]').parent().addClass("state-success");  
                    if ($('input[name=Setting_job_slave_number]').val() == '' ){ ValidationVarible = CreateError('Setting_job_slave_number',"Job/Del AUTOINCREMENT cannot be blank !" ,ValidationVarible); }; 
                    regex = /^[0-9]{1,6}$/;
                    if (!regex.test($('input[name=Setting_job_slave_number]').val())){ValidationVarible = CreateError('Setting_job_slave_number',"Job/Del AUTOINCREMENT Not valid !<br />Must be numbers only !<br />Number from 0 to 999999<br />" ,ValidationVarible);}
                
                } 
                //Invoice
                $('input[name=Setting_prefix_invoice]').parent().addClass("state-success");  
                if ($('input[name=Setting_prefix_invoice]').val() == '' ){ ValidationVarible = CreateError('Setting_prefix_invoice',"Invoice prefix cannot be blank !" ,ValidationVarible); }; 
                regex = /^[a-zA-Z]{1,8}$/;
                if (!regex.test($('input[name=Setting_prefix_invoice]').val())){ValidationVarible = CreateError('Setting_prefix_invoice',"Invoice prefix Not valid !<br />Must be Alphabetic letters only !<br />Cannot be more than 8 characters<br />" ,ValidationVarible);}
                
                $('input[name=Setting_invoice_slave_number]').parent().addClass("state-success");  
                if ($('input[name=Setting_invoice_slave_number]').val() == '' ){ ValidationVarible = CreateError('Setting_invoice_slave_number',"Invoice AUTOINCREMENT cannot be blank !" ,ValidationVarible); }; 
                regex = /^[0-9]{1,6}$/;
                if (!regex.test($('input[name=Setting_invoice_slave_number]').val())){ValidationVarible = CreateError('Setting_invoice_slave_number',"Invoice AUTOINCREMENT Not valid !<br />Must be numbers only !<br />Number from 0 to 999999<br />" ,ValidationVarible);}
                
                //Supplier Order
                if ($("input[name=Setting_allow_orders]").is(':checked')){
                    $('input[name=Setting_prefix_orders]').parent().addClass("state-success");  
                    if ($('input[name=Setting_prefix_orders]').val() == '' ){ ValidationVarible = CreateError('Setting_prefix_orders',"Order prefix cannot be blank !" ,ValidationVarible); }; 
                    regex = /^[a-zA-Z]{1,8}$/;
                    if (!regex.test($('input[name=Setting_prefix_orders]').val())){ValidationVarible = CreateError('Setting_prefix_orders',"Order prefix prefix Not valid !<br />Must be Alphabetic letters only !<br />Cannot be more than 8 characters<br />" ,ValidationVarible);}
                    
                    $('input[name=Setting_order_slave_number]').parent().addClass("state-success");  
                    if ($('input[name=Setting_order_slave_number]').val() == '' ){ ValidationVarible = CreateError('Setting_order_slave_number',"Order AUTOINCREMENT cannot be blank !" ,ValidationVarible); }; 
                    regex = /^[0-9]{1,6}$/;
                    if (!regex.test($('input[name=Setting_order_slave_number]').val())){ValidationVarible = CreateError('Setting_order_slave_number',"Order AUTOINCREMENT Not valid !<br />Must be numbers only !<br />Number from 0 to 999999<br />" ,ValidationVarible);}
                }

                //Supplier Invoice
                $('input[name=Setting_prefix_sinvoice]').parent().addClass("state-success");  
                if ($('input[name=Setting_prefix_sinvoice]').val() == '' ){ ValidationVarible = CreateError('Setting_prefix_sinvoice',"Invoice prefix cannot be blank !" ,ValidationVarible); }; 
                regex = /^[a-zA-Z]{1,8}$/;
                if (!regex.test($('input[name=Setting_prefix_sinvoice]').val())){ValidationVarible = CreateError('Setting_prefix_sinvoice',"Invoice prefix Not valid !<br />Must be Alphabetic letters only !<br />Cannot be more than 8 characters<br />" ,ValidationVarible);}
                
                $('input[name=Setting_sinvoice_slave_number]').parent().addClass("state-success");  
                if ($('input[name=Setting_sinvoice_slave_number]').val() == '' ){ ValidationVarible = CreateError('Setting_sinvoice_slave_number',"Invoice AUTOINCREMENT cannot be blank !" ,ValidationVarible); }; 
                regex = /^[0-9]{1,6}$/;
                if (!regex.test($('input[name=Setting_sinvoice_slave_number]').val())){ValidationVarible = CreateError('Setting_sinvoice_slave_number',"Invoice AUTOINCREMENT Not valid !<br />Must be numbers only !<br />Number from 0 to 999999<br />" ,ValidationVarible);}
                
                //VAT LIST
                if ( $('#Vat_tag').text() == "" || $('#Vat_tag').text() == "[]" ){
                    ValidationVarible = CreateError('Vat',"Need atleast one tax type !" ,ValidationVarible,'textarea'); 
                } 
                var jsonObject = JSON.parse($('#Vat_tag').text() );
                var taxDesignationRegex = /^[- ':;,\./@\%\(\)a-zA-Z0-9]*$/;
                var taxPercentageRegex = /^(?:\d*\.\d{1,2}|\d+)$/;
                for (var i = 0; i < jsonObject.length; i++) {
                    // Check if the tax designation is blank
                    if (jsonObject[i].tax_des === '') {
                        ValidationVarible = CreateError('Vat',"Tax description cannot be blank." ,ValidationVarible,'textarea'); 
                    }
                    // Check if the tax designation matches the regular expression
                    if (!taxDesignationRegex.test(jsonObject[i].tax_des)) {
                        ValidationVarible = CreateError('Vat',"Tax description invalid." ,ValidationVarible,'textarea');
                    }
                    // Check if the tax percentage is blank
                    if (jsonObject[i].tax_per === '') {
                        ValidationVarible = CreateError('Vat',"Amount % cannot be blank." ,ValidationVarible,'textarea');
                    }

                    // Check if the tax percentage matches the regular expression
                    if (!taxPercentageRegex.test(jsonObject[i].tax_per)) {
                        ValidationVarible = CreateError('Vat',"Amount % invalid." ,ValidationVarible,'textarea');
                    }
                    // Check if the tax designation is unique within the array
                    for (var j = i + 1; j < jsonObject.length; j++) {
                        if (jsonObject[i].tax_des === jsonObject[j].tax_des) {
                            ValidationVarible = CreateError('Vat',"Tax description duplication." ,ValidationVarible,'textarea');
                        }
                    }
                }
                if ( $('#Equity_tag').text() == "" || $('#Equity_tag').text() == "[]" ){
                    ValidationVarible = CreateError('Equity',"Need atleast one Owner !" ,ValidationVarible,'textarea'); 
                } 
                //Equity List
                var jsonObject = JSON.parse($('#Equity_tag').text());
                var equityNameRegex = /^[a-z ,.'-]+$/i;
                var equityIntegerRegex = /^(?:(?!.*\b\d{3}\b)(?:0\.\d{2}|[1-9]\d*(?:\.\d{1,2})?)|100)$/;
                var totalEquityInt = 0;
                for (var i = 0; i < jsonObject.length; i++) {
                    // Check if the equity name is blank
                    if (jsonObject[i].equity_name === '') {
                        ValidationVarible = CreateError('Equity',"Owner name cannot be blank." ,ValidationVarible,'textarea');
                    }
                    // Check if the equity name matches the regular expression
                    if (!equityNameRegex.test(jsonObject[i].equity_name)) {
                        ValidationVarible = CreateError('Equity',"Owner name invalid." ,ValidationVarible,'textarea');
                    }
                    // Check if the equity name is unique within the array
                    for (var j = i + 1; j < jsonObject.length; j++) {
                        if (jsonObject[i].equity_name === jsonObject[j].equity_name) {
                            ValidationVarible = CreateError('Equity',"Owner name must be unique." ,ValidationVarible,'textarea');
                        }
                    }
                    // Check if the equity integer is blank
                    if (jsonObject[i].equity_int === '') {
                        ValidationVarible = CreateError('Equity',"Interest % cannot be blank." ,ValidationVarible,'textarea');
                    }
                    // Check if the equity integer matches the regular expression
                    if (!equityIntegerRegex.test(jsonObject[i].equity_int)) {
                        ValidationVarible = CreateError('Equity',"Interest % invalid." ,ValidationVarible,'textarea');
                    }

                    // Add the equity integer to the total
                    totalEquityInt += jsonObject[i].equity_int;
                }

                // Check if the total equity integer is equal to 100
                if (totalEquityInt !== 100) {
                    ValidationVarible = CreateError('Equity',"Total Interest % must be equal to 100." ,ValidationVarible,'textarea');
                }
                const account_names = [
                    'Inventory',
                    'Cash/Bank Account',
                    'Account Payable',
                    'Retained Earnings',
                    'Capital Contrubition',
                    'Capital Account',
                    'Net Income',
                    'Withdraw',
                    'Revenue',
                    'Expenses',
                    'Cost of Goods Sold',
                    'Tax Payable',
                    'Deferred Income Tax',
                    'Sales',
                    'Allowance Uncollectible Accounts Expense',
                    'Account Receivable',
                    'Unallocated Account/Temporary account',
                    'VAT Payable',
                    'Discount Allowed',
                    'Discount Received'
                ];
                regex = /^(?=.*[a-zA-Z])[a-zA-Z\s-\/]{0,100}$/;
                for (let i = 1; i <= 20; i++) {
                    if ($('input[name=Account_System_num_'+i+']').val() == '' ){
                        ValidationVarible = CreateError('Account_System_num_'+i, account_names[i-1]+" cannot be blank." ,ValidationVarible); 
                    }
                    if (!regex.test($('input[name=Account_System_num_'+i+']').val())){
                        ValidationVarible = CreateError('Account_System_num_'+i,account_names[i-1]+" Not valid !<br />Must be Alphabetic letters only !<br />No number<br />Characters allowed:<br>Space ( )<br>Hyphen (-)<br>Forward Slash (/)" ,ValidationVarible);
                    }
                
                }
                regex = /^[a-zA-Z0-9]{0,10}$/;
                for (let i = 1; i <= 20; i++) {
                    if ($('input[name=Account_System_anum_'+i+']').val() == '' ){
                        ValidationVarible = CreateError('Account_System_anum_'+i, account_names[i-1]+" cannot be blank." ,ValidationVarible); 
                    }
                    if (!regex.test($('input[name=Account_System_anum_'+i+']').val())){
                        ValidationVarible = CreateError('Account_System_anum_'+i,account_names[i-1]+" Not valid !<br />Must be Alphabetic letters and Numbers only !<br />No special characters allowed !<br>No more than 10 characters !" ,ValidationVarible);
                    }
                
                }                
                
                if(!ValidationVarible){
                    $('html, body').animate({ scrollTop: $('.state-error:first').offset().top}, 1000);
                }
                return ValidationVarible;
            };
            function CreateError(elem,msg,currentValidation,tag = 'input'){
	            if(!$(tag+"[name="+elem+"]").parent().hasClass("state-error")){
		            if($(tag+"[name="+elem+"]").parent().hasClass("state-success")){
			            $(tag+"[name="+elem+"]").parent().removeClass("state-success");
		            }
		            $(tag+"[name="+elem+"]").parent().addClass("state-error");
		            $("<em for="+elem+" class='invalid'>"+msg+"</em>").insertAfter($(tag+"[name="+elem+"]").parent());
	            }else{
		            if($(tag+"[name="+elem+"]").parent().hasClass("state-success")){
			            $(tag+"[name="+elem+"]").parent().removeClass("state-success");
		            }
	            }
	            return false;
            }
            function Equity_del_onload(index){
                var json = JSON.parse($('#Equity_tag').text());
                $("#Equity_del_index").val(index);
                $("#Equity_del_model h3").text('Delete Owner "'+json[document.getElementById('Equity_del_index').value].equity_name+'"');
            } 
            function Equity_edit_onload(index){
                $("#Equity_edit_index").val(index);
                var json = window.table_equity.rows().data().toArray();
                for (var i = 0; i < json.length; i++) {
                    if (json[i].index === index) {
                        $('#Equity_edit_equity_name').val(json[i].equity_name);
                        $('#Equity_edit_equity_int').val(json[i].equity_int);
                    }
                }
            } 
            function Table_Equity(JSON_EQUITY){
                $('#Equity').text('');
                $('#Equity_tag').text(JSON.stringify(JSON_EQUITY));
                var JSON_EQUITY_processed = processJsonData(JSON.stringify(JSON_EQUITY),"Equity");
                var table_equity = $('#Equity').DataTable({
                    info: false,
                    ordering: false,
                    paging: false,
                    data: JSON_EQUITY_processed,
                    columns: [
                        { title: 'ID', data: 'index' },
                        { title: 'Owner Name', data: 'equity_name' },
                        { title: 'Interest %', data: 'equity_int' },
                        { title: 'Action', data: 'action' }
                    ],
                    columnDefs: [
                        { target: 0, visible: false, searchable: false, orderable: false, width: '20px' },
                        { target: 1, searchable: false, orderable: false },
                        { target: 2, searchable: false, orderable: false, width: '120px' },
                        { target: 3, searchable: false, orderable: false, width: '200px' }
                    ],
                    // remove the search functionality
                    searching: false,
                    "bDestroy": true
                });
                return table_equity;               
            }               
            function Vat_del_onload(index){
                var json = JSON.parse($('#Vat_tag').text());
                $("#Vat_del_index").val(index);
                $("#Vat_del_model h3").text('Delete tax type "'+json[document.getElementById('Vat_del_index').value].tax_des+'"');
            }
            function Vat_edit_onload(index){
                $("#Vat_edit_index").val(index);
                var json = window.table_vat.rows().data().toArray();
                for (var i = 0; i < json.length; i++) {
                    if (json[i].index === index) {
                        $('#Vat_edit_tax_des').val(json[i].tax_des);
                        $('#Vat_edit_amount').val(json[i].tax_per);
                    }
                }
            }                 
            function processJsonData(jsonString,name) {
                // Iterate over each entry in the JSON array and add the "row_index" property
                var jsonString_temp = JSON.parse(jsonString);
                var newJsonData = [];
                for (var i = 0; i < jsonString_temp.length; i++) {
                    var rowIndex = newJsonData.length;
                    jsonString_temp[i]["index"] = rowIndex; // Add a new "row_index" property to each entry in the JSON array
                    jsonString_temp[i]["action"] = "<a class='edit' href='#"+name+"_edit_model' rel='modal:open' onclick='"+name+"_edit_onload("+rowIndex+");'>Edit</a><a class='del' href='#"+name+"_del_model' rel='modal:open' onclick='"+name+"_del_onload("+rowIndex+");'>Delete</a>";
                    newJsonData.push(jsonString_temp[i]); // Include this modified entry in the new JSON array
                }
                return newJsonData;
            }
            function unprocessJsonData(jsonString) {
                // Iterate over each entry in the JSON array and remove the "row_index" property
                var newJsonData = [];
                for (var i = 0; i < jsonString.length; i++) {
                    var tempObj = {};
                    for (var key in jsonString[i]) {
                        if (key !== "row_index" && key !== "action") {// Check if the current property is not "row_index"                        
                            tempObj[key] = jsonString[i][key]; // If it's not "row_index", include this property in a new object
                        }
                    }
                    newJsonData.push(tempObj); // Include this modified object (without the "row_index" property) in the new JSON array
                }
                return newJsonData;
            }
            function Table_VAT(JSON_VAT){
                $('#Vat').text('');
                $('#Vat_tag').text(JSON.stringify(JSON_VAT));
                var JSON_VAT_processed = processJsonData(JSON.stringify(JSON_VAT),"Vat");
                var table_vat = $('#Vat').DataTable({
                    info: false,
                    ordering: false,
                    paging: false,
                    data: JSON_VAT_processed,
                    columns: [
                        { title: 'ID', data: 'index' },
                        { title: 'Tax Description', data: 'tax_des' },
                        { title: 'Amount %', data: 'tax_per' },
                        { title: 'Action', data: 'action' }
                    ],
                    columnDefs: [
                        { target: 0, visible: false, searchable: false, orderable: false, width: '20px' },
                        { target: 1, searchable: false, orderable: false },
                        { target: 2, searchable: false, orderable: false, width: '120px' },
                        { target: 3, searchable: false, orderable: false, width: '200px' }
                    ],
                    // remove the search functionality
                    searching: false,
                    "bDestroy": true
                });
                return table_vat;
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
            <div class="content">
            <div class="content_header">
                    <?php if($Fail){
                        echo "<h2>".$ERROR."</h2>";
                    }else{
                        echo "<h2>Settings For <b>".$billerName."</b></h2>";
                    } ?>
                    <!-- Button with fixed width -->
                    <a href="biller.php">Back</a>
                </div>
                
                <form action='/biller.php' id="SETBILLER" <?php if($Fail){echo "style=display:none;";} ?> >
                    <input type='hidden' name="FORM" value="SETBILLER">
                    <input type='hidden' name="biller_id" value="<?php echo $_REQUEST['biller_id']; ?>">
                    <h3>Client Request Settings</h3>
                    <div class="form-group">
                        <label>Allow estimates </label>
                        <input type="checkbox" name="Setting_allow_estimates"><br>
                    </div> 
                    <br>
                    <div class="form-group">
                        <label>Estimate prefix</label>
                        <input type="text" name="Setting_prefix_estimate" maxlength="8">
                    </div>                   
                    <br>                   
                    <div class="form-group">
                        <label>Allow pro forma </label>
                        <input type="checkbox" name="Setting_allow_proforma"><br>
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Pro Forma prefix</label>
                        <input type="text" name="Setting_prefix_proforma" maxlength="8">
                    </div>                    
                    <br> 
                    <div class="form-group">
                        <label>Allow quotations </label>
                        <input type="checkbox" name="Setting_allow_quotation"><br>
                    </div>   
                    <br>
                    <div class="form-group">
                        <label>Quotation prefix</label>
                        <input type="text" name="Setting_prefix_quotation" maxlength="8">
                    </div> 
                    <br>
                    <div class="form-group">
                        <label>Request AUTOINCREMENT</label>
                        <input type="text" name="Setting_request_slave_number">
                        <p>If the value is 87900 you next Estimate/Quote/Pro Forma that you generate will have the number 87901 as reference</p>
                    </div>                                      
                    <br><h3>Client Job Card And Delivery Note Settings</h3>
                    <div class="form-group">
                        <label>Allow Delivery Notes </label>
                        <input type="checkbox" name="Setting_allow_delnote"><br>
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Delivery Note prefix</label>
                        <input type="text" name="Setting_prefix_delnote" maxlength="8">
                    </div>                    
                    <br> 
                    <div class="form-group">
                        <label>Allow Job Cards </label>
                        <input type="checkbox" name="Setting_allow_jobcard"><br>
                    </div>   
                    <br>
                    <div class="form-group">
                        <label>Job Card prefix</label>
                        <input type="text" name="Setting_prefix_jobcard" maxlength="8">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Job/Del AUTOINCREMENT</label>
                        <input type="text" name="Setting_job_slave_number">
                        <p>If the value is 87900 you next Job Card/Delivery Note that you generate will have the number 87901 as reference</p>
                    </div>                      
                    <br><h3>Client Invoice Settings</h3>
                    <div class="form-group">
                        <label>Invoice prefix</label>
                        <input type="text" name="Setting_prefix_invoice" maxlength="8">
                    </div> 
                    <br>
                    <div class="form-group">
                        <label>Invoice AUTOINCREMENT</label>
                        <input type="text" name="Setting_invoice_slave_number">
                        <p>If the value is 87900 you next Invoice that you generate will have the number 87901 as reference</p>
                    </div> 
                    <br><h3>Supplier Order Settings</h3>
                    <div class="form-group">
                        <label>Allow orders </label>
                        <input type="checkbox" name="Setting_allow_orders"><br>
                    </div>   
                    <br>
                    <div class="form-group">
                        <label>Order prefix</label>
                        <input type="text" name="Setting_prefix_orders" maxlength="8">
                    </div> 
                    <br>
                    <div class="form-group">
                        <label>Order AUTOINCREMENT</label>
                        <input type="text" name="Setting_order_slave_number">
                        <p >If the value is 87900 you next Estimate/Quote/Pro Forma that you generate will have the number 87901 as reference</p>
                    </div> 
                    <br><h3>Supplier Invoice Settings</h3>
                    <div class="form-group">
                        <label>Invoice prefix</label>
                        <input type="text" name="Setting_prefix_sinvoice" maxlength="8">
                    </div> 
                    <br>
                    <div class="form-group">
                        <label>Invoice AUTOINCREMENT</label>
                        <input type="text" name="Setting_sinvoice_slave_number">
                        <p>If the value is 87900 you next Invoice that you generate will have the number 87901 as reference</p>
                    </div>
                    <br>
                    <h3>Business Currency Format</h3>
                    <div class="form-group">
                        <label>Positive Currency</label>
                        <input type="text" name="Example_cur_pos" id="Example_cur_pos" disabled="" value="1234,57" maxlength="40"><br>
                    </div>
                    <br>
                    <div class="form-group">                   
                        <label>Negtive Currency</label>
                        <input type="text" name="Example_cur_neg" id="Example_cur_neg" disabled="" value="1234,57" maxlength="40"><br>
                    </div>   
                    <br> 
                    <br>
                    <div class="form-group">
                        <label>Symbol</label>
                        <select name="Currency_symbol" id="Currency_symbol">
                            <option value="0">Australian Dollar (A$)</option>
                            <option value="1">Bulgarian Lev (лв)</option>
                            <option value="2">Brazilian Real (R$)</option>
                            <option value="3">Canadian Dollar (C$)</option>
                            <option value="4">Swiss Franc (₣)</option>
                            <option value="5">Chinese Yuan (¥)</option>
                            <option value="6">Czech Koruna (Kč)</option>
                            <option value="7">Danish Krone (kr.)</option>
                            <option value="8">Euro (€)</option>
                            <option value="9">British Pound (£)</option>
                            <option value="10">Hong Kong Dollar (HK$)</option>
                            <option value="11">Croatian Kuna (kn)</option>
                            <option value="12">Hungarian Forint (Ft)</option>
                            <option value="13">Indonesian Rupiah (Rp)</option>
                            <option value="14">Israeli New Shekel (₪)</option>
                            <option value="15">Indian Rupee (₹)</option>
                            <option value="16">Icelandic Króna (kr)</option>
                            <option value="17">Japanese Yen (¥)</option>
                            <option value="18">South Korean Won (₩)</option>
                            <option value="19">Mexican Peso (Mex$)</option>
                            <option value="20">Malaysian Ringgit (RM)</option>
                            <option value="21">Norwegian Krone (kr)</option>
                            <option value="22">New Zealand Dollar (NZ$)</option>
                            <option value="23">Philippine Peso (₱)</option>
                            <option value="24">Polish Zloty (zł)</option>
                            <option value="25">Romanian Leu (lei)</option>
                            <option value="26">Russian Ruble (₽)</option>
                            <option value="27">Swedish Krona (kr)</option>
                            <option value="28">Singapore Dollar (S$)</option>
                            <option value="29">Thai Baht (฿)</option>
                            <option value="30">Turkish Lira (₺)</option>
                            <option value="31">US Dollar ($)</option>
                            <option value="32">South African Rand (R)</option>
                        </select>
                        <br>
                    </div>
                    <br><h3>Business Time Zone</h3> 
                    <div class="form-group">
                        <label>Time Zone</label>
                        <select name="System_time_zone" id="System_time_zone">
                            <option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
				            <option value="America/Adak">(GMT-10:00) Hawaii-Aleutian</option>
				            <option value="Etc/GMT+10">(GMT-10:00) Hawaii</option>
				            <option value="Pacific/Marquesas">(GMT-09:30) Marquesas Islands</option>
				            <option value="Pacific/Gambier">(GMT-09:00) Gambier Islands</option>
				            <option value="America/Anchorage">(GMT-09:00) Alaska</option>
				            <option value="America/Ensenada">(GMT-08:00) Tijuana, Baja California</option>
				            <option value="Etc/GMT+8">(GMT-08:00) Pitcairn Islands</option>
				            <option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
				            <option value="America/Denver">(GMT-07:00) Mountain Time (US & Canada)</option>
				            <option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
				            <option value="America/Dawson_Creek">(GMT-07:00) Arizona</option>
				            <option value="America/Belize">(GMT-06:00) Saskatchewan, Central America</option>
				            <option value="America/Cancun">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
				            <option value="Chile/EasterIsland">(GMT-06:00) Easter Island</option>
				            <option value="America/Chicago">(GMT-06:00) Central Time (US & Canada)</option>
				            <option value="America/New_York">(GMT-05:00) Eastern Time (US & Canada)</option>
				            <option value="America/Havana">(GMT-05:00) Cuba</option>
				            <option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
				            <option value="America/Caracas">(GMT-04:30) Caracas</option>
				            <option value="America/Santiago">(GMT-04:00) Santiago</option>
				            <option value="America/La_Paz">(GMT-04:00) La Paz</option>
				            <option value="Atlantic/Stanley">(GMT-04:00) Faukland Islands</option>
				            <option value="America/Campo_Grande">(GMT-04:00) Brazil</option>
				            <option value="America/Goose_Bay">(GMT-04:00) Atlantic Time (Goose Bay)</option>
				            <option value="America/Glace_Bay">(GMT-04:00) Atlantic Time (Canada)</option>
				            <option value="America/St_Johns">(GMT-03:30) Newfoundland</option>
				            <option value="America/Araguaina">(GMT-03:00) UTC-3</option>
				            <option value="America/Montevideo">(GMT-03:00) Montevideo</option>
				            <option value="America/Miquelon">(GMT-03:00) Miquelon, St. Pierre</option>
				            <option value="America/Godthab">(GMT-03:00) Greenland</option>
				            <option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires</option>
				            <option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
				            <option value="America/Noronha">(GMT-02:00) Mid-Atlantic</option>
				            <option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
				            <option value="Atlantic/Azores">(GMT-01:00) Azores</option>
				            <option value="Europe/Belfast">(GMT) Greenwich Mean Time : Belfast</option>
				            <option value="Europe/Dublin">(GMT) Greenwich Mean Time : Dublin</option>
				            <option value="Europe/Lisbon">(GMT) Greenwich Mean Time : Lisbon</option>
				            <option value="Europe/London">(GMT) Greenwich Mean Time : London</option>
				            <option value="Africa/Abidjan">(GMT) Monrovia, Reykjavik</option>
				            <option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
				            <option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
				            <option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
				            <option value="Africa/Algiers">(GMT+01:00) West Central Africa</option>
				            <option value="Africa/Windhoek">(GMT+01:00) Windhoek</option>
				            <option value="Asia/Beirut">(GMT+02:00) Beirut</option>
				            <option value="Africa/Cairo">(GMT+02:00) Cairo</option>
				            <option value="Asia/Gaza">(GMT+02:00) Gaza</option>
				            <option value="Africa/Blantyre" >(GMT+02:00) Harare, Pretoria</option>
				            <option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
				            <option value="Europe/Minsk">(GMT+02:00) Minsk</option>
				            <option value="Asia/Damascus">(GMT+02:00) Syria</option>
				            <option value="Europe/Moscow">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
				            <option value="Africa/Addis_Ababa">(GMT+03:00) Nairobi</option>
				            <option value="Asia/Tehran">(GMT+03:30) Tehran</option>
				            <option value="Asia/Dubai">(GMT+04:00) Abu Dhabi, Muscat</option>
				            <option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
				            <option value="Asia/Kabul">(GMT+04:30) Kabul</option>
				            <option value="Asia/Yekaterinburg">(GMT+05:00) Ekaterinburg</option>
				            <option value="Asia/Tashkent">(GMT+05:00) Tashkent</option>
				            <option value="Asia/Kolkata">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
				            <option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
				            <option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
				            <option value="Asia/Novosibirsk">(GMT+06:00) Novosibirsk</option>
				            <option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
				            <option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
				            <option value="Asia/Krasnoyarsk">(GMT+07:00) Krasnoyarsk</option>
				            <option value="Asia/Hong_Kong">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
				            <option value="Asia/Irkutsk">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
				            <option value="Australia/Perth">(GMT+08:00) Perth</option>
				            <option value="Australia/Eucla">(GMT+08:45) Eucla</option>
				            <option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
				            <option value="Asia/Seoul">(GMT+09:00) Seoul</option>
				            <option value="Asia/Yakutsk">(GMT+09:00) Yakutsk</option>
				            <option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
				            <option value="Australia/Darwin">(GMT+09:30) Darwin</option>
				            <option value="Australia/Brisbane">(GMT+10:00) Brisbane</option>
				            <option value="Australia/Hobart">(GMT+10:00) Hobart</option>
				            <option value="Asia/Vladivostok">(GMT+10:00) Vladivostok</option>
				            <option value="Australia/Lord_Howe">(GMT+10:30) Lord Howe Island</option>
				            <option value="Etc/GMT-11">(GMT+11:00) Solomon Is., New Caledonia</option>
				            <option value="Asia/Magadan">(GMT+11:00) Magadan</option>
				            <option value="Pacific/Norfolk">(GMT+11:30) Norfolk Island</option>
				            <option value="Asia/Anadyr">(GMT+12:00) Anadyr, Kamchatka</option>
				            <option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
				            <option value="Etc/GMT-12">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
				            <option value="Pacific/Chatham">(GMT+12:45) Chatham Islands</option>
                            <option value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa</option>
                            <option value="Pacific/Kiritimati">(GMT+14:00) Kiritimati</option>
                        </select>
                        <br>
                    </div>

                    <br><br><h3>Value Added Tax List</h3> 
                    <div class="form-group">
                        <textarea id="Vat_tag" name="Vat" style="display: none;"></textarea>
                        <table id="Vat" class="display" style="width:100%"></table>
                        <a class="add" href="#Vat_add_model" rel="modal:open">Add Tax Type</a>
                    </div> 

                    <br><br><h3>Owners Interest List (The sum of all owners must equal to 100)</h3> 
                    <div class="form-group">
                        <textarea id="Equity_tag" name="Equity" style="display: none;"></textarea>
                        <table id="Equity" class="display" style="width:100%"></table>
                        <a class="add" href="#Equity_add_model" rel="modal:open">Add Owner Intrest</a>
                    </div>

                    <br><h3>System Account Names</h3>                   
                    <div class="form-group">
                        <label>Inventory</label>
                        <input type="text" name="Account_System_num_1" value="Inventory">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Cash/Bank Account</label>
                        <input type="text" name="Account_System_num_2" value="Cash/Bank Account">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Account Payable</label>
                        <input type="text" name="Account_System_num_3" value="Account Payable">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Retained Earnings</label>
                        <input type="text" name="Account_System_num_4" value="Retained Earnings">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Capital Contrubition</label>
                        <input type="text" name="Account_System_num_5" value="Capital Contrubition">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Capital Account</label>
                        <input type="text" name="Account_System_num_6" value="Capital Account">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Net Income</label>
                        <input type="text" name="Account_System_num_7" value="Net Income">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Withdraw</label>
                        <input type="text" name="Account_System_num_8" value="Withdraw">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Revenue</label>
                        <input type="text" name="Account_System_num_9" value="Revenue">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Expenses</label>
                        <input type="text" name="Account_System_num_10" value="Expenses">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Cost of Goods Sold</label>
                        <input type="text" name="Account_System_num_11" value="Cost of Goods Sold">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Tax Payable</label>
                        <input type="text" name="Account_System_num_12" value="Tax Payable">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Deferred Income Tax</label>
                        <input type="text" name="Account_System_num_13" value="Deferred Income Tax">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Sales</label>
                        <input type="text" name="Account_System_num_14" value="Sales">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Allowance Uncollectible Accounts Expense</label>
                        <input type="text" name="Account_System_num_15" value="Allowance Uncollectible Accounts Expense">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Account Receivable</label>
                        <input type="text" name="Account_System_num_16" value="Account Receivable">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Unallocated Account/Temporary account</label>
                        <input type="text" name="Account_System_num_17" value="Unallocated Account/Temporary account">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>VAT Payable</label>
                        <input type="text" name="Account_System_num_18" value="VAT Payable">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Discount Allowed</label>
                        <input type="text" name="Account_System_num_19" value="Discount Allowed">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Discount Received</label>
                        <input type="text" name="Account_System_num_20" value="Discount Received">
                    </div>
                    <br>
                    <br><h3>System Account Number</h3>                   
                    <div class="form-group">
                        <label>Inventory</label>
                        <input type="text" name="Account_System_anum_1" value="SYS001">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Cash/Bank Account</label>
                        <input type="text" name="Account_System_anum_2" value="SYS002">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Account Payable</label>
                        <input type="text" name="Account_System_anum_3" value="SYS003">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Retained Earnings</label>
                        <input type="text" name="Account_System_anum_4" value="SYS004">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Capital Contrubition</label>
                        <input type="text" name="Account_System_anum_5" value="SYS005">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Capital Account</label>
                        <input type="text" name="Account_System_anum_6" value="SYS006">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Net Income</label>
                        <input type="text" name="Account_System_anum_7" value="SYS007">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Withdraw</label>
                        <input type="text" name="Account_System_anum_8" value="SYS008">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Revenue</label>
                        <input type="text" name="Account_System_anum_9" value="SYS009">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Expenses</label>
                        <input type="text" name="Account_System_anum_10" value="SYS010">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Cost of Goods Sold</label>
                        <input type="text" name="Account_System_anum_11" value="SYS011">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Tax Payable</label>
                        <input type="text" name="Account_System_anum_12" value="SYS012">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Deferred Income Tax</label>
                        <input type="text" name="Account_System_anum_13" value="SYS013">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Sales</label>
                        <input type="text" name="Account_System_anum_14" value="SYS014">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Allowance Uncollectible Accounts Expense</label>
                        <input type="text" name="Account_System_anum_15" value="SYS015">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Account Receivable</label>
                        <input type="text" name="Account_System_anum_16" value="SYS016">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Unallocated Account/Temporary account</label>
                        <input type="text" name="Account_System_anum_17" value="SYS017">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>VAT Payable</label>
                        <input type="text" name="Account_System_anum_18" value="SYS018">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Discount Allowed</label>
                        <input type="text" name="Account_System_anum_19" value="SYS019">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Discount Received</label>
                        <input type="text" name="Account_System_anum_20" value="SYS020">
                    </div>
                    <br><br>
                    <div class="form-submit">
                        <input type="submit" value="Submit Information">
                    </div>                    
                </form>
                <div id="Vat_add_model" class="modal">
                    <h3>Add tax type</h3>
                    <div class="form-group">
                        <label>Tax description</label>
                        <input type="text" id="Vat_add_tax_des">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="text" id="Vat_add_amount">
                    </div>
                    <br>
                    <button id="Vat_add_model_submit">Add Tax Type</button>
                </div> 
                <div id="Vat_edit_model" class="modal">
                    <h3>Edit tax type</h3>
                    <input type="hidden" id="Vat_edit_index" value="">
                    <div class="form-group">
                        <label>Tax description</label>
                        <input type="text" id="Vat_edit_tax_des" disabled>
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="text" id="Vat_edit_amount">
                    </div>
                    <br>
                    <button id="Vat_edit_model_submit">Edit Tax Type</button>
                </div>                 
                <div id="Vat_del_model" class="modal">
                    <h3>Delete tax type []</h3>
                    <input type="hidden" id="Vat_del_index" value="">
                    <button id="Vat_del_model_submit">Yes</button>
                    <a class="close" href="#" rel="modal:close">NO</a>
                </div>
                <div id="Equity_add_model" class="modal">
                    <h3>Add Owner interest</h3>
                    <div class="form-group">
                        <label>Owner Name</label>
                        <input type="text" id="Equity_add_equity_name">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Interest %</label>
                        <input type="text" id="Equaty_add_equity_int">
                    </div>
                    <br>
                    <button id="Equity_add_model_submit">Add Owner</button>
                </div>
                <div id="Equity_edit_model" class="modal">
                    <h3>Edit Owner interest</h3>
                    <input type="hidden" id="Equity_edit_index" value="">
                    <div class="form-group">
                        <label>Owner Name</label>
                        <input type="text" id="Equity_edit_equity_name" disabled>
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Interest %</label>
                        <input type="text" id="Equity_edit_equity_int">
                    </div>
                    <br>
                    <button id="Equity_edit_model_submit">Edit Owner interest</button>
                </div>
                <div id="Equity_del_model" class="modal">
                    <h3>Delete tax type []</h3>
                    <input type="hidden" id="Equity_del_index" value="">
                    <button id="Equity_del_model_submit">Yes</button>
                    <a class="close" href="#" rel="modal:close">NO</a>
                </div>   
            </div>
        </div>
    </body>
</html>