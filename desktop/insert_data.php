<?php

session_start();

require "assets/inc/php/Parameters.php";
require_once "assets/inc/php/functions.php";

//1a Liste des territoires (nomenclature 10 chiffres)

$List_Territoires = PLF::Get_Territoire_List();

if ($List_Territoires[0] < 0) {

   echo $List_Territoires[1];
}

//1b Liste des territoires (nomenclature interne)

$List_Territoires = PLF::Get_Territoire_List(TypeTerritoire: "T");


if ($List_Territoires[0] < 0) {

   echo $List_Territoires[1];

   //
   // .... traitement de l'erreur
   //
}
   

<!DOCTYPE html>
<html>
    <head>
        
        <title>Insert date</title>
        <html lang="fr">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <!-- FICHIERS CSS -->
        <link rel="stylesheet" href="assets/css/insert-data.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
        <link rel="stylesheet" href="https://unpkg.com/@raruto/leaflet-elevation/dist/leaflet-elevation.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
       
        
        
        <!-- FICHIERS JS -->
        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
                    integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
                    crossorigin=""></script>
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/leaflet-ajax/2.1.0/leaflet.ajax.min.js"></script>
        <script src = "https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src = "https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src = "https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
        
    </head>

    <body>
        <div id="main-title"><center> INSERTION DATE DE CHASSE</center></div>

        <container id ="topContainer"><center>
            <form>
                <label id="titreType" for="ftype">Type de nomenclature</label><br>
                <label>
                    <input type="radio" id="id10" name="nomenclature" class="nomenclature" value="id10"></input>Nomenclature 10 chiffres<br>
                </label>
                <label>
                    <input type="radio" id="idInterne" name="nomenclature" class="nomenclature" value ="idInterne"></input>Nomenclature Interne<br>
                </label>
            </form>
            <div class="territoriesNumber">
                <input type="search" id="txtFindTerritoryNumber" placeholder="Numéro de territoire" autocomplete="off"/>
            </div>
            <div>
                <label for="start" id="dateTitle">Date de Chasse :</label>
                <input type="date" id="dateChasse" name="dateChasse" value="2023-10-01" min="2023-10-01" max="2024-01-31" required></input>
            </div>
            <input id="btnFindTerritoryNumber" type="button" value="ENREGISTRER"></input>
            <button id="btnRetour"onclick="window.location.href = 'https://partageonslaforet.be';">QUITTER</button>
            <div id="existing_info"></div>    
        </container>
        
        <container id="bottomContainer">
            <div id="map"></div>
        </container>

</html>
           
        
<script>
     $(document).ready(function() {
        var arTerritoriesIDs =[];
        var listArrayN = [];
        var list_Array = [];
        var val;
        var territoireValue;
        var lyrTerritories;
        
    // ************ RESET DATAS ***************************************************************          
            $('input[type=radio]').click(function() {
                 document.getElementById("txtFindTerritoryNumber").value="";
                 if(lyrTerritories){
                     lyrTerritories.remove();
                 }
                 document.getElementById("existing_info").innerHTML="";
            });    
    
    // ************ MAP INITIALIZATION ************************************************************
    
        var map = L.map('map').setView([49.567574, 5.533507], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        
    
    // ************ INSERT TERRITORY NUMBER ********************************************************
    
        $(".nomenclature").change(function(){
           
            val = $(".nomenclature:checked").val();
            console.log(val);
            if (val=="id10") {
                listArrayN = [];
                console.log("OK")
                listArrayN = <?php echo json_encode($List_Territoires[2]);?>;
                console.log(listArrayN);
            } 
            else {
                listArrayN = [];
                console.log("NOK")
                listArrayN = <?php echo json_encode($List_Territoires[2]);?>;
            }
           
            for(i=0; i<(listArrayN.length); i++){
                if(!arTerritoriesIDs.includes(listArrayN[i]["DA_Numero"])){
                    arTerritoriesIDs.push(listArrayN[i]["DA_Numero"]); 
                    arTerritoriesIDs.sort();
                   
                    
                    $("#txtFindTerritoryNumber").autocomplete({
                        source:arTerritoriesIDs,
                        autoFocus:true
                    });
                }
            }
            
    // ************ SEARCH TERRITORY ***************************************************************
    
    
            $("div").focusout(function() {
                territoireValue = $("#txtFindTerritoryNumber").val().toLowerCase();
                //console.log(territoireValue);
                if (val=="id10") {
                    $.ajax({
                        type: 'GET',
                        url: "assets/inc/php/createjsonN.php",
                        data: "territoireValue="+territoireValue,
                    
                        success: function(response){
                            
                            if(typeof lyrTerritories === 'undefined'){
                                lyrTerritories = L.geoJSON.ajax('assets/datas/territory.json');
                                lyrTerritories.on('data:loaded',function(){
                                    console.log(lyrTerritories);
                                map.fitBounds(lyrTerritories.getBounds().pad(1));
                                }).addTo(map);
                            } else{
                                lyrTerritories.remove();
                                lyrTerritories = L.geoJSON.ajax('assets/datas/territory.json');
                                console.log(lyrTerritories);
                                lyrTerritories.on('data:loaded',function(){
                                map.fitBounds(lyrTerritories.getBounds().pad(1));
                                }).addTo(map);
                            }
                        }
                    })
                } else {
                    
                    $.ajax({
                        type: 'GET',
                        url: "assets/inc/php/createjsonI.php",
                        data: "territoireValue="+territoireValue,
                        
                        success: function(response){
                            
                            if(typeof lyrTerritories === 'undefined'){
                                lyrTerritories = L.geoJSON.ajax('assets/datas/territory.json');
                                lyrTerritories.on('data:loaded',function(){
                                map.fitBounds(lyrTerritories.getBounds().pad(1));
                                }).addTo(map);
                            } else{
                                lyrTerritories.remove();
                                lyrTerritories = L.geoJSON.ajax('assets/datas/territory.json');
                                lyrTerritories.on('data:loaded',function(){
                                map.fitBounds(lyrTerritories.getBounds().pad(1));
                                }).addTo(map);
                            }
                        }
                    })
                }
            })
            
        
    // ************ DATE VALIDATION ***************************************************************    
        
            $("#btnFindTerritoryNumber").click(function(){
                territoireValue = $("#txtFindTerritoryNumber").val().toLowerCase();
                var dateChasse = document.querySelector('input[type="date"]');
                var dateChasseValue = dateChasse.value;
            
                console.log(territoireValue);
                console.log(dateChasseValue);
                
                if (val=="id10") {
                    $.ajax({
                        type: 'GET',
                        url: "assets/inc/php/addDate_TerritoryN.php",
                        data: {var1: territoireValue, var2: dateChasseValue},
                        success: function(response){
                            console.log(response);
                            if(response !== "OK") {
                                $('#existing_info').html('ENREGISTRE');
                            }else{
                                $('#existing_info').html('NON ENREGISTRE');
                            }
                        }
                    })           
                } else {
                    $.ajax({
                        type: 'GET',
                        url: "assets/inc/php/addDate_TerritoryI.php",
                        data: {var1: territoireValue, var2: dateChasseValue},
                        success: function(response){
                            console.log(response);
                            if(response !== "OK") {
                                $('#existing_info').html('ENREGISTRE');
                            }else{
                                $('#existing_info').html('NON ENREGISTRE');
                            }

                        }
                    })           
                }      
            });
        });  
     });
    
</script>
   



 
