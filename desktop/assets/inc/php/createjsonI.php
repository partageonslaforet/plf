<?php

session_start();

require "Parameters.php";
require_once "Functions.php";

if (isset($_GET["territoireValue"])) {
    $nomenclature = $_GET["territoireValue"];
    echo $nomenclature;
    $List_Array = PLF::Territoire_JSON($nomenclature, "t");
    $my_array = json_decode($List_Array,true);
    file_put_contents('../../datas/territory.json', $List_Array);
    if ($List_Array == "") {
    
        $error = plf::Get_Error();
        echo $error;
        exit;
        }
}
?>

