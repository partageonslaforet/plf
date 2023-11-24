<?php

class SPW_Territoires_Gateway
{

    private int $_sequence = 1;
    private string $_old_KEYG = "";
    private string $RC;

    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();

    }



    public function New_Territoire(array $data) {

        if ($data["KEYG"] == $this->_old_KEYG) {
            $this->_sequence++;
            SPW_Territoires_Controller::__Increment_Duplicate_Territoires();
            array_push(errorHandler::$Run_Information, ["Warning", "Duplicate record for territoire : KEYG = " . $data["KEYG"]  . PHP_EOL]);

        } else {
            $this->_old_KEYG = $data["KEYG"];
            $this->_sequence = 1;
        }

        $sql = "INSERT INTO " . $GLOBALS["spw_tbl_territoires_tmp"] . " (" .
                    " OBJECTID," .
                    " KEYG," .
                    " SAISON," .
                    " N_LOT," .
                    " SEQ, " .
                    " NUGC," .
                    " SERVICE," .
                    " TITULAIRE_ADH_UGC," . 
                    " GEOM, " .
                    " DATE_MAJ" .
                "  ) VALUES (" .
                    " :OBJECTID," .
                    " :KEYG," .
                    " :SAISON," .
                    " :N_LOT," .
                    " :SEQ," .
                    " :NUGC," .
                    " :SERVICE," .
                    " :TITULAIRE_ADH_UGC," .
                    " ST_GeomFromGeoJSON(:GEOM), " .
                    " :DATE_MAJ)";


        try {

            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindValue(":OBJECTID", $data["OBJECTID"], PDO::PARAM_INT);
            $stmt->bindValue(":KEYG", $data["KEYG"], PDO::PARAM_STR);
            $stmt->bindValue(":SAISON", $data["SAISON"], PDO::PARAM_INT);
            $stmt->bindValue(":N_LOT", $data["N_LOT"], PDO::PARAM_STR);
            $stmt->bindValue(":SEQ", $this->_sequence, PDO::PARAM_INT);
            $stmt->bindValue(":NUGC", $data["NUGC"], PDO::PARAM_INT);
            $stmt->bindValue(":SERVICE", $data["SERVICE"], PDO::PARAM_STR);
            $stmt->bindValue(":TITULAIRE_ADH_UGC", $data["TITULAIRE_ADH_UGC"], PDO::PARAM_BOOL);
            $stmt->bindValue(":GEOM", $data["GEOM"], PDO::PARAM_LOB);
            $stmt->bindValue(":DATE_MAJ", $data["DATE_MAJ"], PDO::PARAM_STR);


            $stmt->execute();

            SPW_Territoires_Controller::__Increment_Total_Territoires();
            
            //array_push(errorHandler::$Run_Information, ["Info", "new territoire : KEYG = " . $data["KEYG"] . PHP_EOL]);

            $this->_old_KEYG = $data["KEYG"];
            return $this->conn->lastInsertId();

        } catch (pdoException $e) {

                $SQL_Error = $e->errorInfo[1];

                switch ($SQL_Error) {
                    case 1062:
                        $this->New_Territoire($data);
                        break;
                    default:
                        throw new pdoDBException($SQL_Error, $e, "SQL Error :" . $this->rebuildSql($sql,$data));

                }
            } catch (Exception $e) {

            }

    }




    public function Drop_Table(string $tableName) {


        $this->RC = Database::drop_Table($this->conn, $tableName);

    }


    public function Drop_View(string $viewName) {


        $this->RC = Database::drop_View($this->conn, $viewName);

    }

    public function Rename_Table(string $Table_tmp, string $Table_final) {


        $this->RC = Database::rename_Table($this->conn, $Table_tmp, $Table_final);

    }


    public function Create_DB_Table_Territoires(string $tablename): bool 
    {



        $sql = "CREATE TABLE $tablename (
                    `OBJECTID` INT NULL,
                    `SAISON` SMALLINT NOT NULL,
                    `N_LOT` VARCHAR(10) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                    `KEYG` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                    `SEQ` TINYINT NOT NULL,
                    `SERVICE` VARCHAR(5) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                    `NUGC` SMALLINT NULL DEFAULT NULL,
                    `TITULAIRE_ADH_UGC` TINYINT(1) NOT NULL,
                    `DATE_MAJ` DATE NULL DEFAULT NULL,
                    `GEOM` GEOMETRY NOT NULL,
                    PRIMARY KEY (`N_LOT`, `SAISON`, `SEQ`) USING BTREE,
                    UNIQUE INDEX `uk_Saison_lot_seq` (`SAISON`, `N_LOT`, `SEQ`) USING BTREE)
            COLLATE='utf8mb4_unicode_ci'
            ENGINE=InnoDB;";
            
        try {

            $this->RC = $this->conn->exec($sql);

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
          `plf_spw_territoires`.`SEQ` AS `SEQ`,
          `plf_spw_territoires`.`SERVICE` AS `SERVICE`,
          `plf_spw_cantonnements`.`CAN` AS `CAN`,
          `plf_spw_cantonnements`.`CANTON` AS `CANTON`,
          `plf_spw_cantonnements`.`PREPOSE` AS `PREPOSE`,
          `plf_spw_cantonnements`.`TEL_CAN` AS `TEL_CAN`,
          `plf_spw_territoires`.`TITULAIRE_ADH_UGC` AS `TITULAIRE_ADH_UGC`,
          `plf_spw_territoires`.`DATE_MAJ` AS `DATE_MAJ`,
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

            $this->RC = $this->conn->exec($sql);

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