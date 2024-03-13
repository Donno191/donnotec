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
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel='icon' type='image/png' href='icon/donnotec.ico' />
        <title>Dashboard</title>
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
            .content form {
                display: flex;
                flex-direction: column;
                margin: 15px 0; /* Add spacing between form elements. */
            }
            .content label {
                font-weight: bold; /* Make the labels stand out. */
                margin-bottom: 8px; /* Add space between each label and its input field. */
            }
            .content input, #content select, #content textarea {
                width: 100%; /* Occupy full width of the container. */
                padding: 6px; /* Add some space around your inputs. */
                margin-top: 15px; /* Add spacing between each input field. */
                margin-bottom: 15px; /* Add spacing between each input field. */
            }
            /* Form Elements */
            .form_container {
                display: flex;
                justify-content: space-between;
            }

            .form_element {
                border: 1px solid black;
                width: calc(50% - 20px); /* Adjust the width and spacing as needed */
                margin: 10px;
            }

            @media (max-width: 800px) {
                .form_container {
                    flex-direction: column;
                }
            }
            .backbutton{
                width:auto;
                padding: 5px 10px;
                margin: 5px;
            }
        </style>
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
                <div class="form_container"><h2 class="form_element">Biller Information</h2><form class="form_element" id="" action="biller.php"><input class="backbutton" type="submit" value="Back" /></form></div>
                <form>
                    <h3>Basic Information</h3>

                    <label for="billerName">Biller/Company Name</label>
                    <input type="text" id="billerName" value="VRC Prospects"><br>
    
                    <h3>Business Currency Format</h3>
    
                    <label>Positive Currency</label>
                    <input type="text" name="Example_cur_pos" id="Example_cur_pos" disabled="" value="1234,57" maxlength="40"><br>
    
                    <label>Negtive Currency</label>
                    <input type="text" name="Example_cur_neg" id="Example_cur_neg" disabled="" value="1234,57" maxlength="40"><br>
    
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
    
                    <label>Decimal symbol</label>
                    <select class="element select medium" name="Currency_decimal_symbol" id="Currency_decimal_symbol">
                        <option value="002C">,</option>
                        <option value="002E">.</option>
                        <option value="0027">’</option>
                        <option value="00B7">·</option>
                    </select><br>
    
                    <label>Decimal digit</label>
    
                    <select class="element select medium" name="Currency_decimal_digit" id="Currency_decimal_digit">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select><br>
    
                    <label>Digital Grouping symbol</label>

                    <select class="element select medium" name="Currency_grouping_symbol" id="Currency_grouping_symbol">
                        <option value="">(None)</option>
                        <option value="0020">(Space)</option>
                        <option value="002C">,</option>
                        <option value="002E">.</option>
                        <option value="0027">’</option>
                        <option value="00B7">·</option>
                    </select><br>
    
                    <label>Pos Currency Format</label>
    
                    <select class="element select medium" name="Currency_format_pos" id="Currency_format_pos">
                        <option value="%s%v">$1.1</option>
                        <option value="%v%s">1.1$</option>
                        <option value="%s %v" selected="selected">$ 1.1</option>
                        <option value="%v %s">1.1 $</option>
                    </select><br>
    
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

                    <h3>Accounting Details</h3>
    
                    <label>Business Type</label>
    
                    <select class="element select medium" name="Account_type" id="Account_type">
                        <option value="0">Sole proprietor/Partnership</option>
                        <option value="1">Corporation</option>
                    </select><br>

                    <input type="submit" value="Submit Information">
                <form>
            </div>
        </div>
    </body>
</html>
