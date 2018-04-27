<?php
// Start PHP Session.
include("config.php");
session_start();

// Initialize all variables to null to prevent web app issues.
$MTurk_ID = $_SESSION['login_user'];

// Get current task and page information.
$result = mysqli_query($db, "SELECT TASK_ID, PAGE_ID, COMPLETED, CONSENT FROM USER WHERE ID = '$MTurk_ID';");
$row = mysqli_fetch_assoc($result);
$Task_ID = $row['TASK_ID'];
$Page_ID = $row['PAGE_ID'];
$Completed = $row['COMPLETED'];
$Consent = $row['CONSENT'];

if($Completed == 1){
  header("location: exit.php");
}
if($Consent == 1){
  header("location: survey.php");
} else if ($Consent == 0){
  header("location: optout.php");
}
if(($Task_ID == -1) And ($Page_ID == -1)){
  header("location: exit.php");
}

// If we are experiencing post back.
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // If the posting control was our login button..
  if (isset($_POST['submit'])) {
    // Save the MTurk_ID consent
    $sql = "UPDATE USER SET CONSENT = 1 WHERE ID = '$MTurk_ID'";
    $result = mysqli_query($db,$sql);
    $count = mysqli_num_rows($result);

 		header("location: survey.php");
 	} else if (isset($_POST['remit'])) {
    // Save the MTurk_ID consent
    $sql = "UPDATE USER SET CONSENT = 0, COMPLETED = 1 WHERE ID = '$MTurk_ID'";
    $result = mysqli_query($db,$sql);
    $count = mysqli_num_rows($result);

 		header("location: optout.php");
 	}
}

// Close connection
mysqli_close($db);
?>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"> 
    <title>Consent</title>
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
      <div class="span12">
          <form class="form-horizontal" action='' method="POST">
            <fieldset>
              <div>    
                <p>
                  [Insert Consent Text]
                </p>
              </div>
              <div class="control-group">
                <!-- Button -->
                <div class="controls">
                  <br>
                  <input class="btn btn-success" type = "submit" value = "Agree" name = "submit"/ style="width:100%; max-width: 200px;">
                  <br />
                  <br />
                  <input class="btn btn-danger" type = "submit" value = "Disagree" name = "remit" style="width:100%; max-width: 200px;"/>
                  <br />
                </div>
              </div>
            </fieldset>          
          </form>
      </div>
  </div>
</body>
</html>