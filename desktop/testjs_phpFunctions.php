<?php


$result = array();


switch (strtolower($_POST["func"])) {


    case "get_json":
        get_json($_POST["arg1"]);        
        print json_encode($result);
        break;

    case "addit":
        addit($_POST["arg1"]);
        echo json_encode($result);
        break;

    case "multiplyit":
        multiplyit($_POST["arg1"]);
        echo json_encode($result);
        break;

    case "doall":
        doall($_POST["arg1"]);
        echo json_encode($result);
        break;

    default:
        echo("Invalid function called");
}

exit;


function addit($Arguments) {

    global $result;
    $total = 0;

    foreach ($Arguments as $arg) {

        $total = $total + $arg;
    }

    $result = array(
            ["Sum" => $total]
        );
}


function get_json($file_content) {

    global $result;

    $lb72 = file_get_contents("./LB72.json");
    // remove all tabs and new lines -> gives error in javascript.

    
    $patterns = array('/\r\n/', '/\n/', '/\r/', '/\s+/', '/\t+/');
   
    $lb72 = preg_replace($patterns, " ", $lb72);
    $result ["toto"] = "titi";
    $result ["json"] = $lb72;



}



function multiplyit($Arguments) {

    global $result;

    $multiplication = 1;

    foreach ($Arguments as $arg) {

        $multiplication = $multiplication * $arg;
    }

    $result = array(
            ["mult" => $multiplication] 
        );

    }



function doall($Arguments) {

    global $result;

    $total = 0;
    $multiplication = 1;

    foreach ($Arguments as $arg) {

        $total = $total + $arg;
        $multiplication = $multiplication * $arg;
    }

    $result = array(
            ["Sum" => $total],
            ["mult" => $multiplication] 
        );
    };







?>