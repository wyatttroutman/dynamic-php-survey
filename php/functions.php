<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getRandomString($length = 8) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $string = '';
  for ($i = 0; $i < $length; $i++) {
    $string .= $characters[mt_rand(0, strlen($characters) - 1)];
  }
  return $string;
}

function fileUploaded() {
	if(isset($_FILES['files']) ){  
		foreach($_FILES['files']['tmp_name'] as $key => $tmp_name ){
		    if(!empty($_FILES['files']['tmp_name'][$key])){
		    	return true;
		    }
		}
	}

	return false;
}

function validateImage(){
	$filename=$_FILES['fileToUpload']['name'];
	$filetype=$_FILES['fileToUpload']['type'];
	$filename = strtolower($filename);
	$filetype = strtolower($filetype);

	// Check if image contains PHP code
	$pos = strpos($filename,'php');
	if(!($pos === false)) {
		return 'PHP code detected.';
	}

	// Get the file extension type
	$file_ext = strrchr($filename, '.');

	// Check if it is allowed or not
	$whitelist = array(".jpg",".jpeg",".gif",".png"); 
	if (!(in_array($file_ext, $whitelist))) {
		return 'File type is not allowed. Please upload only .jpeg, .jpg, .gif, or .png files.';
	}

	// Check upload type
	$pos = strpos($filetype,'image');
	if($pos === false) {
		return 'File not allowed.';
	}
	$imageinfo = getimagesize($_FILES['fileToUpload']['tmp_name']);
	if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpeg'&& $imageinfo['mime']      != 'image/jpg'&& $imageinfo['mime'] != 'image/png') {
		return 'File not allowed.';
	}
	//check double file type (image with comment)
	if(substr_count($filetype, '/')>1){
		return 'File not allowed.';
	}
	return true;

}

function incrementPage($MTurk_ID, $Task_ID, $Page_ID){
	global $db;
    $sql = "SELECT MAX(PAGE_ID) MAX_PAGE_ID FROM TASK_PAGE_QUESTION WHERE TASK_ID = '$Task_ID';";
    $result = mysqli_query($db,$sql);
    $row = mysqli_fetch_assoc($result);
    $Max_Page_ID = $row['MAX_PAGE_ID'];

    if ($Max_Page_ID <= $Page_ID){
      $sql = "SELECT T1.ID FROM TASK T1 INNER JOIN TASK T2 ON T1.SORT_ORDER > T2.SORT_ORDER WHERE T2.ID = '$Task_ID' LIMIT 1;";
      $result = mysqli_query($db,$sql) or die(mysqli_error($db));
      $row = mysqli_fetch_assoc($result);
      $New_Task_ID = $row['ID'];
      // If we have finished the survey tasks...
      if (is_null($New_Task_ID)) {
        // Update task and page numbers.
        $sql = "UPDATE USER SET TASK_ID = '-1', PAGE_ID = '-1' WHERE ID = '$MTurk_ID';";
        $result = mysqli_query($db,$sql);
      } else {
        $sql = "UPDATE USER SET TASK_ID = '$New_Task_ID', PAGE_ID = '1' WHERE ID = '$MTurk_ID';";
        $result = mysqli_query($db,$sql);
      }
    } else {
      $New_Page_ID = $Page_ID + 1;
      $sql = "UPDATE USER SET PAGE_ID = '$New_Page_ID' WHERE ID = '$MTurk_ID';";
      $result = mysqli_query($db,$sql);
    }

}

function incrementTask($MTurk_ID, $Task_ID){
	global $db;
	$sql = "SELECT T1.ID FROM TASK T1 INNER JOIN TASK T2 ON T1.SORT_ORDER > T2.SORT_ORDER WHERE T2.ID = '$Task_ID' LIMIT 1;";
	$result = mysqli_query($db,$sql) or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	$New_Task_ID = $row['ID'];
	// If we have finished the survey tasks...
	if (is_null($New_Task_ID)) {
	// Update task and page numbers.
	$sql = "UPDATE USER SET TASK_ID = '-1', PAGE_ID = '-1' WHERE ID = '$MTurk_ID';";
	$result = mysqli_query($db,$sql);
	} else {
	$sql = "UPDATE USER SET TASK_ID = '$New_Task_ID', PAGE_ID = '1' WHERE ID = '$MTurk_ID';";
	$result = mysqli_query($db,$sql);
	}

}

function generateControl($QUESTION_ID, $QUESTION_DESC, $CONTROL_TYPE){
	global $db;
	$control = '<div class="controls">';
	switch($CONTROL_TYPE){
		case "RADIO":
			$control = $control . "<label>". $QUESTION_DESC . "</label>";
			$result = mysqli_query($db, "SELECT ANSWER_ID, ANSWER_DESC FROM PROGRAM_ANSWERS WHERE QUESTION_ID = " . $QUESTION_ID . " ORDER BY QA_SORT_ORDER;") or die(mysqli_error($db));
			while($row = mysqli_fetch_array($result)){
				$ANSWER_ID = $row['ANSWER_ID'];
				$ANSWER_DESC = $row['ANSWER_DESC'];
				$control = $control . '<br><br><div class="pretty p-default p-round">';
				$control = $control . '<input type="radio" required name="Rsurvey_control_ID_Q'. $QUESTION_ID . '" value="'. $ANSWER_ID .'" /><div class="state"><label>' . $ANSWER_DESC . '</label></div></div>';
			}
			$control .= '<br><br>';
			break;
		case "CHECKBOX":
			$control = $control . '<label>'. $QUESTION_DESC . '</label>';
			$result = mysqli_query($db, "SELECT ANSWER_ID, ANSWER_DESC FROM PROGRAM_ANSWERS WHERE QUESTION_ID = $QUESTION_ID ORDER BY QA_SORT_ORDER;") or die(mysqli_error($db));
			while($row = mysqli_fetch_array($result)){
				$ANSWER_ID = $row['ANSWER_ID'];
				$ANSWER_DESC = $row['ANSWER_DESC'];
				$control = $control . '<br><br><div class="pretty p-default p-round">';
				$control = $control . '<input type="checkbox" name="Csurvey_control_ID_Q'. $QUESTION_ID . '" value="'. $ANSWER_ID .'" /><div class="state"><label>' . $ANSWER_DESC . '</label></div></div>';
			}
			$control .= '<br><br>';
			break;
		case "TEXT-INPUT":
			$control .= '<div>';
			$control .= '<label for="Tsurvey_control_ID_Q'. $QUESTION_ID . '">'.$QUESTION_DESC.'</label>';
			$control .= '<textarea class="form-control" maxlength="1024" rows="5" name="Tsurvey_control_ID_Q'. $QUESTION_ID . '"></textarea>';
			$control .= '</div>';
			$control .= '<br><br>';
			break;
		case "LABEL":
			$control .= '<div>';
			$control .= '<label for="Lsurvey_control_ID_Q'. $QUESTION_ID . '">'.$QUESTION_DESC.'</label>';
			$control .= '</div>';
			break;
		default:
			$control = '';

	}
	$control = $control . '</div>';

	return $control;
//<input type="text" id="MTurk_ID" name="MTurk_ID" placeholder="" class="input-xlarge">

}

?>