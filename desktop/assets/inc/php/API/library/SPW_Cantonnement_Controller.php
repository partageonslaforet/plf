<?php


class SPW_Cantonnement_Controller
{

    private static int $_max_Record_Count = 2000;


    private string $_spw_Query_Parameters;
    private string $_spw_Query_Count_Parameters;
    private string $_spw_Url_Where_Clause;
    private string $_Rest_Url;
    private int $_iteration_Count;
    private int $_API_Total_Cantonnement;

    public static int $_Duplicate_Cantonnement;
    public static int $_Total_Cantonnement;
    public static array $_List_Cantonnement;
       


    public function __construct(private SPW_Cantonnement_Gateway $gateway) 

    {
        
        $this->_Rest_Url = "";
        $this->_iteration_Count = 0;
        self::$_Duplicate_Cantonnement = 0;
        self::$_Total_Cantonnement = 0;
        self::$_List_Cantonnement = [];

        ErrorHandler::$Run_Information = [];


        $this->_spw_Query_Parameters = "&outFields=*";
        $this->_spw_Query_Parameters .= "&returnGeometry=true";
        $this->_spw_Query_Parameters .= "&returnCountOnly=false";                 
        $this->_spw_Query_Parameters .= "&f=geojson";
        $this->_spw_Query_Parameters .= "&orderByFields=CAN";
        $this->_spw_Query_Count_Parameters = "&returnCountOnly=true";
        $this->_spw_Query_Count_Parameters .= "&outFields=ABREVIATION";
        $this->_spw_Query_Count_Parameters .= "&returnGeometry=false";
        $this->_spw_Query_Count_Parameters .= "&f=geojson";


    }
        
    
    public static function __Increment_Duplicate_Cantonnement(): void {
        self::$_Duplicate_Cantonnement++ ;
    }



    public static function __Increment_Total_Cantonnement(): void {
        self::$_Total_Cantonnement++ ;
    }

    public static function __Update_List_Cantonnement(int $Num_Cantonnement): void {
        array_push(self::$_List_Cantonnement, $Num_Cantonnement) ;
    }

    public function processRequest(): void
    {
    
        
        $this->Prepare_Web_Service_URL();

        $this->_API_Total_Cantonnement = $this->Count_Number_Cantonnement();

        $this->Get_Json_Data_Into_Files();

        $this->gateway->Drop_Table($GLOBALS["spw_cantonnements_tmp"]);

        $this->gateway->Create_DB_Table_Cantonnement($GLOBALS["spw_cantonnements_tmp"]);

        $this->Process_Json_Files();

        $this->gateway->Drop_Table($GLOBALS["spw_cantonnements"]);

        $this->gateway->Rename_Table($GLOBALS["spw_cantonnements_tmp"], $GLOBALS["spw_cantonnements"]);

        $this->gateway->Drop_View($GLOBALS["spw_view_cantonnements"]);
        $this->gateway->Create_View_Cantonnement();

        array_push(errorHandler::$Run_Information, ["Info", "" . PHP_EOL]);
        array_push(errorHandler::$Run_Information, ["Info", self::$_Duplicate_Cantonnement . " duplicate Cantonnements records." . PHP_EOL]);
        array_push(errorHandler::$Run_Information, ["Info", self::$_Total_Cantonnement . " new Cantonnements " . PHP_EOL]);
        array_push(errorHandler::$Run_Information, ["Info", "End of process."]);


        return;

    }




    /**=======================================================================
     * 
     * Format the web Service URL without the query itself.
     * 
     *   ARGUMENTS :
     * 
     *   INPUT : https://geoservices.wallonie.be/arcgis/rest/services/FAUNE_FLORE/TRIAGE/MapServer
     * 
     *   OUTPUT :  $Rest_Url (containing the common fields of the web Service URL)
     * 
     * =======================================================================*/

    private function Prepare_Web_Service_URL(): void
    {
        

        $this->_spw_Url_Where_Clause = urlencode("OBJECTID > 1");

        $this->_Rest_Url = $GLOBALS['spw_Cantonnement_URL'];
        $this->_Rest_Url .= "/" . $GLOBALS['spw_Cantonnement_Folder'];
        $this->_Rest_Url .= "/" . $GLOBALS['spw_Cantonnement_Service'];
        $this->_Rest_Url .= "/MapServer";
        $this->_Rest_Url .= "/" . $GLOBALS['spw_Cantonnement_Index_Cantonnement'];
        $this->_Rest_Url .= "/";
    }


    /**=======================================================================
     * 
     * Get the number of records to retrieve from web service.
     * 
     *   ARGUMENTS :
     * 
     *   INPUT : https://geoservices.wallonie.be/arcgis/rest/services/FAUNE_FLORE/TRIAGE/MapServer
     * 
     *   OUTPUT :  Number of records to retrieve
     * 
     * =======================================================================*/


    private function Count_Number_Cantonnement(): int
    {

        $curl_Url = $this->_Rest_Url . "query?where=" . 
                    $this->_spw_Url_Where_Clause . 
                    $this->_spw_Query_Count_Parameters;

        $Curl = curl_init();

        curl_setopt($Curl, CURLOPT_URL, $curl_Url);
        curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);

        $json_Return = curl_exec($Curl);

        curl_close($Curl);

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
    
        $this->_iteration_Count = ceil($this->_API_Total_Cantonnement / self::$_max_Record_Count);

    
        for ($iteration = 0; $iteration < $this->_iteration_Count; $iteration++) {
    

            $json_file = $GLOBALS['spw_Cantonnement_Json_File'] . "-" . $iteration + 1 . ".json";
            try  {
                unlink($json_file);             // delete file if it exists
            } catch (Exception $e) {

            }
            
            $fp = fopen($json_file, "w");   // create file for writing
    
       
    
            $curl_Url = $this->_Rest_Url . "query?where=" . $this->_spw_Url_Where_Clause . $this->_spw_Query_Parameters;
    
    
            // // replace the <OFFSET> by the correct value

            // $offset = $iteration * self::$_max_Record_Count;
            // $curl_Url = preg_replace("/<OFFSET>/", $offset, $curl_Url);

            // array_push(errorHandler::$Run_Information, ["Info", "processing records with offset " . $offset . PHP_EOL]);

    
    
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
          
    
            $json_file = $GLOBALS['spw_Cantonnement_Json_File'] . "-" . $iteration + 1 . ".json";
      
    
            $Cantonnements = JsonMachine\Items::fromFile($json_file, ['pointer' => '/features']);
    
            foreach ($Cantonnements as $Cantonnement) {
    
                $cantonnement = json_decode(json_encode($Cantonnement), true);

                $CAN = $cantonnement["properties"]["CAN"];

                if ( in_array($CAN, self::$_List_Cantonnement)) {
                    continue;
                }

                $cantonnement_geometry = $cantonnement["geometry"];
                $cantonnement_geometry = json_encode($cantonnement_geometry, JSON_PRETTY_PRINT, 512);


                $PREPOSE = $cantonnement["properties"]["PREPOSE"];
                $PREPOSE = "";
                $GSM = $cantonnement["properties"]["GSM"];
                $CANTON = $cantonnement["properties"]["CANTON"];
                $TEL_CAN = $cantonnement["properties"]["TEL_CAN"];
                $GEOM = $cantonnement_geometry;


  

                $this->gateway->New_Cantonnement([
                    "CAN" => $CAN,
                    "PREPOSE" => $PREPOSE,
                    "GSM" => $GSM,
                    "CANTON" => $CANTON,
                    "TEL_CAN" => $TEL_CAN,
                    "GEOM" => $GEOM
                ]);

                

    
               


            }
        }
    
    }

}
