<?php
$cookie_name = "plf";
if(!isset($_COOKIE[$cookie_name])) {
  session_start();
  $file_suffix = session_id();
  setcookie($cookie_name,session_id(),time() + (86400 * 2), "/");  
} else { $file_suffix = $_COOKIE[$cookie_name];}

require "Parameters.php";
require_once "Functions.php";

if (isset($_GET["territoriesNber"])) {
    
    $territoriesNber = explode(",", $_GET['territoriesNber']);
    
    $length = sizeof($territoriesNber);
    
    
    $file = fopen("../../datas/".$file_suffix."territoriesAllCC.json", 'w+');
    
    fwrite ($file, '{
       "type": "FeatureCollection",
       "name": "NewFeatureType",
       "features": [');
    $delimiter = ",";
    
    for($i=0; $i<$length; $i++){
        if($i==$length-1){
            $delimiter="";
        }
        $nomenclature = $territoriesNber[$i];
        
        $Territoire_Geometry = PLF::CC_JSON($nomenclature);
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