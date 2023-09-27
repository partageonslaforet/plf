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

     private static $Return_Codes = Array(

        -1 => "Aucun record trouvé",
        -2 => "Le territoire n'existe pas",
        -3 => "Plusieurs enregistrements trouvés pour le territoire",
        -4 => "La date est invalide. Doit être au format JJ-MM-AAAA",
        -5 => "Erreur MySql",
        -6 => "Commande SQL invalide",
        -7 => "l'insert à produit une erreur",
        -8 => "pas de correspondance entre territoire et nomnclature et vice versa",
        -9 => "La combinaison date chasse / territoire n'existe pas",
        -10 => "La combinaison date chasse / territoire existe déjà",
        -11 => "Le canton n'existe pas",
        -12 => "Le conseil cynégétique n'existe pas",
        -999 => "Autres erreurs"

     );






    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des territoires basés sur "Territories_id" OU "Nomenclature" (DA_Numero)
     * 
     *      Input     : Database "PLF_Territoires"
     *     
     *      Appel     : Get_Territoire_List(TypeTerritoire: "T")
     *                  Get_Territoire_List()
     * 
     *      Arguments : TypeTerritoire = "T"            Selection basée sur "Territories_id"
     *                                 = non spécifié   Selection basée sur "Nomenclature"
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de territoires 
     *                                  -5 : Erreur MySql
     *                                  -6 : Commande SQL invalide
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Array indexé qui contient chacun une associate array
     *                                      TRI SUR "Territories_Id" OU "Nomenclature"
     *                                      DISTINCT (s'il y a plusieurs territoire avec le même id, seul le premier est sélectionné.)
     *                                 Structure - Array[<index>] = ["DA_Numero"]      = <DA_Numero>, 
     *                                                               "DA_Nom"]         = <DA_Nom>, 
     *                                                               "Territories_id   = <Territories_id>, 
     *                                                               "Territories_name = <Territories_Name>]
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function Get_Territoire_List($TypeTerritoire = NULL)
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        // Connect to database

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            self::$RC = -5;
            self::$RC_Msg = PLF::Get_Error();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);;
        }


        // Build SQL statement

        $sql_cmd = "SELECT DISTINCT DA_Numero, Territories_id, Territories_Name, DA_Nom FROM $GLOBALS[tbl_Territoires] ORDER BY ";


        if (strtolower($TypeTerritoire ?? '') == "t") {
            $sql_cmd .= "Territories_id";
        } else {
            $sql_cmd .= "DA_Numero";
        }


        // Process SQL command

        try {

            foreach ($db_connection->query($sql_cmd) as $record) {

                array_push(self::$List_Array, ["DA_Numero" => $record["DA_Numero"] , 
                                               "DA_Nom" => $record["DA_Nom"], 
                                               "Territories_id" => $record["Territories_id"], 
                                               "Territories_Name" => $record["Territories_Name"]]  );
            }
        } catch (Exception $e) {

            self::$RC = -6;
            self::$RC_Msg = 'Error SELECT ' . " - ";
            self::$RC_Msg .= $e->getMessage() . " - ";
            self::$RC_Msg .= $sql_cmd;

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }


        // Close Database

        PLF::__Close_DB($db_connection);


        // return values

        self::$RC = count(self::$List_Array);
        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }











    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne toutes les informations concernant un territoire "Territories_id" OU "Nomenclature" (DA_Numero)
     * 
     *      Input     : Database "VTerritoires"
     *     
     *      Appel     : Get_Territoire_Info($Territoire_Name, $TypeTerritoire = NULL)
     *                  Get_Territoire_Info($Territoire_Name)
     * 
     *      Arguments : Territoire_Name = Territories_id OU Nomenclature (DA_Numero)
     *                  TypeTerritoire  = "T"            Selection basée sur "Territories_id"
     *                                  = Non spécifié   Selection basée sur "Nomenclature"
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre d'information pour le territoire sélectionné 
     *                                  -2 : Le nom du territoire n'existe pas
     *                                  -3 : Il existe plusieurs enregistrements pour le territoire sélectionné
     *                                  -5 : Erreur MySql
     *                                  -6 : Commande SQL invalide
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Associative array qui contient toutes les informations du territoire ("Territories_Id" OU "Nomenclature")
     *                                      TRI SUR "Territories_Id" OU "Nomenclature"
     *                                      DISTINCT (s'il y a plusieurs territoire avec le même id, seul le premier est sélectionné.)
     *                                 Structure - Array[clé] = valeur
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/



    public static function Get_Territoire_Info($Territoire_Name, $TypeTerritoire = NULL)
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];

        $List_Columns = [
            "tbl_id",
            "Territories_id",
            "Nomenclature",
            "DA_Numero",
            "Territories_name",
            "DA_Nom",
            "TITULAIRE_",
            "NOM_TITULA",
            "PRENOM_TIT",
            "TITULAIRE1",
            "SAISON",
            "COMMENTAIR",
            "DATE_MAJ",
            "ESRI_OID",
            "num_canton",
            "nom_canton",
            "tel_canton",
            "direction_canton",
            "email_canton",
            "attache_canton",
            "CP_canton",
            "localite_canton",
            "rue_canton",
            "numero_canton",
            "latitude",
            "longitude",
            "Code_CC",
            "Nom_CC",
            "President_CC",
            "Secretaire_CC",
            "email_CC",
            "CP_CC",
            "localite_CC",
            "rue_CC",
            "numero_CC",
            "localisation_CC",
            "site_internet_CC",
            "logo_CC",
            "num_triage",
            "nom_triage",
            "nom_Prepose",
            "gsm_Prepose"


        ];

        // Connect to database

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            self::$RC = -5;
            self::$RC_Msg = PLF::Get_Error();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }



        //  Build the SQL statement based on the $list_Columns array

        $sql_cmd = "SELECT ";

        foreach (array_values($List_Columns) as $array_value) {

            $sql_cmd .= "$array_value, ";
        }

        $sql_cmd = preg_replace("/,\s*$/", "", $sql_cmd);


        $sql_cmd .= " FROM $GLOBALS[View_Territoires] WHERE ";

        if (strtolower($TypeTerritoire ?? '') == "t") {
            $sql_cmd .= " Territories_id = '";
        } else {
            $sql_cmd .= " DA_Numero = '";
        }

        $sql_cmd .= "$Territoire_Name'";



        // Process SQL command

        try {

            foreach ($db_connection->query($sql_cmd) as $record) {

                foreach ($List_Columns as $Column) {

                    self::$List_Array[$Column] = $record[$Column];
                }
            }
        } catch (Exception $e) {


            self::$RC = -6;
            self::$RC_Msg =  'Error SELECT ' . " - ";
            self::$RC_Msg .= $e->getMessage() . " - ";
            self::$RC_Msg .= $sql_cmd;

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }




        // Close Database

        PLF::__Close_DB($db_connection);



        // return values

        self::$RC = count(self::$List_Array);
        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }








    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des territoires par date de chasse
     * 
     *      Input     : Database "PLF_Chasses"
     *     
     *      Appel     : Get_Chasse_By_Date(Chasse_Date: <Date Chasse>, TypeTerritoire: "T")
     *                  Get_Chasse_By_Date(Chasse_Date: <Date Chasse>)
     * 
     *      Arguments : Date_Chasse    = date de la chasse (format JJ-MM-AAAA et doit être valide)
     *                  TypeTerritoire = "T"            Selection basée sur "Territories_id"
     *                                 = non spécifié   Selection basée sur "Nomenclature" (DA_Numero)
     * 
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de territoires
     *                                  -4 : La date est erronée. Doit être au format JJ-MM-AAAA et valide
     *                                  -5 : Erreur MySql
     *                                  -6 : Commande SQL invalide
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Array indexé qui contient un array avec le nom du "Territories_Id" ET sa "nomenclature" correspondante
     *                                      TRI sur "Territory_Id" OU "Nomenclature" en fonction de l'appel
     *                                 Structure - Array[index] = Array[<Territory_id>, <Nomenclature>]
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function Get_Chasse_By_Date($Chasse_Date, $TypeTerritoire = NULL)
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        // check date validity. Format DD-MM-YYYY et date is valid

        $Errors_Values = self::__Check_If_Date_Is_Valid($Chasse_Date);

        if (!empty($Errors_Values)) {

            self::$RC = -4;
            self::$RC_Msg = $Errors_Values;

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }



        // Connect to database

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            self::$RC = -5;
            self::$RC_Msg = PLF::Get_Error();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        // Build SQL statement

        $sql_cmd = "SELECT DA_Numero, Territory_id FROM $GLOBALS[tbl_Chasses] ";
        $sql_cmd .= "WHERE Date_Chasse = ";

        $date_delimiter = "'";           // for MySql

        if (strtolower($GLOBALS['DB_MSAccess_or_MySql'] ?? '') == "msaccess") {
            $date_delimiter = "#";       // for MsAccess

        }

        $sql_cmd .= $date_delimiter . PLF::__Convert_2_Sql_Date(Date_DD_MM_YYYY: $Chasse_Date);
        $sql_cmd .= $date_delimiter;

        $sql_cmd .= " ORDER BY ";

        if (strtolower($TypeTerritoire ?? '') == "t") {
            $sql_cmd .= "Territory_id";
        } else {
            $sql_cmd .= "DA_Numero";
        }



        // Process SQL command

        try {


            foreach ($db_connection->query($sql_cmd) as $record) {

                if (strtolower($TypeTerritoire ?? '') == "t") {
                    array_push(self::$List_Array, $record["Territory_id"]);
                } else {
                    array_push(self::$List_Array, $record["DA_Numero"]);
                }
            }
        } catch (Exception $e) {

            self::$RC = -6;
            self::$RC_Msg = 'Error SELECT ' . " - ";
            self::$RC_Msg .= $e->getMessage() . " - ";
            self::$RC_Msg .= $sql_cmd;

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }



        // Close Database

        PLF::__Close_DB($db_connection);



        // return values

        self::$RC = count(self::$List_Array);
        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }










    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des dates de chasse basés sur un "Territories_id" OU "Nomenclature" (DA_Numero)
     * 
     *      Input     : Database "PLF_Chasses"
     *     
     *      Appel     : Get_Chasse_By_Territoire(<Territoire>, TypeTerritoire: "T")
     *                  Get_Chasse_By_Territoire(<Territoire>)
     * 
     *      Arguments : Territoire     = <territory_id> or <Nomenclature>
     *                  TypeTerritoire = "T"            Selection basée sur "Territory_id"
     *                                 = non spécifié   Selection basée sur "Nomenclature"
     *                  
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de Dates de chasses
     *                                  -2 : Le territoire n'existe pas
     *                                  -5 : Erreur MySql
     *                                  -6 : Commande SQL invalide
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Array indexé qui contient les dates de chasses
     *                                      TRI SUR "Date de chasse"
     *                                 Structure - Array[index] = <date>
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function Get_Chasse_By_Territoire($Territoire_Name, $TypeTerritoire = NULL)
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        // check if territoire exist

        $Check_Territoire = self::__Check_If_Territoire_Exists($Territoire_Name, $TypeTerritoire);

        if ($Check_Territoire[0] < 0) {       // MySql error

            self::$RC = $Check_Territoire[0];
            self::$RC_Msg = $Check_Territoire[1];

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($Check_Territoire[2] == false) {

            self::$RC = -2;
            self::$RC_Msg = self::$Return_Codes[self::$RC];

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }




        // Connect to database

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            self::$RC = -5;
            self::$RC_Msg = PLF::Get_Error();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }



        // Build SQL statement

        $sql_cmd = "SELECT Date_Chasse FROM $GLOBALS[tbl_Chasses] ";
        $sql_cmd .= " WHERE ";

        if (strtolower($TypeTerritoire ?? '') == "t") {
            $sql_cmd .= " Territory_id = '";
        } else {
            $sql_cmd .= " DA_Numero = '";
        }

        $sql_cmd .= $Territoire_Name;


        $sql_cmd .= "' ORDER BY Date_Chasse ";



        // Process SQL command

        try {

            foreach ($db_connection->query($sql_cmd) as $record) {

                $sqlDate = new DateTime($record["Date_Chasse"]);
                array_push(self::$List_Array, $sqlDate->format('d-m-Y'));
            }
        } catch (Exception $e) {

            self::$RC = -6;
            self::$RC_Msg = 'Error SELECT ' . " - ";
            self::$RC_Msg .= $e->getMessage() . " - ";
            self::$RC_Msg .= $sql_cmd;

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }







        // Close Database

        PLF::__Close_DB($db_connection);



        // return values

        self::$RC = count(self::$List_Array);
        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }







    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des cantons
     * 
     *      Input     : Database "PLF_Cantonnements"
     *     
     *      Appel     : Get_Territoire_List()
     * 
     *      Arguments : Néant     * 
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de cantons
     *                                  -5 : Erreur MySql
     *                                  -6 : Commande SQL invalide
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Associative array qui contient chacun une associate array
     *                                      TRI SUR "Num_Canton"
     *                                      DISTINCT (s'il y a plusieurs cantons avec le même numéro, seul le premier est sélectionné.)
     *                                 Structure - Array[<num_canton>] = ["nom_canton"]   = <nom_canton>, 
     *                                                                   ["tel_canton"]   = <tel_canton> 
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function Get_Canton_List()
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        // Connect to database

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            self::$RC = -5;
            self::$RC_Msg = PLF::Get_Error();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);;
        }


        // Build SQL statement

        $sql_cmd = "SELECT DISTINCT num_canton, 
                        nom, 
                        tel,
                        direction,
                        email,
                        attache,
                        CP,
                        localite,
                        rue,
                        numero,
                        latitude,
                        longitude
                        FROM $GLOBALS[tbl_cantonnements] ORDER BY ";
        $sql_cmd .= "num_canton";


        // Process SQL command

        try {

            foreach ($db_connection->query($sql_cmd) as $record) {

                self::$List_Array[$record["num_canton"]] = ["num" => $record["num_canton"],
                                                            "nom" => $record["nom"], 
                                                            "tel" => $record["tel"],
                                                            "direction" => $record["direction"],
                                                            "email" => $record["email"],
                                                            "attache" => $record["attache"],
                                                            "CP" => $record["CP"],
                                                            "localite" => $record["localite"],
                                                            "rue" => $record["rue"],
                                                            "numero" => $record["numero"],
                                                            "latitude" => $record["latitude"],
                                                            "longitude" => $record["longitude"]
                                                        ];
            }
        } catch (Exception $e) {

            self::$RC = -6;
            self::$RC_Msg = 'Error SELECT ' . " - ";
            self::$RC_Msg .= $e->getMessage() . " - ";
            self::$RC_Msg .= $sql_cmd;

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }


        // Close Database

        PLF::__Close_DB($db_connection);


        // return values

        self::$RC = count(self::$List_Array);
        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }




    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des territoires par numéro de canton
     * 
     *      Input     : Database "view_territoires"
     *     
     *      Appel     : Get_Territoire_By_Canton(Num_Canton: <numéro de canton>)
     * 
     *      Arguments : Num_Canton     = <Numéro du canton>
     *                  
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de territoires
     *                                  -5 : Erreur MySql
     *                                  -6 : Commande SQL invalide
     *                                 -11 : Le canton n'existe pas
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Array indexé qui contient un array avec le nom du "Territories_Id" ET sa "DA_Numero" correspondante
     *                                      TRI sur "Nomenclature"
     *                                      DISTINCT : n'affiche qu'une seule occurence territories_id <-> DA_Numero
     *                                 Structure - Array[index] = Array[<Territories_id>, <Nomenclature>]
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function Get_Territoire_By_Canton($Num_Canton)
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        // check if canton exist

        $Check_Canton = self::__Check_If_Canton_Exists($Num_Canton);

        if ($Check_Canton[0] < 0) {       // MySql error

            self::$RC = $Check_Canton[0];
            self::$RC_Msg = $Check_Canton[1];

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($Check_Canton[2] == false) {

            self::$RC = -11;
            self::$RC_Msg = self::$Return_Codes[self::$RC];

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }




        // Connect to database

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            self::$RC = -5;
            self::$RC_Msg = PLF::Get_Error();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }



        // Build SQL statement

        $sql_cmd = "SELECT DISTINCT Territories_id, DA_Numero FROM $GLOBALS[View_Territoires] ";
        $sql_cmd .= " WHERE ";
        $sql_cmd .= " num_canton = '" . $Num_Canton . "' ";
        $sql_cmd .= " ORDER BY DA_Numero ";



        // Process SQL command

        try {

            foreach ($db_connection->query($sql_cmd) as $record) {

                array_push(self::$List_Array, [$record["DA_Numero"], $record["Territories_id"]]);
            }
        } catch (Exception $e) {

            self::$RC = -6;
            self::$RC_Msg = 'Error SELECT ' . " - ";
            self::$RC_Msg .= $e->getMessage() . " - ";
            self::$RC_Msg .= $sql_cmd;

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }







        // Close Database

        PLF::__Close_DB($db_connection);



        // return values

        self::$RC = count(self::$List_Array);
        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }














    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des conseils cynégétiques
     * 
     *      Input     : Database "PLF_CC"
     *     
     *      Appel     : Get_CC_List()
     * 
     *      Arguments : Néant     * 
     * 
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de cantons
     *                                  -5 : Erreur MySql
     *                                  -6 : Commande SQL invalide
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Associative array qui contient chacun une associate array
     *                                      TRI SUR "Code_CC"
     *                                      DISTINCT (s'il y a plusieurs cantons avec le même code_cc, seul le premier est sélectionné.)
     *                                 Structure - Array[<Code_CC>] = ["nom_CC"]      = <nom_CC>, 
     *                                                                ["president"]   = <president>,
     *                                                                ["secreataire"] = <secretaire> 
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


     public static function Get_CC_List()
     {
 
         self::$RC = 0;
         self::$RC_Msg = "";
         self::$List_Array = [];
 
 
         // Connect to database
 
         $db_connection = PLF::__Open_DB();
 
         if ($db_connection == NULL) {
 
             self::$RC = -5;
             self::$RC_Msg = PLF::Get_Error();
 
             return array(self::$RC, self::$RC_Msg, self::$List_Array);;
         }
 
 
         // Build SQL statement
 
         $sql_cmd = "SELECT DISTINCT Code, 
                        Nom, 
                        President, 
                        Secretaire, 
                        email, 
                        CP, 
                        localite, 
                        rue, 
                        numero, 
                        localisation, 
                        site_internet, 
                        logo 
                    FROM $GLOBALS[tbl_CC] ORDER BY ";
         $sql_cmd .= "Code";
 
 
         // Process SQL command
 
         try {
 
             foreach ($db_connection->query($sql_cmd) as $record) {
 

                 self::$List_Array[$record["Code"]] = ["nom" => $record["Code"], 
                                                       "president" => $record["President"],
                                                       "secretaire" => $record["Secretaire"],
                                                       "email" => $record["email"],
                                                       "CP" => $record["CP"],
                                                       "localite" => $record["localite"],
                                                       "rue" => $record["rue"],
                                                       "numero" => $record["numero"],
                                                       "localisation" => $record["localisation"],
                                                       "site_internet" => $record["site_internet"],
                                                       "logo" => $record["logo"]
                                                    ];
             }
         } catch (Exception $e) {
 
             self::$RC = -6;
             self::$RC_Msg = 'Error SELECT ' . " - ";
             self::$RC_Msg .= $e->getMessage() . " - ";
             self::$RC_Msg .= $sql_cmd;
 
             return array(self::$RC, self::$RC_Msg, self::$List_Array);
         }
 
 
         // Close Database
 
         PLF::__Close_DB($db_connection);
 
 
         // return values
 
         self::$RC = count(self::$List_Array);
         return array(self::$RC, self::$RC_Msg, self::$List_Array);
     }
 

     














    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Retourne la liste des territoires par Code Conseil Cynégétique
     * 
     *      Input     : Database "view_territoires"
     *     
     *      Appel     : Get_Territoire_By_CC(Code_CC: <Code conseil cynégétique>)
     * 
     *      Arguments : Code_CC       = <Code conseil cynégétique>
     *                  
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de territoires
     *                                  -5 : Erreur MySql
     *                                  -6 : Commande SQL invalide
     *                                 -12 : Le conseil cynégéttique n'existe pas
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Array indexé qui contient un array avec le nom du "Territories_Id" ET sa "DA_Numero" correspondante
     *                                      TRI sur "Nomenclature"
     *                                      DISTINCT : n'affiche qu'une seule occurence territories_id <-> DA_Numero
     *                                 Structure - Array[index] = Array[<Territories_id>, <Nomenclature>]
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function Get_Territoire_By_CC($Code_CC)
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        // check if canton exist

        $Check_CC = self::__Check_If_CC_Exists($Code_CC);

        if ($Check_CC[0] < 0) {       // MySql error

            self::$RC = $Check_CC[0];
            self::$RC_Msg = $Check_CC[1];

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($Check_CC[2] == false) {

            self::$RC = -12;
            self::$RC_Msg = self::$Return_Codes[self::$RC];

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }




        // Connect to database

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            self::$RC = -5;
            self::$RC_Msg = PLF::Get_Error();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }



        // Build SQL statement

        $sql_cmd = "SELECT DISTINCT Territories_id, DA_Numero FROM $GLOBALS[View_Territoires] ";
        $sql_cmd .= " WHERE ";
        $sql_cmd .= " Code_CC = '" . $Code_CC . "' ";
        $sql_cmd .= " ORDER BY DA_Numero ";



        // Process SQL command

        try {

            foreach ($db_connection->query($sql_cmd) as $record) {

                array_push(self::$List_Array, [$record["DA_Numero"], $record["Territories_id"]]);
            }
        } catch (Exception $e) {

            self::$RC = -6;
            self::$RC_Msg = 'Error SELECT ' . " - ";
            self::$RC_Msg .= $e->getMessage() . " - ";
            self::$RC_Msg .= $sql_cmd;

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }







        // Close Database

        PLF::__Close_DB($db_connection);



        // return values

        self::$RC = count(self::$List_Array);
        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }







    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Crée une nouvelle date de chasse.
     * 
     *      Input     : N/A
     *     
     *      Appel     : Chasse_Date_New(Territoire_Name: <nom du territoire>, Date_Chasse: <JJ-MM-AAAA>, TypeTerritoire: "T" )
     *                  Chasse_Date_New(Territoire_Name: <nom du territoire>, Date_Chasse: <JJ-MM-AAAA>)
     * 
     *      Arguments : Territoire     = <territories_id> or <Nomenclature> en 
     *                  Date_Chasse    = Date de la chasse a créé (format JJ-MM-AAAA)
     *                  TypeTerritoire = "T"            Selection basée sur "Territories_id"
     *                                 = non spécifié   Selection basée sur "Nomenclature"
     *                  
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                   0 : Insert OK
     *                                  -2 : Le territoire n'existe pas
     *                                  -4 : La date est invalide. Doit être au format JJ-MM-AAAA
     *                                  -5 : Erreur MySql
     *                                  -6 : Commande SQL invalide
     * 	                                -7 : l'insert à produit une erreur
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Non utilisé
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/


    public static function Chasse_Date_New($Territoire_Name, $Chasse_Date, $TypeTerritoire = NULL)
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        // check date validity. Format DD-MM-YYYY et date is valid

        $Errors_Values = self::__Check_If_Date_Is_Valid($Chasse_Date);

        if (!empty($Errors_Values)) {

            self::$RC = -4;
            self::$RC_Msg = $Errors_Values;

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }


        // check if territoire exist

        $Check_Territoire = self::__Check_If_Territoire_Exists($Territoire_Name, $TypeTerritoire);

        if ($Check_Territoire[0] < 0) {       // MySql error

            self::$RC = $Check_Territoire[0];
            self::$RC_Msg = $Check_Territoire[1];

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($Check_Territoire[2] == false) {

            self::$RC = -2;
            self::$RC_Msg = self::$Return_Codes[self::$RC];

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }



        // check if record already exists.

        $Check_Duplicate = plf::__Check_If_Date_Chasse_Already_Exists($Territoire_Name, $Chasse_Date, $TypeTerritoire);

        if ($Check_Duplicate[2] == true) {

            self::$RC = -10;
            self::$RC_Msg = self::$Return_Codes[self::$RC];

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }


        // Connect to database

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            self::$RC = -5;
            self::$RC_Msg = PLF::Get_Error();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }



        // Get the territories_id corresponding to DA_Numero and vice versa


        $territoire = PLF::__Get_Corresponding_Territoire(Territoire_Name: $Territoire_Name, TypeTerritoire: $TypeTerritoire);

        if ($territoire[0] == -8) {
            self::$RC = $territoire[0];
            self::$RC_Msg = $territoire[1];
            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }




        // Build SQL statement

        $Territoire_id = $territoire[2][0];
        $DA_Numero = $territoire[2][1];


        $sql_insert = "INSERT INTO $GLOBALS[tbl_Chasses] ( Date_Chasse, Territory_id, DA_Numero " .
            " ) VALUES (" .
            " '"   . PLF::__Convert_2_Sql_Date($Chasse_Date) . "', " .
            " '" . $Territoire_id . "', " .
            " '" . $DA_Numero . "' " .
            ")";



        // Execute SQL statement

        try {

            $sql_result = $db_connection->query($sql_insert);
        } catch (Exception $e) {

            self::$RC = -7;
            self::$RC_Msg = 'Error INSERT ' . " - ";
            self::$RC_Msg .= $e->getMessage() . " - ";
            self::$RC_Msg .= $sql_insert;

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }



        // Close Database

        plf::__Close_DB($db_connection);




        // return values

        return array(self::$RC, self::$RC_Msg, self::$List_Array);
    }




    /**-------------------------------------------------------------------------------------------------------------------------------------------
     * 
     *    Supprimer une date de chasse.
     * 
     *      Input     : N/A
     *     
     *      Appel     : Chasse_Date_Delete(Territoire_Name: <nom du territoire>, Date_Chasse: <JJ-MM-AAAA>, TypeTerritoire: "T" )
     *                  Chasse_Date_Delete(Territoire_Name: <nom du territoire>, Date_Chasse: <JJ-MM-AAAA>)
     * 
     *      Arguments : Territoire     = <territoriy_id> or <Nomenclature> en 
     *                  Date_Chasse    = Date de la chasse a supprimer (format JJ-MM-AAAA)
     *                  TypeTerritoire = "T"            Selection basée sur "Territory_id"
     *                                 = non spécifié   Selection basée sur "Nomenclature"
     *                  
     *      Output    : Array contenant 3 éléments
     *                      Array[0] : Code retour.
     *                                  xx : entier >= 0 contenant le nombre de records supprimés
     *                                  -2 : Le territoire n'existe pas
     *                                  -4 : La date est invalide. Doit être au format JJ-MM-AAAA
     *                                  -5 : Erreur MySql
     *                                  -6 : Commande SQL invalide
     * 	                                -7 : le delete à produit une erreur
     *                                  -9 : combinaison date chasse / territoire n'esiste pas
     *                      Array[1] : Message d'erreur éventuel
     *                      Array[2] : Non utilisé
     * 
     *-------------------------------------------------------------------------------------------------------------------------------------------*/



    public static function Chasse_Date_Delete($Territoire_Name, $Chasse_Date, $TypeTerritoire = NULL)
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];





        // check if territoire exist

        $Check_Territoire = self::__Check_If_Territoire_Exists($Territoire_Name, $TypeTerritoire);

        if ($Check_Territoire[0] < 0) {       // MySql error

            self::$RC = $Check_Territoire[0];
            self::$RC_Msg = $Check_Territoire[1];

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($Check_Territoire[2] == false) {

            self::$RC = -2;
            self::$RC_Msg = self::$Return_Codes[self::$RC];

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }



        // check date validity. Format DD-MM-YYYY and date is valid

        $Errors_Values = self::__Check_If_Date_Is_Valid($Chasse_Date);

        if (!empty($Errors_Values)) {

            self::$RC = -4;
            self::$RC_Msg = $Errors_Values;

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }



        // Connect to database      

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            self::$RC = -5;
            self::$RC_Msg = PLF::Get_Error();

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }



        // Build SQL statement

        $date_delimiter = "'";       // for MySql

        if (strtolower($GLOBALS['DB_MSAccess_or_MySql'] ?? '') == "msaccess") {
            $date_delimiter = "#";       // for MsAccess

        }

        $sql_Delete = "DELETE FROM $GLOBALS[tbl_Chasses] WHERE " .
            " Date_Chasse = " . $date_delimiter . PLF::__Convert_2_Sql_Date($Chasse_Date) . $date_delimiter . " AND ";


        if (strtolower($TypeTerritoire ?? '') == "t") {
            $sql_Delete .= " Territory_id = '";
        } else {
            $sql_Delete .= " DA_Numero = '";
        }

        $sql_Delete .= $Territoire_Name . "'";


        $sql_Row_Count = "SELECT ROW_COUNT()";

        // Process SQL command

        try {
            $sql_result_delete = $db_connection->query($sql_Delete);
            $sql_result_Row_Count = $db_connection->affected_rows;
        } catch (Exception $e) {

            self::$RC = -6;
            self::$RC_Msg = 'Error DELETE ' . " - ";
            self::$RC_Msg .= $e->getMessage() . " - ";
            self::$RC_Msg .= $sql_Delete;

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($sql_result_delete == false) {

            self::$RC = -999;

            self::$RC_Msg = self::$Return_Codes[self::$RC] . " - ";
            self::$RC_Msg .= $sql_Delete;

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }



        self::$RC = $sql_result_Row_Count;
        self::$RC_Msg = 'Enregistrement(s) supprimé(s)';

        return array(self::$RC, self::$RC_Msg, self::$List_Array);


        // Close Database

        plf::__Close_DB($db_connection);
    }



    /**
     * 
     *    Create the JSON file for a specific territory
     * 
     *      Output    :  Variable containing JSON data
     *
     *      Calling   : Territoire_JSON(Territoire_Name: <Name of territoire>), TypeTerritoire: "T")
     *                  Territoire_JSON(Territoire_Name: <Name of territoire>)
     *       
     *      Arguments : TypeTerritoire   --> "T"          Select on "territories_id"
     *                                   --> Nothing      Select on "Nomenclature"
     *                  Territoire       --> <territories_id> OR <Nomenclature>
     *
     *      Return    : JSON String
     *                  Possible return codes :
     *                      -2 Territoire does not exist
     *                      -5 MySql error
     */

    public static function Territoire_JSON($Territoire_Name, $TypeTerritoire = NULL)
    {

        self::$RC = 0;
        self::$RC_Msg = "";
        self::$List_Array = [];


        // check if territoire exist

        $Check_Territoire = self::__Check_If_Territoire_Exists($Territoire_Name, $TypeTerritoire);

        if ($Check_Territoire[0] < 0) {       // MySql error

            self::$RC = $Check_Territoire[0];
            self::$RC_Msg = $Check_Territoire[1];

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }

        if ($Check_Territoire[2] == false) {

            self::$RC = -2;
            self::$RC_Msg = self::$Return_Codes[self::$RC];

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }


        // Build SQL statement

        $sql_cmd = "SELECT geometry, DA_Numero, Territories_id, Territories_name FROM $GLOBALS[tbl_Territoires] ";
        $sql_cmd .= " WHERE ";

        if (strtolower($TypeTerritoire ?? '') == "t") {
            $sql_cmd .= " Territories_id = '";
        } else {
            $sql_cmd .= " DA_Numero = '";
        }

        $sql_cmd .= $Territoire_Name . "'";



        // Execute SQL statement


        if (strtolower($GLOBALS['DB_MSAccess_or_MySql']) == "msaccess") {
            $Territory_Data = PLF::__Read_Geometry_MSAccess(sql_cmd: $sql_cmd);
        } else {
            $Territory_Data = PLF::__Read_Geometry_MySql(sql_cmd: $sql_cmd);
        }

        if ($Territory_Data[0] < 0) {

            self::$RC = $Territory_Data[0];
            self::$RC_Msg = $Territory_Data[1];

            return array(self::$RC, self::$RC_Msg, self::$List_Array);
        }


        $headers = "\t{\r\n\t\t\"type\" : \"FeatureCollection\"," .
            "\r\n\t\t\"name\" : \"NewFeatureType\"," .
            "\r\n\t\t\"features\" : [\r\n\t\t\t{\r\n\t\t\t\t\"type\" : \"Feature\",\r\n\t\t\t\t\"geometry\" : ";

        $footer = "\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\r\n\t\t]\r\n\t}\r\n";

        $Geometry = $Territory_Data[2]['geometry'];
        $Territories_id = $Territory_Data[2]['Territories_id'];
        $Territories_name = $Territory_Data[2]['Territories_name'];
        $DA_Numero = $Territory_Data[2]['DA_Numero'];

        // convert some string characters to valid ones 

        $Geometry = preg_replace('/("type": "MultiPolygon")/', '\t$1', $Geometry);
        $Geometry = preg_replace('/("coordinates")/', '\t$1', $Geometry);

        $Geometry = preg_replace('/\\\n[\\\t]+(\d)/', "$1", $Geometry);
        $Geometry = preg_replace('/(\d)\\\n[\\\t]+\]/', "$1]", $Geometry);
        $Geometry = preg_replace('/\\\n}"/', "\r\n\t}" . '"', $Geometry);

        $Geometry = preg_replace('/\\\n/', "\r\n\t\t\t", $Geometry);

        $Geometry = preg_replace('/\\\t/', "\t", $Geometry);

        $Geometry = preg_replace('/^"/', "", $Geometry, 1);   // replace first quote by empty string
        $Geometry = preg_replace('/"$/', "", $Geometry, 1);   // replace last quote by empty string

        // $v_Out = preg_replace('/\x00/', "", $v_Out);

        $Nomenclature = ",\r\n\t\t\t\t\"properties\": {\r\n\t\t\t\t\t\"Nomenclature\": \"" . $DA_Numero . "\", \r\n";
        $Territories_id = "\t\t\t\t\t\"Territories_id\": \"" . $Territories_id . "\", \r\n";
        $Territories_name = "\t\t\t\t\t\"Territories_name\": \"" . $Territories_name . "\" ";

        $Geometry = $headers . $Geometry . $Nomenclature .  $Territories_id . $Territories_name . $footer;
        // $Geometry = $headers . $Geometry;




        return array(self::$RC, self::$RC_Msg, $Geometry);
    }



    /**
     *  Internal Database functions
     * 
     */

    private static function __Open_DB()
    {


        switch (strtolower($GLOBALS['DB_MSAccess_or_MySql'])) {



            case strtolower("MSAccess"):
                try {

                    $db_conn = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=" . $GLOBALS['db_file_name'] . ";Uid=; Pwd=;array(PDO::MYSQL_ATTR_MAX_BUFFER_SIZE=>1024*1024*50)");
                    $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    self::$RC_Msg =  'Error opening MsAccess database' . " - ";
                    self::$RC_Msg .= $e->getMessage() . " - ";
                    return NULL;
                }

                break;





            case strtolower("MySql"):

                try {

                    $db_conn = new mysqli($GLOBALS['MySql_Server'], $GLOBALS['MySql_Login'], $GLOBALS['MySql_Password'], $GLOBALS['MySql_DB']);
                } catch (Exception $e) {
                    self::$RC_Msg =  'Error opening MySql database' . " - ";
                    self::$RC_Msg .= $e->getMessage() . " - ";
                    return NULL;
                }


                break;
        }





        return $db_conn;
    }


    private static function __Close_DB($db_conn)
    {
        unset($db_conn);
    }


    public static function Get_Error()
    {

        return self::$RC_Msg;
    }


    /**
     * 
     *  because the value of geometry can be very long, the way it is read is different for MySql and for MsAccess
     * 
     */



    private static function __Read_Geometry_MySql($sql_cmd)
    {
        /**
         * 
         *  Connect to the database and set settings
         *  
         */



        $RC = 0;
        $RC_Msg = "";
        $List_Array = [];


        // Connect to database

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            $RC = -5;
            $RC_Msg = PLF::Get_Error();

            return array($RC, $RC_Msg, $List_Array);
        }



        // Process SQL command

        try {

            foreach ($db_connection->query($sql_cmd) as $record) {

                $List_Array['geometry'] = $record['geometry'];
                $List_Array['DA_Numero'] = $record['DA_Numero'];
                $List_Array['Territories_id'] = $record['Territories_id'];
                $List_Array['Territories_name'] = $record['Territories_name'];
            }
        } catch (Exception $e) {

            $RC = -6;
            $RC_Msg = 'Error SELECT ' . " - ";
            $RC_Msg .= $e->getMessage() . " - ";
            $RC_Msg .= $sql_cmd;

            return array($RC, $RC_Msg, $List_Array);
        }


        // Close Database

        PLF::__Close_DB($db_connection);

        return array($RC, $RC_Msg, $List_Array);
    }









    /**
     * 
     *  because the value of geometry can be very long, the way it is read is different for MySql and for MsAccess
     * 
     */

    private static function __Read_Geometry_MSAccess($sql_cmd)
    {


        $Territory_Data = [];


        /**
         * 
         *  Connect to the database and set settings
         *  
         */


        $db = odbc_connect("Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$GLOBALS[db_file_name]", "", "");



        // Process SQL command

        $result = odbc_exec($db, $sql_cmd);
        odbc_longreadlen($result, 300000);      // !!!!!!!! this is the maximum record length. 



        while (odbc_fetch_row($result) == true) {

            $Territory_Data['geometry'] = odbc_result($result, 1);
            $Territory_Data['DA_Numero'] = odbc_result($result, 2);
            $Territory_Data['Territories_id'] = odbc_result($result, 3);
            $Territory_Data['Territories_name'] = odbc_result($result, 4);
        }


        // Close Database

        PLF::__Close_DB($db);



        // return values

        return $Territory_Data;
    }







    /**
     * 
     *  Other internal functions
     * 
     */


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

    private static function __Check_If_Date_Is_Valid($Date)
    {

        $Date_Format = 'd-m-Y';

        $Error_Message = "";

        $date_from_format = DateTimeImmutable::createFromFormat($Date_Format, $Date);

        if ($date_from_format == false) {

            $Error_Message .= "Le format de la date n'est pas correct.";
            return $Error_Message;
        } else {
            $Last_Errors = DateTimeImmutable::getLastErrors();

            if (($Last_Errors["warning_count"] == 0) and
                ($Last_Errors["error_count"] == 0)
            ) {

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








    // check if record date chasse already exists. 

    private static function __Check_If_Date_Chasse_Already_Exists($Territoire_Name, $Chasse_Date, $TypeTerritoire)
    {

        $RC = 0;
        $RC_Msg = "";
        $List_Array = [];


        // Connect to database

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            $RC = -5;
            $RC_Msg = PLF::Get_Error();

            return array($RC, $RC_Msg, $List_Array);
        }


        // build SQL statement

        $date_delimiter = "'";           // for MySql

        if (strtolower($GLOBALS['DB_MSAccess_or_MySql'] ?? '') == "msaccess") {
            $date_delimiter = "#";       // for MsAccess

        }


        if (strtolower($TypeTerritoire ?? '') == "t") {
            $sql_cmd = "SELECT Territories_id ";
        } else {
            $sql_cmd = "SELECT DA_Numero ";
        }

        $sql_cmd .= " FROM $GLOBALS[tbl_Chasses] ";
        $sql_cmd .= " WHERE Date_Chasse = ";
        $sql_cmd .= $date_delimiter . PLF::__Convert_2_Sql_Date(Date_DD_MM_YYYY: $Chasse_Date);
        $sql_cmd .= $date_delimiter;
        $sql_cmd .= " AND ";

        if (strtolower($TypeTerritoire ?? '') == "t") {
            $sql_cmd .= " Territories_id = '";
        } else {
            $sql_cmd .= " DA_Numero = '";
        }

        $sql_cmd .= "$Territoire_Name' ";
        $sql_cmd .= " LIMIT 1";


        // Process SQL command

        try {

            $records_found = false;

            foreach ($db_connection->query($sql_cmd) as $record) {

                $records_found = true;

            }
        } catch (Exception $e) {

            $RC = -6;
            $RC_Msg = 'Error SELECT ' . " - ";
            $RC_Msg .= $e->getMessage() . " - ";
            $RC_Msg .= $sql_cmd;

            return array($RC, $RC_Msg, false);
        }


        return array($RC, $RC_Msg, $records_found);




        // Close Database

        PLF::__Close_DB($db_connection);
    }



    // Check the existence of a territoire

    private static function __Check_If_Territoire_Exists($Territoire_Name, $TypeTerritoire)
    {

        $RC = 0;
        $RC_Msg = "";
        $List_Array = [];


        // Connect to database

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            $RC = -5;
            $RC_Msg = PLF::Get_Error();

            return array($RC, $RC_Msg, $List_Array);
        }



        // Build SQL statement

        if (strtolower($TypeTerritoire ?? '') == "t") {
            $sql_cmd = "SELECT Territories_id ";
        } else {
            $sql_cmd = "SELECT DA_Numero ";
        }

        $sql_cmd .= "FROM $GLOBALS[tbl_Territoires] ";
        $sql_cmd .= " WHERE ";

        if (strtolower($TypeTerritoire ?? '') == "t") {
            $sql_cmd .= " Territories_id = '";
        } else {
            $sql_cmd .= " DA_Numero = '";
        }

        $sql_cmd .= "$Territoire_Name' ";
        $sql_cmd .= " LIMIT 1";


        // Process SQL command

        try {

            $records_found = false;

            foreach ($db_connection->query($sql_cmd) as $record) {

                $records_found = true;

            }
        } catch (Exception $e) {

            $RC = -6;
            $RC_Msg = 'Error SELECT ' . " - ";
            $RC_Msg .= $e->getMessage() . " - ";
            $RC_Msg .= $sql_cmd;

            return array($RC, $RC_Msg, false);
        }


        return array($RC, $RC_Msg, $records_found);




        // Close Database

        PLF::__Close_DB($db_connection);
    }




    // Check the existence of a Canton

    private static function __Check_If_Canton_Exists($Num_Canton)
    {

        $RC = 0;
        $RC_Msg = "";
        $List_Array = [];


        // Connect to database

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            $RC = -5;
            $RC_Msg = PLF::Get_Error();

            return array($RC, $RC_Msg, $List_Array);
        }



        // Build SQL statement

        $sql_cmd = "SELECT num_canton ";
        $sql_cmd .= "FROM $GLOBALS[tbl_cantonnements] ";
        $sql_cmd .= " WHERE ";
        $sql_cmd .= " num_canton = '" . "$Num_Canton' ";
        $sql_cmd .= " LIMIT 1";


        // Process SQL command

        try {

            $records_found = false;

            foreach ($db_connection->query($sql_cmd) as $record) {

                $records_found = true;
            }
        } catch (Exception $e) {

            $RC = -6;
            $RC_Msg = 'Error SELECT ' . " - ";
            $RC_Msg .= $e->getMessage() . " - ";
            $RC_Msg .= $sql_cmd;

            return array($RC, $RC_Msg, false);
        }


        return array($RC, $RC_Msg, $records_found);




        // Close Database

        PLF::__Close_DB($db_connection);
    }



    // Check the existence of a Conseil Cynégétique

    private static function __Check_If_CC_Exists($Code_CC)
    {

        $RC = 0;
        $RC_Msg = "";
        $List_Array = [];


        // Connect to database

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            $RC = -5;
            $RC_Msg = PLF::Get_Error();

            return array($RC, $RC_Msg, $List_Array);
        }



        // Build SQL statement

        $sql_cmd = "SELECT Code ";
        $sql_cmd .= "FROM $GLOBALS[tbl_CC] ";
        $sql_cmd .= " WHERE ";
        $sql_cmd .= " Code = '" . "$Code_CC' ";
        $sql_cmd .= " LIMIT 1";


        // Process SQL command

        try {

            $records_found = false;

            foreach ($db_connection->query($sql_cmd) as $record) {

                $records_found = true;
            }
        } catch (Exception $e) {

            $RC = -6;
            $RC_Msg = 'Error SELECT ' . " - ";
            $RC_Msg .= $e->getMessage() . " - ";
            $RC_Msg .= $sql_cmd;

            return array($RC, $RC_Msg, false);
        }


        return array($RC, $RC_Msg, $records_found);




        // Close Database

        PLF::__Close_DB($db_connection);
    }





    private static function __Get_Corresponding_Territoire($Territoire_Name, $TypeTerritoire)
    {

        $RC = 0;
        $RC_Msg = "";
        $List_Array = [];


        // Connect to database

        $db_connection = PLF::__Open_DB();

        if ($db_connection == NULL) {

            $RC = -5;
            $RC_Msg = PLF::Get_Error();

            return array($RC, $RC_Msg, $List_Array);
        }



        // Build SQL statement

        $sql_cmd = "SELECT Territories_id, DA_Numero from $GLOBALS[tbl_Territoires] WHERE ";
        if (strtolower($TypeTerritoire ?? '') ==  "t") {
            $sql_cmd .= " Territories_id = '" . $Territoire_Name . "' ";
        } else {
            $sql_cmd .= " DA_Numero = '" . $Territoire_Name . "' ";
        }

        $Territories_id = "";
        $DA_Numero = "";



        // Process SQL command

        try {

            foreach ($db_connection->query($sql_cmd) as $record) {

                $Territories_id = $record["Territories_id"];
                $DA_Numero = $record["DA_Numero"];
            }
        } catch (Exception $e) {

            $RC = -6;
            $RC_Msg =  'Error SELECT ' . PHP_EOL;
            $RC_Msg .= $e->getMessage() . PHP_EOL;
            $RC_Msg .= $sql_cmd;

            return array($RC, $RC_Msg, $List_Array);
        }

        if ($Territories_id == "") {
            $RC = -8;
            $RC_Msg =  "Aucune territoire trouvé pour la nomenclature $DA_Numero.";
            return array($RC, $RC_Msg, $List_Array);
        }

        if ($DA_Numero == "") {
            $RC = -8;
            $RC_Msg =  "Aucune nomenclature trouvée pour le territoire $Territories_id.";
            return array($RC, $RC_Msg, $List_Array);
        }

        return array($RC, $RC_Msg, [$Territories_id, $DA_Numero]);
    }
}
