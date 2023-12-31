<?php

use PhpOffice\PhpSpreadsheet\Helper\Handler;

class SPW_Chasses_Gateway
{


    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();

    }



    public function New_Chasse(array $data) {

        $Territoire_Exists = PLF::__Check_If_Territoire_Exists($this->conn, $data["KEYG"]);

        if ( ! $Territoire_Exists) {

            array_push(errorHandler::$Run_Information, ["Warning", "Territoire " . $data["KEYG"] . " does not exist for chasse " . $data["KEYG"] . PHP_EOL]);
            return;
        }


        $sql = "INSERT INTO " . $GLOBALS["spw_chasses_tmp"] . " (" .
                    " SAISON," .
                    " N_LOT," .
                    " NUM," .
                    " MODE_CHASSE," .
                    " DATE_CHASSE," . 
                    " FERMETURE," .
                    " KEYG" .
                "  ) VALUES (" .
                    " :SAISON," .
                    " :N_LOT," .
                    " :NUM," .
                    " :MODE_CHASSE," .
                    " :DATE_CHASSE," .
                    " :FERMETURE," .
                    " :KEYG)";



        try {

            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindValue(":SAISON", $data["SAISON"], PDO::PARAM_INT);
            $stmt->bindValue(":N_LOT", $data["N_LOT"], PDO::PARAM_STR);
            $stmt->bindValue(":NUM", $data["NUM"], PDO::PARAM_INT);
            $stmt->bindValue(":MODE_CHASSE", $data["MODE_CHASSE"], PDO::PARAM_STR);
            $stmt->bindValue(":DATE_CHASSE", $data["DATE_CHASSE"], PDO::PARAM_STR);
            $stmt->bindValue(":FERMETURE", $data["FERMETURE"], PDO::PARAM_STR);
            $stmt->bindValue(":KEYG", $data["KEYG"], PDO::PARAM_STR);


            $stmt->execute();
            SPW_Chasses_Controller::__Increment_Total_Chasses();
            //array_push(errorHandler::$Run_Information, ["Info", "new chasse : KEYG = " . $data["KEYG"] . PHP_EOL]);
            
            return $this->conn->lastInsertId();

        } catch (pdoException $e) {

                $SQL_Error = $e->errorInfo[1];

                switch ($SQL_Error) {
                    case 1062:
                        SPW_Chasses_Controller::__Increment_Duplicate_Chasses();
                        array_push(errorHandler::$Run_Information, ["Warning", "Duplicate record for chasse : KEYG = " . $data["KEYG"]  . " and date " . $data["DATE_CHASSE"] . "/" . $data["NUM"] . PHP_EOL]);
                        break;
                    default:
                        throw new pdoDBException($SQL_Error, $e, "SQL Error :" . $this->rebuildSql($sql,$data));

                }
            } catch (Exception $e) {

            }

    }



    public function Drop_Table(string $tableName) {

        $rc = Database::drop_Table($this->conn, $tableName);

    }

    public function Drop_View(string $viewName) {

        $rc = Database::drop_View($this->conn, $viewName);

    }

    public function Rename_Table(string $Table_tmp, string $Table_final) {

        $rc = Database::rename_Table($this->conn, $Table_tmp, $Table_final);

    }

 

    public function Create_DB_Table_Chasses(string $tablename): bool 
    {



        $sql = "CREATE TABLE $tablename (
                    `chasse_id` INT NOT NULL AUTO_INCREMENT,
                    `SAISON` SMALLINT NOT NULL,
                    `N_LOT` VARCHAR(10) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                    `NUM` INT NOT NULL,
                    `MODE_CHASSE` VARCHAR(9) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                    `DATE_CHASSE` DATE NOT NULL,
                    `FERMETURE` VARCHAR(1) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                    `KEYG` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                    INDEX chasse_id (chasse_id),
                    PRIMARY KEY (`chasse_id`) USING BTREE,
                    UNIQUE INDEX `KEYG_DATE_NUM` (`KEYG`, `DATE_CHASSE`, `NUM`) USING BTREE)
            COLLATE='utf8mb4_unicode_ci'
            ENGINE=InnoDB;";
            
        try {

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

        } catch (pdoException $e) {

            $SQL_Error = $e->errorInfo[1];

            switch ($SQL_Error) {

                default:
                    throw new pdoDBException($SQL_Error, $e, "SQL Error :" . $sql);

            }
        } catch (Exception $e) {

        }    


        

        return true;

    }

    public function Create_View_Chasses() {

        

        $sql = "CREATE VIEW " . $GLOBALS["spw_view_chasses"] . " AS
        SELECT
            `plf_spw_chasses`.`SAISON` AS `SAISON`,
            `plf_spw_chasses`.`N_LOT` AS `N_LOT`,
            `plf_spw_chasses`.`NUM` AS `NUM`,
            `plf_spw_chasses`.`MODE_CHASSE` AS `MODE_CHASSE`,
            `plf_spw_chasses`.`chasse_id` AS `chasse_id`,
            `plf_spw_chasses`.`DATE_CHASSE` AS `DATE_CHASSE`,
            `plf_spw_chasses`.`FERMETURE` AS `FERMETURE`,
            `plf_spw_chasses`.`KEYG` AS `KEYG`,
            `plf_spw_territoires`.`SEQ` AS `SEQ`,
            `plf_spw_territoires`.`NUGC` AS `NUGC`
        FROM (
            `plf_spw_chasses` 
            LEFT JOIN `plf_spw_territoires` 
                ON (`plf_spw_chasses`.`SAISON` = `plf_spw_territoires`.`SAISON` 
                    AND `plf_spw_chasses`.`N_LOT` = `plf_spw_territoires`.`N_LOT`));";
    
        try {

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

        } catch (pdoException $e) {

            $SQL_Error = $e->errorInfo[1];

            switch ($SQL_Error) {

                default:
                    throw new pdoDBException($SQL_Error, $e, "SQL Error :" . $sql);

            }
        } catch (Exception $e) {

        }    

        return true;

    }








    private function rebuildSql($string,$data) {
        $indexed=$data==array_values($data);
        foreach($data as $k=>$v) {
            if(is_string($v)) $v="'$v'";
            if(is_null($v)) $v = "''";
            if($indexed) $string=preg_replace('/\?/',$v,$string,1);
            else $string=str_replace(":$k",$v,$string);
        }
        return $string;
    }

}