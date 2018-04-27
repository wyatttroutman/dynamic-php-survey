<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Start PHP Session.
include("config.php");
session_start();

// Include function library and check consent status.
$basedir = realpath(__DIR__);
include($basedir . '/php/functions.php');
//checkConsent();

// Initialize all variables as null to prevent web app issues.
$key = '';
$value = '';
$ID = '';
$success = '';
$Task_ID = '';
$Page_ID = '';
$control_type = '';
$question_id = '';
$question_text = '';
$output = '';
$header = '';
$HTML = '';
$COLOR_1 = 'red';
// Get Globals for page generation and posting
$MTurk_ID = $_SESSION['login_user'];

// Get current task and page information.
$result = mysqli_query($db, "SELECT TASK_ID, PAGE_ID, COMPLETED, CONSENT FROM USER WHERE ID = '$MTurk_ID';");
$row = mysqli_fetch_assoc($result);
$Task_ID = $row['TASK_ID'];
$Page_ID = $row['PAGE_ID'];
$Completed = $row['COMPLETED'];
$Consent = $row['CONSENT'];
$MAX_TASK_ID = $_SESSION['MAX_TASK_ID'];
$MAX_PAGE_ID = $_SESSION['MAX_PAGE_ID'];

if($Completed == 1){
  header("location: exit.php");
}
if($Consent == -1){
  header("location: consent.php");
} else if ($Consent == 0){
  header("location: optout.php");
}
if(($Task_ID == -1) And ($Page_ID == -1)){
  header("location: exit.php");
}

$button_text = 'Next';
if(($Task_ID == $MAX_TASK_ID) And ($Page_ID == $MAX_PAGE_ID)){
  $button_text = 'Finish';
}


// Handle POST request
if($_SERVER["REQUEST_METHOD"] == "POST") {
  // Loop through posting controls and save data.
  if (isset($_POST['submit'])) {
    // Loop through each control in form.
    $flag = '1';
    foreach($_POST as $key => $value){
      // If the control ID contains the substring 'survey_control', access it.
      if (strstr($key, 'survey_control_ID')){
        // First, get question type

        // Radio
        if ((strstr($key, 'Rsurvey_control_ID')) Or (strstr($key, 'Csurvey_control_ID'))){
          // Pull the ID of the control.
          $QID = mysqli_real_escape_string($db, str_replace('Rsurvey_control_ID_Q','',$key));
          $QID = str_replace('Csurvey_control_ID_Q','',$QID);
          $AID = mysqli_real_escape_string($db, $value);

          // Save the question & answer.
          $sql = "REPLACE INTO USER_TASK_QUESTION_ANSWER (USER_ID, TASK_ID, QUESTION_ID, ANSWER_ID) VALUES ('$MTurk_ID', $Task_ID, $QID, $AID);";
          $result = mysqli_query($db,$sql) or die(mysqli_error($db));

        // Text Input
        } else if (strstr($key, 'Tsurvey_control_ID')){
          // Pull the ID of the control.
          $QID = mysqli_real_escape_string($db, str_replace('Tsurvey_control_ID_Q','',$key));
          $AID = mysqli_real_escape_string($db, htmlspecialchars($value));

          // Save the question & answer.
          $sql = "REPLACE INTO USER_TASK_QUESTION_TEXT (USER_ID, TASK_ID, QUESTION_ID, ANSWER_TEXT) VALUES ('$MTurk_ID', $Task_ID, $QID, '$AID');";
          $result = mysqli_query($db,$sql) or die(mysqli_error($db));
        } 
      }
    }

    echo 'Going into function..';
    incrementPage($MTurk_ID, $Task_ID, $Page_ID);
    header("location: survey.php");

  }
// If not post, then it is a normal page load. Generate control lists.
} else {
  // Generate list of controls
  $sql = "SELECT * FROM PROGRAM_QUESTIONS WHERE TASK_ID = $Task_ID AND PAGE_ID = $Page_ID ORDER BY T_SORT_ORDER, PAGE_ID, Q_SORT_ORDER;";
  if($result = mysqli_query($db, $sql)){
      if(mysqli_num_rows($result) > 0){
          while($row = mysqli_fetch_array($result)){
              if ($row['CT_DESC'] == "MODULE"){
                header("location: modules/".$row['QUESTION_DESC'].".php");
              }
              $HTML .= "<hr>".generateControl($row['QUESTION_ID'], $row['QUESTION_DESC'], $row['CT_DESC']) . "<br><br>";
          }
          // Free result set
          mysqli_free_result($result);
      } else{
          $error = "";
      }
  } else {
      $error = "ERROR: Could not execute $sql.";
  }
}

// Close connection
mysqli_close($db);
?>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"> 
    <title>Survey</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    
    <!-- Project Stylesheet -->
    <link rel="stylesheet" href="css/custom.css">
    <link rel="stylesheet" href="css/pretty-checkbox.min.css">

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
    <form class="form-horizontal" action='' method="POST">
      <fieldset>
        <div id="generated_controls" class="control-group">
          <?php echo $HTML ?>
        </div>
        <div class="control-group">
          <!-- Button -->
          <div class="controls">
            <br>
            <input class="btn btn-success" type = "submit" value = "<?php echo $button_text ?>" name = "submit"/><br />
          </div>
        </div>
      </fieldset>          
    </form>
  </div>
</body>
</html>