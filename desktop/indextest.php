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
<html lang="en">
<head>
    <title>Calendrier des chasses</title>
    <html lang="fr">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!---------------- CSS --------------------->
    <!--<link rel="stylesheet" href="assets/css/style.css">-->
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
    
    <!----===== JS ===== -->
   
    <script src = "assets/src/js/leaflet.js"></script>
    <script src = "assets/src/js/jquery-3.7.1.js"></script>
    <script src = "assets/src/js/jquery-ui.js"></script>
    <script src = "assets/inc/js/bootstrap.bundle.min.js"></script>
    <script src = "assets/src/js/L.Control.Zoomslider.js"></script>
    <script src = "assets/src/js/Control.MiniMap.js"></script>
    <script src = "assets/src/js/leaflet-providers.js"></script>
    <script src = "assets/inc/js/bootstrap.bundle.min.js"></script>
    <script src = "assets/src/js/L.Control.Zoomslider.js"></script>
    <script src = "assets/src/js/Control.MiniMap.js"></script>
    <script src = "assets/src/js/leaflet-providers.js"></script>
    <script src = "assets/src/js/L.Control.Locate.js"></script>
    <script src = "assets/src/js/Leaflet.ajax.min.js"></script>
    <script src = "https://cdn.jsdelivr.net/npm/dayjs@1.11.9/dayjs.min.js"></script>
    <script src = "assets/src/js/leaflet.rainviewer.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.7.5/proj4.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/proj4leaflet/1.0.2/proj4leaflet.min.js"></script>
</head>

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
                            <span class="text nav-text">Informations</span>
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
                <li class="nav-link">
                    <a href="#">
                        <i class='fa-solid fa-gears' ></i>
                        <span class="text nav-text">Log</span>
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

    <!-- ****************   MAP  **************** -->

    <section id="Container">
        <div id="map"></div>
        <div id="calendarBtn">
            <a id="calendar"><i class="fa fa-calendar fa-2x text-container d-flex justify-content-center " data-bs-toggle="modal" data-bs-target="#calendarModal"" title="CALENDRIER DES BATTUES"></i></a>
        </div>
    </section>

    <!-- **************** CALENDAR POPUP**************** -->

     <div class="modal" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <div id="maj"></div>
                        <h5 class="modal-title text-uppercase fw-bold text-danger mx-auto d-flex justify-content-center" id="calendarModalLabel"></h5>

                        <button type="button" class="btn-close text-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="popup-content">
                            <div class="date-calendar">
                            <p><input type="text" id="datepicker" placeholder="Cliquez pour choisir une date"></p>
                            </div>    
                            <div id="controlButtons" class="list">
                                
                            </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <div id="search" class='list-item'>
                        <button type="button" class="btn btn-secondary" id="btonSearchDate" ><i class="fa fa-search"></i></button>
                        <div id="message">
                            <div id="retour"></div>
                            <div id="infoRetour">
                                <div id="squareOpen">
                                    <i class='fa-solid fa-square'></i>
                                    <span>Chemins ouverts</span>
                                </div>
                                <div id="squareClose">
                                    <i class='fa-solid fa-square'></i>
                                    <span>Chemins fermés</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </body>
</html>
<script src = "assets/inc/js/main.js"></script>
<script>

    var listTerritories = [];
    var listHuntingDates =[];
    var huntingDates = [];
    var territoriesList = [];
    var arTerritoriesName = [];
    var territoriesNbers = [];
    var listArrayN = [];
    var territoriesInfo = [] ;
    var huntedTerritoriesListNber = [] ;
    var huntedTerritoriesList= [] ;
    var huntedNber=[];
    var lyrTerritories;
    var map;
    var territoireValue;
    var dateValue;
    var formatDate;
    
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


    // ************ INITIALIZATION DAY MAJ ************************************************************ 
    
    var lRT = [];
    var cookieNber= "<?php echo $file_suffix; ?>";
    lRT = <?php echo json_encode($LRT);?>;
    lRTUS = lRT[2]["cron_chasses"]["Infos_Date"];
    lRTEUR = dayjs(lRTUS,'DD-MMM-YYYY HH:mm')
    lRTBE = lRTEUR.format('DD-MMM-YYYY HH:mm')
    
    document.getElementById("maj").innerHTML = "Dernière màj : "+lRTBE;

        
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
      
        
    //ctlScale = L.control.scale({position:'bottomleft', imperial:false, maxWidth:200}).addTo(map);
    //ctlZoomslider = L.control.zoomslider({position:'topleft'}).addTo(map);
    //ctlMeasure = L.control.polylineMeasure({position:'topleft'}).addTo(map);  
    
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
        
    // ************ POPUP CALENDAR ************************************************************
          
    /*    const popup = document.getElementById('popup');
        const closebtn = document.getElementById('closebtn');
        const search = document.getElementById('calendarBtn');
          
        function showPopup(){
            popup.style.display = 'block';
            search.style.display = 'none';
            
        }
          
        function hidePopup(){
            popup.style.display = 'none';
            search.style.display = 'block';
            message.classList.remove('active');
            retour.classList.remove('active');
            infoRetour.classList.remove('active');
            squareOpen.classList.remove('active');
            squareClose.classList.remove('active');
        }
          
        calendar.addEventListener('click', showPopup);
          
        closebtn.addEventListener('click', hidePopup);
          
        window.addEventListener('click', (event) =>{
            if (event.target === popup){
                hidePopup();
            }    
        });*/
            
        // *************** DATE SELECTION ***********************************************************
        
         
        $("#btonSearchDate").click(function(){
            console.log("coucou")
            if (lyrTerritories){
                lyrTerritories.remove();
                map.removeLayer(lyrTerritories);
            }
            dateValue = $('#datepicker').datepicker('getDate');
            formatDate = $.datepicker.formatDate("dd-mm-yy", dateValue);
            console.log(dateValue);
            if(dateValue === null){
                document.getElementById("retour").innerHTML = "Veuillez sélectionner une date";
                retour.classList.add('active');
                message.classList.add('active');
                infoRetour.classList.remove('active');
                squareOpen.classList.remove('active');
                squareClose.classList.remove('active');
                
            }
            else{
                
                // *************** SETTINGS ***********************************************************
                 
                document.getElementById("retour").innerHTML = "";
                var huntedTerritories =[];
                var huntedTerritoriesList =[];
                var huntedNber=[];
                var territoriesNbers=[];
                var territoriesClosed = [];
                var territoriesOpened = [];
                var territoriesList = [];
                
                // ************ SEARCH HUNTING DATES ************************************************************
                
                $.ajax({
                type: 'GET',
                url: "assets/inc/php/hunting_dates_search_by_date.php",
                data: "formatDate="+formatDate,
            
                success: function(response){
                    console.log(response);
                    resultat = JSON.parse(response);
                    if (resultat[0]==-14){
                        document.getElementById("retour").innerHTML = "Pas de chasse pour cette date.";
                        retour.classList.add('active');
                        message.classList.add('active');
                        infoRetour.classList.remove('active');
                        squareOpen.classList.remove('active');
                        squareClose.classList.remove('active');
                    }else{
                        huntedTerritories = JSON.parse(response);
                        console.log(huntedTerritories)
                        huntedNber=(huntedTerritories[2].length);
                        console.log(huntedNber)
                        
                        for(i=0; i<huntedNber; i++){
                            console.log(huntedTerritories[2][i]["FERMETURE"]);
                            territory = huntedTerritories[2][i]["DA_Numero"];
                            territoriesList.push(territory);
                            if (huntedTerritories[2][i]["FERMETURE"]=="O"){
                                territoriesClosed.push(territory)
                            }
                            else {
                                territoriesOpened.push(territory)
                            }
                         }
                        console.log(territoriesClosed);
                        console.log(territoriesOpened);
                        
                        
                        
                        var territoriesNber = territoriesList.join(',');
                        console.log(territoriesNber)
                        
                        if(huntedNber>0){
                            document.getElementById("retour").innerHTML = huntedNber + " territoires chassés le "+ formatDate;
                            retour.classList.add('active');
                            message.classList.add('active');
                            infoRetour.classList.add('active');
                            squareOpen.classList.add('active');
                            squareClose.classList.add('active');
                        }
                         
                        var lyrhuntingterritoriesClosed = createMultiJson(territoriesNber);
                        //var lyrhuntingterritoriesOpened = createMultiJson(territoriesNber);
                        
                        
                        // ************ SEARCH HUNTING TERRITORIES ************************************************************
                        
                        function createMultiJson(territoriesNber){
                            $.ajax({
                            type: 'GET',
                            url: "assets/inc/php/createMultiJson_by_n.php",
                            data: {territoriesNber:territoriesNber},
                            
                                 success: function(response){
                                    console.log(response);
                                   
                                    if (resultat[0]==-14){
                                        document.getElementById("retour").innerHTML = "Pas de chasse pour cette date.";
                                        retour.classList.add('active');
                                        message.classList.remove('active');
                                        infoRetour.classList.remove('active');
                                        squareOpen.classList.remove('active');
                                        squareClose.classList.remove('active');
                                        }
                                    else {
                                    console.log(lyrTerritories)
                                        if(lyrTerritories){
                                            lyrTerritories.remove();
                                            map.removeLayer(lyrTerritories);
                                        }
                                        console.log(lyrTerritories)
                                        lyrTerritories = L.geoJSON.ajax('assets/datas/'+cookieNber+'huntedTerritoryByDate.json',
                                        {style:styleTerritories,onEachFeature:processTerritories});
                                        
                                            function styleTerritories (json) {
                                            var att=json.properties;
                                            console.log(att.Numero_Lot);
                                            console.log(huntedNber);
                                            for(i=0; i<huntedNber; i++){
                                            console.log(huntedTerritories[2][i]["DA_Numero"]);
           
                                               if(att.Numero_Lot==huntedTerritories[2][i]["DA_Numero"]){
                                                console.log(att.Numero_Lot);
                                                   
                                                  if(huntedTerritories[2][i]["FERMETURE"]=="O"){
                                                      
                                                    return {
                                                        fillOpacity: 0.5,
                                                        weight: 4,
                                                        color:'#ef3d33'
                                                        };
                                                      } else{
                                                         return {
                                                            fillOpacity: 0.5,
                                                            weight: 4,
                                                            color:'#fdef49'
                                                            };
                                                        }
                                                    }
                                                    console.log("erreur")
                                                }
                                            }
                                                
                                            function processTerritories (json,lyr){
                                                var att=json.properties;
                                                lyr.on('mouseover', function(){
                                                    lyr.setStyle({fillOpacity: 0.7})
                                                    lyr.bindTooltip('<h3 style="color:#2c3e50"><center>N° de Territoire: <br>'+att.Numero_Lot+'</h3>');
                                                })
                                                lyr.on('mouseout', function(){
                                                    lyr.setStyle({fillOpacity: 0.3} );  
                                                    })    
                                            } 
                                            
                                            lyrTerritories.on('data:loaded',function(){
                                            map.fitBounds(lyrTerritories.getBounds().pad(0));
                                            crs:crs;
                                            }).addTo(map);
                                        }
                                    }
                                })
                            }
                        }
                    }   
                });
            }
         });
    });
</script>