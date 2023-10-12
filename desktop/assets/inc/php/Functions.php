<?php

require_once __DIR__ . "/Parameters.php";


class PLF
{

    // private static $error;
    private static $RC = 0;
    private static $RC_Msg = "";
    private static $List_Array = [];

    /**################################################################################
     * 
     *  List de tous les codes erreurs possibles dans l'ensemble des fonctions.
     *
     *       xx : entier >= 0 reprenant le nombre d'enregistrements retournés ou supprimés
     * 
     *################################################################################*/

    private static $Return_Codes = array(

        -1 => "Aucun record trouvé.",
        -2 => "Le territoire (SAISON/TERRITOIRE) n'existe pas.",
        -3 => "Plusieurs enregistrements trouvés pour le territoire (SAISON/TERRITOIRE).",
        -4 => "La date est invalide. Doit être au format JJ-MM-AAAA",
        -5 => "Erreur MySql",
        -6 => "Commande SQL invalide",
        -7 => "Erreur insert",
        -8 => "pas de correspondance entre territoire et nomnclature et vice versa",
        -9 => "La combinaison date chasse / territoire (SAISON/TERRITOIRE) n'existe pas",
        -10 => "La combinaison date chasse / territoire (SAISON/TERRITOIRE) existe déjà",
        -11 => "Le canton n'existe pas",
        -12 => "Le conseil cynégétique n'existe pas",
        -13 => "La base de données MySql n'est pas accessible.",
        -14 => "pas de chasse (SAISON/TERRITOIRE) pour cette date.",
        -15 => "pas de dates pour cette chasse (SAISON/TERRITOIRE)",
        -16 => "Aucun cantons trouvés",
        -17 => "pas de territoire (SAISON/TERRITOIRE) pour ce canton",
        -18 => "pas de territoire (SAISON/TERRITOIRE) pour ce conseil cynégétique",
        -19 => "Le territoire (SAISON/TERRITOIRE) n'existe pas",
        -20 => "Aucun itinéraire trouvé",
        -21 => "L'itinéraire n'existe pas",
        -999 => "Autres erreurs"

    );


    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des territoires basés sur "N_LOT"
     * 
     *      Input     : Database "plf_spw_territoires"
     *     
     *      Appel     : Get_Territoire_List()
     * 
     *      Arguments : néant
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de territoires 
     *                                  autres : voir le tableau
     *                      Array[1] : Message d'erreur éventuel (voir tableau)
     *                      Array[2] : Array indexé qui contient chacun une associate array
     *                                      TRI SUR "Nomenclature"
     *                                      DISTINCT (s'il y a plusieurs territoire avec le même id, seul le premier est sélectionné.)
     *                                 Structure - Array[<index>] = ["Territories_id   = <Territories_id>, 
     *                                                               "Territories_name = <Territories_Name>]
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/

    public static function Get_Territoire_List(string $Saison = null): array | false
    {


        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];

        if (empty($Saison)) {
            $Saison = self::__Compute_Saison();
        }

        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);;
        }


        // Build SQL statement and pass it to the database and prccess the statement.

        $gateway = new Functions_Gateway($database);

        $sql_cmd = "SELECT KEYG, SAISON, N_LOT, SEQ
                    FROM $GLOBALS[spw_tbl_territoires] 
                    WHERE SAISON = $Saison  
                    ORDER BY SAISON, N_LOT, SEQ";

        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();

        // Check if everything went OK

        if (count($results) == 0) {
            self::$RC = -19;
            self::$RC_Msg = self::$Return_Codes[self::$RC];
            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }


        if ($results[0] == "error") {

            switch ($results[1]) {

                case 1054:                 // invalid column name     
                case 1064:                 // SQL syntax error
                    self::$RC = -6;
                    self::$RC_Msg = $results[2];
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);

                default:                    // other errors
                    self::$RC = -999;
                    self::$RC_Msg = $database->Get_Error_Message();
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);;
            }
        }


        // process the data and return the result

        self::$RC = 0;

        foreach ($results as $result => $value) {

            array_push(self::$List_Array, [
                "KEYG" => $value["KEYG"] . "-" . $value["SEQ"],
                "DA_Numero" => $value["N_LOT"] . "-" . $value["SEQ"],
                "DA_Nom" => "N/A",
                "DA_Saison" => $value["SAISON"],
                "Territories_id" => "obsolete",
                "Territories_Name" => "obsolete",
            ]);

            self::$RC++;      // the number of records = last $value (index number) + 1

        }


        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }



    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne toutes les informations concernant un territoire "N_LOT"
     * 
     *      Input     : Database "view_spw_territoires"
     *     
     *      Appel     : Get_Territoire_Info(<numéro de territoire>)
     * 
     *      Arguments : Numéro de territoire = N_LOT
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre d'information pour le territoire sélectionné 
     *                                  autres : voir le tableau
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Associative array qui contient toutes les informations du territoire ("N_LOT")
     *                                      sans objet
     *                                      DISTINCT (s'il y a plusieurs territoire avec le même numero, seul le premier est sélectionné.)
     *                                 Structure - Array[clé] = valeur
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/



    public static function Get_Territoire_Info(string $Territoire_Name, string $Saison = null): array | false
    {


        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];

        if (empty($Saison)) {
            $Saison = self::__Compute_Saison();
        }

        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);;
        }

        // Build SQL statement and pass it to the database and prccess the statement.

        $gateway = new Functions_Gateway($database);

        $sql_cmd = "SELECT  KEYG,
                            SAISON,
                            N_LOT,
                            SEQ,
                            CAN,
                            CANTON,
                            GSM,
                            PREPOSE,
                            TEL_CAN,
                            TITULAIRE_ADH_UGC,
                            direction_CANTON,
                            email_CANTON,
                            attache_CANTON,
                            CP_CANTON,
                            localite_CANTON,
                            rue_CANTON,
                            numero_CANTON,
                            latitude_CANTON,
                            longitude_CANTON,
                            NUGC_CC,
                            N_AGREMENT_CC,
                            DENOMINATION_CC,
                            ABREVIATION_CC,
                            RUE_CC,
                            NUM_CC,
                            CP_CC,
                            LOCALITE_CC,
                            NOM_PSDT_CC,
                            PRENOM_PSDT_CC,
                            NOM_SECR_CC,
                            PRENOM_SECR_CC,
                            SUPERFICIE_CC,
                            LIEN_CARTE_CC,
                            email_CC,                                    
                            latitude_CC,
                            longitude_CC,
                            site_internet_CC,
                            logo_CC,
                            DATE_MAJ
                    FROM $GLOBALS[spw_view_territoires] 
                    WHERE N_LOT = $Territoire_Name 
                    AND SAISON = $Saison
                    ORDER BY SAISON, N_LOT
                    LIMIT 1";












        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();


        // Check if everything went OK

        if (count($results) == 0) {
            self::$RC = -19;
            self::$RC_Msg = self::$Return_Codes[self::$RC];
            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($results[0] == "error") {

            switch ($results[1]) {

                case 1054:                 // invalid column name     
                case 1064:                 // SQL syntax error
                    self::$RC = -6;
                    self::$RC_Msg = $results[2];
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);

                default:                    // other errors
                    self::$RC = -999;
                    self::$RC_Msg = $database->Get_Error_Message();
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);
            }
        }



        // process the data and return the result

        self::$RC = 0;

        foreach ($results as $result => $value) {


                                  
       

            array_push(self::$List_Array, [
                "KEYG" => $value["KEYG"] . "-" . $value["SEQ"],
                "DA_Numero" => $value["N_LOT"] . "-" . $value["SEQ"],
                "DA_Saison" => $value["SAISON"],
                "Territories_id" => "obsolete",
                "Territories_Name" => "obsolete",
                "DA_Nom" => "N/A",
                "TITULAIRE_" => "N/A",
                "NOM_TITULA" => "N/A",
                "PRENOM_TIT" => "N/A",
                "TITULAIRE1" => "N/A",
                "COMMENTAIR" => "N/A",
                "TITULAIRE_ADH_UGC" => $value["TITULAIRE_ADH_UGC"],
                "DATE_MAJ" => $value["DATE_MAJ"],

                "num_canton" => $value["CAN"],
                "nom_canton" => $value["CANTON"],
                "gsm_canton" => $value["GSM"],
                "prepose_canton" => $value["PREPOSE"],
                "direction_canton" => $value["direction_CANTON"],
                "attache_canton" => $value["attache_CANTON"],
                "tel_canton" => $value["TEL_CAN"],
                "email_canton" => $value["email_CANTON"],
                "rue_canton" => $value["rue_CANTON"],
                "numero_canton" => $value["numero_CANTON"],
                "CP_canton" => $value["CP_CANTON"],
                "localite_canton" => $value["localite_CANTON"],
                "latitude_canton" => $value["latitude_CANTON"],
                "longitude_canton" => $value["longitude_CANTON"],

                "Code_CC" => $value["ABREVIATION_CC"],
                "Nom_CC" => $value["DENOMINATION_CC"],
                "N_AGREMENT_CC" => $value["N_AGREMENT_CC"],
                "President_CC" => $value["NOM_PSDT_CC"] . " " . $value["PRENOM_PSDT_CC"],
                "President_nom_CC" => $value["NOM_PSDT_CC"],
                "President_prenom_CC" => $value["PRENOM_PSDT_CC"],
                "Secretaire_CC" => $value["NOM_SECR_CC"] . " " . $value["PRENOM_SECR_CC"],
                "Secretaire_nom_CC" => $value["NOM_SECR_CC"],
                "Secretaire_prenom_CC" => $value["PRENOM_SECR_CC"],

                "email_CC" => $value["email_CC"],

                "rue_CC" => $value["RUE_CC"],
                "numero_CC" => $value["NUM_CC"],
                "CP_CC" => $value["CP_CC"],
                "localite_CC" => $value["LOCALITE_CC"],
                "Superficie_CC" => $value["SUPERFICIE_CC"],

                "latitude_CC" => $value["latitude_CC"],
                "longitude_CC" => $value["longitude_CC"],
                "site_internet_CC" => $value["site_internet_CC"],
                "logo_CC" => $value["logo_CC"],

                "num_triage" => "N/A",
                "nom_triage" => "N/A",
                "nom_Prepose" => "N/A",
                "gsm_Prepose" => "N/A",
            ]);




            self::$RC++;      // the number of records = last $value (index number) + 1

        }


        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }


    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des territoires par date de chasse
     * 
     *      Input     : Database "PLF_spw_chasses_fermeture"
     *     
     *      Appel     : Get_Chasse_By_Date(Chasse_Date: <Date Chasse>)
     * 
     *      Arguments : Date_Chasse    = date de la chasse (format JJ-MM-AAAA et doit être valide)     * 
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de territoires
     *                                  autres : voir le tableau
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Array indexé qui contient un array avec le numéro de territoire et la saison
     *                                      TRI sur saison et numéro de territoire
     *                                 Structure - Array[index] = Array[<Numero du territoire>,<SAISON>[]
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function Get_Chasse_By_Date(string $Chasse_Date, string $Saison = null): array | false
    {


        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];

        if (empty($Saison)) {
            $Saison = self::__Compute_Saison();
        }

        // check date validity. Format DD-MM-YYYY et date is valid

        $Errors_Values = self::__Check_If_Date_Is_Valid($Chasse_Date);

        if (!empty($Errors_Values)) {

            self::$RC = -4;
            self::$RC_Msg = $Errors_Values;

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }



        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);;
        }



        // Build SQL statement and pass it to the database and prccess the statement.

        $gateway = new Functions_Gateway($database);

        $date_Chasse_Sql = PLF::__Convert_2_Sql_Date(Date_DD_MM_YYYY: $Chasse_Date);

        $sql_cmd = "SELECT KEYG,
                           SAISON,
                           N_LOT,
                           SEQ,
                           FERMETURE,
                           NUGC
                    FROM $GLOBALS[spw_view_chasses] 
                    WHERE DATE_CHASSE = '$date_Chasse_Sql' AND SAISON = $Saison
                    ORDER BY SAISON, N_LOT, SEQ";

        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();

        // Check if everything went OK

        if (count($results) == 0) {
            self::$RC = -14;
            self::$RC_Msg = self::$Return_Codes[self::$RC];
            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($results[0] == "error") {

            switch ($results[1]) {

                case 1054:                 // invalid column name     
                case 1064:                 // SQL syntax error
                    self::$RC = -6;
                    self::$RC_Msg = $results[2];
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);

                default:                    // other errors
                    self::$RC = -999;
                    self::$RC_Msg = $database->Get_Error_Message();
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);;
            }
        }

        // process the data and return the result

        self::$RC = 0;

        foreach ($results as $result => $value) {

            array_push(self::$List_Array, [
                "KEYG" => $value["KEYG"] . "-" . $value["SEQ"], 
                "DA_Saison" => $value["SAISON"],
                "DA_Numero" => $value["N_LOT"]  . "-" . $value["SEQ"],
                "FERMETURE" => $value["FERMETURE"],
                "NUGC" => $value["NUGC"],
                ]);




            self::$RC++;      // the number of records = last $value (index number) + 1

        }


        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }


    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des territoires par date de chasse
     * 
     *      Input     : Database "PLF_spw_chasses_fermeture"
     *     
     *      Appel     : Get_Chasse_By_Date(Chasse_Date: <Date Chasse>)
     * 
     *      Arguments : Date_Chasse    = date de la chasse (format JJ-MM-AAAA et doit être valide)     * 
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de territoires
     *                                  autres : voir le tableau
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Array indexé qui contient un array avec le numéro de territoire et la saison
     *                                      TRI sur saison et numéro de territoire
     *                                 Structure - Array[index] = Array[<Numero du territoire>,<SAISON>[]
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function Get_Chasse_By_Territoire(string $Territoire_Name, string $Saison = null): array | false
    {


        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];

        if (empty($Saison)) {
            $Saison = self::__Compute_Saison();
        }

        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);;
        }



        // Build SQL statement and pass it to the database and prccess the statement.

        $gateway = new Functions_Gateway($database);


        $sql_cmd = "SELECT DATE_CHASSE, 
                           FERMETURE
                     FROM $GLOBALS[spw_chasses] 
                     WHERE N_LOT = $Territoire_Name
                     AND SAISON = $Saison
                     ORDER BY DATE_CHASSE";

        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();

        // Check if everything went OK

        if (count($results) == 0) {
            self::$RC = -15;
            self::$RC_Msg = self::$Return_Codes[self::$RC];
            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($results[0] == "error") {

            switch ($results[1]) {

                case 1054:                 // invalid column name     
                case 1064:                 // SQL syntax error
                    self::$RC = -6;
                    self::$RC_Msg = $results[2];
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);

                default:                    // other errors
                    self::$RC = -999;
                    self::$RC_Msg = $database->Get_Error_Message();
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);;
            }
        }




        // process the data and return the result

        self::$RC = 0;

        foreach ($results as $result => $value) {

            $sqlDate = new DateTime($value["DATE_CHASSE"]);
            // array_push(self::$List_Array, $sqlDate->format('d-m-Y'));

            array_push(self::$List_Array, [
                "DATE" => $sqlDate->format('d-m-Y'), 
                "FERMETURE" => $value["FERMETURE"]
                ]);

                self::$RC++;      // the number of records = last $value (index number) + 1


        }


        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }


    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des cantons
     * 
     *      Input     : Database "PLF_spw_cantonnements"
     *     
     *      Appel     : SPW_Get_Canton_List()
     * 
     *      Arguments : Néant     * 
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de cantons
     *                                  autres : voir le tableau
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Associative array qui contient chacun une associate array
     *                                      TRI SUR "Num_Canton"
     *                                      DISTINCT (s'il y a plusieurs cantons avec le même numéro, seul le premier est sélectionné.)
     *                                 Structure - Array[<num_canton>] = <infos du canton>
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/

    public static function Get_Canton_List(): array | false

    {
        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);;
        }


        // Build SQL statement and pass it to the database and prccess the statement.

        $gateway = new Functions_Gateway($database);

        $sql_cmd = "SELECT DISTINCT CAN, 
                                    PREPOSE,
                                    GSM,
                                    CANTON,
                                    TEL_CAN,
                                    direction,
                                    email,
                                    attache,
                                    CP,
                                    localite,
                                    rue,
                                    numero,
                                    latitude,
                                    longitude
                    FROM $GLOBALS[spw_view_cantonnements] 
                    ORDER BY CAN";


        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();

        // Check if everything went OK

        if (count($results) == 0) {
            self::$RC = -16;
            self::$RC_Msg = self::$Return_Codes[self::$RC];
            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($results[0] == "error") {

            switch ($results[1]) {

                case 1054:                 // invalid column name     
                case 1064:                 // SQL syntax error
                    self::$RC = -6;
                    self::$RC_Msg = $results[2];
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);

                default:                    // other errors
                    self::$RC = -999;
                    self::$RC_Msg = $database->Get_Error_Message();
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);;
            }
        }



        // process the data and return the result

        self::$RC = 0;

        foreach ($results as $result => $value) {

            self::$List_Array[$value["CAN"]] = [
                            "nom" => $value["CANTON"],
                            "num_canton" => $value["CAN"],
                            "prepose" => $value["PREPOSE"],
                            "tel" => $value["TEL_CAN"],
                            "gsm" => $value["GSM"],
                            "direction" => $value["direction"],
                            "email" => $value["email"],
                            "attache" => $value["attache"],
                            "CP" => $value["CP"],
                            "localite" => $value["localite"],
                            "rue" => $value["rue"],
                            "numero" => $value["numero"],
                            "latitude" => $value["latitude"],
                            "longitude" => $value["longitude"]
            ];


            self::$RC++;      // the number of records = last $value (index number) + 1

        }


        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }


    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des territoires par numéro de canton
     * 
     *      Input     : Database "view_spw_territoires"
     *     
     *      Appel     : Get_Territoire_By_Canton(Num_Canton: <numéro de canton>)
     * 
     *      Arguments : Num_Canton     = <Numéro du canton>
     *                  
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de territoires
     *                                  autres : voir le tableau
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Array indexé qui contient un array avec le numéro du territoires
     *                                      TRI sur "numéro de territoire"
     *                                      DISTINCT : n'affiche qu'une seule occurence territories
     *                                 Structure - Array[index] = Array[<numéro de territoire>]
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function Get_Territoire_By_Canton(string $Num_Canton, string $Saison = null): array | false
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];

        if (empty($Saison)) {
            $Saison = self::__Compute_Saison();
        }

        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            return array(self::$RC, self::$RC_Msg, self::$List_Array
            );;
        }



        // Build SQL statement and pass it to the database and prccess the statement.

        $gateway = new Functions_Gateway($database);

        $sql_cmd = "SELECT KEYG, SAISON, N_LOT, SEQ 
                    FROM $GLOBALS[spw_tbl_territoires] 
                    WHERE SERVICE = '$Num_Canton'
                    AND SAISON = $Saison
                    ORDER BY SAISON, N_LOT, SEQ";


        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();

        // Check if everything went OK

        if (count($results) == 0) {
            self::$RC = -17;
            self::$RC_Msg = self::$Return_Codes[self::$RC];
            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($results[0] == "error") {

            switch ($results[1]) {

                case 1054:                 // invalid column name     
                case 1064:                 // SQL syntax error
                    self::$RC = -6;
                    self::$RC_Msg = $results[2];
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);

                default:                    // other errors
                    self::$RC = -999;
                    self::$RC_Msg = $database->Get_Error_Message();
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);;
            }
        }




        // process the data and return the result

        self::$RC = 0;

        foreach ($results as $result => $value) {

            array_push(self::$List_Array, [
                "KEYG" => $value["KEYG"] . "-" . $value["SEQ"],
                "DA_Numero" => $value["N_LOT"] . "-" . $value["SEQ"],
                "DA_Saison" => $value["SAISON"],


            ]);


            self::$RC++;      // the number of records = last $value (index number) + 1

        }


        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }


    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des conseils cynégétiques
     * 
     *      Input     : Database "plf_spw_cc"
     *     
     *      Appel     : SPW_Get_CC_List()
     * 
     *      Arguments : Néant     * 
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de cantons
     *                                  autres : voir le tableau
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Associative array qui contient chacun une associate array
     *                                      TRI SUR "Code_CC"
     *                                      DISTINCT (s'il y a plusieurs cantons avec le même code_cc, seul le premier est sélectionné.)
     *                                 Structure - Array[<Code_CC>] = ["nom_CC"]      = <nom_CC>, 
     *                                                                ["president"]   = <president>,
     *                                                                ["secreataire"] = <secretaire> 
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function Get_CC_List(): array | false
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            return array(
                self::$RC, self::$RC_Msg, self::$List_Array
            );;
        }

        // Build SQL statement and pass it to the database and prccess the statement.

        $gateway = new Functions_Gateway($database);

        $sql_cmd = "SELECT DISTINCT nugc_CC,
                                    DENOMINATION_CC,
                                    ABREVIATION_CC,
                                    RUE_CC,
                                    NUM_CC,
                                    CP_CC,
                                    LOCALITE_CC,
                                    NOM_PSDT_CC,
                                    PRENOM_PSDT_CC,
                                    NOM_SECR_CC,
                                    PRENOM_SECR_CC,
                                    SUPERFICIE_CC,
                                    email_CC,
                                    site_internet_CC,
                                    logo_CC,
                                    latitude_CC,
                                    longitude_CC
                    FROM $GLOBALS[spw_view_cc] 
                    ORDER BY ABREVIATION_CC";


        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();

        // Check if everything went OK

        if (count($results) == 0) {
            self::$RC = -17;
            self::$RC_Msg = self::$Return_Codes[self::$RC];
            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($results[0] == "error") {

            switch ($results[1]) {

                case 1054:                 // invalid column name     
                case 1064:                 // SQL syntax error
                    self::$RC = -6;
                    self::$RC_Msg = $results[2];
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);

                default:                    // other errors
                    self::$RC = -999;
                    self::$RC_Msg = $database->Get_Error_Message();
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);;
            }
        }





        // process the data and return the result

        self::$RC = 0;

        foreach ($results as $result => $value) {





            self::$List_Array[$value["ABREVIATION_CC"]] = [
                "ugc"=> $value["ABREVIATION_CC"],
                "nom" => $value["DENOMINATION_CC"],
                "rue" => $value["RUE_CC"],
                "numero" => $value["NUM_CC"],
                "CP" => $value["CP_CC"],
                "localite" => $value["LOCALITE_CC"],
                "president_nom" => $value["NOM_PSDT_CC"],
                "president_prenom" => $value["PRENOM_PSDT_CC"],
                "president" => $value["NOM_PSDT_CC"] . " " . $value["PRENOM_PSDT_CC"],
                "secretaire_nom" => $value["NOM_SECR_CC"],
                "secretaire_prenom" => $value["PRENOM_SECR_CC"],
                "secretaire" =>$value["NOM_SECR_CC"] . " " .$value["PRENOM_SECR_CC"],
                "superficie" => $value["SUPERFICIE_CC"],
                "email" => $value["email_CC"],
                "site_internet" => $value["site_internet_CC"],
                "logo" => $value["logo_CC"],
                "latitude" => $value["latitude_CC"],
                "longitude" => $value["longitude_CC"],
            ];


            self::$RC++;      // the number of records = last $value (index number) + 1

        }


        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }




    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des territoires par conseil cynégétique
     * 
     *      Input     : Database "view_spw_territoires"
     *     
     *      Appel     : SPW_Get_Territoire_By_CC(Num_Canton: <numéro de canton>)
     * 
     *      Arguments : Code_CC     = <code du conseil cynégétique>
     *                  
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de territoires
     *                                  autres : voir le tableau
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Array indexé qui contient un array avec le numéro du territoires
     *                                      TRI sur "numéro de territoire"
     *                                      DISTINCT : n'affiche qu'une seule occurence territories
     *                                 Structure - Array[index] = Array[<numéro de territoire>]
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function Get_Territoire_By_CC(string $Code_CC, string $Saison = null): array | false
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];

        if (empty($Saison)) {
            $Saison = self::__Compute_Saison();
        }

        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"], $_SERVER["MySql_Login"], $_SERVER["MySql_Password"]);

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            return array(
                self::$RC, self::$RC_Msg, self::$List_Array
            );;
        }



        // Build SQL statement and pass it to the database and prccess the statement.

        $gateway = new Functions_Gateway($database);

        $sql_cmd = "SELECT KEYG, SAISON, N_LOT, SEQ 
                     FROM $GLOBALS[spw_view_territoires] 
                     WHERE SAISON = $Saison
                     AND ABREVIATION_CC = '$Code_CC'
                     ORDER BY SAISON, N_LOT, SEQ";


        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();

        // Check if everything went OK

        if (count($results) == 0) {
            self::$RC = -18;
            self::$RC_Msg = self::$Return_Codes[self::$RC];
            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($results[0] == "error") {

            switch ($results[1]) {

                case 1054:                 // invalid column name     
                case 1064:                 // SQL syntax error
                    self::$RC = -6;
                    self::$RC_Msg = $results[2];
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);

                default:                    // other errors
                    self::$RC = -999;
                    self::$RC_Msg = $database->Get_Error_Message();
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);;
            }
        }




        // process the data and return the result

        self::$RC = 0;

        foreach ($results as $result => $value) {

            array_push(self::$List_Array, [
                "KEYG" => $value["KEYG"] . "-" . $value["SEQ"],
                "DA_Numero" => $value["N_LOT"] . "-" . $value["SEQ"],
                "DA_Saison" => $value["SAISON"],
            ]);


            self::$RC++;      // the number of records = last $value (index number) + 1

        }


        return array(self::$RC, self::$RC_Msg, self::$List_Array
        );
    }

    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Crée un fichier json pour un territoire donné
     * 
     *      Input     : plf_swp_territoires
     *     
     *      Appel     : SPW_Territoire_JSON(<numéro de territoire>)
     * 
     *      Arguments : numéro de territoire 
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de cantons
     *                                  autres : voir le tableau
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Array indexé qui contient le SHAPE du territoire
     *                                 Structure - Array[0] = SHAPE 
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function Territoire_JSON(string $N_LOT, string $Saison = null) : array | false
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        if (str_contains($N_LOT, "-") == false) 
        {
            $N_LOT = $N_LOT . "-1";
        }


        if (empty($Saison)) {
            $Saison = self::__Compute_Saison();
        }


        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            return array(
                self::$RC, self::$RC_Msg, self::$List_Array
            );
        }



        // Build SQL statement and pass it to the database and prccess the statement.

        $gateway = new Functions_Gateway($database);

        $lot_seq = explode("-", $N_LOT);

        $lot = $lot_seq[0];
        $seq = $lot_seq[1];

        $sql_cmd = "SELECT DISTINCT SHAPE,
                                    N_LOT,
                                    SEQ
                    FROM $GLOBALS[spw_tbl_territoires] 
                    WHERE SAISON = $Saison 
                    AND N_LOT = '$lot' 
                    AND SEQ = '$seq'";


        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();

        // Check if everything went OK

        if (count($results) == 0) {
            self::$RC = -2;
            self::$RC_Msg = self::$Return_Codes[self::$RC] . " - " . $Saison . "/" . $N_LOT;
            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($results[0] == "error") {

            switch ($results[1]) {

                case 1054:                 // invalid column name     
                case 1064:                 // SQL syntax error
                    self::$RC = -6;
                    self::$RC_Msg = $results[2];
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);

                default:                    // other errors
                    self::$RC = -999;
                    self::$RC_Msg = $database->Get_Error_Message();
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);;
            }
        }





        // process the data and return the result

        self::$RC = 0;

        $value = $results[0];

        $Geometry = $value['SHAPE'];
        $Territories_name = "N/A";
        $DA_Numero = $value['N_LOT'];



        $headers = '
            {
                "type" : "Feature",';


        $Geometry = '      "geometry" : ' . $Geometry;
        $Geometry .= ",";


        $properties = '
              "properties": {
                  "Numero_Lot": "<N_LOT>", 
                  "Nom": "<TERRITOIRE_NAME>"
              }
            }';

        $properties = preg_replace("/<N_LOT>/", $N_LOT, $properties);
        $properties = preg_replace("/<TERRITOIRE_NAME>/", "N/A", $properties);
    

        $footer = "";



        $Geometry = $headers . $Geometry . $properties .  $footer;

        return array(self::$RC, self::$RC_Msg, $Geometry);

    }


    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Crée un fichier json pour un CC donné
     * 
     *      Input     : plf_swp_CC
     *     
     *      Appel     : SPW_CC_JSON(<numéro de CC>)
     * 
     *      Arguments : numéro de CC 
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de cantons
     *                                  autres : voir le tableau
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Array indexé qui contient le SHAPE du CC
     *                                 Structure - Array[0] = SHAPE 
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function Canton_JSON(string $Canton): array | false
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"], $_SERVER["MySql_Login"], $_SERVER["MySql_Password"]);

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            return array(
                self::$RC, self::$RC_Msg, self::$List_Array
            );;
        }



        // Build SQL statement and pass it to the database and prccess the statement.

        $gateway = new Functions_Gateway($database);

        $sql_cmd = "SELECT DISTINCT GEOM,
                                     CAN
                     FROM $GLOBALS[spw_cantonnements] 
                     WHERE CAN = '$Canton'";


        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();

        // Check if everything went OK

        if (count($results) == 0) {
            self::$RC = -2;
            self::$RC_Msg = self::$Return_Codes[self::$RC];
            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($results[0] == "error") {

            switch ($results[1]) {

                case 1054:                 // invalid column name     
                case 1064:                 // SQL syntax error
                    self::$RC = -6;
                    self::$RC_Msg = $results[2];
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);

                default:                    // other errors
                    self::$RC = -999;
                    self::$RC_Msg = $database->Get_Error_Message();
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);;
            }
        }





        // process the data and return the result

        self::$RC = 0;

        $value = $results[0];

        $Geometry = $value['GEOM'];

        $headers = '
             {
                 "type" : "Feature",';


        $Geometry = '      "geometry" : ' . $Geometry;
        $Geometry .= ",";


        $properties = '
             "properties": {
                 "ABREVIATION": "<ABREVIATION>"
             }
             }';

        $properties = preg_replace("/<ABREVIATION>/", $Canton, $properties);


        $footer = "";



        $Geometry = $headers . $Geometry . $properties .  $footer;

        return array(self::$RC, self::$RC_Msg, $Geometry);
    }




    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Crée un fichier json pour un CC donné
     * 
     *      Input     : plf_swp_CC
     *     
     *      Appel     : SPW_CC_JSON(<numéro de CC>)
     * 
     *      Arguments : numéro de CC 
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de cantons
     *                                  autres : voir le tableau
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Array indexé qui contient le SHAPE du CC
     *                                 Structure - Array[0] = SHAPE 
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function CC_JSON(string $CC): array | false
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"], $_SERVER["MySql_Login"], $_SERVER["MySql_Password"]);

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            return array(
                self::$RC, self::$RC_Msg, self::$List_Array
            );;
        }



        // Build SQL statement and pass it to the database and prccess the statement.

        $gateway = new Functions_Gateway($database);

        $sql_cmd = "SELECT DISTINCT GEOM,
                                    ABREVIATION
                    FROM $GLOBALS[spw_cc] 
                    WHERE ABREVIATION = '$CC'";


        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();

        // Check if everything went OK

        if (count($results) == 0) {
            self::$RC = -2;
            self::$RC_Msg = self::$Return_Codes[self::$RC];
            return array(self::$RC, self::$RC_Msg, self::$List_Array
            );
        }

        if ($results[0] == "error") {

            switch ($results[1]) {

                case 1054:                 // invalid column name     
                case 1064:                 // SQL syntax error
                    self::$RC = -6;
                    self::$RC_Msg = $results[2];
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);

                default:                    // other errors
                    self::$RC = -999;
                    self::$RC_Msg = $database->Get_Error_Message();
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);;
            }
        }





        // process the data and return the result

        self::$RC = 0;

        $value = $results[0];

        $Geometry = $value['GEOM'];

        $headers = '
            {
                "type" : "Feature",';


        $Geometry = '      "geometry" : ' . $Geometry;
        $Geometry .= ",";


        $properties = '
            "properties": {
                "ABREVIATION": "<ABREVIATION>"
            }
            }';

        $properties = preg_replace("/<ABREVIATION>/", $CC, $properties);


        $footer = "";



        $Geometry = $headers . $Geometry . $properties .  $footer;

        return array(self::$RC, self::$RC_Msg, $Geometry);
    }


    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des itineraires
     * 
     *      Input     : Database "plf_cgt_itineraires"
     *     
     *      Appel     : Get_Itineraires_List()
     * 
     *      Arguments : néant
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre d'itinéraires 
     *                                  autres : voir le tableau
     *                      Array[1] : Message d'erreur éventuel (voir tableau)
     *                      Array[2] : Array indexé qui contient chacun une associate array
     *                                      TRI SUR "nom"
     *                                 Structure - Array[<index>] = ["Itineraire_id   = <Itineraire_id>, 
     *                                                               "Itineraire_nom = <Nom>]
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/

    public static function Get_Itineraires_List(): array
    {


        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);;
        }


        // Build SQL statement and pass it to the database and prccess the statement.

        $gateway = new Functions_Gateway($database);

        $sql_cmd = "SELECT itineraire_id, nom, localite, commune, gpx_url
                    FROM $GLOBALS[cgt_itineraires]  
                    ORDER BY commune";

        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();

        // Check if everything went OK

        if (count($results) == 0) {
            self::$RC = -20;
            self::$RC_Msg = self::$Return_Codes[self::$RC];
            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }


        if ($results[0] == "error") {

            switch ($results[1]) {

                case 1054:                 // invalid column name     
                case 1064:                 // SQL syntax error
                    self::$RC = -6;
                    self::$RC_Msg = $results[2];
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);

                default:                    // other errors
                    self::$RC = -999;
                    self::$RC_Msg = $database->Get_Error_Message();
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);;
            }
        }


        // process the data and return the result

        self::$RC = 0;

        foreach ($results as $result => $value) {

            $has_gpx = true;
            if (empty($value["gpx_url"]) == true ) {
                $has_gpx = false;
            }

            array_push(self::$List_Array, [
                "itineraire_id" => $value["itineraire_id"],
                "nom" => $value["nom"],
                "localite" => $value["localite"],
                "commune" => $value["commune"],
                "has_gpx" => $has_gpx,
            ]);

            self::$RC++;      // the number of records = last $value (index number) + 1

        }


        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }

 
    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne toutes les informations concernant un itineraire
     * 
     *      Input     : Database "tbl_cgt_itineraires"
     *     
     *      Appel     : Get_Itineraire_Info(<itineraire_id>)
     * 
     *      Arguments : Itineraire_id
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre d'information pour le territoire sélectionné 
     *                                  autres : voir le tableau
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Associative array qui contient toutes les informations de l'itineraire
     *                                 Structure - Array[clé] = valeur
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/



    public static function Get_Itineraire_Infos(int $itineraire_id): array
    {


        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];

        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"],$_SERVER["MySql_Login"] ,$_SERVER["MySql_Password"] );

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);;
        }

        // Build SQL statement and pass it to the database and prccess the statement.

        $gateway = new Functions_Gateway($database);

        $sql_cmd = "SELECT DISTINCT itineraire_id,
                                    nom,
                                    organisme,
                                    localite,
                                    commune,
                                    urlweb,
                                    idreco,
                                    distance,
                                    typecirc,
                                    signaletique,
                                    hdifmin,
                                    hdifmax,
                                    gpx_url
                    FROM $GLOBALS[cgt_itineraires] 
                    WHERE itineraire_id = $itineraire_id"; 

        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();


        // Check if everything went OK

        if (count($results) == 0) {
            self::$RC = -21;
            self::$RC_Msg = self::$Return_Codes[self::$RC];
            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($results[0] == "error") {

            switch ($results[1]) {

                case 1054:                 // invalid column name     
                case 1064:                 // SQL syntax error
                    self::$RC = -6;
                    self::$RC_Msg = $results[2];
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);

                default:                    // other errors
                    self::$RC = -999;
                    self::$RC_Msg = $database->Get_Error_Message();
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);
            }
        }



        // process the data and return the result

        self::$RC = 0;

        foreach ($results as $result => $value) {

           
            array_push(self::$List_Array, [
                "itineraire_id" => $value["itineraire_id"],
                "nom" => $value["nom"],
                "organisme" => $value["organisme"],
                "localite" => $value["localite"],
                "commune" => $value["commune"],
                "urlweb" => $value["urlweb"],
                "idreco" => $value["idreco"],
                "distance" => floatval($value["distance"]),
                "typecirc" => $value["typecirc"],
                "signaletique" => $value["signaletique"],
                "hdifmin" => $value["hdifmin"],
                "hdifmax" => $value["hdifmax"],
                "gpx_url" => $value["gpx_url"],
                 ]);




            self::$RC++;      // the number of records = last $value (index number) + 1

        }


        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }



    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne Date et status du job cron
     * 
     *      Input     : Database "plf_infos"
     *     
     *      Appel     : Get_LastRunTime()
     * 
     *      Arguments : N/A
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre d'éléments 
     *                                  autres : voir le tableau
     *                      Array[1] : Message d'erreur éventuel (voir tableau)
     *                      Array[2] : Array indexé qui contient chacun une associate array
     *                                      TRI SUR "nom"
     *                                 Structure - Array[<nom du cron>] = ["date d'execution   = <date>, 
     *                                                                     "résultat = <résultat>]
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/

    public static function Get_LastRunTime(): array
    {


        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"], $_SERVER["MySql_Login"], $_SERVER["MySql_Password"]);

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);;
        }


        // Build SQL statement and pass it to the database and prccess the statement.

        $gateway = new Functions_Gateway($database);

        $sql_cmd = "SELECT Infos_Name, Infos_Date, Infos_Value  
                     FROM $GLOBALS[plf_infos]  
                     ORDER BY Infos_Name";

        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();

        // Check if everything went OK

        if (count($results) == 0) {
            self::$RC = -99;
            self::$RC_Msg = self::$Return_Codes[self::$RC];
            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }


        if ($results[0] == "error") {

            switch ($results[1]) {

                case 1054:                 // invalid column name     
                case 1064:                 // SQL syntax error
                    self::$RC = -6;
                    self::$RC_Msg = $results[2];
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);

                default:                    // other errors
                    self::$RC = -999;
                    self::$RC_Msg = $database->Get_Error_Message();
                    return array(self::$RC, self::$RC_Msg, self::$List_Array);;
            }
        }


        // process the data and return the result

        self::$RC = 0;

        foreach ($results as $result => $value) {

            self::$List_Array[$value["Infos_Name"]] = array(
                "Infos_Date" => $value["Infos_Date"],
                "Infos_Value" => $value["Infos_Value"]
            );

            self::$RC++;      // the number of records = last $value (index number) + 1

        }


        return array(self::$RC, self::$RC_Msg, self::$List_Array
        );
    }
 
 
    private static function __Compute_Saison() : string 
    {

        $current_year = (int) date("Y");
        $current_month = (int) date("m");

        if ( $current_month >= 1 and $current_month <= 3) {
            $current_year--;
        }

        return $current_year;


    }
 
 

    // Convert date in format DD-MM-YYYY to MM-DD-YYYY for SQL statements

    private static function __Convert_2_Sql_Date($Date_DD_MM_YYYY)
    {

        $date_Part = explode("-", $Date_DD_MM_YYYY);

        $d = $date_Part[0];
        $m = $date_Part[1];
        $yyyy = $date_Part[2];

        if (strlen($yyyy) == 2) {
            $yyyy = "20" . $yyyy;
        }

        return "$yyyy-$m-$d";
    }



    // Check if the date has a valid format and is valid

    public static function __Check_If_Date_Is_Valid($Date)
    {
        $Date_Format = 'd-m-Y';

        $Error_Message = "";

        $date_from_format = DateTimeImmutable::createFromFormat($Date_Format, $Date);

        if ($date_from_format == false) {

            $Error_Message .= "Le format de la date n'est pas correct.";
            return $Error_Message;
        } else {
            $Last_Errors = DateTimeImmutable::getLastErrors();

            if (($Last_Errors == false) or 
                (($Last_Errors["warning_count"] == 0) and
                ($Last_Errors["error_count"] == 0))
            ){

                $Error_Message = "";
                return $Error_Message;
            } else {

                $Error_Message .= 'La date est invalide. - ';

                foreach ($Last_Errors["warnings"] as $key => $msg) {

                    $Error_Message .= "$msg - ";
                }

                foreach ($Last_Errors["errors"] as $key => $msg) {

                    $Error_Message .= "$msg - ";
                }

                return $Error_Message;
            }
        }
    }




    // check if territoire exists

    public static function __Check_If_Territoire_Exists($keyg): bool {

        // Make a new database connection and test if connection is OK

        $database = new Database($_SERVER["MySql_Server"], $_SERVER["MySql_DB"], $_SERVER["MySql_Login"], $_SERVER["MySql_Password"]);

        $db_conn = $database->getConnection();

        if ($db_conn == false) {

            self::$RC = -13;
            self::$RC_Msg = $database->Get_Error_Message();

            try {$db_conn = null;} catch (pdoException $e) {}


            return array(self::$RC, self::$RC_Msg, self::$List_Array);;
 

        }


        // Build SQL statement and pass it to the database and prccess the statement.

        $sql_cmd = "SELECT KEYG  
                     FROM $GLOBALS[spw_tbl_territoires]  
                     WHERE KEYG = " .  "'" . $keyg . "'";

        $gateway = new Functions_Gateway($database);

        $gateway->set_Sql_Statement($sql_cmd);

        $results = $gateway->DB_Query();

        // Check if everything went OK

        $rc_bool = true;
        if (count($results) == 0) {
            $rc_bool = false;
        }

        try {$db_conn = null;} catch (pdoException $e) {}

        return $rc_bool;
    }



}



