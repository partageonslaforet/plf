<?php

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;

class Database 
{

    private string $_error_message;
    private int $start_time;
    private int $end_time;

    // Construct the class

    public function __construct(private $host,
                                private $name,
                                private $user,
                                private $password) {
    
        $this->_error_message = "";
        $this->start_time = strtotime(date("Y-m-d H:i:s"));
        $this->end_time = strtotime(date("Y-m-d H:i:s"));
    
    }



    // create the connection to the database

    public function getConnection(): PDO | false
    {

        $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";

        try {

            $connection = new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_TIMEOUT => 3600
            ]);
    
        } catch (PDOException $e) {


            switch ($e->getCode()) {
                case 1049:                      // Database does not exist.
                    throw new pdoDBException(1049, $e, "SQL Database does not exist : " . $e->getMessage(), );
                    $x = 4;

                case 2002:                      // Database is unreachable
                    throw new pdoDBException(2002, $e, "Unable to access database : " . $e->getMessage(), );
                
                default:
                    throw new pdoDBException($e->getCode(), $e, "unexpected error : " . $e->getMessage(), );

                }

        } catch (Exception $e) {

        }

        return $connection;


    }


    public static function drop_Table(PDO $conn, string $tablename): bool {


        if ($GLOBALS["debug"]) {print_r("DEBUG drop_Table Start - tablename : " . $tablename . " - database.php <br>");}   
        
        $RC = false;

        $sql = "DROP TABLE IF EXISTS $tablename";

        try {
            $stmt = $conn->prepare($sql);

            $RC = $stmt->execute();
    
        } catch(PDOException $e) {

            if ($GLOBALS["debug"]) {
                print_r("DEBUG drop_Table Error - tablename : " . $tablename . " - database.php <br>"); 
                print_r($e);
                print_r("DEBUG <br>");
                print_r("DEBUG e->getCode() value : " . $e->getCode() . "<br>");
            }
            switch ($e->getCode()) {
                
                default:
                    throw new pdoDBException($e->getCode(), $e, "unexpected error : " . $e->getMessage(), );

                }


        }

        if ($GLOBALS["debug"]) {print_r("DEBUG drop_Table End - tablename : " . $tablename . " - database.php <br>");}  

        return $RC;

 


    }


    public static function drop_View(PDO $conn, string $viewname): bool {

        if ($GLOBALS["debug"]) {print_r("DEBUG drop_view Start - viewname : " . $viewname . " - database.php <br>");} 

        $RC = false;

        $sql = "DROP VIEW IF EXISTS $viewname";

        try {
            $stmt = $conn->prepare($sql);

            $RC = $stmt->execute();
    
        } catch(PDOException $e) {

            if ($GLOBALS["debug"]) {
                print_r("DEBUG drop_view Error - viewname : " . $viewname . " - database.php <br>"); 
                print_r($e);
            }

        }
        if ($GLOBALS["debug"]) {print_r("DEBUG drop_view End - viewname : " . $viewname . " - database.php <br>");}  
        
        return $RC;

    }


    public static function rename_Table(PDO $conn, string $old_tablename, string $new_tablename): bool {

        $sql = "ALTER TABLE $old_tablename RENAME $new_tablename";

        $stmt = $conn->prepare($sql);

        $RC = $stmt->execute();
    
        return $RC;
    
    }


    public function update_LastRuntime(string $cron_load, bool $start): void {


        $date_to_update = null;

        if ($start) {           // start of run
            $date_to_update =  date("Y-m-d H:i:s", $this->start_time);
            $message = "run failed.";

            // echo "$cron_load - start run at " . date("d-m-Y H:i:s", $this->start_time) . PHP_EOL;
        
        } else {                // end run

            $this->end_time = strtotime(date("Y-m-d H:i:s"));
            $date_to_update =  date("Y-m-d H:i:s", $this->end_time);
            $elapse_Time_seconds = $this->end_time - $this->start_time;
            $Time_seconds = $elapse_Time_seconds % 60;
            $Time_hours = $elapse_Time_seconds / 60;
            $Time_minutes = (int)$Time_hours % 60;
            $Time_hours = (int)($Time_hours / 60);            


            $message = "run succesfull. Elapse time : " . 
                        substr("00" . $Time_hours, -2) . ":" . 
                        substr("00" . $Time_minutes, -2) . ":" . 
                        substr("00" . $Time_seconds, -2);  

        }



        $sql = "UPDATE $GLOBALS[plf_infos] 
                SET infos_Date = :Date,
                    infos_Value = :message
                WHERE Infos_Name = :cron_load";


        try {
            $conn = $this->getConnection();
            $stmt = $conn->prepare($sql);
            
            $stmt->bindValue(":Date", $date_to_update, PDO::PARAM_STR);
            $stmt->bindValue(":message", $message, PDO::PARAM_STR);
            $stmt->bindValue(":cron_load", $cron_load, PDO::PARAM_STR);

            $stmt->execute();

        } catch (pdoException $e) {


            switch ($e->getCode()) {
                case 1049:                      // Database does not exist
                    throw new pdoDBException(1049, $e, "SQL Database does not exist : " . $e->getMessage(), );
                    $x = 3;
                    exit;

                case 2002:                      // Database is unreachable
                    throw new pdoDBException(2002, $e, "Unable to access database : " . $e->getMessage(), );

                default:
                    throw new pdoDBException($e->getCode(), $e, "unexpected error : " . $e->getMessage(), );

                }

                $SQL_Error = $e->errorInfo[1];



                
                switch ($SQL_Error) {
                    default:
                        throw new pdoDBException($SQL_Error, $e, "SQL Error : " . $e->getMessage() . " --- " );

                }
            } catch (Exception $e) {

            }

    }




    // retrieve the last error message.


    public function Get_Error_Message()
    {

        return $this->_error_message;
    }


}