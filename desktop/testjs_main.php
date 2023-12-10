<?php

$list_Territoires = ["t1", "t2"];


?>


<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />

    <!-- Required for the map function to work. -->
    <script
  src="https://code.jquery.com/jquery-3.7.1.min.js"
  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
  crossorigin="anonymous"></script>


  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->

    <!-- Load Leaflet from CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.9.2/proj4.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4leaflet/1.0.2/proj4leaflet.js"></script>

 
    <script type="text/javascript" src="./testjs_function.js"></script>


    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #map {
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #323232;
        }
    </style>


</head>

<body>

    <h1>My First Heading</h1>

    <p>My first paragraph.</p>




    <form method="get" name="form" action="testjs_phpFunctions.php"> 
        <input type="text" placeholder="Enter Data" name="data"> 
        <input type="submit" value="Submit"> 
    </form> 


    <div id="map"></div>



    <script type="text/javascript">
        
        display_map(<?php echo (json_encode($list_Territoires)) ?>);
        
    </script>



<h1>My last Heading</h1>

</body>

</html>