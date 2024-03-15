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
        <link rel='icon' type='image/png' href='icon/donnotec.ico' />
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
                margin-top:5px;
                margin-right:15px;
                float:right;
            } 
            /* Form */
            .content form{
                width:950px;
                border: black solid 2px;
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
            }    
            .content form .form-submit input{
                margin-left:15px;
                margin-top:5px;
                margin-right:10px;
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
        <script type="text/javascript" src="javascript/jquery-3.7.1.min.js"></script>
        <script type="text/javascript" src="javascript/accounting.min.js"></script>
        <script type="text/javascript" src="javascript/validation.js"></script>
        <script type="text/javascript" src="javascript/growl-notification.min.js"></script>
        <script language="JavaScript" type="text/javascript">
            /* Javascript onload */
            $( document ).ready(function() {
                /* Prevent Form Submit with Validation attribute*/
                $('form').submit(function(e) {
		            var target = $(this).attr('validation');
		            if (typeof window[target] == 'function') {
			            if(!window[target]()){
				            e.preventDefault();
				            return false;
			            }
		            }
	            });
                /* Default Currency Format Settings*/
                accounting.settings = {
	                currency: {
		                symbol : "$",
		                format: "%s%v",
		                decimal : ".",
		                thousand: ",",
		                precision : 2
	                },
	                number: {
		                precision : 0,
		                thousand: ",",
		                decimal : "."
	                }
                };
                accounting.settings.currency.format = {
	                pos : "%s %v",
	                neg : "%s (%v)",
	                zero: "%s  -- "
                };
                /*Apply Default Currency Format*/
                accounting.settings.currency.symbol = document.getElementById('Currency_symbol').options[document.getElementById('Currency_symbol').selectedIndex].innerHTML;
                accounting.settings.currency.decimal = document.getElementById('Currency_decimal_symbol').options[document.getElementById('Currency_decimal_symbol').selectedIndex].innerHTML;
                accounting.settings.currency.precision = document.getElementById('Currency_decimal_digit').options[document.getElementById('Currency_decimal_digit').selectedIndex].innerHTML;
                if(document.getElementById('Currency_grouping_symbol').options[document.getElementById('Currency_grouping_symbol').selectedIndex].innerHTML == "(None)"){
	                accounting.settings.currency.thousand = '';
                }else if (document.getElementById('Currency_grouping_symbol').options[document.getElementById('Currency_grouping_symbol').selectedIndex].innerHTML == "(Space)"){
	                accounting.settings.currency.thousand = ' ';
                }else{
	                accounting.settings.currency.thousand = document.getElementById('Currency_grouping_symbol').options[document.getElementById('Currency_grouping_symbol').selectedIndex].innerHTML;
                };
                accounting.settings.currency.format.pos = document.getElementById('Currency_format_pos').options[document.getElementById('Currency_format_pos').selectedIndex].value;
                accounting.settings.currency.format.zero = document.getElementById('Currency_format_pos').options[document.getElementById('Currency_format_pos').selectedIndex].value;
                accounting.settings.currency.format.neg = document.getElementById('Currency_format_neg').options[document.getElementById('Currency_format_neg').selectedIndex].value;

                document.getElementById('Example_cur_pos').value = accounting.formatMoney(1234.578);
                document.getElementById('Example_cur_neg').value = accounting.formatMoney(-1234.578);
                /*Apply onchange Currency Format*/
                document.getElementById('Currency_symbol').onchange=function(){
	                accounting.settings.currency.symbol = document.getElementById('Currency_symbol').options[document.getElementById('Currency_symbol').selectedIndex].innerHTML;
	                document.getElementById('Example_cur_pos').value = accounting.formatMoney(1234.578);
	                document.getElementById('Example_cur_neg').value = accounting.formatMoney(-1234.578);
                };

                document.getElementById('Currency_decimal_symbol').onchange=function(){
	                accounting.settings.currency.decimal = document.getElementById('Currency_decimal_symbol').options[document.getElementById('Currency_decimal_symbol').selectedIndex].innerHTML;
	                document.getElementById('Example_cur_pos').value = accounting.formatMoney(1234.578);
	                document.getElementById('Example_cur_neg').value = accounting.formatMoney(-1234.578);
                };

                document.getElementById('Currency_decimal_digit').onchange=function(){
	                accounting.settings.currency.precision = document.getElementById('Currency_decimal_digit').options[document.getElementById('Currency_decimal_digit').selectedIndex].innerHTML;
	                document.getElementById('Example_cur_pos').value = accounting.formatMoney(1234.578);
	                document.getElementById('Example_cur_neg').value = accounting.formatMoney(-1234.578);
                };
                document.getElementById('Currency_grouping_symbol').onchange=function(){
                    if(document.getElementById('Currency_grouping_symbol').options[document.getElementById('Currency_grouping_symbol').selectedIndex].innerHTML == "(None)"){
	                    accounting.settings.currency.thousand = '';
                    }else if (document.getElementById('Currency_grouping_symbol').options[document.getElementById('Currency_grouping_symbol').selectedIndex].innerHTML == "(Space)"){
	                    accounting.settings.currency.thousand = ' ';
                    }else{
	                    accounting.settings.currency.thousand = document.getElementById('Currency_grouping_symbol').options[document.getElementById('Currency_grouping_symbol').selectedIndex].innerHTML;
                    }
	                document.getElementById('Example_cur_pos').value = accounting.formatMoney(1234.578);
	                document.getElementById('Example_cur_neg').value = accounting.formatMoney(-1234.578);
                };

                document.getElementById('Currency_format_pos').onchange=function(){
	                accounting.settings.currency.format.pos = document.getElementById('Currency_format_pos').options[document.getElementById('Currency_format_pos').selectedIndex].value;
	                accounting.settings.currency.format.zero = document.getElementById('Currency_format_pos').options[document.getElementById('Currency_format_pos').selectedIndex].value;
	                document.getElementById('Example_cur_pos').value = accounting.formatMoney(1234.578);
	                document.getElementById('Example_cur_neg').value = accounting.formatMoney(-1234.578);
                };

                document.getElementById('Currency_format_neg').onchange=function(){
	                accounting.settings.currency.format.neg = document.getElementById('Currency_format_neg').options[document.getElementById('Currency_format_neg').selectedIndex].value;
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
                <form action='/biller.php' id="ADDBILLER" validation='FormValidation'>
                    <h3>Basic Information</h3>

                    <input type='hidden' name="FORM" value="ADDBILLER"/>

                    <div class="form-group">
                        <label for="billerName">Biller/Company Name</label>
                        <input type="text" id="billerName" name="billerName"/>
                    </div>

                    <h3>Business Currency Format</h3>
                    <div class="form-group">
                        <label>Positive Currency</label>
                        <input type="text" name="Example_cur_pos" id="Example_cur_pos" disabled="" value="1234,57" maxlength="40"><br>
                    </div>
                    <div class="form-group">                   
                        <label>Negtive Currency</label>
                        <input type="text" name="Example_cur_neg" id="Example_cur_neg" disabled="" value="1234,57" maxlength="40"><br>
                    </div>   
                    <br /> 
                    <div class="form-group">
                        <label>Symbol</label>
                        <select class="element select medium" name="Currency_symbol" id="Currency_symbol">
                            <option value="17db">៛</option>
                            <option value="192">ƒ</option>
                            <option value="20a1">₡</option>
                            <option value="20a6">₦</option>
                            <option value="20a8">₨</option>
                            <option value="20a9">₩</option>
                            <option value="20aa">₪</option>
                            <option value="20ab">₫</option>
                            <option value="20ac">€</option>
                            <option value="20ad">₭</option>
                            <option value="20ae">₮</option>
                            <option value="20b1">₱</option>
                            <option value="20b4">₴</option>
                            <option value="20B9">₹</option>
                            <option value="20BA">₺</option>
                            <option value="24">$</option>
                            <option value="24,55">$U</option>
                            <option value="24,62">$b</option>
                            <option value="41,46,4e">AFN</option>
                            <option value="41,4c,4c">ALL</option>
                            <option value="41,4e,47">ANG</option>
                            <option value="41,52,53">ARS</option>
                            <option value="41,55,44">AUD</option>
                            <option value="41,57,47">AWG</option>
                            <option value="41,5a,4e">AZN</option>
                            <option value="414,438,43d">Дин</option>
                            <option value="42,2f,2e">B/.</option>
                            <option value="42,41,4d">BAM</option>
                            <option value="42,42,44">BBD</option>
                            <option value="42,47,4e">BGN</option>
                            <option value="42,4d,44">BMD</option>
                            <option value="42,4e,44">BND</option>
                            <option value="42,4f,42">BOB</option>
                            <option value="42,52,4c">BRL</option>
                            <option value="42,53,44">BSD</option>
                            <option value="42,57,50">BWP</option>
                            <option value="42,59,52">BYR</option>
                            <option value="42,5a,24">BZ$</option>
                            <option value="42,5a,44">BZD</option>
                            <option value="42,73">Bs</option>
                            <option value="43,24">C$</option>
                            <option value="43,41,44">CAD</option>
                            <option value="43,48,46">CHF</option>
                            <option value="43,4c,50">CLP</option>
                            <option value="43,4e,59">CNY</option>
                            <option value="43,4f,50">COP</option>
                            <option value="43,52,43">CRC</option>
                            <option value="43,55,50">CUP</option>
                            <option value="43,5a,4b">CZK</option>
                            <option value="434,435,43d">ден</option>
                            <option value="43b,432">лв</option>
                            <option value="43c,430,43d">ман</option>
                            <option value="44,4b,4b">DKK</option>
                            <option value="44,4f,50">DOP</option>
                            <option value="440,443,431">руб</option>
                            <option value="45,45,4b">EEK</option>
                            <option value="45,47,50">EGP</option>
                            <option value="45,55,52">EUR</option>
                            <option value="46,4a,44">FJD</option>
                            <option value="46,4b,50">FKP</option>
                            <option value="46,74">Ft</option>
                            <option value="47,42,50">GBP</option>
                            <option value="47,47,50">GGP</option>
                            <option value="47,48,43">GHC</option>
                            <option value="47,49,50">GIP</option>
                            <option value="47,54,51">GTQ</option>
                            <option value="47,59,44">GYD</option>
                            <option value="47,73">Gs</option>
                            <option value="48,4b,44">HKD</option>
                            <option value="48,4e,4c">HNL</option>
                            <option value="48,52,4b">HRK</option>
                            <option value="48,55,46">HUF</option>
                            <option value="49,44,52">IDR</option>
                            <option value="49,4c,53">ILS</option>
                            <option value="49,4d,50">IMP</option>
                            <option value="49,4e,52">INR</option>
                            <option value="49,52,52">IRR</option>
                            <option value="49,53,4b">ISK</option>
                            <option value="4a,24">J$</option>
                            <option value="4a,45,50">JEP</option>
                            <option value="4a,4d,44">JMD</option>
                            <option value="4a,50,59">JPY</option>
                            <option value="4b,10d">Kč</option>
                            <option value="4b,47,53">KGS</option>
                            <option value="4b,48,52">KHR</option>
                            <option value="4b,4d">KM</option>
                            <option value="4b,50,57">KPW</option>
                            <option value="4b,52,57">KRW</option>
                            <option value="4b,59,44">KYD</option>
                            <option value="4b,5a,54">KZT</option>
                            <option value="4c">L</option>
                            <option value="4c,41,4b">LAK</option>
                            <option value="4c,42,50">LBP</option>
                            <option value="4c,4b,52">LKR</option>
                            <option value="4c,52,44">LRD</option>
                            <option value="4c,54,4c">LTL</option>
                            <option value="4c,56,4c">LVL</option>
                            <option value="4c,65,6b">Lek</option>
                            <option value="4c,73">Ls</option>
                            <option value="4c,74">Lt</option>
                            <option value="4d,4b,44">MKD</option>
                            <option value="4d,4e,54">MNT</option>
                            <option value="4d,54">MT</option>
                            <option value="4d,55,52">MUR</option>
                            <option value="4d,58,4e">MXN</option>
                            <option value="4d,59,52">MYR</option>
                            <option value="4d,5a,4e">MZN</option>
                            <option value="4e,41,44">NAD</option>
                            <option value="4e,47,4e">NGN</option>
                            <option value="4e,49,4f">NIO</option>
                            <option value="4e,4f,4b">NOK</option>
                            <option value="4e,50,52">NPR</option>
                            <option value="4e,54,24">NT$</option>
                            <option value="4e,5a,44">NZD</option>
                            <option value="4f,4d,52">OMR</option>
                            <option value="50">P</option>
                            <option value="50,41,42">PAB</option>
                            <option value="50,45,4e">PEN</option>
                            <option value="50,48,50">PHP</option>
                            <option value="50,4b,52">PKR</option>
                            <option value="50,4c,4e">PLN</option>
                            <option value="50,59,47">PYG</option>
                            <option value="51">Q</option>
                            <option value="51,41,52">QAR</option>
                            <option value="52" selected="selected">R</option>
                            <option value="52,24">R$</option>
                            <option value="52,44,24">RD$</option>
                            <option value="52,4d">RM</option>
                            <option value="52,4f,4e">RON</option>
                            <option value="52,53,44">RSD</option>
                            <option value="52,55,42">RUB</option>
                            <option value="52,70">Rp</option>
                            <option value="53">S</option>
                            <option value="53,2f,2e">S/.</option>
                            <option value="53,41,52">SAR</option>
                            <option value="53,42,44">SBD</option>
                            <option value="53,43,52">SCR</option>
                            <option value="53,45,4b">SEK</option>
                            <option value="53,47,44">SGD</option>
                            <option value="53,48,50">SHP</option>
                            <option value="53,4f,53">SOS</option>
                            <option value="53,52,44">SRD</option>
                            <option value="53,56,43">SVC</option>
                            <option value="53,59,50">SYP</option>
                            <option value="54,48,42">THB</option>
                            <option value="54,52,4c">TRL</option>
                            <option value="54,54,24">TT$</option>
                            <option value="54,54,44">TTD</option>
                            <option value="54,56,44">TVD</option>
                            <option value="54,57,44">TWD</option>
                            <option value="55,41,48">UAH</option>
                            <option value="55,53,44">USD</option>
                            <option value="55,59,55">UYU</option>
                            <option value="55,5a,53">UZS</option>
                            <option value="56,45,46">VEF</option>
                            <option value="56,4e,44">VND</option>
                            <option value="58,43,44">XCD</option>
                            <option value="59,45,52">YER</option>
                            <option value="5a,24">Z$</option>
                            <option value="5a,41,52">ZAR</option>
                            <option value="5a,57,44">ZWD</option>
                            <option value="60b">؋</option>
                            <option value="6b,6e">kn</option>
                            <option value="6b,72">kr</option>
                            <option value="6c,65,69">lei</option>
                            <option value="70,2e">p.</option>
                            <option value="7a,142">zł</option>
                            <option value="a2">¢</option>
                            <option value="a3">£</option>
                            <option value="a5">¥</option>
                            <option value="e3f">฿</option>
                            <option value="fdfc">﷼</option>
                        </select><br>
                    </div>
                    <div class="form-group">
                        <label>Decimal symbol</label>
                        <select class="element select medium" name="Currency_decimal_symbol" id="Currency_decimal_symbol">
                            <option value="002C">,</option>
                            <option value="002E">.</option>
                            <option value="0027">’</option>
                            <option value="00B7">·</option>
                        </select><br>
                    </div>

                    <div class="form-group">
                        <label>Decimal digit</label>
    
                        <select class="element select medium" name="Currency_decimal_digit" id="Currency_decimal_digit">
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select><br>
                    </div>

                    <div class="form-group">
                        <label>Digital Grouping symbol</label>
                        <select class="element select medium" name="Currency_grouping_symbol" id="Currency_grouping_symbol">
                            <option value="">(None)</option>
                            <option value="0020">(Space)</option>
                            <option value="002C">,</option>
                            <option value="002E">.</option>
                            <option value="0027">’</option>
                            <option value="00B7">·</option>
                        </select><br>
                    </div>

                    <div class="form-group">
                        <label>Pos Currency Format</label>
                        <select class="element select medium" name="Currency_format_pos" id="Currency_format_pos">
                            <option value="%s%v">$1.1</option>
                            <option value="%v%s">1.1$</option>
                            <option value="%s %v" selected="selected">$ 1.1</option>
                            <option value="%v %s">1.1 $</option>
                        </select><br>
                    </div>
                    <div class="form-group">
                        <label>Neg Currency Format</label>
                        <select class="element select medium" name="Currency_format_neg" id="Currency_format_neg">
                            <option value="(%s%v)" selected="selected">($1.1)</option>
                            <option value="-%s%v">-$1,1</option><option value="%s-%v">$-1.1</option>
                            <option value="%s%v-">$1.1-</option><option value="(%v%s)">(1.1$)</option>
                            <option value="-%v%s">-1.1$</option><option value="%v-%s">1.1-$</option>
                            <option value="%v%s-">1.1$-</option><option value="-%v %s">-1.1 $</option>
                            <option value="-%s %v">-$ 1.1</option><option value="%v %s- ">1.1 $-</option>
                            <option value="%s %v-">$ 1.1-</option><option value="%s -%v">$ -1.1</option>
                            <option value="%v- %s">1.1- $</option><option value="(%s %v)">($ 1.1)</option>
                            <option value="(%v %s)">(1.1 $)</option>
                        </select><br>
                    </div>

                    <h3>Accounting Details</h3>
                    <div class="form-group">
                        <label>Business Type</label>
                        <select class="element select medium" name="Account_type" id="Account_type">
                            <option value="0">Sole proprietor/Partnership</option>
                            <option value="1">Corporation</option>
                        </select><br>
                    </div>

                    <div class="form-submit">
                        <input type="submit" value="Submit Information" />
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
