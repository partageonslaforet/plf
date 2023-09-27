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

$armatchCanton = array();
$armatchNomenclature = array();

if (isset($_GET["territoireValue"])) {
    $cantonNbre = $_GET["territoireValue"];
    
    $List_Territoires = PLF::Get_Territoire_List();
    //print json_encode($List_Territoires[2]);
    
    $length = count($List_Territoires[2]);
    echo '<pre>';
        var_dump($length);
    echo '</pre>';
    
    
    for($i=0; $i<$length; $i++){
        
        $da_Nbre = substr(($List_Territoires[2][$i]["DA_Numero"]),0,3);
        
        
        //print json_encode($da_Nbre);
        
        if (($da_Nbre == $cantonNbre)){
            $matchCanton = $List_Territoires[2][$i]["DA_Numero"];
            array_push($armatchCanton,$matchCanton);
            echo '<pre>';
                var_dump($armatchCanton);
        
            echo '</pre>';
        }
    }
    
    $cantonLength = count($armatchCanton);
    echo '<pre>';
                var_dump($cantonLength);
            echo '</pre>';
    
        
    print_r ($armatchCanton);
    $lengthCanton = count($armatchCanton);
        
    $file = fopen("../../datas/".$file_suffix."territoryDnf.json", 'w+');
    
    fwrite ($file, '{
       "type": "FeatureCollection",
       "name": "NewFeatureType",
       "features": [');
    $delimiter = ",";
    
    for($i=0; $i<$lengthCanton; $i++){
        if($i==$lengthCanton-1){
            $delimiter="";
        }
        $nomenclature = $armatchCanton[$i];
        //print_r($nomenclature);
        echo '<pre>';
            var_dump( $nomenclature);
        echo '</pre>';
        
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

?>