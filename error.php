<?php
// Start PHP Session.
include("config.php");
session_start();

// Close connection
mysqli_close($db);
?>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"> 
    <title>Error</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    
    <!-- Project Stylesheet -->
    <link rel="stylesheet" href="css/custom.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
</head>

<body bgcolor = "#FFFFFF">
  <!-- Navbar -->
  <?php include ('html/navbar.html'); ?>
  <!-- Where all the magic happens -->
  <div class="container">
    <div class="row">
        <div class="span12">
            <form class="form-horizontal" action='' method="POST">
              <fieldset>
                <div id="legend">
                  <legend class="">Error Encountered</legend>
                </div>
        				<div class="">
        					<p>An error was encountered. [Insert error text].</p>
        				</div>
              </fieldset>          
            </form>
        </div>
    </div>
  </div>
</body>
</html>