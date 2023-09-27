<?php
$cookie_name = "plf";
if(!isset($_COOKIE[$cookie_name])) {
  session_start();
  $file_suffix = session_id();
  setcookie($cookie_name,session_id(),time() + (86400 * 2), "/");  
} else { $file_suffix = $_COOKIE[$cookie_name];}

require "assets/inc/php/Parameters.php";
require_once "assets/inc/php/Functions.php";

$List_Territoires = PLF::Get_Territoire_List();

if ($List_Territoires[0] < 0) {

   echo $List_Territoires[1];
}
//var_dump($List_Territoires);

?>
<!DOCTYPE html>
<html>
    <header>
        
        <title>Territories</title>
        <html lang="fr">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <!-- FICHIERS CSS -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;900&display=swap" rel="stylesheet"> 
        <link rel="stylesheet" href="assets/css/territories.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
        <link rel="stylesheet" href="assets/src/css/L.Control.Zoomslider.css">
        <link rel="stylesheet" href="assets/src/css/Control.MiniMap.css">
        <link rel="stylesheet" href="assets/src/css/gh-fork-ribbon.css">
        <link rel="stylesheet" href="assets/src/css/L.Control.Locate.css">
        <link rel="stylesheet" href="assets/src/css/leaflet.rainviewer.css">
        <link rel="stylesheet" href="assets/src/css/jquery-ui.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
        
        <!-- FICHIERS JS -->
        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
        <script src = "assets/src/js/L.Control.Zoomslider.js"></script>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src = "assets/src/js/Control.MiniMap.js"></script>
        <script src = "assets/src/js/leaflet-providers.js"></script>
        <script src = "assets/src/js/L.Control.Zoomslider.js"></script>
        <script src = "assets/src/js/L.Control.Locate.js"></script>
        <script src = "assets/src/js/leaflet.rainviewer.js"></script>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/leaflet-ajax/2.1.0/leaflet.ajax.min.js"></script>
        <script src = "https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src = "assets/src/js/jquery-ui.js"></script>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
        <script src = "https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>

        
    </header>
    
     <body>
        <div id="sidebar">
            <div class="sidebar-header">
                <div id="territoriesSearchLabel">
                    <div>TERRITOIRES DE CHASSE</div>
                </div>
            </div>
            <div class="sidebar-body">
                <div id="findTerritories">
                    <input type="search" id="txtFindTerritoriesName" placeholder="Territoire" autocomplete="off"/> 
                </div>
                <div id="search">
                    <button id="btnFindTerritoriesName"><i class="fa fa-search fa-2x"></i></button>
                </div>
                <div id="territoryInformations">
                    <div id="territoryInfo"></div>
                        <div id="territoryName"></div>
                        <div class="sub-info">
                            <div id="territoryNbre"></div>
                            <div id="territoryCanton"></div>
                            <div id="territoryCC"></div>
                            <div id="territoryArea"></div>
                        </div>
                    <div id="huntingDate"></div>
                    <div id="territoriesHuntingDatesLabel"></div> 
                        <div id="huntingDateList"></div>
                </div>
            </div>
            <div class="sidebar-footer">   
                <button id="btnRetour"onclick="window.location.href = 'https://plf.partageonslaforet.be/desktop';">QUITTER</button>
            </div>   
        </div>
        <container id ="Container"><center>
            <div class="custom-popup" id="map"></div>
        </container>
    </body>
</html>

<script>
    
    
    var sidebar;
    var listTerritories = [];
    var listHuntingDates =[];
    var huntingDates = [];
    var territoriesList = [];
    var arTerritoriesName = [];
    var listArrayN = [];
    var territoriesInfo = [] ;
    var territoriesInfodetails= [] ;
    var territoriesHuntingDatesLabel= [] ;
    var lyrTerritories;
    var map;
    var territoireValue;
    var huntedTerritories =[];
    var jsnTerritories;
    var huntedTerritoriesDetail =[];
    
    $(document).ready(function() {
        
    // ************ MAP INITIALIZATION ************************************************************
    
    map = L.map('map',{zoomControl: false}).setView([49.567574, 5.533507], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        zoomControl:false,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
    
    ctlScale = L.control.scale({position:'bottomleft', imperial:false, maxWidth:200}).addTo(map);
    //ctlZoomslider = L.control.zoomslider({position:'topleft'}).addTo(map);
    //ctlMeasure = L.control.polylineMeasure({position:'topleft'}).addTo(map);  
    
        // ************ LOCALISATION FUNCTION *********************************************************
    
           /*L.control.locate({
                position: 'topleft',
                strings:{
                    title:"Localisez-moi",
                },
                flyTo:true,
                initialZoomLevel:15,
                clickBehavior:{inView: 'stop', outOfView: 'setView', inViewNotFollowing: 'inView'},
                returnToPrevBounds:true
            }).addTo(map);*/
    
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

            //var osm = new L.TileLayer(osmUrl, {
        	//	minZoom: 6,
        	//	maxZoom: 12
        	//});
        	//map.addLayer(osm);
        
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
    
    
    // ************ LIST OF TERRITORY NAME ************************************************************
       
    territoriesList = <?php echo json_encode($List_Territoires[2]);?>;
    console.log(territoriesList);
    
    for(i=0; i<(territoriesList.length); i++){
        if(!arTerritoriesName.includes(territoriesList[i]["DA_Nom"])){
            arTerritoriesName.push(territoriesList[i]["DA_Nom"]); 
            arTerritoriesName.sort();

            $("#txtFindTerritoriesName").autocomplete({
                source:arTerritoriesName,
                autoFocus:true
            });
        }
    }    
    
    
    // ************ SEARCH MAP & INFO TERRITORY ************************************************************
     
    $("#btnFindTerritoriesName").click(function(){
        if(lyrTerritories){
            lyrTerritories.remove();
        }
        var territoireName = $("#txtFindTerritoriesName").val().toLowerCase();
        for(j=0; j<(territoriesList.length); j++ ){
            
            territoriesCheck = territoriesList[j]["DA_Nom"].toLowerCase()
            //console.log(territoriesCheck)
            if (territoireName == territoriesCheck){
                
                territoireValue = territoriesList[j]["DA_Numero"]
                console.log(territoireValue)
                
            }
        }
        
        
        // ************ SEARCH MAP TERRITORY ************************************************************
        
        $.ajax({
            type: 'GET',
            url: "assets/inc/php/createjsonN.php",
            data: "territoireValue="+territoireValue,
        
            success: function(response){
                console.log(response);
                if(response === 'undefined'){
                    
                
                } else {
                    lyrTerritories = L.geoJSON.ajax('assets/datas/territory.json',
                    {style:styleTerritories,onEachFeature:processTerritories});
                    
                        function styleTerritories (json) {
                        return {
                            fillOpacity: 0.3,
                            weight: 4,
                            color:'#fe7924'
                            };
                        }
                        
                        function processTerritories (json,lyr){
                        var att=json.properties;
                        
                        lyr.on('mouseover', function(){
                            lyr.setStyle({fillOpacity: 0.7})
                            lyr.bindTooltip('<div class="custom-popup">'+att.Territories_name+'<br>'+att.Nomenclature+'</div>');
                        })
                        lyr.on('mouseout', function(){
                            lyr.setStyle({fillOpacity: 0.3} );  
                            })    
                        } 
                    lyrTerritories.on('data:loaded',function(){
                        jsnTerritories = turf.area(lyrTerritories.toGeoJSON());
                        console.log(jsnTerritories);
                        var surfaceKM = (jsnTerritories/1000000).toFixed(2);
                        var surfaceHA = (jsnTerritories/10000).toFixed(2);
                        $('#territoryArea').html('Surface : '+surfaceKM +' Km2 ou '+surfaceHA +' Ha' );

                    map.fitBounds(lyrTerritories.getBounds().pad(1));
                    }).addTo(map);
                   
                }
    
            }
        })
        
        // ************ SEARCH INFO TERRITORY ************************************************************
        
        if (territoryName!==null){
                territoryName.classList.add('active')
                }
                else {
                    territoryName.classList.remove('active');
                }
        
        $.ajax({
            type: 'GET',
            url: "assets/inc/php/territories_info.php",
            data: "territoireValue="+territoireValue,
        
            success: function(response){
                console.log(response);
                territoriesInfo = JSON.parse(response);
                //territoriesInfodetails = Object.values(territoriesInfo[2]);
                console.log(territoriesInfo);

                
                console.log(territoryName);

                $('#territoryInfo').html('<center>TERRITOIRE<center>');
                $('#territoryName').html(territoriesInfo["Territories_name"]);
                $('#territoryNbre').html('Nomenclature : '+(territoriesInfo["Nomenclature"]));
                $('#territoryCanton').html('Canton : '+(territoriesInfo["nom_canton"]));
                $('#territoryCC').html('Conseil : '+(territoriesInfo["Code_CC"]));
                $('#territoryArea').html('Surface : '+(jsnTerritories)+' KM2' );
                
                // ************ SEARCH HUNTING DATES ************************************************************
                
                $.ajax({
                    type: 'GET',
                    url: "assets/inc/php/hunting_dates_search.php",
                    data: "territoireValue="+territoireValue,
                
                    success: function(response){
                        console.log(response);
                        huntedTerritories = JSON.parse(response);
                        console.log(huntedTerritories);
                        huntedTerritoriesDetails = Object.values(huntedTerritories[2]);
                        console.log(huntedTerritoriesDetails)
                        
                        
                        if(huntedTerritoriesDetails.length){ 
                            console.log(huntedTerritoriesDetails.length);
                            $('#territoriesHuntingDatesLabel').html('DATES DE CHASSE :');
                            list_date = "<ul id='data-Date'>";
                            for(i=0;i<huntedTerritoriesDetails.length; i++){
                                moment.locale('fr');
                                var huntedTerritoriesDetailsString=huntedTerritories[2][i]
                                
                                function formatDate(inputDate) {
                                      const dateObj = moment(inputDate, 'DD-MM-YYYY','fr').locale('fr'); // Provide the correct format 'DD-MM-YYYY'
                                    
                                      const formattedDate = dateObj.format('ddd D MMMM YYYY');
                                    
                                      return formattedDate;
                                    }
                                    
                                const originalDate = huntedTerritoriesDetailsString;
                                const transformedDate = formatDate(originalDate);
                                console.log(transformedDate);

                                
                                var huntedDateFormat = moment(huntedTerritoriesDetailsString);
                                      
                                console.log(huntedDateFormat.format('DD/MM/YYYY'));
                                //list_date += "<li='" + huntedTerritories[2][i] +"'>"+huntedTerritories[2][i] +"<br>";"</li>";
                                list_date += "<li='" + transformedDate +"'>"+transformedDate +"<br>";"</li>";
                                } 
                            list_date += "</ul>";
                            document.getElementById("huntingDateList").innerHTML = list_date;
                        }
                    }
                })   
            }
        })
        
    });  
})          
</script>