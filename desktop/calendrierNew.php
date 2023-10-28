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
        <link rel="stylesheet" href="../css/bootstrap.css">
        <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
        
        
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
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/leaflet-ajax/2.1.0/leaflet.ajax.min.js"></script>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.7.5/proj4.js"></script>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/proj4leaflet/1.0.2/proj4leaflet.min.js"></script>
    </header>
    
    <body>

    <!---------------------- CALENDAR POPUP ---------------------------------------

    <div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  <div class="modal-title" >
                    <div id="maj"></div>
                  </div>
                  <button type="button" class="btn-close text-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row col-lg-2" id="date-calendar" >
                      <p><input type="text" id="datepicker" placeholder="Cliquez pour choisir une date"></p>
                    </div>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btonSearchDate" ><i class="fa fa-search"></i></button>
                <button type="button" class="btn btn-secondary" id="btnRetour" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
      </div> 
    </div>-->
    <!----------------------- SIDEBAR MENU --------------------------------------------->
        
   
  <body>
    <nav class="sidebar close">
      <header>
        <div class="image-text">
            <span class="image">
                <img src="#" alt="">
            </span>
            <div class="text logo-text">
                <span class="name">Codinglab</span>
                <span class="profession">Web developer</span>
            </div>
        </div>
        <i class='bx bx-chevron-right toggle'></i>
      </header>
        <div class="menu-bar">
          <div class="menu">
            <ul class="menu-links">
                <li class="nav-link">
                    <a href="#">
                        <i class='fa fa-info' ></i>
                        <span class="text nav-text">Information</span>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="#">
                        <i class='fa fa-hiking' ></i>
                        <span class="text nav-text">Parcours</span>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="#">
                        <i class='fa fa-location-dot'></i>
                        <span class="text nav-text">Territoires</span>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="#">
                        <i class='fa fa-tree title' ></i>
                        <span class="text nav-text">DNF</span>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="#">
                        <i class='fa-solid fa-bullseye' ></i>
                        <span class="text nav-text">Conseils Cynégétiques</span>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="#">
                        <i class='fa fa-envelope' ></i>
                        <span class="text nav-text">Contact</span>
                    </a>
                </li>
              </ul>
            </div>

            <div class="bottom-content">
              <li class="">
                  <a href="#">
                      <i class='bx bx-log-out icon' ></i>
                      <span class="text nav-text">Logout</span>
                  </a>
              </li>

              <li class="mode">
                  <div class="sun-moon">
                      <i class='bx bx-moon icon moon'></i>
                      <i class='bx bx-sun icon sun'></i>
                  </div>
                  <span class="mode-text text">Dark mode</span>

                  <div class="toggle-switch">
                      <span class="switch"></span>
                  </div>
              </li>
                
            </div>
        </div>
  </nav>
      

      <!----------------------- CALENDAR BUTTON ---------------------------------------------
      <div id="calendarBtn">
        <a id="calendar" onclick="hiddenBtn()"><i class="fa fa-calendar fa-2x text-container d-flex justify-content-center " data-bs-toggle="modal" data-bs-target="#calendarModal" title="CALENDRIER DES BATTUES"></i></a>
      </div>

      <script>  
       function hiddenBtn() {
          var calendarHidden = document.getElementById('calendar');
          calendarHidden.style.display = 'none';
        }
      </script>
      --------------------- MAP --------------------------------------------->
  <div id ="Container">
    <div id="map"></div>
  </div>
      
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

   
    // *********** SIDEBAR TOGGLE *****************************************************************
   
   
    });

    
</script>
