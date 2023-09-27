<?php


class SPW_Territoires_Controller
{

    private static int $_max_Record_Count = 2000;
    //private static int $_max_Record_Count = 5;



    private string $_spw_Query_Parameters;
    private string $_spw_Query_Count_Parameters;
    private string $_spw_Url_Where_Clause;
    private string $_Rest_Url;
    private int $_iteration_Count;
    private int $_API_Total_Territoires;

    public static int $_Duplicate_Territoires;
    public static int $_Total_Territoires;
       


    public function __construct(private SPW_Territoires_Gateway $gateway) 

    {
        
        $this->_Rest_Url = "";
        $this->_iteration_Count = 0;
        self::$_Duplicate_Territoires = 0;
        self::$_Total_Territoires = 0;

        ErrorHandler::$Run_Information = [];


        $this->_spw_Query_Parameters = "&geometryType=esriGeometryPolygon";
        $this->_spw_Query_Parameters .= "&units=esriSRUnit_Kilometer";
        $this->_spw_Query_Parameters .= "&outFields=*";
        $this->_spw_Query_Parameters .= "&returnGeometry=true";
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
        $this->_spw_Query_Parameters .= "&f=geojson";

        $this->_spw_Query_Count_Parameters = "&returnCountOnly=true";
        $this->_spw_Query_Count_Parameters .= "&outFields=KEYG";
        $this->_spw_Query_Count_Parameters .= "&returnGeometry=false";
        $this->_spw_Query_Count_Parameters .= "&f=pjson";


    }
        
    
    public static function __Increment_Duplicate_Territoires(): void {
        self::$_Duplicate_Territoires++ ;
    }



    public static function __Increment_Total_Territoires(): void {
        self::$_Total_Territoires++ ;
    }



    public function processRequest(): void
    {
    
        
        $this->Prepare_Web_Service_URL();

        $this->_API_Total_Territoires = $this->Count_Number_Territoires();

        // $this->_API_Total_Territoires = 5;
        
        $this->Get_Json_Data_Into_Files();

        $this->gateway->Drop_Table($GLOBALS["spw_tbl_territoires_tmp"]);

        $this->gateway->Create_DB_Table_Territoires($GLOBALS["spw_tbl_territoires_tmp"]);

        $this->Process_Json_Files();
        
        $this->gateway->Drop_Table($GLOBALS["spw_tbl_territoires"]);

        $this->gateway->Rename_Table($GLOBALS["spw_tbl_territoires_tmp"], $GLOBALS["spw_tbl_territoires"]);

        $this->gateway->Drop_View($GLOBALS["spw_view_territoires"]);
        $this->gateway->Create_View_Territoires();

        array_push(errorHandler::$Run_Information, ["Info", "" . PHP_EOL]);
        array_push(errorHandler::$Run_Information, ["Info", self::$_Duplicate_Territoires . " duplicate territoires records." . PHP_EOL]);
        array_push(errorHandler::$Run_Information, ["Info", self::$_Total_Territoires . " new territoires " . PHP_EOL]);
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
        

        $this->_spw_Url_Where_Clause = urlencode("SAISON >= 2023");

        $this->_Rest_Url = $GLOBALS['spw_URL'];
        $this->_Rest_Url .= "/" . $GLOBALS['spw_Folder'];
        $this->_Rest_Url .= "/" . $GLOBALS['spw_Service'];
        $this->_Rest_Url .= "/MapServer";
        $this->_Rest_Url .= "/" . $GLOBALS['spw_Index_Territoire'];
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


    private function Count_Number_Territoires(): int
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
    
        $this->_iteration_Count = ceil($this->_API_Total_Territoires / self::$_max_Record_Count);

    
        for ($iteration = 0; $iteration < $this->_iteration_Count; $iteration++) {
    

            $json_file = $GLOBALS['spw_Territoires_Json_File'] . "-" . $iteration + 1 . ".json";
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
        /// $this->_iteration_Count = 3;        
        ///// _______________________



        for ($iteration = 0; $iteration < $this->_iteration_Count; $iteration++) {
          
    
            $json_file = $GLOBALS['spw_Territoires_Json_File'] . "-" . $iteration + 1 . ".json";
      
    
            $territoires = JsonMachine\Items::fromFile($json_file, ['pointer' => '/features']);
    
            foreach ($territoires as $territoire) {
    

                $territoire = json_decode(json_encode($territoire), true);


                $territory_geometry = $territoire["geometry"];
                $territory_geometry = json_encode($territory_geometry, JSON_PRETTY_PRINT, 512);
    
                // change the EPOCH date into normal dd-mm-yyyy date

                // $Epoch_date = substr($territoire["properties"]["DATE_MAJ"], 0, 10);
                // $local_date = date("Y-m-d", $Epoch_date);

                $OBJECTID = $territoire["properties"]["OBJECTID"];
                $KEYG = $territoire["properties"]["KEYG"];
                $SAISON = $territoire["properties"]["SAISON"];
                $N_LOT = $territoire["properties"]["N_LOT"];
                $SHAPE = $territory_geometry;
                $NUGC = $territoire["properties"]["NUGC"];


                if ($territoire["properties"]["TITULAIRE_ADH_UGC"] == "O") {
                    $territoire["properties"]["TITULAIRE_ADH_UGC"] = true;
                } else {
                    $territoire["properties"]["TITULAIRE_ADH_UGC"] = false;
                }
                
                $TITULAIRE_ADH_UGC = $territoire["properties"]["TITULAIRE_ADH_UGC"];
                $SERVICE = $territoire["properties"]["SERVICE"];

                if (empty($SERVICE)) {
                    $SERVICE = substr($N_LOT,0, 3);
                }
  

                $this->gateway->New_Territoire([
                    "OBJECTID" => $OBJECTID,
                    "KEYG" => $KEYG,
                    "SAISON" => $SAISON,
                    "N_LOT" => $N_LOT,
                    "SHAPE" => $SHAPE,
                    "NUGC" => $NUGC,
                    "SERVICE" => $SERVICE,
                    "TITULAIRE_ADH_UGC" => $TITULAIRE_ADH_UGC,
                    "DATE_MAJ" => date("Y-m-d")
                ]);

            

            }
        }
    
    }

}
