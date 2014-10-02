<?php require_once('../require/settings.php'); ?>

var map;
var user = new L.FeatureGroup();
var weatherprecipitation;
var weatherrain;
var weatherclouds;

var geojsonLayer;

var weatherradar;
var weatherradarrefresh;
var weathersatellite;
var weathersatelliterefresh; 
$( document ).ready(function() {
    
  //setting the zoom functionality for either mobile or desktop
  if( navigator.userAgent.match(/Android/i)
     || navigator.userAgent.match(/webOS/i)
     || navigator.userAgent.match(/iPhone/i)
     || navigator.userAgent.match(/iPod/i)
     || navigator.userAgent.match(/BlackBerry/i)
     || navigator.userAgent.match(/Windows Phone/i))
  {
    var zoom = 8;
  } else {
    var zoom = 9;
  }

  //create the map
  map = L.map('live-map', { zoomControl:false }).setView([<?php print $globalCenterLatitude; ?>,<?php print $globalCenterLongitude; ?>], zoom);

  //initialize the layer group for the aircrft markers
  var layer_data = L.layerGroup();

  //a few title layers
<?php
    if ($globalMapProvider == 'Mapbox') {
?>
  L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
      '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
      'Imagery © <a href="http://mapbox.com">Mapbox</a>',
    id: '<?php print $globalMapboxId; ?>'
  }).addTo(map);
<?php
    } elseif ($globalMapProvider == 'OpenStreetMap') {
?>
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
      '<a href="www.openstreetmap.org/copyright">Open Database Licence</a>'
  }).addTo(map);
<?php
    } elseif ($globalMapProvider == 'MapQuest-OSM') {
?>
  L.tileLayer('http://otile1.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
      '<a href="www.openstreetmap.org/copyright">Open Database Licence</a>, ' +
      'Tiles Courtesy of <a href="http://www.mapquest.com">MapQuest</a>'
  }).addTo(map);
<?php
    } elseif ($globalMapProvider == 'MapQuest-Aerial') {
?>
  L.tileLayer('http://otile1.mqcdn.com/tiles/1.0.0/sat/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
      '<a href="www.openstreetmap.org/copyright">Open Database Licence</a>, ' +
      'Tiles Courtesy of <a href="http://www.mapquest.com">MapQuest</a>, Portions Courtesy NASA/JPL-Caltech and U.S. Depart. of Agriculture, Farm Service Agency"'
  }).addTo(map);
<?php
    }
?>

<?php
    if ($globalLatitudeMin != '' && $globalLatitudeMax != '' && $globalLongitudeMin != '' && $globalLongitudeMax != '') 
    {
    ?>
  //create the bounding box to show the coverage area
  var polygon = L.polygon(
    [ [[90, -180],
    [90, 180],
    [-90, 180],
    [-90, -180]], // outer ring
    [[<?php print $globalLatitudeMin; ?>, <?php print $globalLongitudeMax; ?>],
    [<?php print $globalLatitudeMin; ?>, <?php print $globalLongitudeMin; ?>],
    [<?php print $globalLatitudeMax; ?>, <?php print $globalLongitudeMin; ?>],
    [<?php print $globalLatitudeMax; ?>, <?php print $globalLongitudeMax; ?>]] // actual cutout polygon
    ],{
    color: '#000',
    fillColor: '#000',
    fillOpacity: 0.1,
    stroke: false
    }).addTo(map);
<?php
    }
?>

	// Show airports on map
	
	var airportIcon = L.icon({
	    iconUrl: 'images/runway.png',
	    iconSize: [32, 37],
	    iconAnchor: [16, 37],
	    popupAnchor: [0, -28]
	});
	
	function onEachFeature (feature, layer) {
		var output = '';
//            	output += '<div class="top">';
//		    output += feature.properties.name+', ';
//		    output += feature.properties.city+' / '+feature.properties.country;
//		output += '</div>';

                output += '<div class="top">';
                  output += '<div class="left">';
                  if (typeof feature.properties.image_thumb != 'undefined' && feature.properties.image_thumb != '') {
                  output += '<img src="'+feature.properties.image_thumb+'" /></a>';
                  }
                  output += '</div>';
                  output += '<div class="right">';
                    output += '<div class="callsign-details">';
                      output += '<div class="callsign">'+feature.properties.name+'</div>';
                     // output += '<div class="airline">'+feature.properties.airline_name+'</div>';
                    output += '</div>';
                    output += '<div class="nomobile airports">';
            	     output += '<div class="airport">';
                        output += '<span class="code"><a href="/airport/'+feature.properties.icao+'" target="_blank">'+feature.properties.icao+'</a></span>';
                      output += '</div>';
                     // output += '<i class="fa fa-long-arrow-right"></i>';
                    //  output += '<div class="airport">';
                      //  output += '<span class="code"><a href="/airport/'+feature.properties.arrival_airport_code+'" target="_blank">'+feature.properties.arrival_airport_code+'</a></span>'+feature.properties.arrival_airport;
                     // output += '</div>';
                    output += '</div>';
                  output += '</div>';
                output += '</div>';

                output += '<div class="details">';
//                    output += '<div class="mobile airports">';
  //                    output += '<div class="airport">';
    //                    output += '<span class="code">'+feature.properties.departure_airport_code+'</a></span>'+feature.properties.departure_airport;
      //                output += '</div>';
        //              output += '<i class="fa fa-long-arrow-right"></i>';
          //            output += '<div class="airport">';
        //                output += '<span class="code">rt/'+feature.properties.arrival_airport_code+'" target="_blank">'+feature.properties.arrival_airport_code+'</a></span>'+feature.properties.arrival_airport;
         //             output += '</div>';
          //          output += '</div>';
                    output += '<div>';
                      output += '<span>City</span>';
                        output += feature.properties.city;
                    output += '</div>';
                    if (feature.properties.altitude != "" || feature.properties.altitude != 0)
                    {
                      output += '<div>';
                        output += '<span>Altitude</span>';
                        output += Math.round(feature.properties.altitude*3,2809)+' feet - '+feature.properties.altitude+' m';
                      output += '</div>';
                    }
                      output += '<div>';
                        output += '<span>Country</span>';
                        output += feature.properties.country;
                      output += '</div>';
                      if (feature.properties.homepage != "") {
                    output += '<div>';
                      output += '<span>Links</span>';
                      output += '<a href="'+feature.properties.homepage+'">Homepage</a>';
                    output += '</div>';
                    }
     //               output += '<div>';
       //               output += '<span>Coordinates</span>';
         //             output += feature.properties.latitude+", "+feature.properties.longitude;
           //         output += '</div>';
             //       output += '<div>';
               //       output += '<span>Heading</span>';
                 //     output += feature.properties.heading;
                //    output += '</div>';
                output += '</div>';
                if (typeof feature.properties.squawk != 'undefined') {
                    output += '<div class="bottom">';
                	output += 'Squawk : ';
			output += feature.properties.squawk;
            		if (typeof feature.properties.squawk_usage != 'undefined') {
            			output += ' - '+feature.properties.squawk_usage;
            		}
		    output += '</div>';
                }
                output += '</div>';






		layer.bindPopup(output);
	};
	
	function update_geojsonLayer() {
	    var bbox = map.getBounds().toBBoxString();
	    geojsonLayer = new L.GeoJSON.AJAX("airport-geojson.php?coord="+bbox,{
	    onEachFeature: onEachFeature,
		pointToLayer: function (feature, latlng) {
		    //return L.marker(latlng, {icon:airportIcon});
		    return L.marker(latlng, {icon: L.icon({
			iconUrl: feature.properties.icon,
			iconSize: [16, 18],
			iconAnchor: [16, 37],
			popupAnchor: [0, -28]
			})
                    });
		}
	    }).addTo(map);
	};

	map.on('moveend', function() {
	    map.removeLayer(geojsonLayer);
	    if (map.getZoom() > 7) {
		update_geojsonLayer();
	    }
	});

	
	update_geojsonLayer();
	
	var info = L.control();
	info.onAdd = function (map) {
		this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
		this.update();
		return this._div;
	};
	info.update = function (props) {
		if (typeof props != 'undefined') {
			this._div.innerHTML = '<h4>Aircraft detected</h4>' +  '<b>' + props.flight_cnt + '</b>';
		}
	};
	info.addTo(map);

  function getLiveData()
  {
	var bbox = map.getBounds().toBBoxString();
//    map.removeLayer(layer_data);
//    layer_data = L.layerGroup();

    $.ajax({
      dataType: "json",
//      url: "live/geojson?"+Math.random(),
      url: "live/geojson?"+Math.random()+"&coord="+bbox,
      success: function(data) {
	    map.removeLayer(layer_data);
	    layer_data = L.layerGroup();
          
          var live_data = L.geoJson(data, {
            pointToLayer: function (feature, latLng) {
                
              var markerLabel = "";
              if (feature.properties.callsign != ""){ markerLabel += feature.properties.callsign+'<br />'; }
              if (feature.properties.departure_airport_code != "" || feature.properties.arrival_airport_code != ""){ markerLabel += '<span class="nomobile">'+feature.properties.departure_airport_code+' - '+feature.properties.arrival_airport_code+'</span>'; }
                
              return new L.Marker(latLng, {
                iconAngle: feature.properties.heading,
                title: markerLabel,
                alt: feature.properties.callsign,
                icon: L.icon({
                  iconUrl: 'images/map-icon-shadow.png',
                  iconRetinaUrl: 'images/map-icon-shadow@2x.png',
                  iconSize: [40, 40],
                  iconAnchor: [20, 40]
                })
                  //on marker click show the modal window with the iframe
              })
            },
            onEachFeature: function (feature, layer) {
              var output = '';
                
              //individual aircraft
              if (feature.properties.type == "aircraft"){
		info.update(feature.properties);
                output += '<div class="top">';
                  output += '<div class="left"><a href="/redirect/'+feature.properties.flightaware_id+'" target="_blank"><img src="'+feature.properties.image+'" alt="'+feature.properties.registration+' '+feature.properties.aircraft_name+'" title="'+feature.properties.registration+' '+feature.properties.aircraft_name+'" /></a></div>';
                  output += '<div class="right">';
                    output += '<div class="callsign-details">';
                      output += '<div class="callsign"><a href="/redirect/'+feature.properties.flightaware_id+'" target="_blank">'+feature.properties.callsign+'</a></div>';
                      output += '<div class="airline">'+feature.properties.airline_name+'</div>';
                    output += '</div>';
                    output += '<div class="nomobile airports">';
                      output += '<div class="airport">';
                        output += '<span class="code"><a href="/airport/'+feature.properties.departure_airport_code+'" target="_blank">'+feature.properties.departure_airport_code+'</a></span>'+feature.properties.departure_airport;
                	if (typeof feature.properties.departure_airport_time != 'undefined') {
                	    output += '<br /><span class="time">'+feature.properties.departure_airport_time+'</span>';
                	}
                      output += '</div>';
                      output += '<i class="fa fa-long-arrow-right"></i>';
                      output += '<div class="airport">';
                        output += '<span class="code"><a href="/airport/'+feature.properties.arrival_airport_code+'" target="_blank">'+feature.properties.arrival_airport_code+'</a></span>'+feature.properties.arrival_airport;
                	if (typeof feature.properties.arrival_airport_time != 'undefined') {
                	    output += '<br /><span class="time">'+feature.properties.arrival_airport_time+'</span>';
                	}
                      output += '</div>';
                    output += '</div>';
                  output += '</div>';
                output += '</div>';

                output += '<div class="details">';
                    output += '<div class="mobile airports">';
                      output += '<div class="airport">';
                        output += '<span class="code"><a href="/airport/'+feature.properties.departure_airport_code+'" target="_blank">'+feature.properties.departure_airport_code+'</a></span>'+feature.properties.departure_airport;
                      output += '</div>';
                      output += '<i class="fa fa-long-arrow-right"></i>';
                      output += '<div class="airport">';
                        output += '<span class="code"><a href="/airport/'+feature.properties.arrival_airport_code+'" target="_blank">'+feature.properties.arrival_airport_code+'</a></span>'+feature.properties.arrival_airport;
                      output += '</div>';
                    output += '</div>';
                    output += '<div>';
                      output += '<span>Aircraft</span>';
                      if (feature.properties.aircraft_wiki != 'undefined') {
                        output += '<a href="'+feature.properties.aircraft_wiki+'">';
                        output += feature.properties.aircraft_name;
                        output += '</a>';
                      
                      } else {
                        output += feature.properties.aircraft_name;
                      }
                    output += '</div>';
                    if (feature.properties.altitude != "" || feature.properties.altitude != 0)
                    {
                      output += '<div>';
                        output += '<span>Altitude</span>';
                        output += feature.properties.altitude+'00 feet - '+Math.round(feature.properties.altitude*30.48)+' m (FL'+feature.properties.altitude+')';
                      output += '</div>';
                    }
                    if (feature.properties.registration != "")
                    {
                      output += '<div>';
                        output += '<span>Registration</span>';
                        output += '<a href="/registration/'+feature.properties.registration+'" target="_blank">'+feature.properties.registration+'</a>';
                      output += '</div>';
                    }
                    output += '<div>';
                      output += '<span>Speed</span>';
                      output += feature.properties.ground_speed+' knots - '+Math.round(feature.properties.ground_speed*1.852)+' km/h';
                    output += '</div>';
                    output += '<div>';
                      output += '<span>Coordinates</span>';
                      output += feature.properties.latitude+", "+feature.properties.longitude;
                    output += '</div>';
                    output += '<div>';
                      output += '<span>Heading</span>';
                      output += feature.properties.heading;
                    output += '</div>';
                output += '</div>';
                if (typeof feature.properties.squawk != 'undefined') {
                    output += '<div class="bottom">';
                	output += 'Squawk : ';
			output += feature.properties.squawk;
            		if (typeof feature.properties.squawk_usage != 'undefined') {
            			output += ' - '+feature.properties.squawk_usage;
            		}
		    output += '</div>';
                }
                output += '</div>';

                layer.bindPopup(output);

                layer_data.addLayer(layer);
               }
                
                //aircraft history position as a line
                if (feature.properties.type == "history"){
                    var style = {
                        "color": "#1a3151",
                        "weight": 3,
                        "opacity": 0.3
                    };
                    layer.setStyle(style);
                    layer_data.addLayer(layer);
                }

             }
              
            
              
          });
          layer_data.addTo(map);
          //re-create the bootstrap tooltips on the marker 
          showBootstrapTooltip();
        }
    }).error(function() {
              map.removeLayer(layer_data);

    });
  }

  //load the function on startup
  getLiveData();

  //then load it again every 30 seconds
  setInterval(function(){getLiveData()},30000);

  //adds the bootstrap hover to the map buttons
  $('.button').tooltip({ placement: 'right' });
    
  
});

//adds the bootstrap tooltip to the map icons
function showBootstrapTooltip(){
    $(".leaflet-marker-icon").tooltip('destroy');
    $('.leaflet-marker-icon').tooltip({ html: true });
}

//adds a new weather radar layer on to the map
function showWeatherPrecipitation(){
  //if the weatherradar is currently active then disable it, otherwise enable it
  if (!$(".weatherprecipitation").hasClass("active"))
  {
    //loads the function to load the weather radar
    loadWeatherPrecipitation();
    //automatically refresh radar every 2 minutes
    weatherprecipirationrefresh = setInterval(function(){loadWeatherPrecipitation()}, 120000);
    //add the active class
    $(".weatherprecipitation").addClass("active");
  } else {
      //remove the weather radar layer
      map.removeLayer(weatherprecipitation);
      //remove the active class
      $(".weatherprecipitation").removeClass("active");
      //remove the auto refresh
      clearInterval(weatherprecipitationrefresh);
  }       
}
//adds a new weather radar layer on to the map
function showWeatherRain(){
  //if the weatherradar is currently active then disable it, otherwise enable it
  if (!$(".weatherrain").hasClass("active"))
  {
    //loads the function to load the weather radar
    loadWeatherRain();
    //automatically refresh radar every 2 minutes
    weatherrainrefresh = setInterval(function(){loadWeatherRain()}, 120000);
    //add the active class
    $(".weatherrain").addClass("active");
  } else {
      //remove the weather radar layer
      map.removeLayer(weatherrain);
      //remove the active class
      $(".weatherrain").removeClass("active");
      //remove the auto refresh
      clearInterval(weatherrainrefresh);
  }       
}
//adds a new weather radar layer on to the map
function showWeatherClouds(){
  //if the weatherradar is currently active then disable it, otherwise enable it
  if (!$(".weatherclouds").hasClass("active"))
  {
    //loads the function to load the weather radar
    loadWeatherClouds();
    //automatically refresh radar every 2 minutes
    weathercloudsrefresh = setInterval(function(){loadWeatherClouds()}, 120000);
    //add the active class
    $(".weatherclouds").addClass("active");
  } else {
      //remove the weather radar layer
      map.removeLayer(weatherclouds);
      //remove the active class
      $(".weatherclouds").removeClass("active");
      //remove the auto refresh
      clearInterval(weathercloudsrefresh);
  }       
}

//adds a new weather radar layer on to the map
function showWeatherRadar(){
  //if the weatherradar is currently active then disable it, otherwise enable it
  if (!$(".weatherradar").hasClass("active"))
  {
    //loads the function to load the weather radar
    loadWeatherRadar();
    //automatically refresh radar every 2 minutes
    weatherradarrefresh = setInterval(function(){loadWeatherRadar()}, 120000);
    //add the active class
    $(".weatherradar").addClass("active");
  } else {
      //remove the weather radar layer
      map.removeLayer(weatherradar);
      //remove the active class
      $(".weatherradar").removeClass("active");
      //remove the auto refresh
      clearInterval(weatherradarrefresh);
  }       
}

//actually loads the weather radar
function loadWeatherPrecipitation()
{
    if (weatherprecipitation)
    {
      //remove the weather radar layer
      map.removeLayer(weatherprecipitation);  
    }
    
    weatherprecipitation = L.tileLayer('http://{s}.tile.openweathermap.org/map/precipitation/{z}/{x}/{y}.png', {
	attribution: 'Map data © OpenWeatherMap',
        maxZoom: 18,
        transparent: true,
        opacity: '0.7'
    }).addTo(map);
}
//actually loads the weather radar
function loadWeatherRain()
{
    if (weatherrain)
    {
      //remove the weather radar layer
      map.removeLayer(weatherrain);
    }
    
    weatherrain = L.tileLayer('http://{s}.tile.openweathermap.org/map/rain/{z}/{x}/{y}.png', {
	attribution: 'Map data © OpenWeatherMap',
        maxZoom: 18,
        transparent: true,
        opacity: '0.7'
    }).addTo(map);
}
//actually loads the weather radar
function loadWeatherClouds()
{
    if (weatherclouds)
    {
      //remove the weather radar layer
      map.removeLayer(weatherclouds);
    }
    
    weatherclouds = L.tileLayer('http://{s}.tile.openweathermap.org/map/clouds/{z}/{x}/{y}.png', {
	attribution: 'Map data © OpenWeatherMap',
        maxZoom: 18,
        transparent: true,
        opacity: '0.6'
    }).addTo(map);
}
//actually loads the weather radar
function loadWeatherRadar()
{
    if (weatherradar)
    {
      //remove the weather radar layer
      map.removeLayer(weatherradar);  
    }
    
    weatherradar = L.tileLayer('http://mesonet.agron.iastate.edu/cache/tile.py/1.0.0/nexrad-n0q-900913/{z}/{x}/{y}.png?' + parseInt(Math.random()*9999), {
        format: 'image/png',
        transparent: true,
        opacity: '0.5'
    }).addTo(map);
}

//adds a new weather satellite layer on to the map
function showWeatherSatellite(){
  //if the weatherradar is currently active then disable it, otherwise enable it
  if (!$(".weathersatellite").hasClass("active"))
  {
    //loads the function to load the weather satellite
    loadWeatherSatellite();
    //automatically refresh satellite every 2 minutes
    weathersatelliterefresh = setInterval(function(){loadWeatherSatellite()}, 120000);
    //add the active class
    $(".weathersatellite").addClass("active");
  } else {
      //removes the weather satellite layer
      map.removeLayer(weathersatellite);
      //remove the active class
      $(".weathersatellite").removeClass("active");
      //remove the auto refresh
      clearInterval(weathersatelliterefresh);
  }       
}

//actually loads the weather satellite
function loadWeatherSatellite()
{
    if (weathersatellite)
    {
      //remove the weather satellite layer
      map.removeLayer(weathersatellite);  
    }
    
    weathersatellite = L.tileLayer('http://mesonet.agron.iastate.edu/cache/tile.py/1.0.0/goes-east-vis-1km-900913/{z}/{x}/{y}.png?' + parseInt(Math.random()*9999), {
        format: 'image/png',
        transparent: true,
        opacity: '0.65'
    }).addTo(map);
}

//zooms in the map
function zoomInMap(){
  var zoom = map.getZoom();
  map.setZoom(zoom + 1);
}

//zooms in the map
function zoomOutMap(){
  var zoom = map.getZoom();
  map.setZoom(zoom - 1);
}

//figures out the user's location
function getUserLocation(){
  //if the geocode is currently active then disable it, otherwise enable it
  if (!$(".geocode").hasClass("active"))
  {
    //add the active class
    $(".geocode").addClass("active");
    //check to see if geolocation is possible in the browser
    if (navigator.geolocation) {
        //gets the current position and calls a function to make use of it
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        //if the geolocation is not supported by the browser let the user know
        alert("Geolocation is not supported by this browser.");
        //remove the active class
        $(".geocode").removeClass("active");
    }
  } else {
    //remove the user location marker
    removeUserPosition();
  }
}

//plots the users location on the map
function showPosition(position) {
    //creates a leaflet marker based on the coordinates we got from the browser and add it to the map
    var markerUser = L.marker([position.coords.latitude, position.coords.longitude], {
        title: "Your location",
        alt: "Your location",
        icon: L.icon({
          iconUrl: '/images/map-user.png',
          iconRetinaUrl: '/images/map-user@2x.png',
          iconSize: [40, 40],
          iconAnchor: [20, 40]
        })
    });
    user.addLayer(markerUser);
    map.addLayer(user);
    //pan the map to the users location
    map.panTo([position.coords.latitude, position.coords.longitude]);
}

//removes the user postion off the map
function removeUserPosition(){
  //remove the marker off the map
  map.removeLayer(user);
  //remove the active class
  $(".geocode").removeClass("active");
}

//determines the users heading based on the iphone
function getCompassDirection(){

  //if the compass is currently active then disable it, otherwise enable it
  if (!$(".compass").hasClass("active"))
  {
    //add the active class
    $(".compass").addClass("active");
    //check to see if the device orietntation event is possible on the browser
    if (window.DeviceOrientationEvent) {
      //first lets get the user location to mak it more user friendly
      getUserLocation();
      //disable dragging the map
      map.dragging.disable();
      //disable double click zoom
      map.doubleClickZoom.disable();
      //disable touch zoom
      map.touchZoom.disable();
      //add event listener for device orientation and call the function to actually get the values
      window.addEventListener('deviceorientation', capture_orientation, false);
    } else {
      //if the browser is not capable for device orientation let the user know
      alert("Compass is not supported by this browser.");
      //remove the active class
      $(".compass").removeClass("active");
    }
  } else {
    //remove the event listener to disable the device orientation
    window.removeEventListener('deviceorientation', capture_orientation, false);
    //reset the orientation to be again north to south
    $("#live-map").css({ WebkitTransform: 'rotate(360deg)'});
    $("#live-map").css({'-moz-transform': 'rotate(360deg)'});
    $("#live-map").css({'-ms-transform': 'rotate(360deg)'});
    //remove the active class
    $(".compass").removeClass("active");
    //remove the user location marker
    removeUserPosition();
    //enable dragging the map
    map.dragging.enable();
    //enable double click zoom
    map.doubleClickZoom.enable();
    //enable touch zoom
    map.touchZoom.enable();
  }

}

//gets the users heading information
function capture_orientation (event) {
 //store the values of each of the recorded elements in a variable
   var alpha;
   var css;
    //Check for iOS property
    if(event.webkitCompassHeading) {
      alpha = event.webkitCompassHeading;
      //Rotation is reversed for iOS
      css = 'rotate(-' + alpha + 'deg)';
    }
    //non iOS
    else {
      alpha = event.alpha;
      webkitAlpha = alpha;
      if(!window.chrome) {
        //Assume Android stock and apply offset
        webkitAlpha = alpha-270;
        css = 'rotate(' + alpha + 'deg)';
      }
    }    
  
  //we use the "alpha" variable for the rotation effect
  $("#live-map").css({ WebkitTransform: css});
  $("#live-map").css({'-moz-transform': css});
  $("#live-map").css({'-ms-transform': css});
}
