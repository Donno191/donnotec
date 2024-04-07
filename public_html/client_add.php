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
    if (isset($_REQUEST['CATEGORY'])) {
        $database = new SQLite3('../private/database.db');
        $result = $database->query("SELECT * FROM donnotec_client_category WHERE biller_id = ".$_REQUEST['CATEGORY']." and del = 0 and user_id = '".$_SESSION['user_id']."'");
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) { // Use fetchArray() instead of fetch()
            echo "<option value='".$row['id']."' title='".$row['cat_description']."'>".$row['cat_name']."</option>";
        }        
        die();       
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
            .content form .form-group textarea{
                width:99%;
                box-sizing: border-box;
                vertical-align: middle;
                height:300px;
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
                $("#biller_id").change(function() {
                    $.ajax({url: 'client_add.php?CATEGORY=' + $(this).find('option:selected').val(), type: 'GET',success: function(data, status) {$('#category_id').html(data);}});
                });
                $.ajax({url: 'client_add.php?CATEGORY=' + $(this).find('option:selected').val(), type: 'GET',success: function(data, status) {$('#category_id').html(data);}});
                /* Prevent Form Submit with Validation attribute*/
                $('#ADDCLIENT').submit(function(e) {
		            var target = "FormValidation";
		            if (typeof window[target] == 'function') {
			            if(!window[target]()){
				            e.preventDefault();
				            return false;
			            }
		            }
	            });
                /*Form validation in Javascript pure for user aesthetics/experience main validation server-side*/
                $("#ADDCLIENT input, #ADDCLIENT textarea").keydown(function() {
	                if($(this).parent().hasClass("state-success")){
		                $(this).parent().removeClass("state-success");
	                }
	                if($(this).parent().hasClass("state-error")){
		                $(this).parent().removeClass("state-error");
		                $(this).parent().next().remove();
	                }
                });
                $("#ADDCLIENT input, #ADDCLIENT textarea").change(function() {
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
	            $("#ADDCLIENT em").each(function() {
		            if ( $(this).addClass("invalid") ){	
			            TestError = true;
		            }
	            });
	            if (TestError){
		            GrowlNotification.notify({title: 'Warning!', description: 'Please clear errors before submitting form.',image: 'images/danger-outline.svg',type: 'error',position: 'top-center',closeTimeout: 5000});
		            return false;
	            }
                var regex = '';
                regex = /^[A-Za-z0-9]{1,20}$/;
                $('input[name=account]').parent().addClass("state-success");
	            if ($('input[name=account]').val() == '' ){ ValidationVarible = CreateError('account',"Client Account cannot be blank !" ,ValidationVarible); };
	            if (!regex.test($('input[name=account').val()) ){ ValidationVarible = CreateError('account',"Client Account Not valid !<br />characters match a-z A-Z 0-9<br />not more than 20 characters" ,ValidationVarible); };

                regex = /^(?!\s)(?!.*\s$)(?=.*[a-zA-Z0-9])[a-zA-Z0-9 '~?!]{2,50}$/;
                $('input[name=client_name]').parent().addClass("state-success");
	            if ($('input[name=client_name]').val() == '' ){ ValidationVarible = CreateError('client_name',"Client Name cannot be blank !" ,ValidationVarible); };
	            if (!regex.test($('input[name=client_name').val()) ){ ValidationVarible = CreateError('client_name',"Client Name Not valid !<br />don't start with space<br />don't end with space<br />atleast one alpha or numeric character<br />characters match a-z A-Z 0-9 '~?! <br />minimum 2 characters<br />max 50 characters" ,ValidationVarible); };
                
                if(!ValidationVarible){ 
                    $('html, body').animate({ scrollTop: $('.state-error:first').offset().top}, 1000);
                }else{
                    $('input[name=extra]').val(encodeURIComponent( $('input[name=extra]').val() ));
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
                    <h2>Add Client Information</h2>
                    <!-- Button with fixed width -->
                    <a href="clients.php">Back</a>
                </div>
                <form action='/clients.php' id="ADDCLIENT">
                    <h3>Basic Information</h3>
                    <input type='hidden' name="FORM" value="ADDCLIENT">
                    <div class="form-group">
                        <label for="biller_id">Biller/Company</label>
                        <select name="biller_id" id="biller_id">
                        <?php
                            $database = new SQLite3('../private/database.db');
                            $result = $database->query("SELECT * FROM donnotec_biller WHERE del = 0 and user_id = '".$_SESSION['user_id']."'");
                            while ($row = $result->fetchArray(SQLITE3_ASSOC)) { echo "<option value='".$row['id']."' >".$row['name']."</option>"; };
                        ?>
                        </select>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="category_id">Client Category</label>
                        <select name="category_id" id="category_id">
                        </select>
                        <br>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="account">Client Account</label>
                        <input type="text" id="account" name="account" maxlength="20">
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="client_name">Client Name</label>
                        <input type="text" id="client_name" name="client_name" maxlength="50">
                    </div>
                    <h3>Extra Information</h3>
                    <div class="form-group">
                        <textarea id="extra" name="extra"></textarea>
                    </div>
                    <div class="form-submit">
                        <input type="submit" value="Submit Information">
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>

