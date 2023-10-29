<?php

session_start();

require "Parameters.php";
require_once "Functions.php";

if (isset($_GET["territoireValue"])) {
    $nomenclature = $_GET["territoireValue"];
    echo $nomenclature;
    $Territoire_Geometry = PLF::Territoire_JSON($nomenclature);
    //file_put_contents('../../datas/territory.json', $Territoire_Geometry);
    
    if ($Territoire_Geometry[0] < 0) {

   echo $Territoire_Geometry[1];

   //
   // .... traitement de l'erreur
   //
    }else {

    $fp = fopen("../../datas/territory.json", 'w');
   fwrite($fp, $Territoire_Geometry[2]);
   
}
}

?>

