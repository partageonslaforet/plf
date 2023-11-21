<?php

/**
 * 
 *  Clean temporary files in desktop/assets/datas older than 2 days (2 * 24 * 60 * 60 seconds) - json
 *                           desktop/assets/datas/upload/gpx older than 2 days (2 * 24 * 60 * 60 seconds) - gpx
 * 
 */



class Clean_Temp_Files
{

    private int $_number_Deleted_Files;

    public function __construct() {
        ErrorHandler::$Run_Information = [];
        $this->_number_Deleted_Files = 0;
    }
    

        


    public function Clean_Temporary_Files()
    {


        $list_Folders_To_Clean = $GLOBALS["list_Folders_To_Clean"];
        // get current desktop paths

        $curr_Path = getcwd();

        $tmp_Data_Path = "../../../../../";

        foreach ($list_Folders_To_Clean as $folder => $file_Extension) {

            $full_Folder_Path_Name =   $tmp_Data_Path . $folder;

            array_push(errorHandler::$Run_Information, ["Info", "Processing folder " . $folder . " - extension " . $file_Extension . PHP_EOL]);


            //------------>
            // Create a pattern to list the file extension case insensitive
            //------------>

            $array_Char = str_split($file_Extension, 1);

            $search_Pattern = "";

            foreach ($array_Char as $ext_Char) {
                $search_Pattern = $search_Pattern .  "[" . strtoupper($ext_Char) . strtolower($ext_Char) .  "]";
            }


            $files_List = glob($full_Folder_Path_Name . "/*." . $search_Pattern);

            foreach ($files_List as $file) {

                $file_Age  = time() - filemtime($file);

                if ($file_Age >= 2 * 24 * 60 * 60) {

                    //print_r("      " . $file . " - " . filemtime($file)  . " - " . date("F d Y H:i:s.", filemtime($file)) . " - " . $file_Age . " - deleting" .  PHP_EOL);
                    $this->_number_Deleted_Files++;
                    unlink($file);
                }
            }

        array_push(errorHandler::$Run_Information, ["Info", " " . $this->_number_Deleted_Files . " files deleted." . PHP_EOL]);

        }

        array_push(errorHandler::$Run_Information, ["Info", "" . PHP_EOL]);
        array_push(errorHandler::$Run_Information, ["Info", "End of process."]);

    }
}
