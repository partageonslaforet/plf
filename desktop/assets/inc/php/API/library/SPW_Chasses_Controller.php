<?php


class SPW_Chasses_Controller
{

    private static int $_max_Record_Count = 2000;


    private string $_mode_chasse;
    private string $_spw_Query_Parameters;
    private string $_spw_Query_Count_Parameters;
    private string $_spw_Url_Where_Clause;
    private string $_Rest_Url;
    private int $_iteration_Count;
    private int $_API_Total_Chasses;

    public static int $_Duplicate_Chasses;
    public static int $_Total_Chasses;
       


    public function __construct(private SPW_Chasses_Gateway $gateway) 

    {
        
        // $this->_mode_chasse = "BATTUE";
        $this->_mode_chasse = "";
        $this->_Rest_Url = "";
        $this->_iteration_Count = 0;
        self::$_Duplicate_Chasses = 0;
        self::$_Total_Chasses = 0;

        ErrorHandler::$Run_Information = [];

        $this->_spw_Query_Parameters = "&geometryType=esriGeometryEnvelope";
        $this->_spw_Query_Parameters .= "&spatialRel=esriSpatialRelIntersects";
        $this->_spw_Query_Parameters .= "&units=esriSRUnit_Kilometer";
        $this->_spw_Query_Parameters .= "&outFields=*";
        $this->_spw_Query_Parameters .= "&returnGeometry=false";
        $this->_spw_Query_Parameters .= "&returnTrueCurves=false";
        $this->_spw_Query_Parameters .= "&returnIdsOnly=false";
        $this->_spw_Query_Parameters .= "&returnCountOnly=false";
        $this->_spw_Query_Parameters .= "&orderByFields=KEYG";
        $this->_spw_Query_Parameters .= "&returnDistinctValues=false";
        $this->_spw_Query_Parameters .= "&resultOffset=";
        $this->_spw_Query_Parameters .= "<OFFSET>";
        $this->_spw_Query_Parameters .= "&resultRecordCount=";
        $this->_spw_Query_Parameters .= self::$_max_Record_Count;                   
        $this->_spw_Query_Parameters .= "&returnExtentOnly=false";
        $this->_spw_Query_Parameters .= "&f=pjson";

        $this->_spw_Query_Count_Parameters = "&returnCountOnly=true";
        $this->_spw_Query_Count_Parameters .= "&outFields=KEYG";
        $this->_spw_Query_Count_Parameters .= "&returnGeometry=false";
        $this->_spw_Query_Count_Parameters .= "&f=pjson";


    }
        
    
    public static function __Increment_Duplicate_Chasses(): void {
        self::$_Duplicate_Chasses++ ;
    }



    public static function __Increment_Total_Chasses(): void {
        self::$_Total_Chasses++ ;
    }



    public function processRequest(): void
    {
    
        
        $this->Prepare_Web_Service_URL();

        $this->_API_Total_Chasses = $this->Count_Number_Chasses();

        $this->Get_Json_Data_Into_Files();

        $this->gateway->Drop_Table($GLOBALS["spw_chasses_tmp"]);

        $this->gateway->Create_DB_Table_Chasses($GLOBALS["spw_chasses_tmp"]);

        $this->Process_Json_Files();

        $this->gateway->Drop_Table($GLOBALS["spw_chasses"]);

        $this->gateway->Rename_Table($GLOBALS["spw_chasses_tmp"], $GLOBALS["spw_chasses"]);


        array_push(errorHandler::$Run_Information, ["Info", "" . PHP_EOL]);
        array_push(errorHandler::$Run_Information, ["Info", self::$_Duplicate_Chasses . " duplicate date chasses records." . PHP_EOL]);
        array_push(errorHandler::$Run_Information, ["Info", self::$_Total_Chasses . " new chasses " . PHP_EOL]);
        array_push(errorHandler::$Run_Information, ["Info", "End of process."]);


        return;

    }




    /**=======================================================================
     * 
     * Format the web Service URL without the query itself.
     * 
     *   ARGUMENTS :
     * 
     *   INPUT : https://geoservices3.test.wallonie.be/arcgis/rest/services/APP_DNFEXT/CHASSE_DATEFERM/MapServer
     * 
     *   OUTPUT :  $Rest_Url (containing the common fields of the web Service URL)
     * 
     * =======================================================================*/

    private function Prepare_Web_Service_URL(): void
    {
        

        $this->_spw_Url_Where_Clause = urlencode("SAISON = 2023");
        
        $this->_Rest_Url = $GLOBALS['spw_URL'];
        $this->_Rest_Url .= "/" . $GLOBALS['spw_Folder'];
        $this->_Rest_Url .= "/" . $GLOBALS['spw_Service'];
        $this->_Rest_Url .= "/MapServer";
        $this->_Rest_Url .= "/" . $GLOBALS['spw_Index_Chasse'];
        $this->_Rest_Url .= "/";
    }


    /**=======================================================================
     * 
     * Get the number of records to retrieve from web service.
     * 
     *   ARGUMENTS :
     * 
     *   INPUT : https://geoservices3.test.wallonie.be/arcgis/rest/services/APP_DNFEXT/CHASSE_DATEFERM/MapServer
     * 
     *   OUTPUT :  Number of records to retrieve
     * 
     * =======================================================================*/


    private function Count_Number_Chasses(): int
    {

        $curl_Url = $this->_Rest_Url . "query?where=" . 
                    $this->_spw_Url_Where_Clause . 
                    $this->_spw_Query_Count_Parameters;

        $Curl = curl_init();

        curl_setopt($Curl, CURLOPT_URL, $curl_Url);
        curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);

        $json_Return = curl_exec($Curl);
        $headers = curl_getinfo($Curl);

        curl_close($Curl);


        switch ($headers["http_code"]) {
            case 200: 
                break;
            case 503:
                array_push(errorHandler::$Run_Information, ["CRITICAL", "SPW service unavailable : http_code = " . $headers["http_code"] . " Calling URL = " . $headers["url"] . PHP_EOL]);
                return false;
            case 400:
                array_push(errorHandler::$Run_Information, ["CRITICAL", "SPW Fatal Error Occured : http_code = " . $headers["http_code"] . " Calling URL = " . $headers["url"] . PHP_EOL]);
                return false;
            case 404:
                array_push(errorHandler::$Run_Information, ["CRITICAL", "SPW resource page not found : http_code = " . $headers["http_code"] . " Calling URL = " . $headers["url"] . PHP_EOL]);
                return false;
            default:
                array_push(errorHandler::$Run_Information, ["CRITICAL", "SPW service call error : http_code = " . $headers["http_code"] . " Calling URL = " . $headers["url"] . PHP_EOL]);
                return false;

            }

        return json_decode($json_Return, true)["count"];

    }





    /**=======================================================================
     * 
     * Retrieve the JSON information from the SPF Web Site and save chunks if files
     * 
     *   ARGUMENTS : URI curl query string
     * 
     *   INPUT :
     * 
     *   OUTPUT : json file(s)
     * 
     * =======================================================================*/
    private function Get_Json_Data_Into_Files(): bool
    {


        // the web service has the parameter "resultOffset" which permits to start retrieving the information from a certain record.
        //    this field "<OFFSET>" will be updated for each iteration 
    
    
        // when integration the geometry in the result, returnDisctinctValue can't be set to NO ???    
    
        $this->_iteration_Count = ceil($this->_API_Total_Chasses / self::$_max_Record_Count);

    
        for ($iteration = 0; $iteration < $this->_iteration_Count; $iteration++) {
    

            $json_file = $GLOBALS['spw_Chasses_Json_File'] . "-" . $iteration + 1 . ".json";
            try  {
                unlink($json_file);             // delete file if it exists
            } catch (Exception $e) {

            }
            
            $fp = fopen($json_file, "w");   // create file for writing
    
       
    
            $curl_Url = $this->_Rest_Url . "query?where=" . $this->_spw_Url_Where_Clause . $this->_spw_Query_Parameters;
    
    
            // replace the <OFFSET> by the correct value

            $offset = $iteration * self::$_max_Record_Count;
            $curl_Url = preg_replace("/<OFFSET>/", $offset, $curl_Url);

            array_push(errorHandler::$Run_Information, ["Info", "processing records with offset " . $offset . PHP_EOL]);

    
    
            $Curl = curl_init();
    
            curl_setopt($Curl, CURLOPT_URL, $curl_Url);
            curl_setopt($Curl, CURLOPT_FILE, $fp);
    
            $RC_Bool = curl_exec($Curl);
            $headers = curl_getinfo($Curl);


            fclose($fp);

            switch ($headers["http_code"]) {
                case 200: 
                    break;
                case 503:
                    array_push(errorHandler::$Run_Information, ["CRITICAL", "SPW service unavailable : http_code = " . $headers["http_code"] . " Calling URL = " . $headers["url"] . PHP_EOL]);
                    return false;
                case 404:
                    array_push(errorHandler::$Run_Information, ["CRITICAL", "SPW resource page not found : http_code = " . $headers["http_code"] . " Calling URL = " . $headers["url"] . PHP_EOL]);
                    return false;
                default:
                    array_push(errorHandler::$Run_Information, ["CRITICAL", "SPW service call error : http_code = " . $headers["http_code"] . " Calling URL = " . $headers["url"] . PHP_EOL]);
                    return false;

                }
        }
        
        return true;
    }




    /**=======================================================================
     * 
     * Retrieve the JSON information from the SPF Web Site.
     * 
     *   ARGUMENTS :
     * 
     *   INPUT : json files created in previous step
     * 
     *   OUTPUT : MySql table updated
     * 
     * =======================================================================*/
    private function Process_Json_Files(): void {

        ///// _______________________
        ///// TO REMOVE AFTER TESTING
        //$this->_iteration_Count = 3;        
        ///// _______________________



        for ($iteration = 0; $iteration < $this->_iteration_Count; $iteration++) {
          
    
            $json_file = $GLOBALS['spw_Chasses_Json_File'] . "-" . $iteration + 1 . ".json";
      
    
            $chasses = JsonMachine\Items::fromFile($json_file, ['pointer' => '/features']);
    
            foreach ($chasses as $chasse) {
    

                $chasse = json_decode(json_encode($chasse), true);
    
                // change the EPOCH date into normal dd-mm-yyyy date

                $Epoch_date = substr($chasse["attributes"]["DATE_CHASSE"], 0, 10);
                $local_date = date("Y-m-d", $Epoch_date);

                $SAISON = $chasse["attributes"]["SAISON"];
                $N_LOT = $chasse["attributes"]["N_LOT"];
                $NUM = $chasse["attributes"]["NUM"];
                $MODE_CHASSE = $chasse["attributes"]["MODE_CHASSE"];
                $DATE_CHASSE = $local_date;
                $FERMETURE = $chasse["attributes"]["FERMETURE"];
                $KEYG = $chasse["attributes"]["KEYG"];



                $this->gateway->New_Chasse([
                    "SAISON" => $SAISON,
                    "N_LOT" => $N_LOT,
                    "NUM" => $NUM,
                    "MODE_CHASSE" => $MODE_CHASSE,
                    "DATE_CHASSE" => $DATE_CHASSE,
                    "FERMETURE" => $FERMETURE,
                    "KEYG" => $KEYG
                ]);

                

    
               


            }
        }
    
    }

}
