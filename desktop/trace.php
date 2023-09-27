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
        
        <title>Trace</title>
        <html lang="fr">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <!-- FICHIERS CSS -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;900&display=swap" rel="stylesheet"> 
        <link rel="stylesheet" href="assets/css/trace.css">
        <link rel="stylesheet" href="assets/src/css/leaflet.css">
        <link rel="stylesheet" href="assets/src/css/L.Control.Zoomslider.css">
        <link rel="stylesheet" href="assets/src/css/Control.MiniMap.css">
        <link rel="stylesheet" href="assets/src/css/gh-fork-ribbon.css">
        <link rel="stylesheet" href="assets/src/css/L.Control.Locate.css">
        <link rel="stylesheet" href="assets/src/css/leaflet.rainviewer.css">
        <link rel="stylesheet" href="https://unpkg.com/@raruto/leaflet-elevation/dist/leaflet-elevation.css" />
        <link rel="stylesheet" href="assets/src/css/jquery-ui.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
        
        <!-- FICHIERS JS -->
        <script src = "assets/src/js/leaflet.js"></script>
        <script src = "assets/src/js/L.Control.Zoomslider.js"></script>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src = "assets/src/js/Control.MiniMap.js"></script>
        <script src = "assets/src/js/leaflet-providers.js"></script>
        <script src = "assets/src/js/L.Control.Zoomslider.js"></script>
        <script src = "assets/src/js/L.Control.Locate.js"></script>
        <script src = "assets/src/js/leaflet.rainviewer.js"></script>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/leaflet-ajax/2.1.0/leaflet.ajax.min.js"></script>
        <script src = "https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src = "https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src = "assets/src/js/jquery-ui.js"></script>
        <script src = "assets/src/js/gpx.js"></script>
        <script src = "https://unpkg.com/@raruto/leaflet-elevation/dist/leaflet-elevation.js"></script>
        <script src = "https://unpkg.com/axios@0.24.0/dist/axios.min.js"></script>
        <script src = "https://cdn.jsdelivr.net/npm/dayjs@1.11.9/dayjs.min.js"></script>
        
    </header>
    
     <body>
        <div id="sidebarBtn">
            <a id="sidebarIcon"><i class="fa-solid fa-bars" title="OUVRIR"></i></a>
        </div>
        <div id="sidebar">
            <div class="sidebar-header">
                <!--<button id="sidebarclosebtn">&times;</button>-->
                <div id="traceSearchLabel">
                    <h4>TRACE</h4>
                </div>
                <!--<div id="GPXLabel">
                    <h5>Chargez votre trace GPX</h5>
                </div>-->
            </div>
            <div id="sidebarBody">
                <div id="containerBody">
                    <form action="assets/inc/php/uploadgpx.php" method="post" class="form" id="myForm">
                        <input id="inputFile" value="" type="file" accept=".gpx">
                        <label for="inputFile">
                            <i class="fa-solid fa-arrow-up-from-bracket"></i>
                            &nbsp; Fichier à charger
                        </label>
                        <div id="num-of-files">Aucun fichier chargé</div>
                        <ul id="files-list"></ul>
                    </form>
                </div>
                <div id="search">
                    <button type="submit" id="btnFindTraceFile"><i class="fa fa-search fa-2x"></i></button>
                </div>
                <div id="erreurMsg"></div>
                <div id="elevation-div"></div>
                <div id="newSearch">
                    <button type="submit" id="btnFindNewTraceFile">NOUVEAU FICHIER</button>
                </div>
                
            </div>
            <div class="sidebar-footer">
                <button id="btnRetour"onclick="window.location.href = 'https://plf.partageonslaforet.be/desktop';">QUITTER</button>
                 <div class="" id="GPXName"></div>
            </div>
        </div>
        <div id="calendarBtn">
            <a id="calendar"><i class="fa fa-calendar" title="CLIQUEZ"></i></a>
        </div>
        <div id="popup" class="popup">
            <div id="headPopup">
                <button id="closebtn">&times;</button>
                <div id="maj"></div>
            </div>
            <div class="popup-content">
                <div class="date-calendar">
                   <p><input type="text" id="datepicker" placeholder="Cliquez pour choisir une date"></p>
                </div>    
                <button id="btonSearchDate"><i class="fa fa-search"></i></button>
                <script>
                $( function() {
                    $("#datepicker").datepicker({
                        dateFormat: "dd-mm-yy"
                    });
                    
                 } );
                </script>
            </div>
        </div>
        <div id="message">
            <div id="retour"></div>
            <div id="squareOpen">
                <i class='fa-solid fa-square'></i>
                <span>Chemins ouverts</span>
            </div>
            <div id="squareClose">
                <i class='fa-solid fa-square'></i>
                <span>Chemins fermés</span>
            </div>
        </div>
        <container id ="Container"><center>
            <div id="map"></div>
            <div id="elevation-div"></div>
        </container>
    </body>
</html>

<script>
    
    var sidebar;
    var lyrCircuits;
    var listTerritories = [];
    var listHuntingDates =[];
    var huntingDates = [];
    var territoriesListByCanton = [];
    var arTerritoriesNber = [];
    var listArrayN = [];
    var listByCanton =[];
    var territoriesInfo = [] ;
    var territoryDnf;
    var lyrTerritories;
    var map;
    var territoireValue;
    var huntedTerritories =[];
    var fileInput;
    var fileName;
    var dateValue;
    var formatDate;
    var controlElevation;
    var myFile;

    
    $(document).ready(function() {
       containerBody.classList.add('active');
       sidebarBody.classList.add('active');
       newSearch.classList.remove('active');

        
    // ************ MAP INITIALIZATION ************************************************************
    
    map = L.map('map', {zoomControl: false }).setView([49.567574, 5.533507], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        zoomControl:false,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
    
    ctlScale = L.control.scale({position:'bottomleft', imperial:false, maxWidth:200}).addTo(map);
    //ctlZoomslider = L.control.zoomslider({position:'topleft'}).addTo(map);
    //ctlMeasure = L.control.polylineMeasure({position:'topleft'}).addTo(map);  
    
        // ************ LOCALISATION FUNCTION *********************************************************
    
            L.control.locate({
                position: 'topleft',
                strings:{
                    title:"Localisez-moi",
                },
                flyTo:true,
                initialZoomLevel:15,
                clickBehavior:{inView: 'stop', outOfView: 'setView', inViewNotFollowing: 'inView'},
                returnToPrevBounds:true
            }).addTo(map);
    
        // ************ LAYERS INITIALIZATION *******************************************************
    
            var lyrOSM = L.tileLayer.provider('OpenStreetMap.France');
            var lyrmagnifiedTiles = L.tileLayer.provider('OpenStreetMap.France');
            var lyrCyclo = L.tileLayer.provider('CyclOSM');
            var lyrEsri_WorldImagery = L.tileLayer.provider('Esri.WorldImagery');
            var lyrOpenTopoMap = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
                });
            var osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
             
            var baseLayers = {
            "osm":lyrOSM,
            "Satellite":lyrEsri_WorldImagery,
            "Altitude":lyrOpenTopoMap,
            "Cyclo":lyrCyclo
            };
           
           var overlays = {
               
                };
    
            L.control.layers(baseLayers,overlays).addTo(map);
    
        // ************ MINIMAP INITIALIZATION *******************************************************
    
            var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    		var osmAttrib='Map data &copy; OpenStreetMap contributors';
    		
            var osm2 = new L.TileLayer(osmUrl, {minZoom: 5, maxZoom: 10, attribution: osmAttrib,  });
            var miniMap = new L.Control.MiniMap(osm2, { toggleDisplay: true,position: 'bottomright',zoomControl: false }).addTo(map);
            
         // *********** LEAFLET METEO **************************************************
        
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
        	
        	
        // ************ POPUP SIDEBAR ************************************************************
      
          const sidebarPopup = document.getElementById('sidebar');
          const sidebarcloseBtn = document.getElementById('sidebarclosebtn');
          const sidebarOpen = document.getElementById('sidebarBtn');
          
          
          function showSidebar(){
              sidebarPopup.style.display = 'block';
              sidebarOpen.style.display = 'none';
          }
          
          function hideSidebar(){
            console.log("coucou");
            sidebarPopup.style.display = 'none';
            sidebarOpen.style.display = 'block';
            sidebarBtn.classList.add('active');
            message.classList.remove('active');
            retour.classList.remove('active');
            squareOpen.classList.remove('active');
            squareClose.classList.remove('active');
          }
          
          sidebarOpen.addEventListener('click', showSidebar);
          
          //sidebarcloseBtn.addEventListener('click', hideSidebar);
          
          window.addEventListener('click', (event) =>{
            if (event.target === popup){
                hidePopup();
            }
        });  
        	
        // ****************** GPX UPLOAD *********************************************************       
        let fileList;
        
        console.log(myFile);
        if (myFile !== undefined) {
                console.log(myFile)
                
                //location.reload();
                
            }else{
                let fileInput = document.getElementById("inputFile");
                fileList = document.getElementById("files-list");
                let numOfFiles = document.getElementById("num-of-files");
                console.log(myFile)
                fileInput.addEventListener("change", () => {
                    
                    fileList.innerHTML = "";
                    numOfFiles.textContent = `${fileInput.files.length} Fichier sélectionné`;
                
                    for (i of fileInput.files) {
                        let reader = new FileReader();
                        let listItem = document.createElement("li");
                        fileName = i.name;
                        let fileSize = (i.size / 1024).toFixed(1);
                        listItem.innerHTML = `<p>${fileName}</p><p>${fileSize}KB</p>`;
                        if (fileSize >= 1024) {
                            fileSize = (fileSize / 1024).toFixed(1);
                            listItem.innerHTML = `<p>${fileName}</p><p>${fileSize}MB</p>`;
                            }
                        fileList.appendChild(listItem);
                    }
                console.log(fileList);
                })
            }
        
        $("#btnFindTraceFile").click(function(){ 
            containerBody.classList.remove('active');
            sidebarPopup.style.display = 'none';
            sidebarOpen.style.display = 'block';
            sidebarBtn.classList.add('active');
            containerBody.classList.add('active');
            sidebarBody.classList.add('active');

            if(fileList === ''){
                    document.getElementById("erreurMsg").innerHTML = "Sélectionnez un fichier";
                    erreurMsg.classList.add('active')
                }
            const myForm = document.getElementById("myForm")
            const inputFile = document.getElementById("inputFile")
            console.log(inputFile);
            console.log(myForm);
     
            const endpoint = 'uploadgpx.php';
            const formData = new FormData();
            console.log(endpoint);
            console.log(inputFile.files);
            
            formData.append("inputFile", inputFile.files[0]);


            myFile = inputFile.files[0].name;
            console.log(myFile);
            
            fetch(endpoint, {
                method:"post",
                body:formData
                    }).catch( (err) => {
                        console.log('ERROR:',err.message);
                    });
            
            let url = new URL('http://plf.partageonslaforet.be/desktop/assets/datas/uploadgpx/');
            let newUrl = new URL(myFile, url);
            console.log(newUrl);
            console.log(lyrCircuits)
       
            
         
            var control = L.control.layers(null, null).addTo(map);


            // ****************** LOAD GPX FILE *********************************************************    

        
            var Url = 'assets/datas/uploadgpx/'+fileName // URL to your GPX file or the GPX itself
            console.log(Url)
            
            new L.GPX(Url, {
                async: true,
                marker_options: {
                    startIconUrl: 'assets/img/pin-icon-start.png',
                    endIconUrl: 'assets/img/pin-icon-end.png',
                    shadowUrl: 'assets/img/pin-shadow.png'
                    }
                }).on('loaded', function(e) {
                    map.fitBounds(e.target.getBounds().pad(1));
            }).addTo(map);
            
            // *********** MAP ELEVATION *********************************************
          
            
            var elevation_options = {
        
            theme: "lime-theme",// Default chart colors: theme lime-theme, magenta-theme, ...
            detached: false, // Chart container outside/inside map container
            elevationDiv: "#elevation-div",// if (detached), the elevation chart container
            autohide: true,// if (!detached) autohide chart profile on chart mouseleave
            collapsed: false,// if (!detached) initial state of chart profile control
            position: "bottomleft", // if (!detached) control position on one of map corners
            closeBtn: true,// Toggle close icon visibility
            followMarker: true,// Autoupdate map center on chart mouseover.
            autofitBounds: false, // Autoupdate map bounds on chart update.
            imperial: false,// Chart distance/elevation units.
            reverseCoords: false,// [Lat, Long] vs [Long, Lat] points. (leaflet default: [Lat, Long])
            acceleration: false,// Acceleration chart profile: true || "summary" || "disabled" || false
            slope: false,// Slope chart profile: true || "summary" || "disabled" || false
            speed: false,// Speed chart profile: true || "summary" || "disabled" || false
            altitude: true, // Altitude chart profile: true || "summary" || "disabled" || false
            time: false,// Display time info: true || "summary" || false
            distance: true, // Display distance info: true || "summary" || false
            summary: 'inline',   // Summary track info style: "inline" || "multiline" || false
            downloadLink: 'false',    // Download link: "link" || false || "modal"
            ruler: false,    // Toggle chart ruler filter
            legend: true,// Toggle chart legend filter
            almostOver: true,// Toggle "leaflet-almostover" integration
            distanceMarkers: true,// Toggle "leaflet-distance-markers" integration
            edgeScale: true,// Toggle "leaflet-edgescale" integration
            hotline: false,// Toggle "leaflet-hotline" integration
            timestamps: false,// Display track datetimes: true || false
            waypoints: true,// Display track waypoints: true || "markers" || "dots" || false
            wptLabels: true,// Toggle waypoint labels: true || "markers" || "dots" || false
            preferCanvas: true,// Render chart profiles as Canvas or SVG Paths
            
            // Toggle custom waypoint icons: true || { associative array of <sym> tags } || false
            wptIcons: {
              '': L.divIcon({
                    className: 'elevation-waypoint-marker',
                    html: '<i class="elevation-waypoint-icon"></i>',
                    iconSize: [30, 30],
                    iconAnchor: [8, 30],
                    }),
                },
            };
          
            controlElevation = L.control.elevation(elevation_options).addTo(map);
            controlElevation.load(Url);
            
            // *********** LOAD NEW FILE *********************************************
            
            newSearch.classList.add('active');
            btnFindNewTraceFile.classList.add('active');
            
            
            
            $("#btnFindNewTraceFile").click(function(){ 
                location.reload();
                
            })
        })
   
    
    // ************ SETTING POPUP CALENDAR ************************************************************
    
    console.log(dateValue);
    if (dateValue=='undefined'){
        retour.classList.add('active')
        }
        else {
            retour.classList.remove('active');
        }
    
    
    
    // ************ POPUP CALENDAR ************************************************************
      
          const popup = document.getElementById('popup');
          const closebtn = document.getElementById('closebtn');
          const search = document.getElementById('calendarBtn');
          
          function showPopup(){
              popup.style.display = 'block';
              search.style.display = 'none';
          }
          
          function hidePopup(){
              popup.style.display = 'none';
              search.style.display = 'block';
          }
          
          console.log(myFile);
          if (myFile===undefined){
                calendar.classList.remove('active');
                console.log(myFile);
            }
            
          calendar.addEventListener('click', showPopup);
          
          closebtn.addEventListener('click', hidePopup);
          
          window.addEventListener('click', (event) =>{
            if (event.target === popup){
                hidePopup();
            }
        
        });  
      
         // *************** DATE SELECTION ***********************************************************
        var lRT = [];
       
        var cookieNber= "<?php echo $file_suffix; ?>";
        console.log(cookieNber);
        lRT = <?php echo json_encode($LRT);?>;
        console.log(lRT);
        lRTUS = lRT[2]["cron_chasses"]["Infos_Date"];
        lRTEUR = dayjs(lRTUS,'DD-MMM-YYYY HH:mm')
        lRTBE = lRTEUR.format('DD-MMM-YYYY HH:mm')
        console.log(lRTBE)
        document.getElementById("maj").innerHTML = "Dernière màj : "+lRTBE;
         
        $("#btonSearchDate").click(function(){
            dateValue = $('#datepicker').datepicker('getDate');
            formatDate = $.datepicker.formatDate("dd-mm-yy", dateValue);
             if(dateValue === null){
                document.getElementById("retour").innerHTML = "Veuillez sélectionner une date";
                    retour.classList.add('active');
                    message.classList.add('active');
                    squareOpen.classList.remove('active');
                    squareClose.classList.remove('active');
                }
            
            console.log(dateValue);
            console.log(formatDate);
            
                
            // *************** SETTINGS ***********************************************************
            
            if (lyrTerritories){
                lyrTerritories.remove();
            }
            
            
            
            //document.getElementById("retour").innerHTML = "";
            var huntedTerritories =[];
            var huntedTerritoriesList =[];
            var territoriesList = [];
            var territoriesClosed = [];
            var territoriesOpened = [];
            
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
                            squareOpen.classList.add('active');
                            squareClose.classList.add('active');
                            message.classList.add('active');
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
                                if (resultat[0]==-14){
                                        document.getElementById("retour").innerHTML = "Pas de chasse pour cette date.";
                                        retour.classList.add('active');
                                        message.classList.remove('active');
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
                                                    lyr.bindTooltip('<h3 style="color:#2c3e50"><center><b> '+att.Nom+'</h3></b><br>'+att.Numero_Lot);
                                                })
                                                lyr.on('mouseout', function(){
                                                    lyr.setStyle({fillOpacity: 0.3} );  
                                                    })    
                                            } 
                                            
                                        lyrTerritories.on('data:loaded',function(){
                                            
                                            }).addTo(map);
                                        }
                            }
                        }) 
                    }
                    

                    
                    }
                 
                    
                }
            });
         });
})      
</script>