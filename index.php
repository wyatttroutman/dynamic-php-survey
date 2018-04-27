<?php
// Start PHP Session.
include("config.php");
session_start();

// Initialize all variables to null to prevent web app issues.
$MTurk_ID = null;
$MAX_TASK_ID = null;
$MAX_PAGE_ID = null;

// If we are experiencing post back.
if($_SERVER["REQUEST_METHOD"] == "POST") {
  // If the posting control was our login button..
  if (isset($_POST['submit'])) {
    // MTurk_ID and password sent from form 
    $MTurk_ID = mysqli_real_escape_string($db,$_POST['MTurk_ID']);

    // Select user from the user table where their input matches table data.
    $result = mysqli_query($db, "SELECT ID FROM USER WHERE ID = '$MTurk_ID' LIMIT 1;") or die(mysqli_error($db));
    $row = mysqli_fetch_assoc($result);
    $ID = $row['ID'];

    $result = mysqli_query($db, "SELECT TASK_ID MAX_TASK_ID, PAGE_ID MAX_PAGE_ID FROM TASK_PAGE_QUESTION ORDER BY TASK_ID DESC, PAGE_ID DESC LIMIT 1;") or die(mysqli_error($db));
    $row = mysqli_fetch_assoc($result);
    $MAX_TASK_ID = $row['MAX_TASK_ID'];
    $MAX_PAGE_ID = $row['MAX_PAGE_ID'];

    // If result matched $myMTurk_ID and $mypassword, table row must be 1 row
    if($ID == $MTurk_ID) {
      // If we have 1 row, it was a successful login. Set session variables and redirect to the dashboard.
      $_SESSION['login_user'] = $MTurk_ID;
      $_SESSION['MAX_TASK_ID'] = $MAX_TASK_ID;
      $_SESSION['MAX_PAGE_ID'] = $MAX_PAGE_ID;
      header("location: consent.php");
    } else {
      // Redirect user to register page.
      $_SESSION['login_user'] = $MTurk_ID;
      $_SESSION['MAX_TASK_ID'] = $MAX_TASK_ID;
      $_SESSION['MAX_PAGE_ID'] = $MAX_PAGE_ID;
      header("location: register.php");
    }
  }
}

// Close connection
mysqli_close($db);
?>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"> 
    <title>Survey Login</title>
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
    <div class="jumbotron">
            <h1>[Survey Title]</h1> 
            <p>Use the form below to enter your Amazon Mechnical Turk ID and begin the survey.</p> 
    </div>
    <div class="span12">
        <form class="form-horizontal" action='' method="POST">
          <fieldset>
            <div id="legend">
              <legend class="">Login</legend>
            </div>
            <div class="control-group">
              <!-- MTurk_ID -->
              <label class="control-label"  for="MTurk_ID">MTurk ID</label>
              <div class="controls">
                <input type="text" id="MTurk_ID" name="MTurk_ID" placeholder="" class="input-xlarge">
              </div>
            </div>
            <div class="control-group">
              <!-- Button -->
              <div class="controls">
                <br>
                <input class="btn btn-success" type = "submit" value = "Submit" name = "submit"/><br />
              </div>
            </div>
          </fieldset>          
        </form>
    </div>
  </div>
</body>
</html>