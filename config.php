<?php
// Store database connection information here for security and portability.
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', '[user]');
   define('DB_PASSWORD', '[password]');
   define('DB_DATABASE', '[survey_db]');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
?>
