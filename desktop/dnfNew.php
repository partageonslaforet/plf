<?php
$cookie_name = "plf";
if (!isset($_COOKIE[$cookie_name])) {
    session_start();
    $file_suffix = session_id();
    setcookie($cookie_name, session_id(), time() + (86400 * 2), "/");
} else {
    $file_suffix = $_COOKIE[$cookie_name];
}



require "assets/inc/php/Parameters.php";
require_once "assets/inc/php/Functions.php";

$List_Canton = PLF::Get_Canton_List();

if ($List_Canton[0] < 0) {

   echo $List_Canton[1];

}

$List_Territoires = PLF::Get_Territoire_List();

if ($List_Territoires[0] < 0) {

   echo $List_Territoires[1];
}
?>