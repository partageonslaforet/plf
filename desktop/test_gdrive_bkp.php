<?php

require_once __DIR__ . "/assets/inc/php/Parameters.php";

use PHPMailer\PHPMailer\PHPMailer;

// Global settings


global $pg_dump_Path;
global $rclone_Path;
global $zip_Path;
global $stdout;
global $stderr;

// initialize variables

$pg_dump_Path = '"C:\Program Files\PostgreSQL\16\bin\pg_dump.exe"';
$rclone_Path = '"C:\Users\chris\Downloads\rclone\rclone.exe"';
$zip_Path = '"C:\laragon\bin\git\usr\bin\gzip.exe"';

$stdout = "";
$stderr = "";

ErrorHandler::$Run_Information = [];

// Database settings

$dbhost = $_SERVER["PostgreSql_Server"]; // usually localhost
$dbuser = $_SERVER["PostgreSql_Login"]; //enter your database username here
$dbpass = $_SERVER["PostgreSql_Password"]; //enter your database password here
$dbname = $_SERVER["PostgreSql_DB"]; // enter your database name here


// email settings


$sendfrom = "Christian.lurkin@hotmail.com";
$sendfromName = "Christian Lurkin PLF";
$Print_Mail_Header = "<br><i>Run Log for the Backup of the PostgreSQL database $dbname.</i> - run of " .date("d/m/Y H:i:s") . "<br><br>";
$Print_Mail_Footer = "<br><br><i>END Run Log for Backup of the PostgreSQL database $dbname.</i> - run of " . date("d/m/Y H:i:s") . "<br><br>";
$replyTo = "Christian.lurkin@hotmail.com";
$subject = "PLF logging - Backup of PostgreSql database.";

$bodyemail = ""; 



// backup settings

$backupfile = $dbname . date("Y-m-d_H-i-s") . '.sql.gz';
$backup_retention = "20h";


/**
 * 
 *  Main process.
 * 
 */



// -------> Take Backup
array_push(errorHandler::$Run_Information, ["info", "Starting the backup process. - "  . date("d/m/Y H:i:s") . "<br>\n" ]);
Take_Database_Backup($backupfile);


// -------> Copy to Google Drive
array_push(errorHandler::$Run_Information, ["info", "Copying backup to Google Drive. - "  . date("d/m/Y H:i:s") . "<br>\n" ]);
Copy_Backup_File($backupfile);


// -------> Delete local file
array_push(errorHandler::$Run_Information, ["info", "Deleting local backup file $backupfile. - "  . date("d/m/Y H:i:s") . "<br>\n" ]);
Delete_Local_Backup($backupfile);


// -------> Remove the old backups

array_push(errorHandler::$Run_Information, ["info", "Starting backup cleanup. - "  . date("d/m/Y H:i:s") . "<br>\n" ]);
Remove_Old_Backups($backup_retention);

Send_Email();

exit;









/**
*
* take the backup of the database.
*
*/

function Take_Database_Backup($backupfile) {

    global $pg_dump_Path;
    global $zip_Path;
    global $dbhost;
    global $dbname;
    global $dbuser;
    global $stdout;
    global $stderr;

    $cmd = "$pg_dump_Path " . 
            " --host=$dbhost " . 
            " --format=plain " . 
            " --dbname=$dbname " . 
            " --username=$dbuser " . 
            " --clean " . 
            " --create " . 
            " --verbose " .
            " | " . 
            " $zip_Path > $backupfile";


    $return_value = call_external_program($cmd, $stdout, $stderr);



    if ($return_value <> 0 ) {

        array_push(errorHandler::$Run_Information, ["Error", "Error taking backup of the database $dbname \n" ]);
        array_push(errorHandler::$Run_Information, ["Error", "return value : $return_value \n" ]);
        Fill_Run_Log($stdout, "Info");
        Fill_Run_Log($stderr, "Warning");
        Send_Email();
    } else {
        
        Fill_Run_Log($stdout, "Info");
        Fill_Run_Log($stderr, "Info");
        array_push(errorHandler::$Run_Information, ["Info", "End backup process - " . date("d/m/Y H:i:s") . "<br><br>"  ]); 
    
    }

    return;

}



/**
 * 
 * copy the backup file to google drive
 * 
 * 
 */


function Copy_Backup_File($backupfile) {

    global $rclone_Path;
    global $stdout;
    global $stderr;

    $cmd = $rclone_Path . 
            " copy " . 
            $backupfile . 
            " google_drive:/PG_Backups";


     $return_value = call_external_program($cmd, $stdout, $stderr);


    if ($return_value <> 0 ) {

        array_push(errorHandler::$Run_Information, ["Error", "Error copying file to Google drive \n" ]);
        array_push(errorHandler::$Run_Information, ["Error", "return value : $return_value \n" ]);
        Fill_Run_Log($stdout, "Info");
        Fill_Run_Log($stderr, "Warning");
        Send_Email();
    } else {
        
        Fill_Run_Log($stdout, "Info");
        Fill_Run_Log($stderr, "Info");
        array_push(errorHandler::$Run_Information, ["Info", "End Copy process - " . date("d/m/Y H:i:s") . "<br><br>"  ]); 
    
    }




}



/**
 * 
 * Delete local file
 * 
 * 
 */

function Delete_Local_Backup($backupfile) {

    try {
        unlink($backupfile);
        }
    catch(Exception $e) {
        array_push(errorHandler::$Run_Information, ["Error", $e->getCode() . " - " . $e->getMessage() . "\n" ]);
        Send_Email();
    }
    
    array_push(errorHandler::$Run_Information, ["Info", "End Deleting local backup file. - " . date("d/m/Y H:i:s") . "<br><br>"  ]); 

}




/**
 * 
 * copy the backup file to google drive
 * 
 * 
 */

function Remove_Old_Backups($retention)
{

    global $rclone_Path;
    global $stdout;
    global $stderr;


    // delete the files

    $cmd = $rclone_Path .
        " delete " . 
        " --log-level INFO " .
        " google_drive:/PG_Backups " .
        " --include " .
        ' "PLF\d\d\d\d-\d\d-\d\d*" ' .
        " --min-age  $retention";


    $return_value = call_external_program($cmd, $stdout, $stderr);


    if ($return_value <> 0) {

        array_push(errorHandler::$Run_Information, ["Error", "Error removing Old backups rom Google drive \n"]);
        array_push(errorHandler::$Run_Information, ["Error", "return value : $return_value \n"]);
        Fill_Run_Log($stdout, "Info");
        Fill_Run_Log($stderr, "Warning");
        Send_Email();
    } 

    Fill_Run_Log($stdout, "Info");
    Fill_Run_Log($stderr, "Info");
    array_push(errorHandler::$Run_Information, ["Info", "End removing old backups - " . date("d/m/Y H:i:s") . "<br><br>"  ]);

}




/**
 * 
 *  Execute an external command and retrieve the stdout and stderr.
 */

function call_external_program($cmd, &$stdout=null, &$stderr=null) {

    echo("Executing command : " . PHP_EOL);
    echo($cmd . PHP_EOL);

    $pipes = array();
    $descriptorspec = [
        0 => ["pipe", "r"],  // stdin
        1 => ["pipe", "w"],  // stdout
        2 => ["pipe", "w"],  // stderr
     ];
    
    
    $proc = proc_open($cmd, $descriptorspec, $pipes, dirname(__FILE__), null);
    
    if (is_resource($proc)) {
    
        $status = proc_get_status($proc);
        while($status['running']) {
            $status = proc_get_status($proc);
        }
    
    
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        $return_value = proc_close($proc);
    }
    
    return $return_value;

}



/**
 *
 *  Send email result.
 *
*/

function Send_eMail() {


    global $sendfrom;
    global $sendfromName;
    global $replyTo;
    global $subject;
    global $bodyemail;
    global $Print_Mail_Header;
    global $Print_Mail_Footer;


    $plf_mail = new PHPMailer();
    $plf_mail->From = $sendfrom;
    $plf_mail->FromName = $sendfromName;
    $plf_mail->addReplyTo($replyTo);
    $plf_mail->Subject = $subject;
    $plf_mail->AltBody = "Alt " . $bodyemail;
    $plf_mail->isHTML(true);


    foreach($_ENV as $key => $mailRecipient) {


        if ( substr(strtoupper($key),0,7) == "LOGMAIL") {
            $plf_mail->addAddress($mailRecipient);
            echo "sending to mail " . $mailRecipient . "<br>";
        }
    }

    $plf_mail->Body = $Print_Mail_Header;

    foreach (errorHandler::$Run_Information as $run_item) {

        if ($run_item[1] == "\n") {
            $plf_mail->Body .= "<br>";
        } else {
            $run_item[1] = preg_replace("/\n/", "<br>", $run_item[1]);
            $plf_mail->Body .= "(<b>" . $run_item[0] . "</b>) - " . $run_item[1];
        }
    }

    $plf_mail->Body .= $Print_Mail_Footer;



    if ( !$plf_mail->send()) {
        echo "Mailer Error: " . $plf_mail->ErrorInfo;
    } else {
        echo "Mail successfully sent.";
    }

    exit;
}






/**
 * 
 * Fill run information
 * 
 *  Input a string containing \r\n
 *  Output an array of elements containing <info type> <type>
 * 
 */


 function Fill_Run_Log($output, $info_type) {
    
    $out_Array = array();

    if (strpos($output, "\r\n") > 0) {
        $out_Array = explode("\r\n", $output);
    } elseif (strpos($output, "\n") > 0) {
        $out_Array = explode("\n", $output);
    }
    

    foreach ($out_Array as $record) {
        array_push(errorHandler::$Run_Information, [$info_type, $record . "\n" ]);
    }
    

    
}