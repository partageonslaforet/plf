<?php

session_start();

require "Parameters.php";
require_once "Functions.php";


//5b Ajouter une nouvelle date de chasse (nomenclature 10 chiffres)

$nomenclature = $_GET["var1"];
$dateChasse = $_GET["var2"];

$dateChasseEU = date('d-m-Y', strtotime($dateChasse));

//var_dump($nomenclature);
//var_dump($dateChasseEU);

$List_Array = PLF::Chasse_Date_New($nomenclature,$dateChasseEU);

if ($List_Array == false) {

    $error = plf::Get_Error();
    echo $error;
    exit;
}
echo json_encode("OK");

?>