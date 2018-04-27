<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start PHP Session.
include("config.php");
session_start();

// Include function library and check consent status.
$basedir = realpath(__DIR__);
include($basedir . '/php/functions.php');

// Get Globals for page generation and posting
$MTurk_ID = $_SESSION['login_user'];

if ($MTurk_ID == ''){
  header("location: index.php");
}

$row = mysqli_fetch_assoc(mysqli_query($db, "SELECT ISNULL(MTURK_CODE) NULLVAR, MTURK_CODE FROM USER WHERE ID = '$MTurk_ID';"));
$Null = $row["NULLVAR"];
$DB_Token = $row['MTURK_CODE'];


if ($Null == '1'){
  $DB_Token = getRandomString(8);

  // Save the question & answer.
  $sql = "UPDATE USER SET MTURK_CODE = '$DB_Token', COMPLETED = 1 WHERE ID = '$MTurk_ID';";
  $result = mysqli_query($db,$sql) or die(mysqli_error($db));

  $Token = $DB_Token;
} else {
  $Token = $DB_Token;
}

// Close connection
mysqli_close($db);
?>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"> 
    <title>Done!</title>
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
                  <legend class="">Thank you for participating.</legend>
                </div>
                <div>
                  <p>
                    Your validation code is: <span style="color:red;"><?php echo $Token ?></span>
                    <br><br>
                    [Insert Exit Text]
                  </p>
                </div>
              </fieldset>          
            </form>
        </div>
    </div>
  </div>
</body>
</html>