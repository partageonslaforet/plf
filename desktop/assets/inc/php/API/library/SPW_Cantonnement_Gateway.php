<?php

use PhpOffice\PhpSpreadsheet\Helper\Handler;

class SPW_Cantonnement_Gateway
{


    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();

    }



    public function New_Cantonnement(array $data) {



        $sql = "INSERT INTO " . $GLOBALS["spw_cantonnements_tmp"] . " (" .
                    " CAN," .
                    " PREPOSE," .
                    " GSM," . 
                    " CANTON," .
                    " TEL_CAN," .
                    " GEOM" .
                "  ) VALUES (" .
                    " :CAN," .
                    " :PREPOSE," .
                    " :GSM," .
                    " :CANTON," .
                    " :TEL_CAN," .
                    " :GEOM)";



        try {

            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindValue(":CAN", $data["CAN"], PDO::PARAM_INT);
            $stmt->bindValue(":PREPOSE", $data["PREPOSE"], PDO::PARAM_STR);
            $stmt->bindValue(":GSM", $data["GSM"], PDO::PARAM_STR);
            $stmt->bindValue(":CANTON", $data["CANTON"], PDO::PARAM_STR);
            $stmt->bindValue(":TEL_CAN", $data["TEL_CAN"], PDO::PARAM_STR);
            $stmt->bindValue(":GEOM", $data["GEOM"], PDO::PARAM_LOB);


            $stmt->execute();
            SPW_Cantonnement_Controller::__Increment_Total_Cantonnement();
            SPW_Cantonnement_Controller::__Update_List_Cantonnement($data["CAN"]);
            //array_push(errorHandler::$Run_Information, ["Info", "new Cantonnement: " . $data["CAN"] . " - " . $data["CANTON"] . PHP_EOL]);
            
            
            return $this->conn->lastInsertId();

        } catch (pdoException $e) {

            $SQL_Error = "9999";

            if (! empty($e->errorInfo[1]) ) {
                $SQL_Error = $e->errorInfo[1];
            }    



            switch ($SQL_Error) {
                case 1062:
                    // SPW_Cantonnement_Controller::__Increment_Duplicate_Cantonnement();
                    // array_push(errorHandler::$Run_Information, ["Warning", "Duplicate record for Cantonnement : " . $data["CAN"] . " - " . $data["CANTON"] . PHP_EOL]);
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

 

    public function Create_DB_Table_Cantonnement(string $tablename): bool 
    {

        $sql = "CREATE TABLE $tablename (
            `cantonnement_id` INT(10) NOT NULL AUTO_INCREMENT,
            `CAN` SMALLINT(5) NOT NULL,
            `PREPOSE` VARCHAR(30) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
            `GSM` VARCHAR(14) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
            `CANTON` VARCHAR(20) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
            `TEL_CAN` VARCHAR(14) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
            `GEOM` MEDIUMBLOB NULL DEFAULT NULL,
            PRIMARY KEY (`cantonnement_id`) USING BTREE,
            UNIQUE INDEX `CAN` (`CAN`) USING BTREE,
            INDEX `cantonnement_id` (`cantonnement_id`) USING BTREE
        )
        COLLATE='utf8mb4_unicode_ci'
        ENGINE=InnoDB
            ";
            
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





    public function Create_View_Cantonnement() {

        
        $sql = "CREATE VIEW " . $GLOBALS["spw_view_cantonnements"] . " AS
        SELECT
        `plf_spw_cantonnements`.`cantonnement_id` AS `cantonnement_id`,
        `plf_spw_cantonnements`.`CAN` AS `CAN`,
        `plf_spw_cantonnements`.`PREPOSE` AS `PREPOSE`,
        `plf_spw_cantonnements`.`GSM` AS `GSM`,
        `plf_spw_cantonnements`.`CANTON` AS `CANTON`,
        `plf_spw_cantonnements`.`TEL_CAN` AS `TEL_CAN`,
        `plf_spw_cantonnements`.`GEOM` AS `GEOM`,
        `plf_spw_cantonnements_adresses`.`direction` AS `direction`,
        `plf_spw_cantonnements_adresses`.`email` AS `email`,
        `plf_spw_cantonnements_adresses`.`attache` AS `attache`,
        `plf_spw_cantonnements_adresses`.`CP` AS `CP`,
        `plf_spw_cantonnements_adresses`.`localite` AS `localite`,
        `plf_spw_cantonnements_adresses`.`rue` AS `rue`,
        `plf_spw_cantonnements_adresses`.`numero` AS `numero`,
        `plf_spw_cantonnements_adresses`.`latitude` AS `latitude`,
        `plf_spw_cantonnements_adresses`.`longitude` AS `longitude`
      FROM (`plf_spw_cantonnements`
        LEFT JOIN `plf_spw_cantonnements_adresses`
          ON ((`plf_spw_cantonnements`.`CAN` = `plf_spw_cantonnements_adresses`.`num_canton`)));";
    
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

}