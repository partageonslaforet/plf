<?php


/**
 *  ----------------------------------------------------------------------
 *  Creation the fichier json "Feature collection"
 *  ----------------------------------------------------------------------
 * 
 * 
 *  SELECT json_build_object(
 *	  	     'type', 'FeatureCollection',
 *	      	 'features', json_agg(
 *	          				     json_build_object(
 *	              			     'type', 'Feature',
 *	              			     'id', N_LOT,
 *						  	           'crs', json_build_object(
 *							                      'type',      'name', 
 *							                      'properties', json_build_object(
 *		          /* Lambert 72 Spatial reference (CSR) /
 *                                                    'name', 'EPSG:31370')
 *                                                  ),
 *                           'properties', json_build_object(
 *                                           'N_LOT', N_LOT,
 *               /* return in SQM /
 *                                           'Surface', st_area(geom),
 *               /* return in meters /
 *                                           'Perimetre', st_perimeter(geom)
 *                                         ),
 *                           'geometry', ST_AsGeoJSON(geom)::json
 *                        )
 *                      )
 *   
 *        )
 *  AS json
 *  FROM ( 
 *      SELECT N_LOT, geom
 *      FROM "plf_spw_territoires_geom" 
 *      WHERE N_LOT = '9533500053' OR N_LOT = '9110501007' OR N_LOT = '7123253071'
 *  ) AS t
*/



/**
 *  ----------------------------------------------------------------------
 *  Calculer le point central de toutes les geom sélectionnées
 *  ---------------------------------------------------------------------- 
 * 
 *  SELECT st_astext(
 *            st_centroid(
 *                st_Union(
 *                    ST_FlipCoordinates(
 *                        st_transform(
 *                          ST_Centroid(geom), 4326)
 *                        )
 *                    )
 *                )
 *            )
 *  FROM plf_spw_territoires_geom WHERE N_LOT = '9533500053' OR N_LOT = '9110501007' OR N_LOT = '7123253071'
 *
 * 
 * 
 * RESULTAT : POINT(49.99272102264476 5.301898429683742)
 *  
 */





$lb72 = file_get_contents("./LB72.json");


?>



<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
  



  <!-- Load Leaflet from CDN -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

  <!-- Make sure you put this AFTER Leaflet's CSS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.9.2/proj4.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4leaflet/1.0.2/proj4leaflet.js"></script>



  <style>
    body {
      margin: 0;
      padding: 0;
    }

    #map {
      position: absolute;
      top: 0;
      bottom: 0;
      right: 0;
      left: 0;
      font-family: Arial, Helvetica, sans-serif;
      font-size: 14px;
      color: #323232;
    }
  </style>


<title>Esrix Leaflet Tutorials: Display a map</title>


</head>

<body>

  this is the first line.

  <div id="map"></div>


  <script>






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
      //crs: epsg31370,
      zoom: 13,
      maxZoom: 17,
      minZoom: 0,
      continuousWorld: true,
      worldCopyJump: false
      }).setView([ 50.2944900, 5.1001500]);  // Ciney


// add the OpenStreetMap layer to the map

var osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);


// add the geojson layer to the map

var geom = L.Proj.geoJson(<?php echo $lb72; ?>).addTo(map);


//  set the zoom level to show the entire geoJson layer
var bounds = geom.getBounds();
map.fitBounds(bounds);


// Add the map scale (bottom legt)
ctlScale = L.control.scale({
    position: 'bottomleft',
    imperial: false,
    maxWidth: 200
}).addTo(map);



  </script>

</body>

</html>