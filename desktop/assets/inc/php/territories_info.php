
<?php

session_start();

require "Parameters.php";
require_once "Functions.php";

if (isset($_GET["territoireValue"])) {
    $nomenclature = $_GET["territoireValue"];

    $Territories_Info = PLF::Get_Territoire_Info($nomenclature);
    var_dump($Territories_Info[0]);
    
        if ($Territories_Info[0] < 0) {

           //print_r ($Territories_Info[O]);
           //
           // .... traitement de l'erreur
           //
        }else {
            print json_encode($Territories_Info[2]);
        }
    }
    //var_dump($Territories_Info);
?>