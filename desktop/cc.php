<?php
$cookie_name = "plf";
if (!isset($_COOKIE[$cookie_name])) {
    session_start();
    $file_suffix = session_id();
    setcookie($cookie_name, session_id(), time() + (86400 * 2), "/");
} else {
    $file_suffix = $_COOKIE[$cookie_name];
}


require "assets/inc/php/Parameters.php";
require_once "assets/inc/php/Functions.php";

$List_CC = PLF::Get_CC_List();

if ($List_CC[0] < 0) {

    echo $List_CC[1];

    //
    // .... traitement de l'erreur
    //
}

//var_dump($List_CC);
$LRT = PLF::Get_LastRunTime();

?>
<!DOCTYPE html>
<html>
<header>

    <title>Conseil Cynégétique</title>
    <html lang="fr">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- FICHIERS CSS -->
    <link rel="stylesheet" href="assets/css/cc.css">
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
    <script src="assets/src/js/leaflet.js"></script>
    <script src="assets/src/js/L.Control.Zoomslider.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="assets/src/js/Control.MiniMap.js"></script>
    <script src="assets/src/js/leaflet-providers.js"></script>
    <script src="assets/src/js/L.Control.Locate.js"></script>
    <script src="assets/src/js/leaflet.rainviewer.js"></script>
    <script src="assets/src/js/Leaflet.PolylineMeasure.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-ajax/2.1.0/leaflet.ajax.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="assets/src/js/jquery-ui.js">
        >
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.9/dayjs.min.js"></script>

</header>

<body>
    <div id="sidebar">
        <h1 class="sidebar-header"></h1>
        <div id="territoriesSearch">
            <div id="territoriesSearchLabel">
                <div>CONSEILS CYNEGETIQUES</div>
            </div>
            <div id="findCC">
                <!--<div class="custom-checkbox">
                        <input type="checkbox" id="allConseils">
                        <label for="allConseils">Voir tous les Conseils</label>
                    </div>-->
                <select type="search" id="txtFindCCName" placeholder="Conseil Cynégétique"></select>
            </div>
            <div id="search">
                <button id="btnFindCCName"><i class="fa fa-search fa-2x"></i></button>
            </div>
            <div id="messageErreur"></div>
        </div>
        <div id="ccInfo"></div>
        <div id="ccFullName"></div>
        <div id="ccName"></div>
        <div id="ccInfoDetails">
            <div id="ccNbre"></div>
            <div id="ccArea"></div>
            <div id="ccTerritoriesNber"></div>
            <div id="territoryArea"></div>
            <div id="ccAddress"></div>
            <div id="ccPresident"></div>
            <div id="ccSecretaire"></div>
        </div>
        <div id="btonClose">
            <button id="btnRetour" onclick="window.location.href = '..';">RETOUR</button>
        </div>
    </div>
    <div id="calendarBtn">
        <a id="calendar"><i class="fa fa-calendar fa-2x" title="CLIQUEZ"></i></a>
    </div>
    <div id="popup" class="popup">
        <button id="closebtn">&times;</button>
        <div id="headPopup">
            <div id="maj"></div>
        </div>
        <div class="popup-content">
            <div class="date-calendar">
                <p><input type="text" id="datepicker" placeholder="Cliquez pour choisir une date"></p>
            </div>
            <button id="btonSearchDate"><i class="fa fa-search"></i></button>
            <script>
                $(function() {
                    $("#datepicker").datepicker({
                        dateFormat: "dd-mm-yy",
                        dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
                        monthNames: ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "December"],
                        nextText: "Suivant",
                        prevText: "Précédent"
                    });

                });
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
    <container id="Container">
        <center>
            <div id="map"></div>
    </container>
</body>

</html>

<script>
    var sidebar;
    var listTerritories = [];
    var listHuntingDates = [];
    var huntingDates = [];
    var territoriesListByCC = [];
    var arTerritoriesNber = [];
    var territoriesNbers = [];
    var listArrayN = [];
    var listByCanton = [];
    var territoriesInfo = [];
    var territoriesNber;
    var lyrTerritoriesCC;
    var lyrTerritories;
    var map;
    var territoireValue;
    var huntedTerritories = [];
    var territoriesList = [];
    var territoriesClosed = [];
    var territoriesOpened = [];
    var dateValue;
    var formatDate;
    var ccNber;
    var huntedNber;


    $(document).ready(function() {

        var cookieNber = "<?php echo $file_suffix; ?>";

        // ************ MAP INITIALIZATION ************************************************************

        map = L.map('map', {
            zoomControl: false
        }).setView([49.567574, 5.533507], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            zoomControl: false,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        ctlScale = L.control.scale({
            position: 'bottomleft',
            imperial: false,
            maxWidth: 200
        }).addTo(map);
        //ctlZoomslider = L.control.zoomslider({position:'topleft'}).addTo(map);
        //ctlMeasure = L.control.polylineMeasure({position:'topleft'}).addTo(map);  

        // ************ LOCALISATION FUNCTION *********************************************************

        /*L.control.locate({
            position: 'bottomright',
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
            "osm": lyrOSM,
            "Satellite": lyrEsri_WorldImagery,
            "Altitude": lyrOpenTopoMap,
            "Cyclo": lyrCyclo
        };

        var overlays = {

        };

        L.control.layers(baseLayers, overlays).addTo(map);

        // ************ MINIMAP INITIALIZATION *******************************************************

        var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        var osmAttrib = 'Map data &copy; OpenStreetMap contributors';

        var osm2 = new L.TileLayer(osmUrl, {
            minZoom: 5,
            maxZoom: 10,
            attribution: osmAttrib,
        });
        var miniMap = new L.Control.MiniMap(osm2, {
            toggleDisplay: true,
            position: 'bottomright',
            zoomControl: false
        }).addTo(map);

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


        // ************ ALL CONSEILS************************************************************
        /* document.getElementById('allConseils').onclick = function() {
            var arConseilRef=[];
            var tab=[]
            var keys=[]
            conseilNber =  arTerritoriesNber.length
            console.log(conseilNber);
            
            for(i=0; i<conseilNber; i++){
                keys = arTerritoriesNber[i];
                console.log(keys)
                arConseilRef.push(keys)
            }
            
            
            var territoriesNber = arConseilRef.join(',');
            console.log(territoriesNber)
            
       
            if (document.getElementById('allConseils').checked == true){
                var url = "https://partageonslaforet.be/assets/inc/php/createAllCC.php"
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json', 
                    },
                })
                .then(response => {
                    if(!response.ok){
                        throw new Error('Network response was not ok');
                    }
                     return response.json();
                })
                .then(data => {
                  console.log(data); // Faire quelque chose avec les données
                })
                .catch(error => {
                  console.error('Fetch error:', error);
                });
                
                
                
                
                $.ajax({
                type: 'GET',
                url: "assets/inc/php/createAllCC.php",
                data: {territoriesNber:territoriesNber},
            
                    success: function(response){
                        console.log(response);
                        if(lyrTerritoriesCC){
                            lyrTerritoriesCC.remove();
                            map.removeLayer(lyrTerritoriesCC);
                        }

                        lyrTerritoriesCC = L.geoJSON.ajax('assets/datas/'+cookieNber+'territoriesAllCC.json',
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
                                lyr.bindTooltip('<h3 style="color:#2c3e50"><center><b> '+att.Nom+'</h3></b><br>'+att.Numero_Lot);
                            })
                            lyr.on('mouseout', function(){
                                lyr.setStyle({fillOpacity: 0.3} );  
                                })    
                        } 
                    }
                })
            }
        }*/


        // ************ LIST OF CC NAME ************************************************************

        listBycc = <?php echo json_encode($List_CC); ?>;
        var listByCC = Object.values(listBycc[2])

        console.log(listByCC)
        var ccNbre = Object.keys(listByCC).length;
        console.log(ccNbre);

        for (i = 0; i < (ccNbre); i++) {
            //console.log(listByCC[i]["nom"]);
            if (!arTerritoriesNber.includes(listByCC[i]["ugc"])) {
                arTerritoriesNber.push(listByCC[i]["ugc"]);
                arTerritoriesNber.sort();
            };
        }

        const selectMenu = $("#txtFindCCName");
        arTerritoriesNber.forEach(function(item) {
            selectMenu.append($("<option>", {
                value: item,
                text: item
            }));
        });

        selectMenu.selectmenu({
            classes: {
                "ui-selectmenu-menu-text": "highlight"

            }
        });

        // ************ SEARCH MAP TERRITORY ************************************************************

        document.getElementById("txtFindCCName").addEventListener("focus", focusIn);

        function focusIn() {
            //document.getElementById("allConseils").disabled = true;
            document.getElementById("txtFindCCName").value = "";
        }

        document.getElementById("txtFindCCName").addEventListener("focusout", focusOut);

        function focusOut() {
            //document.getElementById("allConseils").disabled = false;
        }


        $("#btnFindCCName").click(function() {
            //document.getElementById("allConseils").disabled = true;

            if (lyrTerritoriesCC) {
                lyrTerritoriesCC.remove();
                map.removeLayer(lyrTerritoriesCC);
                var territoryCC;
                $('#ccFullName').html('');
                $('#ccPresident').html('');
                $('#ccSecretaire').html('');
                $('#ccTerritoriesNber').html('');
            }

            document.getElementById("messageErreur").innerHTML = "";
            var arCCinfo = [];

            var territoireName = $("#txtFindCCName").val().toLowerCase();
            document.getElementById("txtFindCCName").value = "";
            //document.getElementById("allConseils").disabled = false;
            console.log(territoireName);
            if (territoireName === '') {
                document.getElementById("messageErreur").innerHTML = "Sélectionnez un Conseil";
                messageErreur.classList.add('active');
                ccInfo.classList.remove('active');
                ccInfoDetails.classList.remove('active');
                ccFullName.classList.remove('active');

            } else {
                messageErreur.classList.remove('active');
                ccInfo.classList.add('active');
                ccInfoDetails.classList.add('active');
                ccFullName.classList.add('active');

                for (i = 0; i < (listByCC.length); i++) {
                    territoireCheck = listByCC[i]["ugc"].toLowerCase()
                    console.log(territoireName)
                    console.log(territoireCheck);

                    if (territoireName == territoireCheck) {
                        territoireValue = listByCC[i]["ugc"]
                        console.log(territoireValue)
                        arCCinfo.push(listByCC[i]["nom"],
                            listByCC[i]["ugc"],
                            listByCC[i]["numero"],
                            listByCC[i]["rue"],
                            listByCC[i]["CP"],
                            listByCC[i]["localite"],
                            listByCC[i]["president_nom"],
                            listByCC[i]["president_prenom"],
                            listByCC[i]["secretaire_nom"],
                            listByCC[i]["secretaire_prenom"],
                            listByCC[i]["email"],
                            listByCC[i]["logo"],
                            listByCC[i]["site_internet"],
                            listByCC[i]["latitude"],
                            listByCC[i]["longitude"])
                        break;
                    }
                }


                console.log(arCCinfo);
                console.log(territoireValue);

                // ************ SEARCH CC TERRITORIES ************************************************************
                var ccNber = 0;


                $.ajax({
                    type: 'GET',
                    url: "assets/inc/php/createJsonCC.php",
                    data: "territoireValue=" + territoireValue,

                    success: function(response) {
                        if (typeof response === 'undefined') {
                            alert("erreur");

                        } else {
                            console.log(response)
                            if (lyrTerritoriesCC) {
                                lyrTerritoriesCC.remove();
                                map.removeLayer(lyrTerritoriesCC);
                            }

                            lyrTerritoriesCC = L.geoJSON.ajax('assets/datas/' + cookieNber + 'territoryCC.json', {
                                style: styleTerritories,
                                onEachFeature: processTerritories
                            });



                            function styleTerritories(json) {
                                return {
                                    fillOpacity: 0.3,
                                    weight: 4,
                                    color: '#fe7924'
                                };
                            }

                            function processTerritories(json, lyr) {
                                var att = json.properties;

                                lyr.on('mouseover', function() {
                                    lyr.setStyle({
                                        fillOpacity: 0.7
                                    })
                                    lyr.bindTooltip('<h3 style="color:#2c3e50"><center><b> ' + att.Nom + '</h3></b><br>' + att.Numero_Lot);
                                })
                                lyr.on('mouseout', function() {
                                    lyr.setStyle({
                                        fillOpacity: 0.3
                                    });
                                })
                            }

                            // ************ CC INFO ************************************************************     

                            lyrTerritoriesCC.on('data:loaded', function() {
                                map.fitBounds(lyrTerritoriesCC.getBounds().pad(1))
                                jsnTerritories = turf.area(lyrTerritoriesCC.toGeoJSON());
                                ccArea.classList.add('active');
                                var surfaceKM = new Intl.NumberFormat('de-DE').format((jsnTerritories / 1000000).toFixed(2));
                                var surfaceHA = new Intl.NumberFormat('de-DE').format((jsnTerritories / 10000).toFixed(0));
                                $('#ccArea').html('Surface : ' + surfaceKM + ' Km2 ou ' + surfaceHA + ' Ha');
                                ccNber = lyrTerritoriesCC.getLayers().length;

                                //$('#ccInfo').html('<h3><center><b>CONSEIL CYNEGETIQUE</b></h3><h3 style="color:#fe7924";><b><center>');
                                $('#ccNbre').html(ccNber + ' territoires gérés');
                                $('#ccFullName').html(arCCinfo[0] + '</br> (' + arCCinfo[1] + ')');
                                $('#ccAddress').html(arCCinfo[2] + ', ' + arCCinfo[3] + '</br>' + arCCinfo[4] + ' ' + arCCinfo[5]);
                                $('#ccPresident').html('President : ' + arCCinfo[7] + ' ' + arCCinfo[6]);
                                $('#ccSecretaire').html('Secrétaire : ' + arCCinfo[9] + ' ' + arCCinfo[8]);
                                //$('#ccTerritoriesNber').html('Nombre de territoires : '+territoryCC.length);
                                //var lat = (arCCInfo[6]);
                                //var long = (arCCInfo[7]);


                                /*var dnfIcon = L.icon({
                                    iconUrl: 'assets/img/Logo_dnf.png',
                                    iconSize:     [25, 25], // size of the icon
                                });
                                marker = new L.marker([lat,long], {icon: dnfIcon});
                                map.addLayer(marker);*/

                            }).addTo(map);

                        }
                    }
                });
            }
        })



        // ************ POPUP CALENDAR ************************************************************

        const popup = document.getElementById('popup');
        const closebtn = document.getElementById('closebtn');
        const search = document.getElementById('calendarBtn');
        const message = document.getElementById('message');

        function showPopup() {
            popup.style.display = 'block';
            search.style.display = 'none';
        }

        function hidePopup() {
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

        window.addEventListener('click', (event) => {
            if (event.target === popup) {
                hidePopup();
            }

        });


        // *************** DATE SELECTION ***********************************************************
        var lRT = [];

        var cookieNber = "<?php echo $file_suffix; ?>";
        console.log(cookieNber);
        lRT = <?php echo json_encode($LRT); ?>;
        lRTUS = lRT[2]["cron_chasses"]["Infos_Date"];
        lRTEUR = dayjs(lRTUS, 'DD-MMM-YYYY HH:mm')
        lRTBE = lRTEUR.format('DD-MMM-YYYY HH:mm')

        document.getElementById("maj").innerHTML = "Dernière màj : " + lRTBE;

        $("#btonSearchDate").click(function() {
            dateValue = $('#datepicker').datepicker('getDate');
            formatDate = $.datepicker.formatDate("dd-mm-yy", dateValue);
            console.log(dateValue);
            if (dateValue === null) {
                document.getElementById("retour").innerHTML = "Veuillez sélectionner une date";
                retour.classList.add('active');
                message.classList.add('active');
                infoRetour.classList.remove('active');
                squareOpen.classList.remove('active');
                squareClose.classList.remove('active');
            }


            // *************** SETTINGS ***********************************************************



            //document.getElementById("retour").innerHTML = "";
            var huntedTerritories = [];
            var huntedTerritoriesList = [];

            // ************ SEARCH HUNTING DATES ************************************************************
            var cookieNber = "<?php echo $file_suffix; ?>";
            $.ajax({
                type: 'GET',
                url: "assets/inc/php/hunting_dates_search_by_date.php",
                data: "formatDate=" + formatDate,

                success: function(response) {
                    console.log(response);
                    resultat = JSON.parse(response);
                    if (resultat[0] == -14) {
                        document.getElementById("retour").innerHTML = "Pas de chasse pour cette date.";
                        retour.classList.add('active');
                        message.classList.add('active');
                        infoRetour.classList.remove('active');
                        squareOpen.classList.remove('active');
                        squareClose.classList.remove('active');
                    } else {
                        huntedTerritories = JSON.parse(response);
                        console.log(huntedTerritories)
                        huntedNber = (huntedTerritories[2].length);
                        console.log(huntedNber)


                        for (i = 0; i < huntedNber; i++) {
                            console.log(huntedTerritories[2][i]["FERMETURE"]);
                            territory = huntedTerritories[2][i]["DA_Numero"];
                            territoriesList.push(territory);
                            if (huntedTerritories[2][i]["FERMETURE"] == "O") {
                                territoriesClosed.push(territory)
                            } else {
                                territoriesOpened.push(territory)
                            }
                        }

                        console.log(territoriesClosed);
                        console.log(territoriesOpened);


                        var tab = []
                        var keys = []
                        if (huntedNber > 0) {

                            for (i = 0; i < huntedNber; i++) {
                                keys = Object.entries(huntedTerritories[2][i])
                                territoriesNbers.push(keys[2][1])
                            }

                        }
                        var territoriesNberAll = huntedTerritories
                        console.log(territoriesNberAll)
                        console.log(territoireValue)
                        territoriesNber = territoriesSelection(territoriesNberAll, territoireValue)

                        function territoriesSelection(territoriesNberAll, territoireValue) {
                            dnfTerritoriesNber = territoriesNberAll[2].length
                            console.log(dnfTerritoriesNber)
                            arhuntedTerritories = [];
                            for (i = 0; i < ((dnfTerritoriesNber)); i++) {
                                var dnfCode = huntedTerritories[2][i]["DA_Numero"].substring(0, 3);
                                console.log(i);
                                if (territoireValue == dnfCode) {
                                    console.log(territoireValue)
                                    console.log(dnfCode)
                                    arhuntedTerritories.push(huntedTerritories[2][i]["DA_Numero"])
                                    console.log(arhuntedTerritories)
                                }
                            }

                            huntedNber = (arhuntedTerritories.length);
                            console.log(huntedNber)
                        }

                        console.log(territoriesNber)
                        console.log(huntedNber)



                        territoriesNber = territoriesNbers.join(',');
                        console.log(territoriesNber)
                        var territoryNber = (territoriesNbers.length);
                        console.log(territoryNber)

                        const arTerNber = territoriesNber.split(/,/);
                        var territoriesNbersCC = [];
                        for (i = 0; i < territoryNber; i++) {
                            codeCC = arTerNber[i]
                            console.log(codeCC);
                            //code= codeCC.substr(0, 3)
                            //console.log(code);
                            if (codeCC == territoireValue) {
                                territoriesNbersCC.push(codeCC)
                            }
                        }

                        if (huntedNber > 0) {
                            document.getElementById("retour").innerHTML = huntedNber + " territoires chassés le " + formatDate;
                            retour.classList.add('active');
                            message.classList.add('active');
                            infoRetour.classList.add('active');
                            squareOpen.classList.add('active');
                            squareClose.classList.add('active');
                        }


                        var lyrhuntingterritories = createMultiJson(territoriesNber);

                        // ************ SEARCH HUNTING TERRITORIES ************************************************************

                        function createMultiJson(territoriesNber) {
                            $.ajax({
                                type: 'GET',
                                url: "assets/inc/php/createMultiJson_by_n.php",
                                data: {
                                    territoriesNber: territoriesNber
                                },

                                success: function(response) {
                                    console.log(resultat);
                                    if (resultat[0] == -14) {
                                        document.getElementById("retour").innerHTML = "Pas de chasse pour cette date.";
                                        retour.classList.add('active');
                                        message.classList.remove('active');
                                        infoRetour.classList.remove('active');
                                        squareOpen.classList.remove('active');
                                        squareClose.classList.remove('active');
                                    } else {
                                        console.log(lyrTerritories)
                                        if (lyrTerritories) {
                                            lyrTerritories.remove();
                                            map.removeLayer(lyrTerritories);
                                        }
                                        console.log(huntedNber)
                                        lyrTerritories = L.geoJSON.ajax('assets/datas/' + cookieNber + 'huntedTerritoryByDate.json', {
                                            style: styleTerritories,
                                            onEachFeature: processTerritories
                                        });
                                        console.log(lyrTerritories)

                                        function styleTerritories(json) {
                                            var att = json.properties;
                                            for (i = 0; i < huntedNber; i++) {
                                                console.log(att.Numero_Lot)
                                                console.log(dnfTerritoriesNber)
                                                for (j = 0; j < (dnfTerritoriesNber); j++) {
                                                    console.log(huntedTerritories[2][j]["DA_Numero"]);
                                                    if (att.Numero_Lot == huntedTerritories[2][j]["DA_Numero"]) {
                                                        console.log("coucou")
                                                        if (huntedTerritories[2][j]["FERMETURE"] == "O") {
                                                            console.log("coucou1")
                                                            return {
                                                                fillOpacity: 0.5,
                                                                weight: 4,
                                                                color: '#ef3d33'
                                                            };
                                                        } else {
                                                            return {
                                                                fillOpacity: 0.5,
                                                                weight: 4,
                                                                color: '#fdef49'
                                                            };
                                                        }
                                                    }
                                                }

                                            }
                                        }

                                        function processTerritories(json, lyr) {
                                            var att = json.properties;

                                            lyr.on('mouseover', function() {
                                                lyr.setStyle({
                                                    fillOpacity: 0.7
                                                })
                                                lyr.bindTooltip('<div class="custom-popup">' + att.Territories_name + 'br>' + att.Nomenclature);
                                            })
                                            lyr.on('mouseout', function() {
                                                lyr.setStyle({
                                                    fillOpacity: 0.3
                                                });
                                            })
                                        }

                                        lyrTerritories.on('data:loaded', function() {
                                            // map.fitBounds(lyrTerritories.getBounds().pad(0));
                                        }).addTo(map);
                                    }

                                }
                            })
                        }
                    }
                }
            });
        })
    })
</script>