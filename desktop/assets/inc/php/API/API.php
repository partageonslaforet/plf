<?php
/**
*  DEBUGGING : XDEBUG_SESSION=thunder
*/

$Print_Mail_Title = "";
$Print_Mail_header = "";
$Print_Mail_Footer = "";

use PHPMailer\PHPMailer\PHPMailer;


require_once __DIR__ . "/../Parameters.php";

$requestUri = $_SERVER["REQUEST_URI"];
$requestUri = preg_replace("/(\?)*XDEBUG_SESSION=thunder/", "",$requestUri);


$Url_cleaned = preg_replace("/.*\/API.php/i", "", $requestUri);

$parts = explode("/",$Url_cleaned);

// change all parts names to uppercase (this does the trick)
$parts = array_flip($parts);
$parts = array_change_key_case($parts,CASE_UPPER);
$parts = array_flip($parts);



/**
 * 
 * possible values for parts array :
 * 
 * 
 *  0 = ""
 *  1 = "api"
 *  2 = "spw"
 *  3 = "chasses"
 *  4 = "1"
 *
 *  0 = ""
 *  1 = "api"
 *  2 = "spw"
 *  3 = "territoires"
 *  4 = "2"
 *
 *  0 = ""
 *  1 = "api"
 *  2 = "spw"
 *  3 = "cc" 
 *  4 = "0"
 * 
 *  0 = ""
 *  1 = "api"
 *  2 = "cgt"
 *  3 = "itineraires"
 * 

 * 
 */


if ($parts[1] != "API") {

    http_response_code(404);
    exit;
}



if ($parts[2] == "CGT" and $parts[3] == "ITINERAIRES" and $parts[4] == "STEP1") {

    echo("<pre>");
    print_r( json_encode([
        "0" => "", 
        "1" => "api",
        "2" => "cgt",
        "3" => "itineraires",
        "4" => "STEP1"
        ]));
    echo("</pre>");
    


    $Print_Mail_Title = "CGT Itineraires - retrieve Web Service json file.";
    $Print_Mail_header = "<br><i>CGT Itineraires - STEP 1 - Web Service call.</i> - run of " .date("d/m/Y H:i:s") . "<br><br>";     
  
    $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );
    $database->update_LastRuntime("cron_itineraires_step1", $start=true); 

    $controller = new CGT_Itineraires_Controller_Step1(); 

    array_push(errorHandler::$Run_Information, ["Info", "calling URI : api/cgt/itineraires/step1" . PHP_EOL]); 
    $controller->processRequest();
    
    $Print_Mail_Footer = "<br><br><i>CGT Itineraires - STEP 1 - Web Service call.</i> - run of " . date("d/m/Y H:i:s") . "<br><br>";

    $database->update_LastRuntime("cron_itineraires_step1", $start=false);

    Send_Run_logs_By_eMail();

    return;

}





if ($parts[2] == "CGT" and $parts[3] == "ITINERAIRES" and $parts[4] == "STEP2") {

    echo("<pre>");
    print_r( json_encode([
        "0" => "", 
        "1" => "api",
        "2" => "cgt",
        "3" => "itineraires",
        "4" => "STEP2"
        ]));
    echo("</pre>");
    


    $Print_Mail_Title = "CGT Itineraires - STEP 2 - rebuild database table.";
    $Print_Mail_header = "<br><i>Run Log for CGT Itineraires Step2 - Upload database table.</i> - run of " .date("d/m/Y H:i:s") . "<br><br>";     
  
    $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );
    $database->update_LastRuntime("cron_itineraires_step2", $start=true); 

    $gateway = new CGT_Itineraires_Gateway_Step2($database);
    $controller = new CGT_Itineraires_Controller_Step2($gateway); 

    array_push(errorHandler::$Run_Information, ["Info", "calling URI : api/cgt/itineraires/step2" . PHP_EOL]); 
    $controller->processRequest();
    
    $Print_Mail_Footer = "<br><br><i>CGT Itineraires - STEP 2 - Upload database table.</i> - run of " . date("d/m/Y H:i:s") . "<br><br>";

    $database->update_LastRuntime("cron_itineraires_step2", $start=false);

    Send_Run_logs_By_eMail();

    return;

}


if ($parts[2] == "SPW" and $parts[3] == "TERRITOIRES" and $parts[4] == "1") {

    echo json_encode([
        "0" => "", 
        "1" => "api",
        "2" => "spw",
        "3" => "territoires",
        "4" => "1",
        ]);


        $Print_Mail_Title = "Upload SPW Territoires.";
        $Print_Mail_header = "<br><i>Run Log for SPW Territoires API call.</i> - run of " .date("d/m/Y H:i:s") . "<br><br>"; 

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );
        $database->update_LastRuntime("cron_territoires", $start=true);
        
        $gateway = new SPW_Territoires_Gateway($database);
        $controller = new SPW_Territoires_Controller($gateway);   
        
        array_push(errorHandler::$Run_Information, ["Info", "calling URI : api/spw/territoires/1" . PHP_EOL]);         
        $controller->processRequest();
        
        $Print_Mail_Footer = "<br><br><i>END Run Log for SPW Territoires (1) API call.</i> - run of " . date("d/m/Y H:i:s") . "<br><br>";
        
        $database->update_LastRuntime("cron_territoires", $start=false);
        
        Send_Run_logs_By_eMail();

        return;

}


if ($parts[2] == "SPW" and $parts[3] == "CHASSES" and $parts[4] == "2") {

    echo json_encode([
        "0" => "", 
        "1" => "api",
        "2" => "spw",
        "3" => "chasses",
        "4" => "2",
        ]);


        $Print_Mail_Title = "Upload SPW Chasses.";
        $Print_Mail_header = "<br><i>Run Log for SPW Chasses API call.</i> - run of " .date("d/m/Y H:i:s") . "<br><br>";    
       
        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );
        $database->update_LastRuntime("cron_chasses", $start=true);
       
        $gateway = new SPW_Chasses_Gateway($database);
        $controller = new SPW_Chasses_Controller($gateway);   
       
        array_push(errorHandler::$Run_Information, ["Info", "calling URI : api/spw/chasses/2" . PHP_EOL]);         
       
        $controller->processRequest();
       
        $Print_Mail_Footer = "<br><br><i>END Run Log for SPW Chasses API call.</i> - run of " . date("d/m/Y H:i:s") . "<br><br>";
       
        $database->update_LastRuntime("cron_chasses", $start=false);
       
        Send_Run_logs_By_eMail();

        return;

}


if ($parts[2] == "SPW" and $parts[3] == "CC" and $parts[4] == "0") {

    echo json_encode([
        "0" => "", 
        "1" => "api",
        "2" => "spw",
        "3" => "cc",
        "4" => "0",
        ]);


        $Print_Mail_Title = "Upload SPW CC.";
        $Print_Mail_header = "<br><i>Run Log for SPW CC API call.</i> - run of " .date("d/m/Y H:i:s") . "<br><br>";    
       
        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );
        $database->update_LastRuntime("cron_cc", $start=true);
       
        $gateway = new SPW_CC_Gateway($database);
        $controller = new SPW_CC_Controller($gateway);   
       
        array_push(errorHandler::$Run_Information, ["Info", "calling URI : api/spw/CC/0" . PHP_EOL]);         
       
        $controller->processRequest();
       
        $Print_Mail_Footer = "<br><br><i>END Run Log for SPW CC API call.</i> - run of " . date("d/m/Y H:i:s") . "<br><br>";
       
        $database->update_LastRuntime("cron_cc", $start=false);
       
        Send_Run_logs_By_eMail();

        return;

}

if ($parts[2] == "SPW" and $parts[3] == "CANTONNEMENT" and $parts[4] == "1") {

    echo json_encode([
        "0" => "", 
        "1" => "api",
        "2" => "spw",
        "3" => "cantonnement",
        "4" => "1",
        ]);


        $Print_Mail_Title = "Upload SPW Cantonnement.";
        $Print_Mail_header = "<br><i>Run Log for SPW Cantonnement API call.</i> - run of " .date("d/m/Y H:i:s") . "<br><br>";    
       
        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );
        $database->update_LastRuntime("cron_cantonnement", $start=true);
       
        $gateway = new SPW_Cantonnement_Gateway($database);
        $controller = new SPW_Cantonnement_Controller($gateway);   
       
        array_push(errorHandler::$Run_Information, ["Info", "calling URI : api/spw/Cantonnement/1" . PHP_EOL]);         
       
        $controller->processRequest();
       
        $Print_Mail_Footer = "<br><br><i>END Run Log for SPW Cantonnement API call.</i> - run of " . date("d/m/Y H:i:s") . "<br><br>";
       
        $database->update_LastRuntime("cron_cantonnement", $start=false);
       
        Send_Run_logs_By_eMail();

        return;

}



function Send_Run_logs_By_eMail(): void {

    global $Print_Mail_Title;
    global $Print_Mail_header;
    global $Print_Mail_Footer;


    require_once __DIR__ . "../../vendor/autoload.php";

    

    if ($Print_Mail_Title == "Upload SPW Chasses.") {

        $cur_time = strtotime(date("H:i:s"));
        $start_time = strtotime("18:30:00");
        $end_time = strtotime("19:10:00");

        if ($cur_time < $start_time or $cur_time > $end_time) {
            echo "Job ended without sending email. start = " . $start_time . " - current : " . $cur_time . " - End : " . $end_time;
            return;
        }
    }




    $plf_mail = new PHPMailer();
    $plf_mail->From = "Christian.lurkin@hotmail.com";
    $plf_mail->FromName = "Christian Lurkin PLF";


   
    foreach($_ENV as $key => $mailRecipient) {


        if ( substr(strtoupper($key),0,7) == "LOGMAIL") {
            $plf_mail->addAddress($mailRecipient);
            echo "sending to mail " . $mailRecipient . "<br>";
        }
    }
    $plf_mail->addAddress("Christian.lurkin@hotmail.com");


    
    $plf_mail->addReplyTo("Christian.lurkin@hotmail.com");
    $plf_mail->isHTML(true);
    $plf_mail->Subject = "PLF logging - " . $Print_Mail_Title;

    $plf_mail->AltBody = "Run Log for spw API call.";



    $plf_mail->Body = $Print_Mail_header;

    foreach (errorHandler::$Run_Information as $run_item) {

        $run_item[1] = preg_replace("/\n/", "<br>", $run_item[1]);
        
        $plf_mail->Body .= "(<b>" . $run_item[0] . "</b>) - " . $run_item[1];

    }

    $plf_mail->Body .= $Print_Mail_Footer;

    if ( !$plf_mail->send()) {
        echo "Mailer Error: " . $plf_mail->ErrorInfo;
    } else {
        echo "message successfully sent.";
    }


}