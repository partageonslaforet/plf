<?php

use PhpOffice\PhpSpreadsheet\Helper\Handler;

class SPW_CC_Gateway
{


    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();

    }



    public function New_CC(array $data) {


        $sql = "INSERT INTO " . $GLOBALS["spw_cc_tmp"] . " (" .
                    " OBJECTID," .
                    " N_AGREMENT," .
                    " DENOMINATION," .
                    " ABREVIATION," .
                    " RUE_CC," .
                    " NUM_CC," . 
                    " CP_CC," .
                    " LOCALITE_CC," .
                    " NOM_PSDT," .
                    " PRENOM_PSDT," .
                    " NOM_SECR," .
                    " PRENOM_SECR," .
                    " SUPERFICIE," .
                    " LIEN_CARTE," .
                    " GEOM" .
                "  ) VALUES (" .
                    " :OBJECTID," .
                    " :N_AGREMENT," .
                    " :DENOMINATION," .
                    " :ABREVIATION," .
                    " :RUE_CC," .
                    " :NUM_CC," .
                    " :CP_CC," .
                    " :LOCALITE_CC," .
                    " :NOM_PSDT," .
                    " :PRENOM_PSDT," .
                    " :NOM_SECR," .
                    " :PRENOM_SECR," .
                    " :SUPERFICIE," .
                    " :LIEN_CARTE," .
                    " :GEOM)";



        try {

            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindValue(":OBJECTID", $data["OBJECTID"], PDO::PARAM_INT);
            $stmt->bindValue(":N_AGREMENT", $data["N_AGREMENT"], PDO::PARAM_INT);
            $stmt->bindValue(":DENOMINATION", $data["DENOMINATION"], PDO::PARAM_STR);
            $stmt->bindValue(":ABREVIATION", $data["ABREVIATION"], PDO::PARAM_STR);
            $stmt->bindValue(":RUE_CC", $data["RUE_CC"], PDO::PARAM_STR);
            $stmt->bindValue(":NUM_CC", $data["NUM_CC"], PDO::PARAM_STR);
            $stmt->bindValue(":CP_CC", $data["CP_CC"], PDO::PARAM_STR);
            $stmt->bindValue(":LOCALITE_CC", $data["LOCALITE_CC"], PDO::PARAM_STR);
            $stmt->bindValue(":NOM_PSDT", $data["NOM_PSDT"], PDO::PARAM_STR);
            $stmt->bindValue(":PRENOM_PSDT", $data["PRENOM_PSDT"], PDO::PARAM_STR);
            $stmt->bindValue(":NOM_SECR", $data["NOM_SECR"], PDO::PARAM_STR);
            $stmt->bindValue(":PRENOM_SECR", $data["PRENOM_SECR"], PDO::PARAM_STR);
            $stmt->bindValue(":SUPERFICIE", $data["SUPERFICIE"], PDO::PARAM_STR);
            $stmt->bindValue(":LIEN_CARTE", $data["LIEN_CARTE"], PDO::PARAM_STR);
            $stmt->bindValue(":GEOM", $data["GEOM"], PDO::PARAM_LOB);


            $stmt->execute();
            SPW_CC_Controller::__Increment_Total_CC();
            //array_push(errorHandler::$Run_Information, ["Info", "new Conseil Cynégétique : " . $data["ABBREVIATION"] . " - " . $data["DENOMINATION"] . PHP_EOL]);
            
            return $this->conn->lastInsertId();

        } catch (pdoException $e) {

                $SQL_Error = $e->errorInfo[1];

                switch ($SQL_Error) {
                    case 1062:
                        SPW_CC_Controller::__Increment_Duplicate_CC();
                        array_push(errorHandler::$Run_Information, ["Warning", "Duplicate record for Conseil Cynégétique : " . $data["ABBREVIATION"] . " - " . $data["DENOMINATION"] . PHP_EOL]);
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

 

    public function Create_DB_Table_CC(string $tablename): bool 
    {


        $sql = "CREATE TABLE $tablename (
                    `cc_id` INT NOT NULL AUTO_INCREMENT,
                    `OBJECTID` SMALLINT NOT NULL,
                    `N_AGREMENT` SMALLINT NOT NULL,
                    `DENOMINATION` VARCHAR(300) NOT NULL,
                    `ABREVIATION` VARCHAR(300) NOT NULL,
                    `RUE_CC` VARCHAR(200) DEFAULT NULL,
                    `NUM_CC` VARCHAR(20) DEFAULT NULL,
                    `CP_CC` VARCHAR(12) DEFAULT NULL,
                    `LOCALITE_CC` VARCHAR(50) DEFAULT NULL,
                    `NOM_PSDT` VARCHAR(150) DEFAULT NULL,
                    `PRENOM_PSDT` VARCHAR(150) DEFAULT NULL,
                    `NOM_SECR` VARCHAR(150) DEFAULT NULL,
                    `PRENOM_SECR` VARCHAR(150) DEFAULT NULL,
                    `SUPERFICIE` DECIMAL(8,2) DEFAULT 0,
                    `LIEN_CARTE` VARCHAR(510) DEFAULT NULL,
                    `GEOM` MEDIUMBLOB DEFAULT NULL,
                    INDEX cc_id (cc_id),
                    PRIMARY KEY (`cc_id`) USING BTREE)
            COLLATE='utf8mb4_unicode_ci',
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





    public function Create_View_CC() {

        

        $sql = "CREATE VIEW " . $GLOBALS["spw_view_cc"] . " AS
            SELECT
            `plf_spw_cc`.`cc_id` AS `cc_id`,
            `plf_spw_cc_concordance`.`nugc` AS `nugc_CC`,
            `plf_spw_cc`.`N_AGREMENT` AS `N_AGREMENT_CC`,
            `plf_spw_cc`.`DENOMINATION` AS `DENOMINATION_CC`,
            `plf_spw_cc`.`ABREVIATION` AS `ABREVIATION_CC`,
            `plf_spw_cc`.`RUE_CC` AS `RUE_CC`,
            `plf_spw_cc`.`NUM_CC` AS `NUM_CC`,
            `plf_spw_cc`.`CP_CC` AS `CP_CC`,
            `plf_spw_cc`.`LOCALITE_CC` AS `LOCALITE_CC`,
            `plf_spw_cc`.`NOM_PSDT` AS `NOM_PSDT_CC`,
            `plf_spw_cc`.`PRENOM_PSDT` AS `PRENOM_PSDT_CC`,
            `plf_spw_cc`.`NOM_SECR` AS `NOM_SECR_CC`,
            `plf_spw_cc`.`PRENOM_SECR` AS `PRENOM_SECR_CC`,
            `plf_spw_cc`.`SUPERFICIE` AS `SUPERFICIE_CC`,
            `plf_spw_cc`.`LIEN_CARTE` AS `LIEN_CARTE_CC`,
            `plf_spw_cc_adresses`.`email` AS `email_CC`,
            `plf_spw_cc_adresses`.`site_internet` AS `site_internet_CC`,
            `plf_spw_cc_adresses`.`logo` AS `logo_CC`,
            `plf_spw_cc_adresses`.`latitude` AS `latitude_CC`,
            `plf_spw_cc_adresses`.`longitude` AS `longitude_CC`
        FROM ((`plf_spw_cc`
            LEFT JOIN `plf_spw_cc_adresses`
            ON ((`plf_spw_cc_adresses`.`Code` = `plf_spw_cc`.`ABREVIATION`)))
            JOIN `plf_spw_cc_concordance`
            ON ((`plf_spw_cc_concordance`.`cc_id` = `plf_spw_cc`.`cc_id`)))";
    
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