
<?php
$cookie_name = "plf";
if(!isset($_COOKIE[$cookie_name])) {
  session_start();
  $file_suffix = session_id();
  setcookie($cookie_name,session_id(),time() + (86400 * 2), "/");  
} else { $file_suffix = $_COOKIE[$cookie_name];}

require "assets/inc/php/Parameters.php";
require_once "assets/inc/php/Functions.php";

$List_Canton = PLF::Get_Canton_List();

if ($List_Canton[0] < 0) {

   echo $List_Canton[1];

}



$List_Territoires = PLF::Get_Territoire_List();

if ($List_Territoires[0] < 0) {

   echo $List_Territoires[1];
}


$LRT = PLF::Get_LastRunTime();

?>
<!DOCTYPE html>
<html>
    <header>
        
        <title>DNF</title>
        <html lang="fr">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <!-- FICHIERS CSS -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;900&display=swap" rel="stylesheet"> 
        <link rel="stylesheet" href="assets/css/dnf.css">
        <link rel="stylesheet" href="assets/src/css/leaflet.css">
        <link rel="stylesheet" href="assets/src/css/L.Control.Zoomslider.css">
        <link rel="stylesheet" href="assets/src/css/Control.MiniMap.css">
        <link rel="stylesheet" href="assets/src/css/gh-fork-ribbon.css">
        <link rel="stylesheet" href="assets/src/css/L.Control.Locate.css">
        <link rel="stylesheet" href="assets/src/css/leaflet.rainviewer.css">
        <link rel="stylesheet" href="assets/src/css/Leaflet.PolylineMeasure.css">
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
        <script src = "assets/src/js/Leaflet.PolylineMeasure.js"></script>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/leaflet-ajax/2.1.0/leaflet.ajax.min.js"></script>
        <script src = "https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src = "https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src = "assets/src/js/jquery-ui.js"></script>
        <script src = "https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
        <script src = "https://cdn.jsdelivr.net/npm/dayjs@1.11.9/dayjs.min.js"></script>
        
    </header>
    
     <body>
         <div id="sidebar">
            <h1 class="sidebar-header"></h1>
            <div id="territoriesSearch">
                <div id="territoriesSearchLabel">
                    <div>CANTONNEMENT DNF</div>
                </div>
                <div id="findDnf">
                   <!--<div class="custom-checkbox">
                        <input type="checkbox" id="allCantons" disabled>
                        <label for="allCantons">Voir tous les cantonnements</label>
                    </div>-->
                    <select type="search" id="txtFindDnfName" placeholder="Cantonnement"></select> 
                </div>
                <div id="search">
                    <button id="btnFindDnfName"><i class="fa fa-search fa-2x"></i></button>
                </div>
                <div id="cantonInfo"></div>
                <div id="cantonName"></div>
                <div id="cantonInfoDetails">
                    <div id="cantonNbre"></div>
                    <div id="cantonArea"></div>
                    <div id="cantonId"></div>
                    <div id="cantonDir"></div>
                    <div id="cantonResp"></div>
                    <div id="cantonTel"></div>
                    <div id="cantonEmail"></div>
                    <div id="cantonAdresse"></div>
                    <div id="messageErreur"></div>
                </div>
            </div>
            <button id="btnRetour"onclick="window.location.href = '..';">RETOUR</button>
        </div>
        <div id="calendarBtn">
            <a id="calendar"><i class="fa fa-calendar fa-2x" title="CLIQUEZ"></i></a>
        </div>
        <div id="popup" class="popup">
            <button id="closebtn">&times;</button>
            <div class="popup-content">
                <div id="maj"></div>
                <div class="date-calendar">
                   <p><input type="text" id="datepicker" placeholder="Cliquez pour choisir une date"></p>
                </div>    
                <button id="btonSearchDate"><i class="fa fa-search"></i></button>
                <script>
                $( function() {
                    $("#datepicker").datepicker({
                        dateFormat: "dd-mm-yy",
                        dayNamesMin: [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ],
                        monthNames: [ "Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "December" ],
                        buttonImageOnly: true
                    });
                    
                 } );
                </script>
            </div>
        </div>
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
        <container id ="Container"><center>
            <div id="map"></div>
        </container>
    </body>
</html>

<script>
    
    
    var sidebar;
    var listTerritories = [];
    var listHuntingDates =[];
    var huntingDates = [];
    var huntedTerritories =[];
    var territoriesListByCanton = [];
    var arhuntedTerritories = [];
    var arTerritoriesNber = [];
    var listArrayN = [];
    var listByCanton =[];
    var territoriesInfo = [] ;
    var territoriesNbers = [] ;
    var territoriesList = [];
    var lyrTerritories;
    var lyrTerritoriesDnf;
    var map;
    var territoireValue;
    var huntedTerritories =[];
    var territoriesCheck;
    var territoireName;
    var territoriesNbersDnf =[];
    var dnfTerritoriesNber;
    
$(document).ready(function() {
       
        
    // ************ MAP INITIALIZATION ************************************************************
    
    map = L.map('map', { zoomControl: false }).setView([49.567574, 5.533507], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        zoomControl:false,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
    
    ctlScale = L.control.scale({position:'bottomleft', imperial:false, maxWidth:200}).addTo(map);
    //ctlZoomslider = L.control.zoomslider({position:'topleft'}).addTo(map);
    //ctlMeasure = L.control.polylineMeasure({position:'topleft'}).addTo(map);  
    
    // ************ LOCALISATION FUNCTION *********************************************************

        /* L.control.locate({
            position: 'topleft',
            strings:{
                title:"Localisez-moi",
            },
            flyTo:true,
            initialZoomLevel:15,
            clickBehavior:{inView: 'stop', outOfView: 'setView', inViewNotFollowing: 'inView'},
            returnToPrevBounds:true
        }).addTo(map);*/

    // ************ LEAFLET INITIALIZATION *******************************************************
     
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
        	
  
    // ************ DNF SEARCH CODE************************************************************       

    	// ************ LIST OF CANTON NAME ************************************************************
   
        listByCantonBt = <?php echo json_encode($List_Canton);?>;
        listTerritories =<?php echo json_encode($List_Territoires[2]);?>;
        listTerritoriesNb=listTerritories.length;
        //console.log(listTerritoriesNb)
        var listByCanton = Object.values(listByCantonBt[2])
        var cantonNbre = listByCanton.length;
        //console.log(listByCanton);
        for(i=0; i<cantonNbre; i++){     
            
            if(!arTerritoriesNber.includes(listByCanton[i]["nom"])){
                arTerritoriesNber.push(listByCanton[i]["nom"]); 
                arTerritoriesNber.sort();
            }    
        }
        
        const selectMenu = $("#txtFindDnfName");
        arTerritoriesNber.forEach(function (item) {
            selectMenu.append($("<option>", { value: item, text: item }));
        });   
        
        selectMenu.selectmenu({
              classes: {
                "ui-selectmenu-menu-text": "highlight"

              }
        });
        
        
        // ************ SEARCH MAP TERRITORY ************************************************************
        
        document.getElementById("txtFindDnfName").addEventListener("focus", focusIn);

        function focusIn() {
          document.getElementById("txtFindDnfName").value = "";
        }
        
        document.getElementById("btnFindDnfName").addEventListener("focusout", focusOut);

        function focusOut() {
          //document.getElementById("allCantons").disabled = false;
        }
        
        
        $("#btnFindDnfName").click(function(){
          
            if(lyrTerritories){
                lyrTerritories.remove();
            }
            
            if(lyrTerritoriesDnf){
                lyrTerritoriesDnf.remove();
                map.removeLayer(marker);
                $('#cantonInformations').html('');
                $('#cantonInfo').html('');
                $('#cantonName').html('');
                $('#cantonNbre').html('');
                $('#cantonDir').html('');
                $('#cantonResp').html('');
                $('#cantonTel').html('');
                $('#cantonEmail').html('');
                $('#cantonAdresse').html('');
                $('#retour').html('');
                retour.classList.remove('active');
                document.getElementById("datepicker").value = "";
            }
            
            document.getElementById("messageErreur").innerHTML = "";
            var arDnfInfo = [];
            territoireName = $("#txtFindDnfName").val().toLowerCase();
            //console.log(territoireName)
            
            if(territoireName === ''){
                document.getElementById("messageErreur").innerHTML = "Sélectionnez un Cantonnement";
                messageErreur.classList.add('active');
                cantonInfo.classList.remove('active');
                cantonInfoDetails.classList.remove('active')
                cantonName.classList.remove('active');
                cantonArea.classList.remove('active');
                
            }
            else {
                messageErreur.classList.remove('active');
                cantonName.classList.add('active');
                cantonArea.classList.add('active');
                cantonInfo.classList.add('active');
                cantonInfoDetails.classList.add('active');
            
            
                for(j=0; j<(listByCanton.length); j++ ){
                    
                    territoriesCheck = listByCanton[j]["nom"].toLowerCase()
                    //console.log(territoriesCheck)
                    //console.log(territoireName)
                    if (territoireName == territoriesCheck){
                        
                        territoireValue = listByCanton[j]["num_canton"]
                        //console.log(territoireValue)
                        arDnfInfo.push(listByCanton[j]["nom"],
                            listByCanton[j]["num_canton"],
                            listByCanton[j]["direction"],
                            listByCanton[j]["attache"],
                            listByCanton[j]["tel"],
                            listByCanton[j]["email"],
                            listByCanton[j]["latitude"],
                            listByCanton[j]["longitude"],
                            listByCanton[j]["localite"],
                            listByCanton[j]["rue"],
                            listByCanton[j]["numero"],
                            listByCanton[j]["CP"])
                        
                        break;
                    }
                    //console.log(arDnfInfo);
                }
                
              
                // ************ SEARCH DNF TERRITORIES ************************************************************
                
                var cookieNber= "<?php echo $file_suffix; ?>";
                var cantonNber=0;
                $.ajax({
                type: 'GET',
                url: "assets/inc/php/createJsonDnf.php",
                data: "territoireValue="+territoireValue,
            
                    success: function(response){
                        //console.log(response);
                        if(typeof response === 'undefined'){
                            alert("erreur");    
                            } else {
                                //console.log(lyrTerritoriesDnf)
                                if(lyrTerritoriesDnf){
                                    lyrTerritoriesDnf.remove();
                                    map.removeLayer(lyrTerritoriesDnf);
                                }

                                lyrTerritoriesDnf = L.geoJSON.ajax('assets/datas/'+cookieNber+'territoryDnf.json',
                                {style:styleTerritories,onEachFeature:processTerritories});
                                //console.log(lyrTerritoriesDnf.length)

                                
                                    function styleTerritories (json) {
                                        return {
                                            fillOpacity: 0.3,
                                            weight: 2,
                                            fillColor:'#fe7924',
                                            color:'#fe7924'
                                            };
                                    }
                                    
                                    function processTerritories (json,lyr){
                                        var att=json.properties;
                                        cantonNber=cantonNber+1 
                                        lyr.on('mouseover', function(){
                                            lyr.setStyle({fillOpacity: 0.7})
                                            lyr.bindTooltip('<h3 style="color:#2c3e50"><center>N° de Territoire: <br>'+att.Numero_Lot+'</h3>');
                                        })
                                        lyr.on('mouseout', function(){
                                            lyr.setStyle({fillOpacity: 0.3} );  
                                            })    
                                    } 
                                    
                                 // ************ DNF INFO ************************************************************     
                                    
                                lyrTerritoriesDnf.on('data:loaded',function(){
                                    jsnTerritories = turf.area(lyrTerritoriesDnf.toGeoJSON());
                                    cantonArea.classList.add('active');
                                    
                                    var surfaceKM = new Intl.NumberFormat('de-DE').format((jsnTerritories/1000000).toFixed(2));
                                    var surfaceHA = new Intl.NumberFormat('de-DE').format((jsnTerritories/10000).toFixed(0));
                                    $('#cantonArea').html('Surface : '+surfaceKM +' Km2 ou '+surfaceHA +' Ha' );
                                    map.fitBounds(lyrTerritoriesDnf.getBounds().pad(0));
                                    $('#cantonInfo').html('<center>CANTONNEMENT<center>');
                                    $('#cantonName').html(arDnfInfo[0]);
                                    $('#cantonNbre').html(cantonNber+ ' territoires gérés');
                                    $('#cantonId').html("N° d'Identification : "+arDnfInfo[1]);
                                    $('#cantonDir').html('Direction : '+arDnfInfo[2]);
                                    $('#cantonResp').html('Chef de canton : '+arDnfInfo[3]);
                                    $('#cantonTel').html('<i class="fa-solid fa-phone"></i>&nbsp'+arDnfInfo[4]);
                                    $('#cantonEmail').html("<i class='fa-solid fa-envelope'>&nbsp"+"</i><a href='mailto:"+arDnfInfo[5]+"'>"+arDnfInfo[5]+"</a>");
                                    $('#cantonAdresse').html('<i class="fa-solid fa-location-dot"></i>&nbsp '+arDnfInfo[10]+', '+arDnfInfo[9]+' '+arDnfInfo[11]+' '+arDnfInfo[8]);
                                
                                    var lat = (arDnfInfo[6]);
                                    var long = (arDnfInfo[7]);
                                    //console.log(lat);
                                    //console.log(long);
                                    
                                    var dnfIcon = L.icon({
                                        iconUrl: 'assets/img/Logo_dnf.png',
                                        iconSize:     [25, 25], // size of the icon
                                    });
                                    marker = new L.marker([lat,long], {icon: dnfIcon});
                                    map.addLayer(marker);
                                
                                }).addTo(map);
                                    
                            }  
                    }
                });
                                          
                // ********** FUNCTIONS TERRITORIES *********************************************************************
    
                    function styleTerritories (json) {
                        return {
                            fillOpacity: 0.5,
                            weight: 4,
                            color:'#fe7924'
                            };
                        }
                
                    function processTerritories (json,lyr){
                        var att=json.properties;
                        lyr.on('mouseover', function(){
                            lyr.setStyle({fillOpacity: 0.5})
                            lyr.bindTooltip('<div class="custom-popup" N° du territoire : <br>'+att.Nomenclature+'</div>');
                        })
                        
                        lyr.on('mouseout', function(){
                            lyr.setStyle({fillOpacity: 0.3} );
                            $('#divTerritoriesData1').html('');     
                        })    
                    }    
            }   
        })       
        
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
            message.classList.remove('active');
            retour.classList.remove('active');
            squareOpen.classList.remove('active');
            squareClose.classList.remove('active');
            }
        
        calendar.addEventListener('click', showPopup);
        
        closebtn.addEventListener('click', hidePopup);
        
        window.addEventListener('click', (event) =>{
            if (event.target === popup){
                hidePopup();
            }
        
        });  
      
    
         // *************** DATE SELECTION ***********************************************************
        var cookieNber= "<?php echo $file_suffix; ?>";
        lRT = <?php echo json_encode($LRT);?>;
        lRTUS = lRT[2]["cron_chasses"]["Infos_Date"];
        lRTEUR = dayjs(lRTUS,'DD-MMM-YYYY HH:mm')
        lRTBE = lRTEUR.format('DD-MMM-YYYY HH:mm')
        
        document.getElementById("maj").innerHTML = "Dernière màj : "+lRTBE;
         
        $("#btonSearchDate").click(function(){
            //console.log(lyrTerritories)
            if (lyrTerritories){
                    lyrTerritories.remove();
                    map.removeLayer(lyrTerritories);
                }
                //console.log(lyrTerritories)
            dateValue = $('#datepicker').datepicker('getDate');
            formatDate = $.datepicker.formatDate("dd-mm-yy", dateValue);
            //console.log(dateValue);
            if(dateValue === null){
                document.getElementById("retour").innerHTML = "Veuillez sélectionner une date";
                    retour.classList.add('active');
                    message.classList.add('active');
                    squareOpen.classList.remove('active');
                    squareClose.classList.remove('active');
            }
            else{  
                
                // *************** SETTINGS ***********************************************************
                 
                document.getElementById("retour").innerHTML = "";
                
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
                        //console.log(response);
                        resultat = JSON.parse(response);
                        //console.log(resultat)
                        if (resultat[0]==-14){
                            document.getElementById("retour").innerHTML = "Pas de chasse pour cette date.";
                            retour.classList.add('active');
                            message.classList.add('active');
                            infoRetour.classList.remove('active');
                            squareOpen.classList.remove('active');
                            squareClose.classList.remove('active');
                        }else{
                            huntedTerritories = JSON.parse(response);
                            //console.log(huntedTerritories)
                            huntedNber=(huntedTerritories[2].length);
                            //console.log(huntedNber)

                            for(i=0; i<huntedNber; i++){
                                //console.log(huntedTerritories[2][i]["FERMETURE"]);
                                territory = huntedTerritories[2][i]["DA_Numero"];
                                territoriesList.push(territory);
                                if (huntedTerritories[2][i]["FERMETURE"]=="O"){
                                    territoriesClosed.push(territory)
                                }
                                else {
                                    territoriesOpened.push(territory)
                                }
                            }
                                                       
                            //console.log(territoriesClosed);
                            //console.log(territoriesOpened);
                    
                            var territoriesNberAll = huntedTerritories
                            //console.log(territoriesNberAll)
                            //console.log(territoireValue)
                            territoriesNber = territoriesSelection (territoriesNberAll,territoireValue)        

                            function territoriesSelection(territoriesNberAll,territoireValue){
                                dnfTerritoriesNber= territoriesNberAll[2].length
                                //console.log(dnfTerritoriesNber)
                                arhuntedTerritories = [];
                                for (i=0; i<((dnfTerritoriesNber));i++){
                                    var dnfCode = huntedTerritories[2][i]["DA_Numero"].substring(0, 3);
                                    //console.log(i);
                                    if(territoireValue == dnfCode ){
                                        //console.log(territoireValue)
                                        //console.log(dnfCode)
                                        arhuntedTerritories.push(huntedTerritories[2][i]["DA_Numero"])
                                        //console.log(arhuntedTerritories)
                                    }        
                                }
                                
                                huntedNber=(arhuntedTerritories.length);
                                //console.log(huntedNber)
                            }

                            //console.log(territoriesNber)
                            //console.log(huntedNber)
                            if(huntedNber>0){
                                //console.log("coucou");
                                document.getElementById("retour").innerHTML = huntedNber + " territoires chassés le "+ formatDate;
                                retour.classList.add('active');
                                message.classList.add('active');
                                infoRetour.classList.add('active');
                                squareOpen.classList.add('active');
                                squareClose.classList.add('active');
                            }
                            //console.log(arhuntedTerritories) 
                                 
                            territoriesNber=arhuntedTerritories.join(',');
                            var lyrhuntingterritoriesClosed = createMultiJson(territoriesNber);
                            //var lyrhuntingterritoriesOpened = createMultiJson(territoriesNber);
                            
                // ************ SEARCH HUNTING TERRITORIES ************************************************************
                    
                    function createMultiJson(territoriesNber){
                        $.ajax({
                            type: 'GET',
                            url: "assets/inc/php/createMultiJson_by_n.php",
                            data: {territoriesNber:territoriesNber},
                            
                            success: function(response){
                                //console.log(response);
                            
                                if (resultat[0]==-14){
                                    document.getElementById("retour").innerHTML = "Pas de chasse pour cette date.";
                                    retour.classList.add('active');
                                    message.classList.remove('active');
                                    infoRetour.classList.remove('active');
                                    squareOpen.classList.remove('active');
                                    squareClose.classList.remove('active');
                                }
                                else {
                                //console.log(lyrTerritories)
                                    if(lyrTerritories){
                                        lyrTerritories.remove();
                                        map.removeLayer(lyrTerritories);
                                    }
                                    //console.log(huntedNber)
                                    lyrTerritories = L.geoJSON.ajax('assets/datas/'+cookieNber+'huntedTerritoryByDate.json',
                                    {style:styleTerritories,onEachFeature:processTerritories});
                                    //console.log(lyrTerritories)
                                    
                                    function styleTerritories (json) {
                                        var att=json.properties;
                                        for(i=0; i<huntedNber; i++){
                                            //console.log(att.Numero_Lot) 
                                            //console.log(dnfTerritoriesNber)
                                            for(j=0;j<(dnfTerritoriesNber); j++){
                                                //console.log(huntedTerritories[2][j]["DA_Numero"]);
                                                if(att.Numero_Lot == huntedTerritories[2][j]["DA_Numero"]){
                                                   // console.log("coucou")  
                                                    if(huntedTerritories[2][j]["FERMETURE"]=="O"){  
                                                        //console.log("coucou1")   
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
                                            }
                                            
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
                                       // map.fitBounds(lyrTerritories.getBounds().pad(0));
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