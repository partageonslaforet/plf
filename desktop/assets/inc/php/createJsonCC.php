<?php
$cookie_name = "plf";
if(!isset($_COOKIE[$cookie_name])) {
  session_start();
  $file_suffix = session_id();
  setcookie($cookie_name,session_id(),time() + (86400 * 2), "/");  
} else { $file_suffix = $_COOKIE[$cookie_name];}

header('Access-Control-Allow-Origin','http://plf.partageonslaforet.be');
header('Access-Control-Allow-Headers','accept, content-type');

require "Parameters.php";
require_once "Functions.php";

$territoriesNber = array();
$huntedTerritories = array();
$armatchCanton = array();

if (isset($_GET["territoireValue"])) {
    $CCNom = $_GET["territoireValue"];
    
    $List_Territoires = PLF::Get_Territoire_By_CC($CCNom);
     echo '<pre>';
        var_dump($List_Territoires);
    echo '</pre>';
    
    $length = count($List_Territoires[2]);
    echo '<pre>';
        var_dump($length);
    echo '</pre>';
    
    
    
    for($i=0; $i<$length; $i++){
        $matchCanton = $List_Territoires[2][$i]["DA_Numero"];
        array_push($armatchCanton,$matchCanton);
        echo '<pre>';
            var_dump($armatchCanton);
        echo '</pre>';
    }
    
    $file = fopen("../../datas/".$file_suffix."territoryCC.json", 'w+');
    
    fwrite ($file, '{
       "type": "FeatureCollection",
       "name": "NewFeatureType",
       "features": [');
    $delimiter = ",";
    
    for($i=0; $i<$length; $i++){
        if($i==$length-1){
            $delimiter="";
        }
        $nomenclature = $armatchCanton[$i];
        
        $Territoire_Geometry = PLF::Territoire_JSON($nomenclature);
        if ($Territoire_Geometry[0] < 0) {
    
            echo $Territoire_Geometry[1];
            }else {
                print_r($Territoire_Geometry[2]);
                fwrite ($file, $Territoire_Geometry[2].$delimiter);
            }
        }
        
    fwrite ($file, '] }');
   
    fclose($file);
}        