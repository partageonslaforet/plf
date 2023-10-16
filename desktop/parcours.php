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

$LRT = PLF::Get_LastRunTime();

$List_Parcours = PLF::Get_Itineraires_List();
//var_dump($List_Parcours);

//$List_Parcours_info = PLF::Get_Itineraire_infos();


?>
<!DOCTYPE html>

<html>
<header>

    <title>Parcours</title>
    <html lang="fr">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- FICHIERS CSS -->
    <link rel="stylesheet" href="assets/css/parcours.css">
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
    <script src="assets/src/js/leaflet.js"></script>
    <script src="assets/src/js/L.Control.Zoomslider.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="assets/src/js/Control.MiniMap.js"></script>
    <script src="assets/src/js/leaflet-providers.js"></script>
    <script src="assets/src/js/L.Control.Zoomslider.js"></script>
    <script src="assets/src/js/L.Control.Locate.js"></script>
    <script src="assets/src/js/leaflet.rainviewer.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-ajax/2.1.0/leaflet.ajax.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="assets/src/js/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
    <script src="assets/src/js/gpx.js"></script>
    <script src="https://unpkg.com/@raruto/leaflet-elevation/dist/leaflet-elevation.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.9/dayjs.min.js"></script>

</header>

<body>
    <div id="sidebar">
        <h1 class="sidebar-header"></h1>
        <!--<button id="sidebarclosebtn">&times;</button>
            <button id="sidebarBtn"><a id="sidebarCalendar"><i class="fa fa-calendar" title="CLIQUEZ"></i></a></button>-->
        <div id="parcoursSearch">
            <div id="parcoursSearchLabel">
                <div>PARCOURS BALISES</div>
            </div>
            <div id="findParcours">
                <form>
                    <!--<label for="selectType" id="selectType">Sélectionner une Localité ou une Commune</label>-->
                    <select id="selectCityType">
                        <!--<option value="choix">Votre choix</option>-->
                        <option value="arLocaliteName">Localité</option>
                        <option value="arCommuneName">Commune</option>
                    </select>
                    <form>
                        <div id="city">
                            <label for="selectCity" id="selectCity">Sélectionnez un lieu</label>
                            <div id="selectCityForm">
                                <select type="search" id="txtFindCityName" placeholder="Localité"></select>
                                <button id="btnFindCityName" class="searchItems"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                        <div id="parcours">
                            <label for="selectParcours" id="selectParcours">Sélectionnez un parcours</label>
                            <div id="selectParcoursForm">
                                <select type="search" id="txtFindParcoursName" placeholder="Parcours"></select>
                                <button id="btnFindParcoursName" class="searchItems"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
            </div>
            <div id="parcoursInfo"></div>
            <div id="parcoursNom"></div>
            <div id="parcoursInfoDetails">
                <div id="parcoursOrganisme"></div>
                <div id="parcoursLocalite"></div>
                <div id="parcoursCommune"></div>
                <div id="parcoursDistance"></div>
                <div id="parcoursD"></div>
                <div id="parcoursSignal"></div>
                <div id="parcoursType"></div>
                <div id="parcoursTrace"></div>
                <div id="messageErreur"></div>
            </div>
            <div>
                <button id="btnRetour" onclick="window.location='../index.php';">RETOUR</button>
            </div>
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
            <div id="controlButtons" class="list">
                <button id="btonSearchDate"><i class="fa fa-search fa-2x"></i></button>
            </div>
        </div>
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

    <body>
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
    var territoriesList = [];
    var arRouteNber = [];
    var arCityName = [];
    var arSelectedCityList = [];
    var arselectedParcoursList = [];
    var arCityList = [];
    var listArrayN = [];
    var territoriesInfo = [];
    var lyrTerritories;
    var map;
    var territoireValue;
    var huntedTerritories = [];
    var lyrRoute;
    var arRouteId = [];
    var routeNbre;
    var routeName;
    var selectMenu;
    var traceGPX;

    $(document).ready(function() {
        const headers = new Headers();
        headers.append('Access-Control-Allow-Origin', "*");

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

        //ctlScale = L.control.scale({position:'bottomleft', imperial:false, maxWidth:200}).addTo(map);
        //ctlZoomslider = L.control.zoomslider({position:'topleft'}).addTo(map);
        //ctlMeasure = L.control.polylineMeasure({position:'topleft'}).addTo(map);  

        // ************ LOCALISATION FUNCTION *********************************************************

        L.control.locate({
            position: "bottomright",
            flyTo: true,
            strings: {
                title: "LOCALISEZ MOI",
                initialZoomLevel: 15,
                returnToPrevBounds: true
            }
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



        // ************ LIST OF ROUTE NAME ************************************************************

        listByRouteDB = <?php echo json_encode($List_Parcours); ?>;

        var listByRoute = Object.values(listByRouteDB[2]);
        var routeNbre = listByRoute.length;

        var selectedCategory;
        var arLocaliteName = [];
        var arCommuneName = [];

        // ************ Chargement des tables localité & Commune ************************************************************
        for (i = 0; i < routeNbre; i++) {
            if (!arLocaliteName.includes(listByRoute[i]["localite"])) {
                arLocaliteName.push(listByRoute[i]["localite"]);
            }
            if (!arCommuneName.includes(listByRoute[i]["commune"])) {
                arCommuneName.push(listByRoute[i]["commune"]);
            }
        }
        arLocaliteName.sort((a, b) => a.localeCompare(b, 'fr'));
        arCommuneName.sort((a, b) => a.localeCompare(b, 'fr'));

        const tableDatas = {
            arLocaliteName,
            arCommuneName
        }

        menuSelectionCity = selectionMenu(tableDatas)

        // ************ Fonction SelectMenu Localité/Commune ************************************************************  


        function selectionMenu(initialChoices) {

            selectOption = $("#txtFindCityName").selectmenu();

            $("#selectCityType").on('change', function() {
                selectedCategory = $(this).val();
                //console.log(selectedCategory)
                const choices = initialChoices[selectedCategory];
                //console.log(choices);
                selectOption.empty();

                if (choices) {
                    choices.forEach(function(choice) {
                        selectOption.append($("<option>", {
                            value: choice,
                            text: choice
                        }));
                    });
                    selectOption.selectmenu("refresh");
                }

            });
            $("#selectCityType").trigger("change");
        }

        //*************************** Parcours Selection *********************************/
        $("#btnFindCityName").click(function(e) {
            e.preventDefault()
            //$("#txtFindCityName-button").on('focusout', function() {
            selectedCity = $("#txtFindCityName").val()
            //console.log(selectedCity)
            //console.log(selectedCategory)
            arSelectedCityList = [];

            switch (selectedCategory) {
                case "arLocaliteName":
                    for (i = 0; i < listByRoute.length; i++) {
                        //console.log(selectedCity)
                        if (selectedCity == listByRoute[i]["localite"]) {
                            arSelectedCityList.push(listByRoute[i]["localite"]);
                            arSelectedCityList.push(listByRoute[i]['itineraire_id']);
                            arSelectedCityList.push(listByRoute[i]['nom']);
                            //console.log(arSelectedCityList)
                        }
                    }
                case "arCommuneName":
                    for (i = 0; i < listByRoute.length; i++) {
                        //console.log(selectedCity)
                        if (selectedCity == listByRoute[i]["commune"]) {
                            arSelectedCityList.push(listByRoute[i]['commune'])
                            arSelectedCityList.push(listByRoute[i]['itineraire_id'])
                            arSelectedCityList.push(listByRoute[i]['nom'])

                        }
                    }
            }
            arselectedParcoursList = [];
            for (i = 2; i < arSelectedCityList.length; i++) {
                arselectedParcoursList.push(arSelectedCityList[i])
                i = i + 2
            }

            // ************ Fonction SelectMenu Parcours ************************************************************  


            selectOption1 = $("#txtFindParcoursName").selectmenu();
            //console.log(arselectedParcoursList)
            var choices1 = arselectedParcoursList;
            //console.log(choices1);
            selectOption1.empty();

            if (choices1) {
                choices1.forEach(function(choice1) {
                    selectOption1.append($("<option>", {
                        value: choice1,
                        text: choice1
                    }));
                });
                selectOption1.selectmenu("refresh");
            }

        });

        $("#txtFindCityName").trigger("focusout");


        // ************ SEARCH ROUTE MAP ************************************************************

        document.getElementById("txtFindParcoursName").addEventListener("focus", focusIn);

        function focusIn() {
            document.getElementById("txtFindParcoursName").value = "";
        }

        document.getElementById("btnFindParcoursName").addEventListener("focusout", focusOut);

        function focusOut() {

        }

        $("#btnFindParcoursName").click(function(e) {
            e.preventDefault()
            //console.log(lyrRoute);
            if (lyrRoute) {
                console.log(lyrRoute);
                lyrRoute.remove();
                map.removeLayer(lyrRoute);
            }
            routeName = $("#txtFindParcoursName").val().toLowerCase();
            //console.log(routeName)

            for (j = 0; j < (listByRoute.length); j++) {

                routeCheck = listByRoute[j]["nom"].toLowerCase()
                //console.log(routeCheck)
                //console.log(routeName)
                if (routeName == routeCheck) {

                    routeValue = listByRoute[j]["itineraire_id"]
                    break;
                }
                messageErreur.classList.remove('active');
                parcoursNom.classList.add('active');

                parcoursInfo.classList.add('active');
                parcoursInfoDetails.classList.add('active');
            }


            // ************ SEARCH ROUTE INFO ************************************************************

            var cookieNber = "<?php echo $file_suffix; ?>";

            $.ajax({
                type: 'GET',
                url: "assets/inc/php/searchRouteInfo.php",
                data: "routeValue=" + routeValue,

                success: function(response) {
                    resultat = JSON.parse(response);
                    //console.log(resultat);
                    var argpx = resultat[0]["gpx_url"]
                    //console.log(argpx);
                    messageErreur.classList.remove('active');
                    parcoursNom.classList.add('active');

                    parcoursInfo.classList.add('active');
                    parcoursInfoDetails.classList.add('active');

                    $('#parcoursInfo').html('<center>PARCOURS<center>');
                    $('#parcoursNom').html(resultat[0]["nom"]);
                    $('#parcoursOrganisme').html('Organisation : ' + resultat[0]["organisme"]);
                    $('#parcoursLocalite').html('Localité : ' + resultat[0]["localite"]);
                    $('#parcoursCommune').html('Commune : ' + resultat[0]["commune"]);
                    $('#parcoursDistance').html('Distance : ' + resultat[0]["distance"] + ' km');
                    //$('#parcoursD').html(resultat[0]["distance"]);
                    $('#parcoursSignal').html('Signalisation : ' + resultat[0]["signaletique"]);
                    $('#parcoursType').html('Type : ' + resultat[0]["typecirc"]);

                    if (resultat[0]["gpx_url"] == "") {
                        $('#parcoursTrace').html('Pas de trace disponible');

                    }

                    const headers = new Headers();
                    headers.append('Access-Control-Allow-Origin', argpx);
                    headers.append('Content-Type', 'application/json');
                    headers.append('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,PATCH,OPTIONS');

                    console.log(traceGPX)

                    if (traceGPX) {
                        map.removeLayer(traceGPX);
                    }

                    var fetchResult = fetch(argpx);

                    traceGPX = new L.GPX(argpx, {
                        async: true,
                        marker_options: {
                            startIconUrl: 'assets/img/pin-icon-start.png',
                            endIconUrl: 'assets/img/pin-icon-end.png',
                            shadowUrl: 'assets/img/pin-shadow.png'
                        }
                    }).on('loaded', function(e) {
                        map.fitBounds(e.target.getBounds().pad(1));
                    }).addTo(map);
                }

            })


            // *************** DATE SELECTION ***********************************************************
            // ************ SETTING POPUP CALENDAR ************************************************************




            // ************ POPUP CALENDAR ************************************************************

            const popup = document.getElementById('popup');
            const closebtn = document.getElementById('closebtn');
            const search = document.getElementById('calendarBtn');
            const message = document.getElementById('message');

            function showPopup() {
                popup.style.display = 'block';
                search.style.display = 'none';
                message.style.display = 'block';
            }

            function hidePopup() {
                popup.style.display = 'none';
                message.style.display = 'none';
                search.style.display = 'block';
                message.classList.remove('active');
                retour.classList.remove('active');
                infoRetour.classList.remove('active');
                squareOpen.classList.remove('active');
                squareClose.classList.remove('active');
            }


            calendar.addEventListener('click', showPopup);

            closebtn.addEventListener('click', hidePopup);

            indow.addEventListener('click', (event) => {
                if (event.target === popup) {
                    hidePopup();
                }

            });

            // *************** DATE SELECTION ***********************************************************
            var lRT = [];

            var cookieNber = "<?php echo $file_suffix; ?>";
            //console.log(cookieNber);
            lRT = <?php echo json_encode($LRT); ?>;
            //console.log(lRT);
            lRTUS = lRT[2]["cron_chasses"]["Infos_Date"];
            lRTEUR = dayjs(lRTUS, 'DD-MMM-YYYY HH:mm')
            lRTBE = lRTEUR.format('DD-MMM-YYYY HH:mm')
            //console.log(lRTBE)
            document.getElementById("maj").innerHTML = "Dernière màj : " + lRTBE;

            $("#btonSearchDate").click(function(e) {
                e.preventDefault()
                dateValue = $('#datepicker').datepicker('getDate');
                formatDate = $.datepicker.formatDate("dd-mm-yy", dateValue);
                if (dateValue === null) {
                    document.getElementById("retour").innerHTML = "Veuillez sélectionner une date";
                    retour.classList.add('active');
                    message.classList.add('active');
                    infoRetour.classList.remove('active');
                    squareOpen.classList.remove('active');
                    squareClose.classList.remove('active');
                } else {

                    // *************** SETTINGS ***********************************************************

                    document.getElementById("retour").innerHTML = "";
                    var huntedTerritories = [];
                    var huntedTerritoriesList = [];
                    var huntedNber = [];
                    var territoriesNbers = [];
                    var territoriesClosed = [];
                    var territoriesOpened = [];
                    var territoriesList = [];

                    if (lyrTerritories) {
                        lyrTerritories.remove();
                        map.removeLayer(lyrTerritories);
                    }

                    // ************ SEARCH HUNTING DATES ************************************************************

                    $.ajax({
                        type: 'GET',
                        url: "assets/inc/php/hunting_dates_search_by_date.php",
                        data: "formatDate=" + formatDate,

                        success: function(response) {
                            //console.log(response);
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
                                //console.log(huntedTerritories)
                                huntedNber = (huntedTerritories[2].length);
                                //console.log(huntedNber)

                                for (i = 0; i < huntedNber; i++) {
                                    //console.log(huntedTerritories[2][i]["FERMETURE"]);
                                    territory = huntedTerritories[2][i]["DA_Numero"];
                                    territoriesList.push(territory);
                                    if (huntedTerritories[2][i]["FERMETURE"] == "O") {
                                        territoriesClosed.push(territory)
                                    } else {
                                        territoriesOpened.push(territory)
                                    }
                                }
                                //console.log(territoriesClosed);
                                //console.log(territoriesOpened);



                                var territoriesNber = territoriesList.join(',');
                                //console.log(territoriesNber)

                                if (huntedNber > 0) {
                                    document.getElementById("retour").innerHTML = huntedNber + " territoires chassés le " + formatDate;
                                    retour.classList.add('active');
                                    squareOpen.classList.add('active');
                                    squareClose.classList.add('active');
                                    infoRetour.classList.add('active');
                                    message.classList.add('active');
                                }

                                var lyrhuntingterritoriesClosed = createMultiJson(territoriesNber);
                                //var lyrhuntingterritoriesOpened = createMultiJson(territoriesNber);


                                // ************ SEARCH HUNTING TERRITORIES ************************************************************

                                function createMultiJson(territoriesNber) {
                                    $.ajax({
                                        type: 'GET',
                                        url: "assets/inc/php/createMultiJson_by_n.php",
                                        data: {
                                            territoriesNber: territoriesNber
                                        },

                                        success: function(response) {
                                            //console.log(response);

                                            if (resultat[0] == -14) {
                                                document.getElementById("retour").innerHTML = "Pas de chasse pour cette date.";
                                                retour.classList.add('active');
                                                message.classList.remove('active');
                                            } else {
                                                //console.log(lyrTerritories)
                                                if (lyrTerritories) {
                                                    lyrTerritories.remove();
                                                    map.removeLayer(lyrTerritories);
                                                }
                                                //console.log(lyrTerritories)
                                                lyrTerritories = L.geoJSON.ajax('assets/datas/' + cookieNber + 'huntedTerritoryByDate.json', {
                                                    style: styleTerritories,
                                                    onEachFeature: processTerritories
                                                });

                                                function styleTerritories(json) {
                                                    var att = json.properties;
                                                    for (i = 0; i < huntedNber; i++) {
                                                        //console.log(huntedTerritories[2][i]["DA_Numero"]);

                                                        if (att.Numero_Lot == huntedTerritories[2][i]["DA_Numero"]) {
                                                            //console.log(att.Numero_Lot);

                                                            if (huntedTerritories[2][i]["FERMETURE"] == "O") {

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
                                                        //console.log("erreur")
                                                    }
                                                }

                                                function processTerritories(json, lyr) {
                                                    var att = json.properties;
                                                    lyr.on('mouseover', function() {
                                                        lyr.setStyle({
                                                            fillOpacity: 0.7
                                                        })
                                                        lyr.bindTooltip('<h3 style="color:#2c3e50"><center>N° de Territoire: <br>' + att.Numero_Lot + '</h3>');
                                                    })
                                                    lyr.on('mouseout', function() {
                                                        lyr.setStyle({
                                                            fillOpacity: 0.3
                                                        });
                                                    })
                                                }

                                                lyrTerritories.on('data:loaded', function() {
                                                    //map.fitBounds(lyrTerritories.getBounds().pad(0));
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
    });
</script>