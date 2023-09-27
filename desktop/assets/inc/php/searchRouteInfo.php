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


if (isset($_GET["routeValue"])) {
    $nomenclature = $_GET["routeValue"];
    
    $Route_Info = PLF::Get_Itineraire_Infos($nomenclature);

        if ($Route_Info [0] < 0) {

           //print_r ($Territories_Info[O]);
           //
           // .... traitement de l'erreur
           //
        }else {
           
            print json_encode($Route_Info[2]);
        }
   }

?>
