<?php
$cookie_name = "plf";
if(!isset($_COOKIE[$cookie_name])) {
  session_start();
  $file_suffix = session_id();
  setcookie($cookie_name,session_id(),time() + (86400 * 2), "/");  
} else { $file_suffix = $_COOKIE[$cookie_name];}


session_start();

require "Parameters.php";
require_once "Functions.php";

$List_territoires = array();
$no_data = "no_data";
if (isset($_GET["formatDate"])) {
    $hunting_date = $_GET["formatDate"];
    
    $List_Chasse_Territories_By_Date = PLF::Get_Chasse_By_Date($hunting_date);
    
        print json_encode($List_Chasse_Territories_By_Date);
}

?>