<?php

use PHPMailer\PHPMailer\PHPMailer;

class ErrorHandler {


    public static array $Run_Information;


    public static function handleException(Throwable $exception): void
    {
        http_response_code(500);


        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        ]);


       self::Send_eMail("ERROR", 
                        implode(" - ", [
                            "code" => $exception->getCode(),
                            "message" => $exception->getMessage(),
                            "file" => $exception->getFile(),
                            "line" => $exception->getLine()
                            ]),
                        $exception->getTrace());


    }


    public static function handleError(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline): bool 
        {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);

        }


    public static function Send_eMail(string $info, string $message, array $trace): void {

        $fmt_trace = "<pre>";
        $fmt_trace .= print_r($trace, true);
        $fmt_trace .= "</pre>";
        
        // $trace = preg_replace("/\n/", "<br>", $trace);

        $plf_mail = new PHPMailer();
        $plf_mail->From = "Christian.lurkin@hotmail.com";
        $plf_mail->FromName = "Christian Lurkin PLF";
        $plf_mail->addAddress("christian.lurkin@gmail.com");
        $plf_mail->addReplyTo("Christian.lurkin@hotmail.com");
        $plf_mail->isHTML(true);
        $plf_mail->Subject = "PLF ERROR Launching task - ";
    
        $plf_mail->AltBody = "Run Log for spw API call.";
        
    
        
    
    
        $plf_mail->Body = "<br><i>Run Log for spw API call.</i> - run of " .date("d/m/Y H:i:s") . "<br><br>";

    
        $plf_mail->Body .= "(<b>" . $info . "</b>) - " . $message;
        $plf_mail->Body .= "<br>" . $fmt_trace . "<br>";

    
        if ( !$plf_mail->send()) {
            echo "Mailer Error: " . $plf_mail->ErrorInfo;
        } else {
            echo "message successfully sent.";
        }
    
    
    }

}


class pdoDBException extends PDOException {

    private string $_code;
    private string $_msg;

    public function __construct(string $SQLerrorCode, PDOException $e, string $customString) {

        $this->_code = $SQLerrorCode;
        $this->_msg = $customString;


        if ( ! empty($e->errorInfo[1])) {

            // $SQLstate = $e->errorInfo[0];
            // $SQLerrorCode = $e->errorInfo[1];
            
            switch ($SQLerrorCode) {

                case 1049:
                    $this->_code = $SQLerrorCode;
                    $this->_msg =  "SQL Database does not exist : " . $customString;
                    break;

                case 1062:
                    $this->_code = $SQLerrorCode;
                    $this->_msg =  "Duplicate record for KEYG : " . $customString;
                    break;
    
                case 2002:
                    $this->_code = $SQLerrorCode;
                    $this->_msg =  "MySql database is not accessible : " . $customString;
                    break;
        
    
                default:            
                    $this->_code = $SQLerrorCode;
                    $this->_msg =  $SQLerrorCode . " - SQL Statment : " . $customString;
                    $this->_msg = preg_replace("/\r\n/", "", $this->_msg);
                    $this->_msg = preg_replace("/\s+/", " ", $this->_msg);            
                    break;
            }
        }


       parent::__construct($this->_msg , (int) $this->_code, $e);
    }



}