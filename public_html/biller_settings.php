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
                var JSON_VAT = [{  // replace with your own JSON data
                    "tax_des": "VAT",
                    "tax_per": 15
                }, {
                    "tax_des": "Export",
                    "tax_per": 0
                }];
                window.table_vat = Table_VAT(JSON_VAT);
                $("#Vat_del_model_submit").click(function(){
                    GrowlNotification.notify({title: 'Done', description: 'Tax type '+JSON_VAT[document.getElementById('Vat_del_index').value].tax_des+" has been Edited!",image: 'images/danger-outline.svg',type: 'success',position: 'top-center',closeTimeout: 5000});
                    JSON_VAT.splice(document.getElementById('Vat_del_index').value, 1);
                    $('#Vat_tag').text(JSON.stringify(JSON_VAT));
                    JSON_VAT_processed = processJsonData(JSON.stringify(JSON_VAT));
                    window.table_vat = Table_VAT(JSON_VAT);
                    $.modal.close();
                });
                $("#Vat_edit_model_submit").click(function(){
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
                    var RegEXP_amount = /^(?:\d*\.\d{1,2}|\d+)$/;
                    $.modal.close();
                });   
                $("#Vat_add_model_submit").click(function(){
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


                var jsonData1 = [{  // replace with your own JSON data
                    "equity_name": "Donovan R Fourie",
                    "equity_int": 100,
                    "action": "<button class='edit'>Edit</button><button class='delete'>Delete</button>"
                }];
    
                var table_equity = $('#Equity').DataTable({
                    info: false,
                    ordering: false,
                    paging: false,
                    data: jsonData1, // use the JSON data instead of a URL
                    columns: [
                        { title: 'Owner Name', data: 'equity_name' },
                        { title: 'Interest %', data: 'equity_int' },
                        { title: 'Action', data: 'action' }
                    ],
                    columnDefs: [
                        { target: 0, searchable: false, orderable: false },
                        { target: 1, searchable: false, orderable: false, width: '120px' },
                        { target: 2, searchable: false, orderable: false, width: '200px' }
                    ],
                    // remove the search functionality
                    searching: false
                });  

            }); 
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
            function processJsonData(jsonString) {
                // Iterate over each entry in the JSON array and add the "row_index" property
                var jsonString_temp = JSON.parse(jsonString);
                var newJsonData = [];
                for (var i = 0; i < jsonString_temp.length; i++) {
                    var rowIndex = newJsonData.length;
                    jsonString_temp[i]["index"] = rowIndex; // Add a new "row_index" property to each entry in the JSON array
                    jsonString_temp[i]["action"] = "<a class='edit' href='#Vat_edit_model' rel='modal:open' onclick='Vat_edit_onload("+rowIndex+");'>Edit</a><a class='del' href='#Vat_del_model' rel='modal:open' onclick='Vat_del_onload("+rowIndex+");'>Delete</a>";
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
                var JSON_VAT_processed = processJsonData(JSON.stringify(JSON_VAT));
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
                    <h2>Settings For</h2>
                    <!-- Button with fixed width -->
                    <a href="biller.php">Back</a>
                </div>
                
                <form action='/biller.php' id="SETBILLER">
                    <input type='hidden' name="FORM" value="SETBILLER">
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
                        <input type="text" name="Setting_job_slave_number">
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
                    <br><h3>Value Added Tax List</h3> 
                    <div class="form-group">
                    <textarea id="Vat_tag" name="Vat" style="display: none;"></textarea>
                    <table id="Vat" class="display" style="width:100%"></table>
                    <a class="add" href="#Vat_add_model" rel="modal:open">Add Tax Type</a>
                    </div>  
                    <br><br><h3>Owners Interest List (The sum of all owners must equal to 100)</h3> 
                    <div class="form-group">
                    <textarea id="Equity_tag" name="Equity" style="display: none;"></textarea>
                        <table id="Equity" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Owner Name</th>
                                    <th>Intrest %</th>
                                    <th class="table_action">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <button id="myBtn" class="add">Add Owner Intrest</button>
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
            </div>
        </div>
    </body>
</html>

