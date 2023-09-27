<?php

session_start();

require "Parameters.php";
require_once "Functions.php";

//4a Selection date en fonction territoires (nomenclature 10 chiffres)

$List_dates = array();

if (isset($_GET["territoireValue"])) {
    
    $nomenclature = $_GET["territoireValue"];
    
    
    $List_Chasse_Dates_By_Territories = PLF::Get_Chasse_By_Territoire($nomenclature);
    
    if ($List_Chasse_Dates_By_Territories[0] < 0) {

       //
       // .... traitement de l'erreur
       //
    } elseif ($List_Chasse_Dates_By_Territories[0] == 0) {
        
         echo "Pas de date de chasse pour ce territoire.";
        } else {
        
            print json_encode($List_Chasse_Dates_By_Territories);
        }
    
 }

?>