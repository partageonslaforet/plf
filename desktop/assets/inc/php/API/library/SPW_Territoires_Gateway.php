<?php

use PhpOffice\PhpSpreadsheet\Helper\Handler;

class SPW_Territoires_Gateway
{


    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();

    }



    public function New_Territoire(array $data) {


        $sql = "INSERT INTO " . $GLOBALS["spw_tbl_territoires_tmp"] . " (" .
                    " OBJECTID," .
                    " KEYG," .
                    " SAISON," .
                    " N_LOT," .
                    " NUGC," .
                    " SERVICE," .
                    " TITULAIRE_ADH_UGC," . 
                    " DATE_MAJ," .
                    " SHAPE" .
                "  ) VALUES (" .
                    " :OBJECTID," .
                    " :KEYG," .
                    " :SAISON," .
                    " :N_LOT," .
                    " :NUGC," .
                    " :SERVICE," .
                    " :TITULAIRE_ADH_UGC," .
                    " :DATE_MAJ," . 
                    " :SHAPE)";


        try {

            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindValue(":OBJECTID", $data["OBJECTID"], PDO::PARAM_INT);
            $stmt->bindValue(":KEYG", $data["KEYG"], PDO::PARAM_STR);
            $stmt->bindValue(":SAISON", $data["SAISON"], PDO::PARAM_INT);
            $stmt->bindValue(":N_LOT", $data["N_LOT"], PDO::PARAM_STR);

            $data["SHAPE"] = preg_replace('/\n\s+/', ' ', $data["SHAPE"]);
            $stmt->bindValue(":SHAPE", $data["SHAPE"] ?? "", PDO::PARAM_LOB);
            $stmt->bindValue(":NUGC", $data["NUGC"], PDO::PARAM_INT);
            $stmt->bindValue(":SERVICE", $data["SERVICE"], PDO::PARAM_STR);
            $stmt->bindValue(":TITULAIRE_ADH_UGC", $data["TITULAIRE_ADH_UGC"], PDO::PARAM_BOOL);
            $stmt->bindValue(":DATE_MAJ", $data["DATE_MAJ"], PDO::PARAM_STR);



            $stmt->execute();
            SPW_Territoires_Controller::__Increment_Total_Territoires();
            //array_push(errorHandler::$Run_Information, ["Info", "new territoire : KEYG = " . $data["KEYG"] . PHP_EOL]);
            return $this->conn->lastInsertId();

        } catch (pdoException $e) {

                $SQL_Error = $e->errorInfo[1];

                switch ($SQL_Error) {
                    case 1062:
                        SPW_Territoires_Controller::__Increment_Duplicate_Territoires();
                        array_push(errorHandler::$Run_Information, ["Warning", "Duplicate record for territoire : KEYG = " . $data["KEYG"]  . PHP_EOL]);
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

    public function Create_DB_Table_Territoires(string $tablename): bool 
    {


        $sql = "CREATE TABLE $tablename (
                    `OBJECTID` INT NULL DEFAULT NULL,
                    `SAISON` SMALLINT NOT NULL,
                    `KEYG` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                    `N_LOT` VARCHAR(10) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                    `SERVICE` VARCHAR(5) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                    `NUGC` SMALLINT NULL DEFAULT NULL,
                    `TITULAIRE_ADH_UGC` TINYINT(1) NOT NULL,
                    `DATE_MAJ` DATE NULL DEFAULT NULL,
                    `SHAPE` MEDIUMBLOB NULL DEFAULT NULL,
                    PRIMARY KEY (`N_LOT`, `SAISON`) USING BTREE,
                    UNIQUE INDEX `uk_Saison_N_lot` (`SAISON`, `N_LOT`) USING BTREE)
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


    public function Create_View_Territoires() {

        

        $sql = "CREATE VIEW " . $GLOBALS["spw_view_territoires"] . " AS
        SELECT
          `plf_spw_territoires`.`KEYG` AS `KEYG`,
          `plf_spw_territoires`.`SAISON` AS `SAISON`,
          `plf_spw_territoires`.`N_LOT` AS `N_LOT`,
          `plf_spw_territoires`.`SERVICE` AS `SERVICE`,
          `plf_spw_cantonnements`.`CAN` AS `CAN`,
          `plf_spw_cantonnements`.`CANTON` AS `CANTON`,
          `plf_spw_cantonnements`.`GSM` AS `GSM`,
          `plf_spw_cantonnements`.`PREPOSE` AS `PREPOSE`,
          `plf_spw_cantonnements`.`TEL_CAN` AS `TEL_CAN`,
          `plf_spw_territoires`.`TITULAIRE_ADH_UGC` AS `TITULAIRE_ADH_UGC`,
          `plf_spw_territoires`.`DATE_MAJ` AS `DATE_MAJ`,
          `plf_spw_territoires`.`SHAPE` AS `SHAPE`,
          `plf_spw_cantonnements_adresses`.`direction` AS `direction_CANTON`,
          `plf_spw_cantonnements_adresses`.`email` AS `email_CANTON`,
          `plf_spw_cantonnements_adresses`.`attache` AS `attache_CANTON`,
          `plf_spw_cantonnements_adresses`.`CP` AS `CP_CANTON`,
          `plf_spw_cantonnements_adresses`.`localite` AS `localite_CANTON`,
          `plf_spw_cantonnements_adresses`.`rue` AS `rue_CANTON`,
          `plf_spw_cantonnements_adresses`.`numero` AS `numero_CANTON`,
          `plf_spw_cantonnements_adresses`.`latitude` AS `latitude_CANTON`,
          `plf_spw_cantonnements_adresses`.`longitude` AS `longitude_CANTON`,
          `plf_spw_territoires`.`NUGC` AS `NUGC_CC`,
          `view_spw_cc`.`N_AGREMENT_CC` AS `N_AGREMENT_CC`,
          `view_spw_cc`.`DENOMINATION_CC` AS `DENOMINATION_CC`,
          `view_spw_cc`.`ABREVIATION_CC` AS `ABREVIATION_CC`,
          `view_spw_cc`.`RUE_CC` AS `RUE_CC`,
          `view_spw_cc`.`NUM_CC` AS `NUM_CC`,
          `view_spw_cc`.`CP_CC` AS `CP_CC`,
          `view_spw_cc`.`LOCALITE_CC` AS `LOCALITE_CC`,
          `view_spw_cc`.`NOM_PSDT_CC` AS `NOM_PSDT_CC`,
          `view_spw_cc`.`PRENOM_PSDT_CC` AS `PRENOM_PSDT_CC`,
          `view_spw_cc`.`NOM_SECR_CC` AS `NOM_SECR_CC`,
          `view_spw_cc`.`PRENOM_SECR_CC` AS `PRENOM_SECR_CC`,
          `view_spw_cc`.`SUPERFICIE_CC` AS `SUPERFICIE_CC`,
          `view_spw_cc`.`LIEN_CARTE_CC` AS `LIEN_CARTE_CC`,
          `view_spw_cc`.`email_CC` AS `email_CC`,
          `view_spw_cc`.`site_internet_CC` AS `site_internet_CC`,
          `view_spw_cc`.`logo_CC` AS `logo_CC`,
          `view_spw_cc`.`latitude_CC` AS `latitude_CC`,
          `view_spw_cc`.`longitude_CC` AS `longitude_CC`
        FROM (((`plf_spw_territoires`
          LEFT JOIN `plf_spw_cantonnements`
            ON ((`plf_spw_territoires`.`SERVICE` = `plf_spw_cantonnements`.`CAN`)))
          LEFT JOIN `plf_spw_cantonnements_adresses`
            ON ((`plf_spw_cantonnements`.`CAN` = `plf_spw_cantonnements_adresses`.`num_canton`)))
          LEFT JOIN `view_spw_cc`
            ON ((`view_spw_cc`.`nugc_CC` = `plf_spw_territoires`.`NUGC`)));";
    
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