<?php


class CGT_Itineraires_Controller_Step2
{


    private string $_Rest_Url;
    private string $_Query_Parameters;
    public static int $_Total_Itineraires;
    public static int $_Duplicate_Itineraires;
    public static DateTime $_Run_Time;




    public function __construct(private CGT_Itineraires_Gateway_Step2 $gateway)
    {

        $this->_Rest_Url = "";

        ErrorHandler::$Run_Information = [];

        $this->_Query_Parameters = "OTH-A0-009F-5MSN";
        $this->_Query_Parameters .= ";content=3";
        $this->_Query_Parameters .= ";info=true";
        $this->_Query_Parameters .= ";infolvl=0";
        self::$_Total_Itineraires = 0;
        self::$_Duplicate_Itineraires = 0;

    }
        
    public static function __Increment_Total_Itineraires(): void {
        self::$_Total_Itineraires++ ;
    }

    public static function __Increment_Duplicate_Itineraires(): void {
        self::$_Duplicate_Itineraires++ ;
    }




    public function processRequest(): void
    {
           

        //echo("DEBUG : Start ProcessRequest" . "<br>");

        if ($this->Get_Step1_Execution_Status() == true) {

            //echo("DEBUG : Drop_Table " . $GLOBALS["cgt_itineraires_tmp"] . "<br>");
            $this->gateway->Drop_Table($GLOBALS["cgt_itineraires_tmp"]);
    
            //echo("DEBUG : Create_DB_Table_Itineraires" . $GLOBALS["cgt_itineraires_tmp"] . "<br>");
            $this->gateway->Create_DB_Table_Itineraires($GLOBALS["cgt_itineraires_tmp"]);
    
            //echo("DEBUG : Process_Json_Files" . "<br>");
            $this->Process_Json_Files();
    
            //echo("DEBUG : Drop_Table" . $GLOBALS["cgt_itineraires"] . "<br>");
            $this->gateway->Drop_Table($GLOBALS["cgt_itineraires"]);
    
            //echo("DEBUG : Rename_Table" . $GLOBALS["cgt_itineraires_tmp"] . " to " . $GLOBALS["cgt_itineraires"] . "<br>");
            $this->gateway->Rename_Table($GLOBALS["cgt_itineraires_tmp"], $GLOBALS["cgt_itineraires"]);
    
            array_push(errorHandler::$Run_Information, ["Info", "" . PHP_EOL]);
            array_push(errorHandler::$Run_Information, ["Info", self::$_Duplicate_Itineraires . " duplicate itineraires records." . PHP_EOL]);
            array_push(errorHandler::$Run_Information, ["Info", self::$_Total_Itineraires . " new itineraires." . PHP_EOL]);
    
        } else {
            array_push(errorHandler::$Run_Information, ["Critical", "" . PHP_EOL]);
            array_push(errorHandler::$Run_Information, ["Critical", "Step 1 - retrieve Web Service data FAILED." . PHP_EOL]);
            array_push(errorHandler::$Run_Information, ["Critical", "Step 2 - aborted and no data updated" . PHP_EOL]);     
        }

        //echo("DEBUG : End of process" . "<br>");

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


        $json_file = $GLOBALS['cgt_Itineraires_Json_File'];

        $itineraires = JsonMachine\Items::fromFile($json_file, ['pointer' => '/offre']);

        foreach ($itineraires as $itineraire) {

            $itineraire = json_decode(json_encode($itineraire), true);

            $DB_Fields = array();

            $DB_Fields["nom"] = $itineraire["nom"];
            $DB_Fields["localite"] = $itineraire["adresse1"]["localite"][0]["value"];
            $DB_Fields["organisme"] = $itineraire["adresse1"]["organisme"]["label"];


            $relOffre = $itineraire["relOffre"];

            if (empty($relOffre)) {
                $DB_Fields["gpx_url"] = "";                
            } else {
                $DB_Fields["gpx_url"] = $this->Get_GPX_Url($relOffre);    
            }




            foreach ($itineraire["spec"] as $spec ) {

                
                switch ($spec["urn"]) {

                    case "urn:fld:urlweb":
                        $DB_Fields["urlweb"] = $spec["value"];
                        break;

                    case "urn:fld:idreco":
                        $DB_Fields["idreco"] = $spec["value"];
                        break;

                    case "urn:fld:typecirc":
                        $DB_Fields["typecirc"] = $spec["valueLabel"][0]["value"];
                        break;

                    case "urn:fld:signal":
                        $DB_Fields["signal"] = $spec["valueLabel"][0]["value"];
                        break;

                    case "urn:fld:dist":
                        $DB_Fields["distance"] = $spec["value"];
                        break;

                    case "urn:fld:hdifmin":
                        $DB_Fields["hdifmin"] = $spec["value"];
                        break;

                    case "urn:fld:hdifmax":
                        $DB_Fields["hdifmax"] = $spec["value"];
                        break;
                    
                    default:

                    }                             
            }



            $this->gateway->New_Itineraire($DB_Fields);

            
            

        }
    }
    
    private function Get_GPX_Url(array $relOffre): string  {

        foreach ($relOffre as $key => $spec) {

            if ($spec["urn"] != "urn:lnk:media:autre") {
                continue;
            }

            foreach ($spec["offre"]["spec"] as $media_spec ) {

                if ($media_spec["urn"] == "urn:fld:url") {
                    if ( strtoupper(substr($media_spec["value"],-4)) == strtoupper(".gpx")) {
                        return $media_spec["value"];
                    }
                }


            }
   
    
        }

        return "";
    
    }

    private function Get_Step1_Execution_Status(): bool {


        $status = $this->gateway->Get_Step1_Status();

        if (strtoupper($status) == "FAILED") {
            return false;
        } else {
            return true;
        } 
        return true;




    }



}

