<?php


class CGT_Itineraires_Controller_Step1
{


    private string $_Rest_Url;
    private string $_Query_Parameters;
    public static DateTime $_Run_Time;



    public function __construct()
    {

        $this->_Rest_Url = "";

        ErrorHandler::$Run_Information = [];

        $this->_Query_Parameters = "OTH-A0-009F-5MSN";
        $this->_Query_Parameters .= ";content=3";
        $this->_Query_Parameters .= ";info=true";
        $this->_Query_Parameters .= ";infolvl=0";

    }
        

    public function processRequest(): void
    {
           

        echo("DEBUG : Start ProcessRequest" . "<br>");


        $this->Prepare_Web_Service_URL();

        echo("DEBUG : Get_Json_Data_Into_Files" . "<br>");
        $RC = $this->Get_Json_Data_Into_Files();

        if ($RC == false) { return;}

        array_push(errorHandler::$Run_Information, ["Info", "" . PHP_EOL]);
        array_push(errorHandler::$Run_Information, ["Info", "Get all web service Itineraires" . PHP_EOL]);
        array_push(errorHandler::$Run_Information, ["Info", "Successfull end of process."]);

        echo("DEBUG : End of process" . "<br>");

    }





    /**=======================================================================
     * 
     * Format the web Service URL without the query itself.
     * 
     *   ARGUMENTS :
     * 
     *   INPUT : https://pivotweb.tourismewallonie.be/PivotWeb-3.1/query/OTH-A0-009F-5MSN;content=3;info=true
     * 
     *   OUTPUT :  $Rest_Url (containing the common fields of the web Service URL)
     * 
     * =======================================================================*/

    private function Prepare_Web_Service_URL(): void
    {
        
        $this->_Rest_Url = $GLOBALS['cgt_URL'];

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
    private function Get_Json_Data_Into_Files(): Bool
    { 
    

            $json_file = $GLOBALS['cgt_Itineraires_Json_File'];

            try  {
                unlink($json_file);             // delete file if it exists
            } catch (Exception $e) {

            }
            
            $fp = fopen($json_file, "w");   // create file for writing
    
       
    
            $curl_Url = $this->_Rest_Url . $this->_Query_Parameters;
    

            array_push(errorHandler::$Run_Information, ["Info", "Retrieving CGT API itineraires " . PHP_EOL]);
    
    
            $Curl = curl_init();
    
            $http_header = array(
                "Content-Type: application/json", 
                "Accept: application/json",
                "ws_key: cd8680b9-43c8-4faf-a6a8-d9574e2470e3"
            );

            curl_setopt($Curl, CURLOPT_HEADER, 0);
            curl_setopt($Curl, CURLOPT_URL, $curl_Url);
            curl_setopt($Curl, CURLOPT_FILE, $fp);
            curl_setopt($Curl, CURLOPT_TIMEOUT, 600);
            curl_setopt($Curl, CURLOPT_VERBOSE, true);
            curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($Curl, CURLOPT_HTTPHEADER, $http_header);
    
            $RC_Bool = curl_exec($Curl);
            $headers = curl_getinfo($Curl);

            curl_close($Curl);

            fclose($fp);

            switch ($headers["http_code"]) {
                case 200: 
                    break;
                case 502:
                    array_push(errorHandler::$Run_Information, ["CRITICAL", "Bad Gateway : http_code = " . $headers["http_code"] . " Calling URL = " . $headers["url"] . PHP_EOL]);
                    return false;
                case 503:
                    array_push(errorHandler::$Run_Information, ["CRITICAL", "CGT service unavailable : http_code = " . $headers["http_code"] . " Calling URL = " . $headers["url"] . PHP_EOL]);
                    return false;
                case 404:
                    array_push(errorHandler::$Run_Information, ["CRITICAL", "CGT resource page not found : http_code = " . $headers["http_code"] . " Calling URL = " . $headers["url"] . PHP_EOL]);
                    return false;
                default:
                    array_push(errorHandler::$Run_Information, ["CRITICAL", "CGT service call error : http_code = " . $headers["http_code"] . " Calling URL = " . $headers["url"] . PHP_EOL]);
                    return false;
                
            }
       
        return true;
    }

 
}

