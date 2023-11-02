
    // ************ LIST OF ROUTE NAME ************************************************************

   

    var listByRoute = Object.values(listByRouteDB[2]);
    console.log(listByRoute)
    //var listCityName = Object.values(listParcoursInfo[2])
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
        const selectCity = document.getElementById('"#selectCityType');
        console.log(selectCity);
        document.getElementById("#selectCityType").addEventListener("change", function(){

        //$("#selectCityType").on('change', function() {
            selectedCategory = $(this).val();
            console.log(selectedCategory)
            const choices = initialChoices[selectedCategory];
            console.log(choices);
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
        console.log(selectedCity)
        console.log(selectedCategory)
        arSelectedCityList = [];

        switch (selectedCategory) {
            case "arLocaliteName":
                console.log("coucou")
                for (i = 0; i < listByRoute.length; i++) {
                    console.log(selectedCity)
                    if (selectedCity == listByRoute[i]["localite"]) {
                        arSelectedCityList.push(listByRoute[i]["localite"]);
                        arSelectedCityList.push(listByRoute[i]['itineraire_id']);
                        arSelectedCityList.push(listByRoute[i]['nom']);
                        console.log(arSelectedCityList)
                    }
                }
            case "arCommuneName":
                console.log("coucou1")
                for (i = 0; i < listByRoute.length; i++) {
                    console.log(selectedCity)
                    if (selectedCity == listByRoute[i]["commune"]) {
                        arSelectedCityList.push(listByRoute[i]['commune'])
                        arSelectedCityList.push(listByRoute[i]['itineraire_id'])
                        arSelectedCityList.push(listByRoute[i]['nom'])

                    }
                }
        }
        arselectedParcoursList = [];
        for (i = 2; i < arSelectedCityList.length; i++) {

            console.log(i)
            console.log(arSelectedCityList[i])
            arselectedParcoursList.push(arSelectedCityList[i])
            i = i + 2
        }

        // ************ Fonction SelectMenu Parcours ************************************************************  


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

    });

    $("#txtFindCityName").trigger("focusout");


    // ************ SEARCH ROUTE MAP ************************************************************

    

    $("#btnFindParcoursName").click(function(e) {
        e.preventDefault()
        console.log(lyrRoute);
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
        }


        // ************ SEARCH ROUTE INFO ************************************************************

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
    })
