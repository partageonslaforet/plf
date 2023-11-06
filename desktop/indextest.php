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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Calendrier des chasses</title>
    <html lang="fr">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!------------------------------- CSS --------------------------------------->
    <!--<link rel="stylesheet" href="assets/css/style.css">-->

    <link rel="stylesheet" href="assets/css/calendarNew.css">
    <link rel="stylesheet" href="assets/src/css/leaflet.css">
    <link rel="stylesheet" href="assets/src/css/L.Control.Zoomslider.css">
    <link rel="stylesheet" href="assets/src/css/Control.MiniMap.css">
    <link rel="stylesheet" href="assets/src/css/gh-fork-ribbon.css">
    <link rel="stylesheet" href="assets/src/css/jquery-ui.css">
    <link rel="stylesheet" href="assets/src/css/L.Control.Locate.css">
    <link rel="stylesheet" href="assets/src/css/leaflet.rainviewer.css">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/src/css/GpPluginLeaflet.css" />
    <link rel="stylesheet" href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/@raruto/leaflet-elevation/dist/leaflet-elevation.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900">

    <!----------------------------- JS ------------------------------------------>

    <script src="assets/src/js/leaflet.js"></script>
    <script src="assets/src/js/jquery-3.7.1.js"></script>
    <script src="assets/src/js/jquery-ui.js"></script>
    <script src="assets/inc/js/bootstrap.bundle.min.js"></script>
    <script src="assets/src/js/L.Control.Zoomslider.js"></script>
    <script src="assets/src/js/Control.MiniMap.js"></script>
    <script src="assets/src/js/leaflet-providers.js"></script>
    <script src="assets/inc/js/bootstrap.bundle.min.js"></script>
    <script src="assets/src/js/L.Control.Zoomslider.js"></script>
    <script src="assets/src/js/Control.MiniMap.js"></script>
    <script src="assets/src/js/leaflet-providers.js"></script>
    <script src="assets/src/js/L.Control.Locate.js"></script>
    <script src="assets/src/js/Leaflet.ajax.min.js"></script>
    <script src="assets/src/js/leaflet.rainviewer.js"></script>
    <script src="assets/src/js/GpPluginLeaflet.js"></script>
    <script src="assets/src/js/gpx.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.9/dayjs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.7.5/proj4.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4leaflet/1.0.2/proj4leaflet.min.js"></script>



</head>
<!----------------------------- BODY ------------------------------------------>

<body>
    <!-- ******************   SIDEBAR  ************************************* -->
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
                            <i class='fa fa-info' data-bs-target="#SPWModal" data-bs-toggle="modal" title="INFORMATIONS GENERALES"></i>
                            <span class="text nav-text">Informations</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#parcours" data-bs-toggle="offcanvas" role="button" aria-controls="parcours">
                            <i class='fa fa-hiking'></i>
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
                            <i class='fa fa-tree title'></i>
                            <span class="text nav-text">DNF</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#">
                            <i class='fa-solid fa-bullseye'></i>
                            <span class="text nav-text">Conseils Cynégétiques</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#">
                            <i class='fa fa-envelope' data-bs-target="#emailContact" data-bs-toggle="modal" title="CONTACT"></i>
                            <span class="text nav-text">Contact</span>
                        </a>
                    </li>

                </ul>
            </div>

            <div class="bottom-content">
                <li class="nav-link">
                    <a href="#">
                        <i class='fa-solid fa-gears'></i>
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

    <!-- *************************   MAP  *********************************** -->

    <section id="Container">
        <div id="map"></div>
        <div id="calendarBtn">
            <a id="calendar"><i class="fa fa-calendar fa-2x text-container d-flex justify-content-center " data-bs-toggle="modal" data-bs-target="#calendarModal" title="CALENDRIER DES BATTUES"></i></a>
        </div>

        <div class="spinner-border m-5 text-danger" id="spinner" role="status">
            <span class="sr-only">Chargement...</span>
        </div>
    </section>

    <!-- ***************************** CALENDAR POPUP  ************************************* -->

    <div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
        <div class="modal-dialog calendarWidth" role="document">
            <div class="modal-content w-auto calModal">
                <div class="modal-header">
                    <div id="maj"></div>
                    <h5 class="modal-title text-uppercase fw-bold text-danger mx-auto d-flex justify-content-center" id="calendarModalLabel"></h5>
                    <button type="button" id="btn-close" class="btn-close text-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="popup-content">
                        <div class="date-calendar align-self-center">
                            <p><input type="text" id="datepicker" class="align-self-center" placeholder="Cliquez pour choisir une date"></p>
                        </div>
                        <div id="search" class='list-item'>
                            <button type="button" class="btn btn-secondary " id="btonSearchDate"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
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
    </div>

    <!-- ************************** INFORMATION POPUP ****************************** -->

    <div class="modal fade" id="SPWModal" tabindex="-1" role="dialog" aria-labelledby="messageSPWModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase display-6 fw-bold text-danger mx-auto d-flex justify-content-center" id="infoGenerale">INFORMATION GENERALE</h5>
                    <button type="button" id="btn-close" class="btn-close text-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body w-100 text-justify px-4">
                    <div class="popup-content ">
                        <div class='text-center mb-2 fw-bold text-secondary' id="brochure">
                            <a class="brochure" href="../assets/img/brochure_partageons_la_foret.pdf" download="brochure_partageons_la_foret.pdf">Télécharger la brochure "Comment entrer en foret en la respectant"</a>
                        </div>
                        <h6>

                            Bienvenue sur l'application permettant de localiser les territoires de chasse ayant obtenu une autorisation de fermeture des chemins en forêt les jours où des actions de chasse sont organisées <a class="text-danger affiche" href="../assets/img/Affiche Battues.jpg" id="affichesR" target="_blank">(affiches rouges).</a></br>
                        </h6>
                        <h6>
                            Elle permet également de localiser les territoires sur lesquels des titulaires du droit de chasse ont déclaré des actions de chasse, sans pour autant solliciter une autorisation de fermeture des chemins <a class="text-warning danger affiche" href="../assets/img/Affiche Annonce.jpg" id="affichesJ" target="_blank">(affiches jaunes).</a></br>
                        </h6>
                        <h6>
                            Les informations communiquées sur ce site visent à améliorer la transparence sur les activités de chasse et n’ont qu’une valeur informative. Seules les affiches d'interdiction de circulation apposées aux entrées des bois ont valeur légale.</br>
                        </h6>
                        <h6>
                            En effet, plusieurs points d’attention sont à prendre en considération :</br>
                            <ul>
                                <li>
                                    <h6>Les limites des territoires de chasse sur cette carte interactive sont celles qui ont été communiquées à l'administration par les conseils cynégétiques ou les titulaires de droit de chasse eux-mêmes. Elles ne sont pas toutes nécessairement d’une grande précision et parfaitement à jour. En conséquence et à titre d’exemple, un chemin/sentier en périphérie du territoire peut également être fermé alors qu’il apparaît en dehors du périmètre du territoire sur la carte. Cette donnée sera chaque année améliorée dans sa qualité. </h6></br>
                                </li>
                                <li>
                                    <h6>Précisons également que les titulaires n'ont pas nécessairement le droit de chasse sur l'entièreté de la surface comprise à l'intérieur de ces limites (exemple : les zones habitées).</h6></br>
                                </li>
                                <li>
                                    <h6>Dans l'état actuel de la réglementation, des actions de chasse peuvent être organisées sans que le titulaire du droit de chasse en informe l’administration ou sans qu’il ne demande la fermeture des chemins. L’application vous présente les territoires chassés (avec ou sans fermeture des chemins) dont l’administration a connaissance.</h6></br>
                                </li>
                                <li>
                                    <h6>Les informations communiquées sur ce site sont normalement mises à jour quotidiennement.</h6></br>
                                </li>
                            </ul>
                        </h6>
                        <h6>
                            La responsabilité du SPW ne peut être invoquée du fait que les informations communiquées sur ce site seraient inexactes en ce que ces dernières ont une valeur purement informative.</br>
                        </h6>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <div id="search" class='list-item'>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btonClose">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- **************************** CONTACT FORM POPUP ******************************** -->

    <div class="modal fade" id="emailContact" tabindex="-1" aria-labelledby="#emailContactLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content w-auto  ">
                <div class="modal-header d-flex justify-content-center modal-dialog-centered">
                    <h5 class="modal-title text-uppercase fw-bold text-danger display-6 mx-auto d-flex justify-content-center" id="emailContact">Contactez-nous</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" novalidate>
                        <!-- Name input -->
                        <div class="form-floating border border-1 border text-primary border-primary rounded mb-1">
                            <input type="text" id="name" class="form-control" required>
                            <label for="name" class="form-label">Votre nom</label>
                            <div class="valid-feedback">
                                C'est parfait !
                            </div>
                            <div class="invalid-feedback">
                                Format incorrect !
                            </div>
                        </div>

                        <!-- Email input -->
                        <div class="form-outline bs-4 form-floating border text-primary border-1 border border-primary rounded mb-1">
                            <input type="email" id="email" class="form-control" required>
                            <label class="form-label" for="email">Votre adresse Email</label>
                        </div>

                        <!-- textarea input -->
                        <div class="form-outline bs-4 form-floating border text-primary border-1 border border-primary rounded mb-1">
                            <textarea id="textarea" rows="4" class="form-control" required></textarea>
                            <label class="form-label" for="textarea">Votre message</label>
                        </div>

                        <!-- Checkbox -->
                        <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input me-2" type="checkbox" value="" id="checkbox4" checked />
                            <label class="form-check-label" for="checkbox4">
                                Envoyez-moi une copie de ce messsage
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer p-2 d-flex justify-content-center">
                    <!-- Submit button -->
                    <button type="submit" class="btn btn-secondary">
                        <i class="fa-solid fa-paper-plane"></i>
                        Envoyer
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- ************************** CANVAS PARCOURS ************************************* -->

    <div style='z-index:2001; width:20%;' class="offcanvas offcanvas-start" tabindex="-1" id="parcours" aria-labelledby="parcoursLabel">
        <div class="offcanvas-header">
            <div class="offcanvas-title text-primary fw-bold fs-4 mx-auto d-flex justify-content-center" id="parcoursLabel">PARCOURS BALISES</div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <?php require_once "parcoursNew.php"; ?>
        <div class="offcanvas-body">
            <div class="container w-auto mx-auto">
                <div class="row">
                    <form class="d-flex justify-content-center mb-5">
                        <div class="form-check-inline">
                            <input class="form-check-input border border-primary" type="checkbox" value="typeMarche" id="typeMarche">
                            <label class="form-check-label" for="flexCheckChecked">
                                Marche
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <input class="form-check-input border border-primary" type="checkbox" value="typeVTT" id="typeVTT" checked>
                            <label class="form-check-label" for="flexCheckDefault">
                                VTT
                            </label>
                        </div>
                    </form>
                </div>
            </div>
            <div class="container w-auto mx-auto">
                <div class="row">
                    <label class="d-flex justify-content-center fw-bold text-primary" for="selectCityLabel" id="selectCityLabel">Commune ou Localité</label>
                    <div class="row g-1 mb-5">
                        <select id="selectCityType" class="form-select mx-auto text-primary border border-primary" style="width: 100%;" tabindex="-1" aria-hidden="true">>
                            <option value="arLocaliteName">Localité</option>
                            <option value="arCommuneName">Commune</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="city">
                <div id="selectCityForm" class="container w-100 mx-auto">
                    <label class="d-flex justify-content-center fw-bold text-primary" for="selectCity" id="selectCity">Sélectionnez un lieu</label>
                    <div class="row g-1 mb-5">
                        <div class="col-lg-9 d-flex align-items-center">
                            <select id="txtFindCityName" class="form-select mx-auto fs-5 text-primary" style="width: 100%; z-index:2100;" placeholder="Localité"></select>
                        </div>
                        <div class="col-lg-3 d-flex align-items-center">
                            <button id="btnFindCityName" class="searchItems btn btn-secondary" style="width: 100%;"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="parcours">
                <div id="selectParcoursForm" class="container w-100 mx-auto">
                    <label class="d-flex justify-content-center fw-bold text-primary" for="selectParcours" id="selectParcours">Sélectionnez un parcours</label>
                    <div class="row g-1 mb-5">
                        <div class="col-lg-9 d-flex align-items-center">
                            <select id="txtFindParcoursName" class="form-select mx-auto fs-5 text-primary border border-primary" style="width: 100%;z-index:2100;" placeholder="Parcours"></select>
                        </div>
                        <div class="col-lg-3 d-flex align-items-center">
                            <button id="btnFindParcoursName" class="searchItems btn btn-secondary " style="width: 100%;"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div id="parcoursInfo" class=" my-3 fs-4 fw-bold text-primary mx-auto d-flex justify-content-center"></div>
                <div id="parcoursNom" class="fs-4 fw-bold text-secondary mx-auto d-flex justify-content-center"></div>
                <div id="parcoursInfoDetails" class="fs-6 text-primary mx-auto">
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
            </div>

        </div>-
    </div>

</body>

</html>


<!----------------------------- SCRIPT JS ------------------------------------------>

<script src="assets/inc/js/main.js"></script>
<!--<script src="assets/inc/js/parcours.js"></script>-->
<script>
    // ************************** GLOBAL VARIABLE *************************************
    let listTerritories = [];
    let listHuntingDates = [];
    let huntingDates = [];
    let territoriesList = [];
    let arTerritoriesName = [];
    let territoriesNbers = [];
    let listArrayN = [];
    let territoriesInfo = [];
    let huntedTerritoriesListNber = [];
    let huntedTerritoriesList = [];
    let huntedNber = [];
    let lyrTerritories;
    let map;
    let territoireValue;
    let dateValue;
    let formatDate;

    /*window.addEventListener("load", function() {
        // Masquer le spinner
        var spinner = document.getElementById("spinner");
        spinner.style.display = "none";
    window.onload = function() {
        Gp.Services.getConfig({
            apiKey: 'fm1imiky2s48ngh0mxqxtdz9',
            onSucess: function(response) {*/
    document.addEventListener("DOMContentLoaded", function() {
        let spinner = document.getElementById("spinner");
        spinner.style.display = "block"; // Show the spinner
        // You can add more loading logic here.
    });

    // Hide the spinner when the page is fully loaded
    window.addEventListener("load", function() {
        let spinner = document.getElementById("spinner");
        spinner.style.display = "none"; // Hide the spinner
        // You can add additional logic for when the page is fully loaded.
    });

    $(document).ready(function() {

        // ********************* INITIALIZATION DATE PICKER ******************************************** 

        $(function() {
            $("#datepicker").datepicker({
                dateFormat: "dd-mm-yy",
                dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
                monthNames: ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "December"],
                buttonImageOnly: true
            });
            var todayDate = new Date()
            //console.log(todayDate)
            $('#datepicker').datepicker('setDate', todayDate);

            var todayDate = new Date()
            formatDate = $.datepicker.formatDate("dd-mm-yy", todayDate);
            var x = findTerritories(formatDate)

            var modal = document.getElementById('calendarModal');

            var modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();

        });



        // ********************* INITIALIZATION DAY MAJ *****************************************************

        var lRT = [];
        var cookieNber = "<?php echo $file_suffix; ?>";
        lRT = <?php echo json_encode($LRT); ?>;
        lRTUS = lRT[2]["cron_chasses"]["Infos_Date"];
        lRTEUR = dayjs(lRTUS, 'DD-MMM-YYYY HH:mm')
        lRTBE = lRTEUR.format('DD-MMM-YYYY HH:mm')

        document.getElementById("maj").innerHTML = "Dernière màj : " + lRTBE;

        // ********************** MAP INITIALIZATION ***********************************************************

        //proj4.defs("EPSG:31370", "+proj=lcc +lat_1=51.16666723333333 +lat_2=49.8333339 +lat_0=90 +lon_0=4.367486666666666 +x_0=150000.013 +y_0=5400088.438 +ellps=intl +units=m +no_defs");

        /*var crs = new L.Proj.CRS(
                'EPSG:31370',
                '+proj=+proj=lcc +lat_0=90 +lon_0=4.36748666666667 +lat_1=501.1666672333333 +lat_2=49.8333339 +x_0=150000.013 +y_0=5400088.438 +ellps=intl +towgs84=-106.8686,52.2978,-103.7239,-0.3366,0.457,-1.8422,-1.2747 +units=m +no_defs +type=crslcc +lat_1=51.16666723333334 +lat_2=49.8333339 +lat_0=90 +lon_0=4.367486666666666 +x_0=150000.013 +y_0=5400088.438 +ellps=intl +towgs84=106.869,-52.2978,103.724,-0.33657,0.456955,-1.84218,1 +units=m +no_defs +type=crs', {
                    //resolutions: [8192, 4096, 2048, 1024, 512],
                    //origin: [0, 0],
                    //bounds: L.bounds([0, 0], [8192, 8192])
                }),*/

        map = L.map('map', {
            //crs: crs,
            zoomControl: false,
        }).setView([49.567574, 5.533507], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);


        //ctlScale = L.control.scale({position:'bottomleft', imperial:false, maxWidth:200}).addTo(map);
        //ctlZoomslider = L.control.zoomslider({position:'topleft'}).addTo(map);
        //ctlMeasure = L.control.polylineMeasure({position:'topleft'}).addTo(map);  

        // ********************** LAYERS INITIALIZATION *******************************************************

        var lyrOSM = L.tileLayer.provider('OpenStreetMap.France');
        var lyrmagnifiedTiles = L.tileLayer.provider('OpenStreetMap.France');
        var lyrCyclo = L.tileLayer.provider('CyclOSM');
        var lyrEsri_WorldImagery = L.tileLayer.provider('Esri.WorldImagery');
        var lyrOpenTopoMap = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
        });
        var osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

        //var lyrlambert = L.geoJSON.ajax('assets/datas/Trotti.gpx');

        var baseLayers = {
            "osm": lyrOSM,
            "Satellite": lyrEsri_WorldImagery,
            "Altitude": lyrOpenTopoMap,
            "Cyclo": lyrCyclo
        };

        var overlays = {

        };

        L.control.layers(baseLayers, overlays).addTo(map);

        // **************************** SET LOCATION *************************************************************

        L.control.locate({
            position: "bottomright",
            flyTo: true,
            strings: {
                title: "LOCALISEZ MOI",
                initialZoomLevel: 15,
                returnToPrevBounds: true
            }
        }).addTo(map);


        // ************************** MINIMAP INITIALIZATION *******************************************************

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

        // **************************** LEAFLET METEO *****************************************************************

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

        // **************************** DATE SELECTION *****************************************************************


        $("#btonSearchDate").click(function() {

            if (lyrTerritories) {
                lyrTerritories.remove();
                map.removeLayer(lyrTerritories);
            }

            dateValue = $('#datepicker').datepicker('getDate');
            formatDate = $.datepicker.formatDate("dd-mm-yy", dateValue);
            //console.log(dateValue);
            if (dateValue === null) {
                document.getElementById("retour").innerHTML = "Veuillez sélectionner une date";
                retour.classList.add('active');
                message.classList.add('active');
                infoRetour.classList.remove('active');
                squareOpen.classList.remove('active');
                squareClose.classList.remove('active');
            } else {
                var x = findTerritories(dateValue)
            }

        });

        // ******************************** GENERAL FUNCTIONS ***********************************************************

        function findTerritories(dateValue) {

            // ******************************** INITIALISATION ***********************************************************

            document.getElementById("retour").innerHTML = "";
            let huntedTerritories = [];
            let huntedTerritoriesList = [];
            let huntedNber = [];
            let territoriesNbers = [];
            let territoriesClosed = [];
            let territoriesOpened = [];
            let territoriesList = [];

            // ************************** SEARCH HUNTING DATES ************************************************************

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
                            // console.log(huntedTerritories[2][i]["FERMETURE"]);
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
                            message.classList.add('active');
                            infoRetour.classList.add('active');
                            squareOpen.classList.add('active');
                            squareClose.classList.add('active');
                        }

                        var lyrhuntingterritoriesClosed = createMultiJson(territoriesNber);
                        //var lyrhuntingterritoriesOpened = createMultiJson(territoriesNber);

                        // ************************ SEARCH HUNTING TERRITORIES ************************************************************

                        function createMultiJson(territoriesNber) {
                            $.ajax({
                                type: 'GET',
                                url: "assets/inc/php/createMultiJson_by_n.php",
                                data: {
                                    territoriesNber: territoriesNber
                                },

                                success: function(response) {
                                    console.log(response);

                                    if (resultat[0] == -14) {
                                        document.getElementById("retour").innerHTML = "Pas de chasse pour cette date.";
                                        retour.classList.add('active');
                                        message.classList.remove('active');
                                        infoRetour.classList.remove('active');
                                        squareOpen.classList.remove('active');
                                        squareClose.classList.remove('active');
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
                                            //console.log(att.Numero_Lot);
                                            //console.log(huntedNber);
                                            for (i = 0; i < huntedNber; i++) {
                                                if (att.Numero_Lot == huntedTerritories[2][i]["DA_Numero"]) {
                                                    if (huntedTerritories[2][i]["FERMETURE"] == "O") {
                                                        return {
                                                            fillOpacity: 0.5,
                                                            weight: 4,
                                                            color: '#990047'
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

                                        // ************************ POPOVER HUNTING TERRITORIES ************************************************************

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
                                            // crs: L.CRS.proj4("EPSG:31370"),
                                            map.fitBounds(lyrTerritories.getBounds().pad(0));
                                        }).addTo(map);
                                    }
                                }
                            })
                        }
                    }
                }
            });
        };

        // ********************** PARCOURS SCRIPT ***********************************************************

        let lyrRoute;
        let traceGPX;
        let arselectedParcoursList;
        let routeValue;


        // ********************** CHECKBOX MARCHE / VTT ***********************************************************


        const walk = document.getElementById("typeMarche")

        walk.addEventListener("change", function() {
            if (walk.checked) {
                let checkboxWalk = "OK";
                console.log("La case est cochée marche.");
            } else {
                console.log("La case n'est pas cochée marche.");
            }
        });

        const VTT =  document.getElementById("typeVTT");

        VTT.addEventListener("change", function() {
            if (VTT.checked) {
                let checkboxVTT="OK";
                console.log("La case est cochée VTT.");
            } else {
                console.log("La case n'est pas cochée VTT.");
            }
        });

        

        // ******************* LIST OF ROUTE NAME ************************************************************

        listByRouteDB = <?php echo json_encode($List_Parcours); ?>;

        let listByRoute = Object.values(listByRouteDB[2]);
        console.log(listByRoute)
        //var listCityName = Object.values(listParcoursInfo[2])
        let routeNbre = listByRoute.length;

        let parcoursList = [];

        


        let selectedCategory;
        let arLocaliteName = [];
        let arCommuneName = [];

        // ************ LOADING LOCALITE & COMMUNE ARRAY ********************************************************

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

        let tableDatas = {
            arLocaliteName,
            arCommuneName
        }

        menuSelectionCity = selectionMenu(tableDatas)


        // ************ SELECTION FUNCTION LOCALITE OR COMMUNE ****************************************************  

        function selectionMenu(initialChoices) {
            selectOption = $("#txtFindCityName").selectmenu();

            $("#selectCityType").on('change', function() {
                selectedCategory = $(this).val();
                console.log(selectedCategory)
                const choices = initialChoices[selectedCategory];
                //console.log(choices);
                selectOption.empty();

                if (choices) {
                    choices.forEach(function(choice) {
                        //console.log(choice)
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

        //********************  PARCOURS SELECTION  **************************************************************

        $("#btnFindCityName").click(function(e) {
            e.preventDefault()
            selectedCity = $("#txtFindCityName").val()
            //console.log(selectedCity)
            //console.log(selectedCategory)
            arSelectedCityList = [];

            switch (selectedCategory) {
                case "arLocaliteName":
                    console.log("coucou")
                    getArselectedCity("localite")
                case "arCommuneName":
                    console.log("coucou1")
                    getArselectedCity("commune")

            }

            function getArselectedCity(typeSearch) {
                for (i = 0; i < listByRoute.length; i++) {
                    //console.log(selectedCity)
                    if (selectedCity == listByRoute[i][typeSearch]) {
                        arSelectedCityList.push([listByRoute[i]["localite"], listByRoute[i]['itineraire_id'], listByRoute[i]['nom'], listByRoute[i]['has_gpx']]);
                    }
                }
            }

            console.log(arSelectedCityList)

            for (i = 0; i < (arSelectedCityList.length); i++) {
                let routeValue;
                routeValue = arSelectedCityList[i][1]
                console.log(routeValue)

                let cookieNber = "<?php echo $file_suffix; ?>";

                searchTrace(routeValue);

                function searchTrace(routeValue) {
                    console.log(routeValue)
                    $.ajax({
                        type: 'GET',
                        url: "assets/inc/php/searchRouteInfo.php",
                        data: "routeValue=" + routeValue,

                        success: function(response) {
                            resultat = JSON.parse(response);
                            console.log(resultat);
                            var argpx = resultat[0]["gpx_url"]
                            console.log(argpx);
                            if (resultat[0]["gpx_url"] == "") {
                                $('#parcoursTrace').html('Pas de trace disponible');

                            }

                            const headers = new Headers();
                            headers.append('Access-Control-Allow-Origin', argpx);
                            headers.append('Content-Type', 'application/json');
                            headers.append('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,PATCH,OPTIONS');

                            console.log(traceGPX)

                            var fetchResult = fetch(argpx);

                            traceGPX = new L.GPX(argpx, {
                                onEachFeature: processTerritories,
                                polyline_options: [{
                                    color: '#34495e',
                                    opacity: 0.75,
                                    weight: 3,
                                    lineCap: 'round'
                                }],
                                async: true,
                                marker_options: {
                                    startIconUrl: 'assets/img/pin-icon-start.png',
                                    endIconUrl: 'assets/img/pin-icon-end.png',
                                    shadowUrl: 'assets/img/pin-shadow.png'
                                }
                            }).on('loaded', function(e) {
                                map.fitBounds(e.target.getBounds().pad(1));
                                map.on("addpoint", function(e) {
                                    var point = e.point;
                                    point.bindPopup("Latitude: " + point._latlng.lat + "<br>Longitude: " + point._latlng.lng).openPopup();
                                });
                            }).addTo(map);

                            function processTerritories(json, lyr) {
                                //var att = trk.properties;
                                lyr.on('mouseover', function() {
                                    lyr.setStyle({
                                        fillOpacity: 0.7
                                    })
                                    lyr.bindTooltip('<h3 style="color:#2c3e50"><center>' + trk.name + '</h3>');
                                })
                                lyr.on('mouseout', function() {
                                    lyr.setStyle({
                                        fillOpacity: 0.3
                                    });
                                })
                            }
                        }

                    })
                }


            }

            /*
            // ************ PARCOURS SELECTION MENU ************************************************************  

            selectOption1 = $("#txtFindParcoursName").selectmenu();
            console.log(arselectedParcoursList)
            var choices1 = arselectedParcoursList;
            console.log(choices1);
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

            $("#txtFindCityName").trigger("focusout");

            // ***************** SEARCH PARCOURS MAP ************************************************************

            $("#btnFindParcoursName").click(function(e) {
                e.preventDefault()

                if (lyrRoute) {
                    console.log(lyrRoute);
                    lyrRoute.remove();
                    map.removeLayer(lyrRoute);
                }
                routeName = $("#txtFindParcoursName").val().toLowerCase();
                console.log(routeName)

                for (j = 0; j < (listByRoute.length); j++) {

                    routeCheck = listByRoute[j]["nom"].toLowerCase()
                    console.log(routeCheck)
                    console.log(routeName)
                    if (routeName == routeCheck) {

                        routeValue = listByRoute[j]["itineraire_id"]
                        break;
                    }
                    console.log("coucou")
                    messageErreur.classList.remove('active');
                    parcoursNom.classList.add('active');

                    parcoursInfo.classList.add('active');
                    parcoursInfoDetails.classList.add('active');
                }*/

            // ************ SEARCH PARCOURS INFO ************************************************************

            var cookieNber = "<?php echo $file_suffix; ?>";

            $.ajax({
                type: 'GET',
                url: "assets/inc/php/searchRouteInfo.php",
                data: "routeValue=" + routeValue,

                success: function(response) {
                    resultat = JSON.parse(response);
                    console.log(resultat);
                    var argpx = resultat[0]["gpx_url"]
                    console.log(argpx);
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

        });
    });
</script>