<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Start PHP Session.
include("../config.php");
session_start();

// Include function library and check consent status.
$basedir = realpath(__DIR__ . '/..');
include($basedir . '/php/functions.php');

// Get Globals for page generation and posting
$MTurk_ID = $_SESSION['login_user'];

// Get current task and page information.
$result = mysqli_query($db, "SELECT TASK_ID, PAGE_ID, COMPLETED, CONSENT FROM USER WHERE ID = '$MTurk_ID';");
$row = mysqli_fetch_assoc($result);
$Task_ID = $row['TASK_ID'];
$Page_ID = $row['PAGE_ID'];
$Completed = $row['COMPLETED'];
$Consent = $row['CONSENT'];
$STEP_MODULE_MTURK = $_SESSION['STEP_MODULE'];
$Error = '';
$Upload_Error = 'A problem was encountered while uploading your image. Please try again. If the issue persists, try using a different image.';

if($Completed == 1){
  header("location: ../exit.php");
}
if($Consent == -1){
  header("location: ../consent.php");
} 
if($Consent == 0){
  header("location: ../optout.php");
}
if(($Task_ID == -1) And ($Page_ID == -1)){
  header("location: ../exit.php");
}


if ( $STEP_MODULE_MTURK == ''){
	$_SESSION['STEP_MODULE'] = 0;
}

$Step = $_SESSION['STEP_MODULE'];

//echo 'MTurk: ' . $MTurk_ID . '<br>';
//echo 'Task ID: ' . $Task_ID . '<br>';
//echo 'Page ID: ' . $Page_ID . '<br>';
//echo 'Step: ' . $Step;

// Handle POST request
if($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST['submit'])) {
		switch($Step){
			case 0:
				if (isset($_POST["FIELD_1"])){
					$Val = mysqli_real_escape_string($db, $_POST["FIELD_1"]);

					if ($Val != '') {
						// Save the question & answer.
						$sql = "REPLACE INTO MODULE_TEXT (USER_ID, TASK_ID, MODULE_ID, KEY_ID, DATA) VALUES ('$MTurk_ID', $Task_ID, 'module_photoupload', 'FIELD_1', '$Val');";
						$result = mysqli_query($db,$sql) or die(mysqli_error($db));

						$_SESSION['STEP_MODULE'] = 1;
					}
				}
				break;
			case 1:
				if (isset($_POST["FIELD_2"])){
					// Go to next step.
					$Val = $_POST["FIELD_2"];

					if ($Val == '2') {
						// Mark survey as complete
						incrementTask($MTurk_ID, $Task_ID);
						$_SESSION['STEP_MODULE'] = 2;
						header("location: ../modules/module_photoupload.php");
					} else if ($Val == '1'){
						$_SESSION['STEP_MODULE'] = 2;
					}
				}
				break;
			case 2:
				if (isset($_POST["FIELD_3"])){
					$Val = $_POST["FIELD_3"];

					if ($Val == '1'){
						$_SESSION['STEP_MODULE'] = 3;
					}
				}
				break;
			case 3:
				if (isset($_POST["FIELD_4"])){
					// Go to next step.
					$Val = $_POST["FIELD_4"];

					if ($Val == '1'){
						// Willing to share
						$_SESSION['STEP_MODULE'] = 4;
					} else if($Val == '2') {
						// Not willing to share
						$_SESSION['STEP_MODULE'] = 5;
					}
				}
				break;
			case 4:
				// Handle upload of personal photo.

				$validation = validateImage();
				// Validate image
				if ($validation == true){
					$target_dir = "../uploads/".$MTurk_ID. "/";
					if (file_exists($target_dir) == false) {   
					    mkdir( $target_dir, 0750);  
					} 

					$target_dir = "../uploads/".$MTurk_ID. "/".$Task_ID."/";
					if (file_exists($target_dir) == false) {   
					    mkdir( $target_dir, 0750);  
					} 

					$target_dir = "../uploads/".$MTurk_ID. "/".$Task_ID."/personal/";
					if (file_exists($target_dir) == false) {
						mkdir( $target_dir, 0750);
					} 

					// Get the file data again
					$filename=$_FILES['fileToUpload']['name'];
					$filetype=$_FILES['fileToUpload']['type'];
					$filename = strtolower($filename);
					$filetype = strtolower($filetype);
					$file_ext = strrchr($filename, '.');
					// Rename image
 					$upload_file = $target_dir . md5(basename($_FILES['fileToUpload']['name'])).$file_ext;

 					if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $upload_file)) {
						// Image uploaded. Go to next step.
						$_SESSION['STEP_MODULE'] = 6;
					} else {
						$Error = $Upload_Error;
					}
				} else {
					$Error = $Upload_Error;
				}
				break;
			case 5:
				// Save text for sensitive content.
				$Val = '';
				if (isset($_POST["similarFileText"])){
					$Val = mysqli_real_escape_string($db, $_POST["similarFileText"]);
				}

				$Flag = true;
				if (is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) {
					// Handle upload of online photo.
					$validation = validateImage();
					// Validate image
					if ($validation == true){
						$target_dir = "../uploads/".$MTurk_ID. "/";
						if (file_exists($target_dir) == false) {   
						    mkdir( $target_dir, 0750);  
						} 

						$target_dir = "../uploads/".$MTurk_ID. "/".$Task_ID."/";
						if (file_exists($target_dir) == false) {   
						    mkdir( $target_dir, 0750);  
						} 

						$target_dir = "../uploads/".$MTurk_ID. "/".$Task_ID."/online/";
						if (file_exists($target_dir) == false) {
							mkdir( $target_dir, 0750);
						} 

						// Get the file data again
						$filename=$_FILES['fileToUpload']['name'];
						$filetype=$_FILES['fileToUpload']['type'];
						$filename = strtolower($filename);
						$filetype = strtolower($filetype);
						$file_ext = strrchr($filename, '.');

						// Rename image
	 					$upload_file = $target_dir . md5(basename($_FILES['fileToUpload']['name'])).$file_ext;

	 					if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $upload_file)) {
							// Image uploaded. Go to next step.
							$_SESSION['STEP_MODULE'] = 6;
						} else {
							$Error = $Upload_Error;
							$Flag = false;
						}
					} else {
						$Error = $Upload_Error;
						$Flag = false;
					}
				}

				if ($Val != '') {
					// Handle content text of online photo.
					$sql = "REPLACE INTO MODULE_TEXT (USER_ID, TASK_ID, MODULE_ID, KEY_ID, DATA) VALUES ('$MTurk_ID', $Task_ID, 'module_photoupload', 'SIMILAR_FILE_TEXT', '$Val');";
					$result = mysqli_query($db,$sql) or die(mysqli_error($db));

					// Go to next step.
					$_SESSION['STEP_MODULE'] = 6;
				}
				break;
			case 6:
				if (isset($_POST["FIELD_5"])){
					// Save text for sensitive content.
					$Val = mysqli_real_escape_string($db, $_POST["FIELD_5"]);

					if ( $Val <> ""){
						// Save the question & answer.
						$sql = "REPLACE INTO MODULE_TEXT (USER_ID, TASK_ID, MODULE_ID, KEY_ID, DATA) VALUES ('$MTurk_ID', $Task_ID, 'module_photoupload', 'FIELD_5', '$Val');";
						$result = mysqli_query($db,$sql) or die(mysqli_error($db));

						// Exit module and resume survey
						incrementPage($MTurk_ID, $Task_ID, $Page_ID);
						$_SESSION['STEP_MODULE'] = 2;
						header("location: ../survey.php");
					}
				}
				break;
			default:
				break;
		}
	}
}



$HTML = '';
$control = '';

switch($_SESSION['STEP_MODULE']){
	case 0:
		$text = '[TEXT]';
		$control .= '<div>';
		$control .= $text;
		$control .= '<textarea class="form-control" maxlength="50" rows="1" name="FIELD_1"></textarea>';
		$control .= '</div>';
		$control .= '<hr>';

		$HTML .= $control;
		break;
	case 1:
		$text = '[TEXT]';
		$control .= '<div>';
		$control .= $text;
		$control .= '<div class="radio"><label>';
		$control .= '<input type="radio" required name="FIELD_2" value="1">Yes</label></div>';
		$control .= '<div class="radio"><label>';
		$control .= '<input type="radio" required name="FIELD_2" value="2">No</label></div>';
		$control .= '</div>';
		$control .= '<hr>';

		$HTML .= $control;
		break;
	case 2:
		switch($Task_ID){
			case 2:
				$text = '[TEXT]';
				break;
			case 3:
				$text = '[TEXT]';
				break;
			case 4:
				$text = '[TEXT]';
				break;
			case 5:
				$text = '[TEXT]';
				break;
			case 6:
				$text = '[TEXT]';
				break;
			default:
				$text ='Unable to find task.';
				break;
		}

		$control = '';
		$control .= '<div>';
		$control .= $text;
		$control .= '<div class="radio"><label>';
		$control .= '<input type="radio" required name="FIELD_3" value="1">[TEXT]</label></div>';
		$control .= '<hr>';

		$HTML .= $control;
		break;
	case 3:
		$control = '';
		$text = '[TEXT]';
		$control .= '<div>';
		$control .= $text;
		$control .= '<div class="radio"><label>';
		$control .= '<input type="radio" required name="FIELD_4" value="1">Yes</label></div>';
		$control .= '<div class="radio"><label>';
		$control .= '<input type="radio" required name="FIELD_4" value="2">No</label></div>';
		$control .= '</div>';
		$control .= '<hr>';

		$HTML .= $control;
		break;
	case 4:
		$text = '[TEXT]';
		$control .= '<div>';
		$control .= $text;
		$control .= '<input type="file" name="fileToUpload" id="fileToUpload">';
		$control .= '</div>';
		$control .= '<hr>';

		$HTML .= $control;
		break;
	case 5:	
		switch ($Task_ID){
			case 2:
				$text = '[TEXT]';
				break;
			case 3:
				$text = '[TEXT]';
				break;
			case 4:
				$text = '[TEXT]';
				break;
			case 5:
				$text = '[TEXT]';
				break;
			case 6:
				$text = '[TEXT]';
				break;
			default:
				$text = ".";
				break;
		}
		$HTML .= $text;
		$text = '[TEXT]';
		$control .= '<div>';
		$control .= $text;
		$control .= '<input type="file" name="fileToUpload" id="fileToUpload">';
		$control .= '</div>';
		$control .= '<hr>';

		$text = '[TEXT]';
		$control .= '<div>';
		$control .= $text;
		$control .= '<textarea class="form-control" maxlength="5000" rows="5" name="similarFileText"></textarea>';
		$control .= '</div>';
		$control .= '<hr>';

		$HTML .= $control;
		break;

	case 6:
		$text = '[TEXT]';
		$control .= '<div>';
		$control .= $text;
		$control .= '<textarea class="form-control" maxlength="5000" rows="5" name="FIELD_5"></textarea>';
		$control .= '</div>';
		$control .= '<hr>';

		$HTML .= $control;
		break;
	default:
		break;
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
  <?php include ($basedir . '/html/navbar.html'); ?>
  <!-- Where all the magic happens -->
  <div class="container">
    <div class="span12">
        <form class="form-horizontal" action='' method="POST" enctype="multipart/form-data">
          <fieldset>
          	<span style="color:red";><?php echo $Error ?></span>
            <div>
              <?php echo $HTML ?>
            </div>
           	<div class="control-group">
	          <!-- Button -->
	          <div class="controls">
	            <input class="btn btn-success" type = "submit" value = "Next" name = "submit"/><br />
	          </div>
	        </div>
          </fieldset>          
        </form>
    </div>
  </div>
</body>
</html>