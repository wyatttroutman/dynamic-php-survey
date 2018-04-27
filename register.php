<?php
// Start PHP Session.
include("config.php");
session_start();

// Initialize all variables to null to prevent web app issues.
$MTurk_ID = $_SESSION['login_user'];

// Register the MTurk_ID
$sql = "INSERT INTO USER (ID, CONSENT, COMPLETED, TASK_ID, PAGE_ID) VALUES ('$MTurk_ID', -1, 0, 1, 1)";
$result = mysqli_query($db,$sql);

// If insert was completed, result is true. Otherwise an error was encountered.
if($result) {
  // The insert was completed. Redirect to the consent page.
  header("location: consent.php");
} else {
  // The insert failed. Redirect user to error page.
  header("location: error.php");
}


// Close connection
mysqli_close($db);

// If successful, redirect to consent form.
header("location: consent.php");
?>