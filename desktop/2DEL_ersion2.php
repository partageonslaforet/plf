<?php
$cookie_name = "plf";
if(!isset($_COOKIE[$cookie_name])) {
  session_start();
  $file_suffix = session_id();
  setcookie($cookie_name,session_id(),time() + (86400 * 2), "/");  
} else { $file_suffix = $_COOKIE[$cookie_name];}
//echo $file_suffix;
?>


<!DOCTYPE html>
<html>
    <head>
        <!--Global site tag (gtag.js) - Google Analytics-->
            <script async src="https://www.googletagmanager.com/gtag/js?id=G-3H936HP3BY"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());
            
              gtag('config', 'G-3H936HP3BY');
            </script>
        <title>PARTAGEONSLAFORET.BE</title>
    
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico" />
        <meta name="google-site-verification" content="Z_Yzazljtl2FP8SxBOhbSMVNeg_90JfD-ia6feUM5KU" />
    
        <!-- FICHIERS CSS -->
        
        <link rel="stylesheet" href="assets/css/general.css">
        <!--<link rel="stylesheet" href="assets/css/calendar.css">
        <link rel="stylesheet" href="assets/css/cc.css">
        <link rel="stylesheet" href="assets/css/dnf.css">
        <link rel="stylesheet" href="assets/css/territories.css">
        <link rel="stylesheet" href="assets/css/trace.css">-->
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;900&display=swap" rel="stylesheet">
        
        
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
        <link rel="stylesheet" href="assets/src/css/L.Control.Zoomslider.css">
        <link rel="stylesheet" href="assets/src/css/Control.MiniMap.css">
        <link rel="stylesheet" href="assets/src/css/gh-fork-ribbon.css">
        <link rel="stylesheet" href="assets/src/css/L.Control.Locate.css">
        <link rel="stylesheet" href="assets/src/css/leaflet.rainviewer.css">
        <link rel="stylesheet" href="https://unpkg.com/@raruto/leaflet-elevation/dist/leaflet-elevation.css" />
        <link rel="stylesheet" href="assets/src/css/jquery-ui.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="assets/src/css/leaflet-sidebar.css">
        
        <!-- FICHIERS JS -->
            
        <script src = "https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
        <script src = "assets/src/js/L.Control.Zoomslider.js"></script>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src = "assets/src/js/Control.MiniMap.js"></script>
        <script src = "assets/src/js/leaflet-providers.js"></script>
        <script src = "assets/src/js/L.Control.Zoomslider.js"></script>
        <script src = "assets/src/js/L.Control.Locate.js"></script>
        <script src = "assets/src/js/leaflet.rainviewer.js"></script>
        <script src = "https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
            
    
    
       
    </head>

    <body>
        <!-- **************** SIDEBAR **************** -->
            
        <div id="sidebar" class="sidebar collapsed">
            <!-- Nav tabs https: //www.chasseengaume.be/contact/-->
            <div class="sidebar-tabs"  >
                <ul role="tablist">
                    <li><a href="#mail" target="_blank" role="tab"><i class="fa fa-envelope" title="CONTACT"></i></a></li>
                    <li><a href="#information" role="tab"><i class="fa fa-info" title="INFORMATIONS"></i></a></li>
                    <li><a href="#parcours" role="tab"><i class="fa fa-hiking" title="PARCOURS"></i></a></li>
                    <li><a href="#database" role="tab"><i class="fa fa-database" title="RECHERCHES"></i></a></li>
                    <li><a href="#dnf" role="tab"><i class="fa fa-tree" title="DNF"></i></a></li>
                    <li><a href="#gpx" role="tab"><i class="fa fa-location-arrow" title="TRACE GPX"></i></a></li>
                </ul>
    
                <ul role="tablist">
                    <li class="disabled"><a href="#settings" role="tab"><i class="fa fa-gear"></i></a></li>
                </ul>
            </div>
            
            <!-- Tab panes -->
            <div class="sidebar-content">
                <div class="sidebar-pane" id="mail">
                    <h1 class="sidebar-header">
                        ENVOYER UN MESSAGE
                        <span class="sidebar-close"><i class="fa fa-caret-left"></i></span>
                    </h1>
                    <h5 class="infoEmail">Une question ? Une suggestion?</h5>
                    <form class="contact-form" action="contactform.php" method="post">
                        <div class="col-md-10-offset rounded align-center">
                            <div class="form-group input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" name="name" placeholder="Votre nom & prénom" Required>
                            </div>
                            <div class="form-group input-group">
                                <span class="input-group-text"><i class="fas fa-at"></i></span>
                                <input type="text" class="form-control"name="mail" placeholder="Votre e-mail" required>
                            </div>
                            <div class="form-group input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="test" class="form-control"name="subject" placeholder="Sujet" required>
                            </div>
                            <div class="form-group input-group">
                                <span class="input-group-text"><i class="fas fa-comment-alt"></i></span>
                                <textarea name="message" class="form-control"placeholder="Votre message" rows="4" required></textarea>
                            </div>
                            <button id="envoiMail" type="submit" name="submit"><span id=" fas fa-paper-plane"></span>  ENVOYER</button>
                        </div>
                    </form>
                    <h3>
                        <?php 
                        $resultMailPos="";
                        $resultMailNeg="";
                        if(isset($_GET['success'])){
                            $resultMailPos="Votre message a été transmit";
                            echo "<script> alert ('$resultMailPos');</script>";
                            }
                        ?>
                    </h3>
                    <h3 class="text-center text-danger"></h3>
                </div>
                
                
                <div class="sidebar-pane" id="information">
                    <h1 class="sidebar-header">
                        INFORMATIONS
                        <span class="sidebar-close"><i class="fa fa-caret-left"></i></span>
                    </h1>
                    <div class="container-brochure">
                        <div class="couverture_brochure">
                            <img src="images/Couverture_partageonslaforet.png" width="80%">
                        </div>
                        <a href="images/brochure_partageons_la_foret.pdf" target="_blank" class="bton_telecharger">Télécharger la brochure</a>
                        <div class="disclaimer">
                            <h5> Les dates de battues sont données à titre indicatif et n’engage pas le site partageonslaforet.be<br> Il est impératif de se référer à l’affichage à l’entrée en forêt.</h5>
                            <div class="image-chasse">
                                <img src="images/panneaux-de-chasses.jpg" width="80%">
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="sidebar-pane" id="parcours">
                    <h1 class="sidebar-header">
                        PARCOURS
                        <span class="sidebar-close"><i class="fa fa-caret-left"></i></span>
                    </h1>
                    <div class="container">
                        <div id="parcoursSelection" class="row"></div>
                        <div class="col-xs-9" id="findParcours">
                            <input type="search" id="txtFindParcoursName" class="form-control input-lg" placeholder="Nom d'un parcours balisé" autocomplete="off"/> 
                        </div>
                        <div id="distance" class="row">
                            <h6 class="col-12">Distance du parcours en km: <span id="valFilterDist-5">15</span> - <span id="valFilterDist">20</span><h6>
                         </div>
                        <div id="distRange" class="row">
                            <div id="sldrin" class="col-1">0</div>
                            <input id="numDist" class="col-9" type="range" min="5" max="100" step="5" value="10">
                            <div id="sldrout"class="col-1"> 60</div>
                        </div>
                       <div id="distanceActu" class="row">
                            <h6 class="col-9">A <span id="valFilterDistActu">10</span> km de ma position actuelle</h6>
                        </div>
                        <div id="distRange" class="row">
                            <div id="sldrin" class="col-1">1</div>
                            <input id="numDistActu" class="col-9" type="range" min="1" max="100" step="1" value="10">
                            <div id="sldrout"class="col-1"> 100</div>
                        </div>
                        <div class="col-xs-3">
                            <button id="btnFindParcoursName" class="btn btn-primary btn-block"><i class="fa fa-search"></i></button>
                        </div>
                        <div id="missingName"></div>
                    </div>
                    <div id="parcoursMapId" class="col-xs-12"></div>
                    <div class="container">
                        <table id="tableResults" table class='table table-striped table-hover table-sm table align-middle' >
                            <thead class="table-light" data-sort-name='KM' data-sort-order="desc"><tr class="text-center"><th>#</th class="text-center"><th>Nom du parcours</th><th class="text-center">KM</th></tr></thead>
                            <tbody id="tablecontent"></tbody>
                        </table>
                        <div id="infoCircuits"><h5 align ="center"><b></b></h5></div>
                    </div>
                    <div id="elevation-divP" class="elevation-divP"></div>
                    <div id="dataSummaryPar">
                        <div id="summaryPar">
                            <span class="totlenP">
                                <span class="summarylabel">Distance: </span>
                                <span class="summaryvalue">0</span></br>
                            </span>
                            <span class="maxeleP">
                                <span class="summarylabel">Point le + haut: </span>
                                <span class="summaryvalue">0</span></br>
                            </span>
                            <span class="mineleP">
                                <span class="summarylabel">Point le + bas: </span>
                                <span class="summaryvalue">0</span></br>
                            </span>
                            <span class="gainP">
                                <span class="summarylabel">D+: </span>
                                <span class="summaryvalue">0</span></br>
                            </span>
                            <span class="lossP">
                                <span class="summarylabel">D-: </span>
                                <span class="summaryvalue">0</span>
                        </div>
                    </div>
                </div>
                
                
                <div class="sidebar-pane" id="database" class="col-xs-12">
                    <h1 class="sidebar-header" class="col-xs-12">RECHERCHE<span class="sidebar-close"><i class="fa fa-caret-left"></i></span></h1>
                    <div id="territoriesSearch" class="col-xs-12">
                        <div id="territoriesSearchLabel" class="col-xs-12">
                            <h4>TERRITOIRES DE CHASSE</h4>
                        </div>
                        <div id="divTerritoriesError" class="errorMsg col-xs-12"></div>
                        <div id="divFindTerritories" class="row has-error"></div>
                        <div class="col-xs-9" id="findTerritories">
                            <input type="search" id="txtFindTerritoriesName" class="form-control input-lg" placeholder="Nom d'un territoire" autocomplete="off"/> 
                        </div>
                        <div class="col-xs-3">
                            <button id="btnFindTerritoriesName" class="btn btn-primary btn-block"><i class="fa fa-search"></i></button>
                        </div>
                        <div class="" id="divTerritoriesData"></div>
                    </div>
                    <div id="territoriesList">
                        <div id="territoriesNameLabel">
                            <div id="huntingDate"></div>
                            <div class="" id="listeTerritoriesName"></div>-->
                            <div id="huntingDateList">
                                <div id="huntingDateTitle"></div>
                                <div id="dataDate"></div>
                                <div id="nohuntingMessage"></div>
                           </div>
                        </div>
                    </div>
                </div>   
                
                
                <div class="sidebar-pane" id="dnf" class="col-xs-12">
                    <h1 class="sidebar-header" class="col-xs-12">DNF<span class="sidebar-close"><i class="fa fa-caret-left"></i></span></h1>
                    <div id="cantonnementLabel" class="col-xs-12">
                        <h4>CANTONNEMENTS</h4>
                    </div>
                     <div id="findDnf">
                        <input id="txtFindCantonName" type="search" placeholder="Nom d'un canton" autocomplete="off">
                    </div>
                    <div id="noSearchItemMessage"></div>
                    <div class="btonSearchCanton">
                        <button type="button" name="btonCantonSearch" id="btnFindCantonName"><i class="fa fa-search fa-2x"></i></button><br>
                    </div>
                    <div id="cantonResult">
                        <div id="cantonName"></div>
                        <div id="cantonNbre"></div>
                        <div id="cantonDatasDetails"></div>    
                    </div>
                </div>
                
                
                <div class="sidebar-pane" id="gpx" class="col-xs-12">
                    <h1 class="sidebar-header" class="col-xs-12">GPX<span class="sidebar-close"><i class="fa fa-caret-left"></i></span></h1>     
                    <div id="GPXLabel" class="col-xs-12">
                        <h5>Chargez votre trace GPX</h5>
                    </div>
                    <form class="form" id="myForm">
                        <input id="inputFile" value="" type="file" accept=".gpx">
                        <div class="searchButtonGPX">
                            <button type="submit" id="btnuploadgpx" class="btn btn-primary bton-xs"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                    
                    <div class="" id="GPXName"></div>
                    <div id="elevation-div" class="elevation-div"></div>
                    <div id="dataSummary">
                        <div id="summary">
                            <span class="totlen">
                                <span class="summarylabel">Distance: </span>
                                <span class="summaryvalue">0</span></br>
                            </span>
                            <span class="maxele">
                                <span class="summarylabel">Point le + haut: </span>
                                <span class="summaryvalue">0</span></br>
                            </span>
                            <span class="minele">
                                <span class="summarylabel">Point le + bas: </span>
                                <span class="summaryvalue">0</span></br>
                            </span>
                            <span class="gain">
                                <span class="summarylabel">D+: </span>
                                <span class="summaryvalue">0</span></br>
                            </span>
                            <span class="loss">
                                <span class="summarylabel">D-: </span>
                                <span class="summaryvalue">0</span></br>
                            </span>
                            <span>
                                <span class="speed">
                                <span class="summarylabel">Vitesse moy.: </span>
                                <span class="summaryvalue">0</span></br>
                            </span>
                        </div>
                     </div>
                </div>
                
                
                <div class="sidebar-pane" id="settings">
                    <h1 class="sidebar-header">
                        SETTINGS<span class="sidebar-close"><i class="fa fa-caret-left"></i></span>
                    </h1>
                    <!-- Default switch -->
                   <div class="custom-control custom-switch">
                        <button id='btonLocate' class='btn btn-primary btn-block'>LOCALISATION</button><br>
                        <input type="checkbox" class="custom-control-input" id="btonlocate" onclick="caca()"></input>
                        <label class="custom-control-label" for="btonlocate">Localisation</label> 
                    </div>
                     <form name="formulaire">
                        <label for="distance">Distance :</label>
                        <input type="text" name="distance" value="">
                    </form>
                </div>
            </div>
        </div>
         <!--Loading script-->
                    
        <div class='spinner-wrapper'>
            <div class="spinner"></div>
        </div>
         
        <script>
            let spinnerWrapper = document.querySelector('.spinner-wrapper');
            window.addEventListener('load', function () {
            // spinnerWrapper.style.display = 'none';
            spinnerWrapper.parentElement.removeChild(spinnerWrapper);
            });
        </script>   
        
        <!-- **************** MAP **************** -->
        
        <div id="map"></div>
        <script src = "assets/src/js/leaflet-sidebar.js"></script>

    </body>
</html>

<script>
    
    // ************ MAP INITIALIZATION *********************************************************

        var map = L.map('map', {
            center:[49.9167, 5.366679],
            zoom:12,
            zoomControl:false
            });
        
         
            
           sidebar = L.control.sidebar('sidebar').addTo(map);
            
           
            

      L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
            }).addTo(map);
  
   
    
</script>
    
