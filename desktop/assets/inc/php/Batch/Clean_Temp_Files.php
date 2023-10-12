<?php

/**
 * 
 *  Clean temporary files in desktop/assets/datas older than 2 days (2 * 24 * 60 * 60 seconds) - json
 *                           desktop/assets/datas/upload/gpx older than 2 days (2 * 24 * 60 * 60 seconds) - gpx
 * 
 */

$list_Folders_To_Clean[0] = ["desktop/assets/datas", "json"];
$list_Folders_To_Clean[1]= ["desktop/assets/datas/uploadgpx", "gpx"];

require_once __DIR__ . "/../Parameters.php";

// get current desktop paths

$curr_Path = getcwd();

$tmp_Data_Path = "../../../../../";

foreach ($list_Folders_To_Clean as $folder => $file_Extension) {

    $full_Folder_Path_Name =   $tmp_Data_Path . $folder;

    //print_r("Processing folder : " . $full_Folder_Path_Name . " - extension : " . $extension . PHP_EOL );

    //------------>
    // Create a pattern to list the file extension case insensitive
    //------------>

    
    $array_Char = str_split($file_Extension, 1);

    $search_Pattern = "";

    foreach ($array_Char as $ext_Char) {
        $search_Pattern = $search_Pattern .  "[" . strtoupper($ext_Char) . strtolower($ext_Char) .  "]";

    }
    
    $xxx = scandir($full_Folder_Path_Name);

    $files_List = glob($full_Folder_Path_Name . "/*." . $search_Pattern);

    foreach ($files_List as $file) {
    
        $file_Age  = time() - filemtime($file);
        
        if ($file_Age >= 2 * 24 * 60 * 60 ) {
    
            print_r("      " . $file . " - " . filemtime($file)  . " - " . date("F d Y H:i:s.", filemtime($file)) . " - " . $file_Age . " - deleting" .  PHP_EOL);
            unlink($file);
        }
    
    }



}




$x=1;
