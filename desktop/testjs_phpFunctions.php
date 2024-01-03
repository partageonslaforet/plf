<?php


$result = array();


switch (strtolower($_POST["func"])) {


    case "get_json":
        $dbConn = getConnection();
        $territories_array = $_POST["arg1"];
        $file_name = $_POST["arg2"][0];
        get_json($territories_array, $file_name);       
        print json_encode($result);
        break;


    default:
        echo("Invalid function called");
}

exit;


function get_json($territories, $file_name) {

    global $result;

    $lb72 = file_get_contents($file_name);
    $result ["toto"] = "titi";
    $result ["json"] = $lb72;



}

function getConnection() : PDO | false {



    $dsn = "pgsql:host={$_SERVER["PostgreSql_Server"]};dbname={$_SERVER["PostgreSql_DB"]}";
    $user = $_SERVER["PostgreSql_Login"];
    $password = $_SERVER["PostgreSql_Password"];


    try {

        $connection = new PDO($dsn, $user, $password, [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::ATTR_TIMEOUT => 3600
        ]);

    } catch (PDOException $e) {


        switch ($e->getCode()) {
            case 1049:                      // Database does not exist.
                throw new pdoDBException(1049, $e, "MySql Database does not exist : " . $e->getMessage(), );

            case 7:                      // Postgresql Database does not exist.
                throw new pdoDBException(7, $e, "PostegreSQL Database does not exist : " . $e->getMessage(), );

            case 2002:                      // Database is unreachable
                throw new pdoDBException(2002, $e, "Unable to access database : " . $e->getMessage(), );
            
            default:
                throw new pdoDBException($e->getCode(), $e, "Unexpected error : " . $e->getMessage(), );

            }

    } catch (Exception $e) {

    }

    return $connection;

   };







?>