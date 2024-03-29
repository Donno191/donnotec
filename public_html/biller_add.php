<!-- This file is part of Donnotec System - Small Business, licensed under the MIT License. -->
<!-- Copyright (C) <?php echo date("Y"); ?>, Donovan R Fourie, Donnotec -->
<!-- https://github.com/Donno191/donnotec -->
<!-- http://donnotec.com -->
<?php
    // This file is part of Donnotec System - Small Business, licensed under the MIT License. See the LICENSE file in the project root for full license information.
    // Copyright (C) 2024, Donovan R Fourie, Donnotec"
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
        <link href="css/light-theme.min.css" rel="stylesheet">
        <link href="css/dark-theme.min.css" rel="stylesheet">
        <link href="css/colored-theme.min.css" rel="stylesheet">
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
            /* Content STYLE */
            .content{
                width:100%;
                text-align: center;
            }
            /* Content_header */
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
            .invalid{
                color:red;
            }                              
        </style>
        <script src="javascript/jquery-3.7.1.min.js"></script>
        <script src="javascript/accounting.min.js"></script>
        <script src="javascript/validation.js"></script>
        <script src="javascript/growl-notification.min.js"></script>
        <script>
            /* Javascript onload */
            $( document ).ready(function() {
                /* Prevent Form Submit with Validation attribute*/
                $('#ADDBILLER').submit(function(e) {
		            var target = "FormValidation";
		            if (typeof window[target] == 'function') {
			            if(!window[target]()){
				            e.preventDefault();
				            return false;
			            }
		            }
	            });
                /* Default Currency Format Settings*/
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

                /*Form validation in Javascript pure for user aesthetics/experience main validation server-side*/
                $("#ADDBILLER input").keydown(function() {
	                if($(this).parent().hasClass("state-success")){
		                $(this).parent().removeClass("state-success");
	                }
	                if($(this).parent().hasClass("state-error")){
		                $(this).parent().removeClass("state-error");
		                $(this).parent().next().remove();
	                }
                });
                $("#ADDBILLER input").change(function() {
	                if($(this).parent().hasClass("state-success")){
		                $(this).parent().removeClass("state-success");
	                }
	                if($(this).parent().hasClass("state-error")){
		                $(this).parent().removeClass("state-error");
		                $(this).parent().next().remove();
	                }
                });
            });
            FormValidation = function(){
	            var ValidationVarible = true;
                var TestError = false;
	            $("#ADDBILLER em").each(function() {
		            if ( $(this).addClass("invalid") ){	
			            TestError = true;
		            }
	            });
	            if (TestError){
		            GrowlNotification.notify({title: 'Warning!', description: 'Please clear errors before submitting form.',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 5000});
		            return false;
	            }

                $('input[name=billerName]').parent().addClass("state-success");
	            if ($('input[name=billerName]').val() == '' ){ ValidationVarible = CreateError('billerName',"Biller/Company Name cannot be blank !" ,ValidationVarible); };
	            if (!BillerNameValidation($('input[name=billerName').val()) ){ ValidationVarible = CreateError('billerName',"Biller/Company Name Not valid !<br />don't start with space<br />don't end with space<br />atleast one alpha or numeric character<br />characters match a-z A-Z 0-9 '~?! <br />minimum 2 characters" ,ValidationVarible); };
                
                if(!ValidationVarible){ 
                    $('html, body').animate({ scrollTop: $('.state-error:first').offset().top}, 1000);
                }
                return ValidationVarible;
            };
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
                    <h2>Add Biller Information</h2>
                    <!-- Button with fixed width -->
                    <a href="biller.php">Back</a>
                </div>
                <form action='/biller.php' id="ADDBILLER">
                    <h3>Basic Information</h3>

                    <input type='hidden' name="FORM" value="ADDBILLER">

                    <div class="form-group">
                        <label for="billerName">Biller/Company Name</label>
                        <input type="text" id="billerName" name="billerName">
                    </div>

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
                            <option value="31" selected="selected">US Dollar ($)</option>
                            <option value="32">South African Rand (R)</option>
                        </select>
                        <br>
                    </div>
                    <div class="form-submit">
                        <input type="submit" value="Submit Information">
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
