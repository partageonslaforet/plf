<?php

// use PhpOffice\PhpSpreadsheet\Helper\Handler;

class Functions_Gateway
{


    private PDO $conn;
    private string $sql_cmd;


    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function set_Sql_Statement(string $sql_cmd)
    {

        $this->sql_cmd = $sql_cmd;
    }




    public function DB_Query(): array
    {


        $data = [];

        try {

            $stmt = $this->conn->query($this->sql_cmd);
        } catch (PDOException $e) {
            $data = [
                "error",
                $e->errorInfo[1],
                $e->errorInfo[2]
            ];
            return $data;
        } catch (Exception $e) {
            $data = [
                "error",
                $e->getCode(),
                $e->getMessage()
            ];
            return $data;
        }



        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $data[] = $row;
        }

        return $data;
    }




    public function New_Territoire(array $data)
    {

        $sql = "INSERT INTO " . $GLOBALS["spw_tbl_territoires"] . " (" .
            " OBJECTID," .
            " KEYG," .
            " SAISON," .
            " N_LOT," .
            " NUGC," .
            " CODESERVICE," .
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
            " :CODESERVICE," .
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
            $stmt->bindValue(":CODESERVICE", $data["CODESERVICE"], PDO::PARAM_STR);
            $stmt->bindValue(":SERVICE", $data["SERVICE"], PDO::PARAM_STR);
            $stmt->bindValue(":TITULAIRE_ADH_UGC", $data["TITULAIRE_ADH_UGC"], PDO::PARAM_STR);
            $stmt->bindValue(":DATE_MAJ", $data["DATE_MAJ"], PDO::PARAM_STR);



            $stmt->execute();
            SPW_Chasses_Fermeture_OK_Controller::__Increment_Total_Territoires();

            return $this->conn->lastInsertId();

        } catch (pdoException $e) {

            $SQL_Error = $this->get_Sql_Error_Number($stmt);

            switch ($SQL_Error) {
                case 1062:
                    echo ("(WARNING) : Duplicate record for territoire : KEYG = " . $data["KEYG"]  . PHP_EOL);
                    SPW_Chasses_Fermeture_OK_Controller::__Increment_Duplicate_Territoires();
                    break;

                default:
                    throw new pdoDBException($SQL_Error, $e, "SQL Error :" . $this->rebuildSql($sql, $data));
            }
        } catch (Exception $e) {
        }
    }



    public function New_Date_Chasses(array $data)
    {


        $sql = "INSERT INTO " . $GLOBALS["spw_chasses_fermeture"] . " (" .
            " ROW_NUM," .
            " OBJECTID," .
            " KEYG," .
            " SAISON," .
            " N_LOT," .
            " NUM," .
            " MODE_CHASSE," .
            " DATE_CHASSE," .
            " DATE_EPOCH," .
            " FERMETURE," .
            " SERVICE" .
            "  ) VALUES (" .
            " :ROW_NUM," .
            " :OBJECTID," .
            " :KEYG," .
            " :SAISON," .
            " :N_LOT," .
            " :NUM," .
            " :MODE_CHASSE," .
            " :DATE_CHASSE," .
            " :DATE_EPOCH," .
            " :FERMETURE," .
            " :SERVICE)";


        try {


            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":ROW_NUM", $data["ROW_NUM"], PDO::PARAM_INT);
            $stmt->bindValue(":OBJECTID", $data["OBJECTID"], PDO::PARAM_INT);
            $stmt->bindValue(":KEYG", $data["KEYG"], PDO::PARAM_STR);
            $stmt->bindValue(":SAISON", $data["SAISON"], PDO::PARAM_INT);
            $stmt->bindValue(":N_LOT", $data["N_LOT"], PDO::PARAM_STR);
            $stmt->bindValue(":NUM", $data["NUM"] ?? 0, PDO::PARAM_INT);
            $stmt->bindValue(":MODE_CHASSE", $data["MODE_CHASSE"] ?? "", PDO::PARAM_STR);
            $stmt->bindValue(":DATE_CHASSE", $data["DATE_CHASSE"] ?? "", PDO::PARAM_STR);
            $stmt->bindValue(":DATE_EPOCH", $data["DATE_EPOCH"] ?? 0, PDO::PARAM_INT);
            $stmt->bindValue(":FERMETURE", $data["FERMETURE"] ?? "", PDO::PARAM_STR);
            $stmt->bindValue(":SERVICE", is_null($data["SERVICE"]) ?? "", PDO::PARAM_STR);


            $stmt->execute();
            SPW_Chasses_Fermeture_OK_Controller::__Increment_Total_Chasses();
            return $this->conn->lastInsertId();
        } catch (pdoException $e) {

            $SQL_Error = $this->get_Sql_Error_Number($stmt);

            switch ($SQL_Error) {
                case 1062:
                    echo ("(WARNING) : Duplicate chasse record : KEYG = " . $data["KEYG"] . " and date = " . $data["DATE_CHASSE"]  . PHP_EOL);
                    SPW_Chasses_Fermeture_OK_Controller::__Increment_Duplicate_Chasses();
                    break;
                default:
                    throw new pdoDBException($SQL_Error, $e, "SQL Error :" . $this->rebuildSql($sql, $data));
            }
        } catch (Exception $e) {
        }
    }

    public function drop_DB_table(string $tablename): bool
    {

        $sql = "DROP TABLE IF EXISTS $tablename";

        $stmt = $this->conn->prepare($sql);

        $RC = $stmt->execute();

        if ($RC) {
            return json_encode("Table successfully deleted.");
        }

        return json_encode("Error deleting table. " . $stmt->errorInfo());
    }


    public function Create_DB_Table_Territoires(string $tablename): bool
    {

        // 
        $sql = "CREATE TABLE $tablename (
                    `OBJECTID` INT NULL DEFAULT NULL,
                    `SAISON` SMALLINT NOT NULL,
                    `KEYG` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                    `N_LOT` VARCHAR(10) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                    `CODESERVICE` VARCHAR(9) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                    `NUGC` SMALLINT NULL DEFAULT NULL,
                    `TITULAIRE_ADH_UGC` VARCHAR(1) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                    `DATE_MAJ` DATE NULL DEFAULT NULL,
                    `SHAPE` MEDIUMBLOB NULL DEFAULT NULL,
                    `SERVICE` VARCHAR(50) NULL DEFAULT '',
                    PRIMARY KEY (`N_LOT`, `SAISON`) USING BTREE,
                    UNIQUE INDEX `uk_Saison_N_lot` (`SAISON`, `N_LOT`) USING BTREE)
            COLLATE='utf8mb4_unicode_ci'
            ENGINE=InnoDB;";

        try {

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
        } catch (pdoException $e) {

            $SQL_Error = $this->get_Sql_Error_Number($stmt);

            switch ($SQL_Error) {

                default:
                    throw new pdoDBException($SQL_Error, $e, "SQL Error :" . $sql);
            }
        } catch (Exception $e) {
        }




        return true;
    }




    public function Create_DB_Table_chasses(string $tablename): bool
    {


        $sql = "CREATE TABLE $tablename (
                    `tbl_id` INT NOT NULL AUTO_INCREMENT,
                    `ROW_NUM` INT NOT NULL,
                    `OBJECTID` INT NOT NULL,
                    `SAISON` SMALLINT NOT NULL,
                    `N_LOT` VARCHAR(10) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                    `NUM` SMALLINT NULL DEFAULT 0,
                    `MODE_CHASSE` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                    `DATE_CHASSE` DATE NULL DEFAULT NULL,
                    `DATE_EPOCH` INT NULL DEFAULT 0,
                    `FERMETURE` VARCHAR(1) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                    `KEYG` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                    `SERVICE` VARCHAR(50) NULL DEFAULT '',
                    PRIMARY KEY (`tbl_id`) USING BTREE,
                    UNIQUE INDEX `uk_Saison_N_lot_Date` (`SAISON`, `N_LOT`, `DATE_EPOCH`) USING BTREE)
            COLLATE='utf8mb4_unicode_ci'
            ENGINE=InnoDB;";

        try {

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
        } catch (pdoException $e) {

            $SQL_Error = $this->get_Sql_Error_Number($stmt);

            switch ($SQL_Error) {
                default:
                    throw new pdoDBException($SQL_Error, $e, "SQL Error :" . $sql);
            }
        } catch (Exception $e) {
        }



        return true;
    }


    private function get_Sql_Error_Number(PDOStatement $stmt): string
    {

        $errInfo = $stmt->errorInfo();

        $SQL_Error = "";

        if (!is_null($errInfo[1])) {
            $SQL_Error = $errInfo[1];
        }

        return $SQL_Error;
    }







    private function rebuildSql($string, $data)
    {
        $indexed = $data == array_values($data);
        foreach ($data as $k => $v) {
            if (is_string($v)) $v = "'$v'";
            if (is_null($v)) $v = "''";
            if ($indexed) $string = preg_replace('/\?/', $v, $string, 1);
            else $string = str_replace(":$k", $v, $string);
        }
        return $string;
    }






    public function Get_Territoire_Basic_Info(string $keyg): array | false
    {

        $sql = "SELECT * " .
            " FROM " . $GLOBALS["spw_tbl_territoires"] .
            " WHERE KEYG = :KEYG";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":KEYG", $keyg, PDO::PARAM_STR);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);


        return $data;
    }
}
