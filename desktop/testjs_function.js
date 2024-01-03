



    function display_map(list_territoires) {


        // Definition of the Lambert 72 31370 spatial reference (crs)

        crs31370_code = "EPSG:31370";
        crs31370_defs = "+proj=lcc +lat_1=51.16666723333333 +lat_2=49.8333339 +lat_0=90 +lon_0=4.367486666666666 +x_0=150000.013 +y_0=5400088.438 +ellps=intl +towgs84=-106.8686,52.2978,-103.7239,0.3366,-0.457,1.8422,-1.2747 +units=m +no_defs";

        var epsg31370 = new L.Proj.CRS(
        crs31370_code,
        crs31370_defs,
            {
                resolutions: [ 130912, 65456, 32768, 16364, 8192, 4096, 2048, 1024, 512, 256, 128, 64, 32, 16, 8, 4, 2, 1]
            }
        )


        // create the map

        map = L.map('map', {
            zoom: 13,
            maxZoom: 17,
            minZoom: 0,
            continuousWorld: true,
            worldCopyJump: false
        }).setView([50.2944900, 5.1001500]); // Ciney


        // add the OpenStreetMap layer to the map

        var osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);


        var geometry = "";


        var territories_array = ["7123253071", 
                                 "9110501007",
                                 "9533500053"
                                ];
        jQuery.ajax({
            type: "POST",
            url: "./testjs_phpFunctions.php",
            async: false,
            datatype: 'json',
            data: { func : "get_json", 
                    arg1 : territories_array,
                    arg2 : ["./LB72.json"]

                  },
            success: function (result) {

                resultArray = jQuery.parseJSON(result);

                geometry = jQuery.parseJSON(resultArray["json"]);
                xyz = resultArray["toto"];

                console.log(resultArray["toto"]);
            },
            error: function(error) {
                console.log(error.status);
                console.log(error.statusText);
                // console.log(error.responseText);
            }
        });

        


        // add the geojson layer to the map



        var geom = L.Proj.geoJson(geometry).addTo(map);


        //  set the zoom level to show the entire geoJson layer
        var bounds = geom.getBounds();
        map.fitBounds(bounds);


        // Add the map scale (bottom legt)
        ctlScale = L.control.scale({
            position: 'bottomleft',
            imperial: false,
            maxWidth: 200
        }).addTo(map);

    }
