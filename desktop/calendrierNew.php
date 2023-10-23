<?php
$cookie_name = "plf";
if(!isset($_COOKIE[$cookie_name])) {
  session_start();
  $file_suffix = session_id();
  setcookie($cookie_name,session_id(),time() + (86400 * 2), "/");  
} else { $file_suffix = $_COOKIE[$cookie_name];}

require "assets/inc/php/Parameters.php";
require_once "assets/inc/php/Functions.php";

$LRT = PLF::Get_LastRunTime();

?>

<!DOCTYPE html>
<html>
    <header>
        <title>Calendrier des chasses</title>
        <html lang="fr">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- FICHIERS CSS -->
        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet"> 
        <link rel="stylesheet" href="assets/css/calendarNew.css">
        <link rel="stylesheet" href="assets/src/css/leaflet.css">
        <link rel="stylesheet" href="assets/src/css/L.Control.Zoomslider.css">
        <link rel="stylesheet" href="assets/src/css/Control.MiniMap.css">
        <link rel="stylesheet" href="assets/src/css/gh-fork-ribbon.css">
        <link rel="stylesheet" href="https://unpkg.com/@raruto/leaflet-elevation/dist/leaflet-elevation.css" />
        <link rel="stylesheet" href="assets/src/css/jquery-ui.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="assets/src/css/L.Control.Locate.css">
        <link rel="stylesheet" href="assets/src/css/leaflet.rainviewer.css">
        
        
        <!-- FICHIERS JS -->
        <script src = "assets/src/js/leaflet.js"></script>
        <script src = "assets/src/js/jquery-3.7.1.js"></script>
        <script src = "assets/src/js/jquery-ui.js"></script>
        <script src = "assets/inc/js/bootstrap.bundle.min.js"></script>
        <script src = "assets/src/js/L.Control.Zoomslider.js"></script>
        <script src = "assets/src/js/Control.MiniMap.js"></script>
        <script src = "assets/src/js/leaflet-providers.js"></script>
        <script src = "assets/src/js/popper.js"></script>
        <script src = "assets/src/js/L.Control.Locate.min.js"></script>
        <script src = "assets/inc/js/search_hunting_dates.js"></script>
        <script src = "assets/inc/js/main.js"></script>
        <script src = "https://cdn.jsdelivr.net/npm/dayjs@1.11.9/dayjs.min.js"></script>
        <script src = "assets/src/js/leaflet.rainviewer.js"></script>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.7.5/proj4.js"></script>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/proj4leaflet/1.0.2/proj4leaflet.min.js"></script>
    </header>
    
    <body>

    <!---------------------- CALENDAR POPUP --------------------------------------->

    <div class="modal" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="calendarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close text-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div></div>
                    <p></p>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    
    <!----------------------- SIDEBAR MENU --------------------------------------------->
        
    <div class="wrapper d-flex align-items-stretch">
        <nav id="sidebar">
            <div class="custom-menu">
                <button type="button" id="sidebarCollapse" class="btn btn-primary"></button>
            </div>
            <div class="img bg-wrap text-center py-4" style="">
                <div class="user-logo">
                    <div class="img" style=""></div>
                    <h3>Catriona Henderson</h3>
                </div>
            </div>
                <ul class="list-unstyled components mb-5">
                <li class="active">
                    <a href="#"><span class="fa fa-home mr-3"></span> Home</a>
                </li>
                <li>
                    <a href="#"><span class="fa fa-download mr-3 notif"><small class="d-flex align-items-center justify-content-center">5</small></span> Download</a>
                </li>
                <li>
                    <a href="#"><span class="fa fa-gift mr-3"></span> Gift Code</a>
                </li>
                <li>
                    <a href="#"><span class="fa fa-trophy mr-3"></span> Top Review</a>
                </li>
                <li>
                    <a href="#"><span class="fa fa-cog mr-3"></span> Settings</a>
                </li>
                <li>
                    <a href="#"><span class="fa fa-support mr-3"></span> Support</a>
                </li>
                <li>
                    <a href="#"><span class="fa fa-sign-out mr-3"></span> Sign Out</a>
                </li>
                </ul>
    	</nav>

    <!----------------------- MAP --------------------------------------------->

        <container id ="Container">
            <div id="map"></div>
            
        </container>

    </body>
</html>

<script>
    $(document).ready(function() {

    // ************ INITIALIZATION DAY JS ************************************************************ 

        $( function() {
            $("#datepicker").datepicker({
                dateFormat: "dd-mm-yy",
                dayNamesMin: [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ],
                monthNames: [ "Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "December" ],
                buttonImageOnly: true
            });
        });


    // ************ MAP INITIALIZATION ************************************************************ 
    var crs=  new L.Proj.CRS(
            'EPSG:31370',
            '+proj=lcc +lat_1=51.16666723333334 +lat_2=49.8333339 +lat_0=90 +lon_0=4.367486666666666 +x_0=150000.013 +y_0=5400088.438 +ellps=intl +towgs84=106.869,-52.2978,103.724,-0.33657,0.456955,-1.84218,1 +units=m +no_defs',
            {
                resolutions: [8192, 4096, 2048, 1024, 512],
                origin: [0, 0],
                bounds: L.bounds([0, 0], [8192, 8192])
            }),

        map = L.map('map',{zoomControl: false},{crs: crs} ).setView([49.567574, 5.533507], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            crs : crs,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);
      
    // ************ LAYERS INITIALIZATION *******************************************************

    var lyrOSM = L.tileLayer.provider('OpenStreetMap.France', {crs: crs}).addTo(map);
    var lyrmagnifiedTiles = L.tileLayer.provider('OpenStreetMap.France');
    var lyrCyclo = L.tileLayer.provider('CyclOSM');
    var lyrEsri_WorldImagery = L.tileLayer.provider('Esri.WorldImagery');
    var lyrOpenTopoMap = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
        });
    var osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    
    // ************ SET LOCATION *******************************************************
    
    L.control.locate({
            position: "bottomright",
            flyTo: true,
            strings: {
            title: "LOCALISEZ MOI",
            initialZoomLevel:15,
            returnToPrevBounds:true
            }
        }).addTo(map);
      

    // ************ MINIMAP INITIALIZATION *******************************************************

    var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
	var osmAttrib='Map data &copy; OpenStreetMap contributors';
	
    var osm2 = new L.TileLayer(osmUrl, {minZoom: 5, maxZoom: 10, attribution: osmAttrib,  });
    var miniMap = new L.Control.MiniMap(osm2, { toggleDisplay: true,position: 'bottomright',zoomControl: false }).addTo(map);
    
     // *********** LEAFLET METEO *****************************************************************
        
    L.control.rainviewer({
		position: 'bottomright',
		nextButtonText: '>',
		playStopButtonText: 'Start/Stop',
		prevButtonText: '<',
		positionSliderLabelText: "Time:",
		opacitySliderLabelText: "Opacity:",
		animationInterval: 500,
		opacity: 0.5
	}).addTo(map);

    
    });
</script>
