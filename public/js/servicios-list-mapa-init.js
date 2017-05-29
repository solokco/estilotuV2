// Note: This example requires that you consent to location sharing when
// prompted by your browser. If you see the error "The Geolocation service
// failed.", it means you probably did not give permission for the browser to
// locate you.

var servicios_repetidos = new Array();
var icono_original;
var categoria = "";

function initMap() {
	// inicializo el mapa
	var map = new google.maps.Map(document.getElementById('map'), {
		center: {lat: 10.4969672, lng: -66.8874203},
		zoom: 14
	});
	
	/* ******************************** */
	// Creo las burbujas de cada marker
	/* ******************************** */
	var infowindow = new google.maps.InfoWindow({ maxWidth: 280	});
	/* ******************************** */
	
	/* ******************************** */
	// BUSCADOR
	/* ******************************** */
	var buscador = document.getElementById('pac-input');
	var searchBox = new google.maps.places.SearchBox(buscador);
	//var autocomplete = new google.maps.places.Autocomplete(buscador);
	
	// Desabilitar el ENTER de la busqueda
	jQuery('form').keypress(function(e) { 
		return e.keyCode != 13;
	});
	/* ******************************** */
	
	/* ******************************** */
	// Iconos de las categorias
	/* ******************************** */
	var iconBase = 'https://maps.google.com/mapfiles/kml/shapes/';
	var icons = {					
		'entrenamiento-y-fisioterapia': {
			name: 'Entretamiento',
			icon: '/wp-content/uploads/2016/11/icon-training-32.png'
		},

		'nutricion-y-alimentacion': {
			name: 'Nutricion',
			icon: '/wp-content/uploads/2016/11/icon-nutrition-32.png'
		},
		
		'fisioterapia': {
			name: 'Fisioterapia',
			icon: '/wp-content/uploads/2016/11/icon-fisio-32.png'
		},
							
		general: {
			name: 'General',
			icon: '/wp-content/uploads/2016/10/Marker_ET_32.png'
			//icon: '/wp-content/uploads/2015/03/Marker_ET_64.png'
		}
	};
	/* ******************************** */
	
	var markers = [];
	
	/* ******************************** */
	// CREO LA LEYENDA EN EL MAPA
	/* ******************************** */
	var legend_categories = document.getElementById('legend_categories');
    for (var key in icons) {
      var type = icons[key];
      var name = type.name;
      var icon = type.icon;
      var div = document.createElement('div');
      div.innerHTML = '<img src="' + icon + '"> ' + name;
      legend_categories.appendChild(div);
    }

    map.controls[google.maps.ControlPosition.RIGHT_TOP].push(legend_categories);
    
    var legend_distancia = document.getElementById('legend_distancia');
    map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(legend_distancia);
    /* ******************************** */
    
	/* ******************************** */
	/* CREO LOS CIRCULOS DEL MAPA */
	/* ******************************** */
	function crear_circulos () {
		
		var radiusCircle_4KM = new google.maps.Circle({
		      strokeColor: '#ad5151',
		      strokeOpacity: 0.8,
		      strokeWeight: 2,
		      fillColor: '#ad5151',
		      fillOpacity: 0.105,
		      map: map,
		      center: map.getCenter(),
		      radius: 4000
	    });
	    
	    var radiusCircle_2KM = new google.maps.Circle({
	      strokeColor: '#ffdf31',
	      strokeOpacity: 0.8,
	      strokeWeight: 2,
	      fillColor: '#ffdf31',
	      fillOpacity: 0.15,
	      map: map,
	      center: map.getCenter(),
	      radius: 2000
	    });
	    
	    var radiusCircle_1KM = new google.maps.Circle({
	      strokeColor: '#50b848',
	      strokeOpacity: 0.8,
	      strokeWeight: 2,
	      fillColor: '#50b848',
	      fillOpacity: 0.25,
	      map: map,
	      center: map.getCenter(),
	      radius: 1000
	    });
		
	}
	/* ******************************** */
	
	/* ******************************** */
	/* CONSEGUIR LA POSICION DEL USUARIO */
	/* ******************************** */
	function position_actual (pos) {
		var crd = pos.coords;
							
		var the_pos = {
			lat: crd.latitude ,
			lng: crd.longitude
		};
			
		map.panTo(the_pos);	
		
		crear_circulos();
		
		var marker = new google.maps.Marker({
			position: map.getCenter(),
			//icon: mapicon,
			map: map
		});
		
		ajax_mapa ();
	};
	
	function error_posicion_actual(error_posicion_actual) {
		
		ajax_mapa ();
	};
	
	
	// Try HTML5 geolocation.
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition( position_actual , error_posicion_actual); 

	} else {
		// Browser doesn't support Geolocation
		handleLocationError(false, infoWindow, map.getCenter());
	}
	/* ******************************** */
	
	/* ******************************** */
	/* AJAX MAPA */
	/* ******************************** */
	function ajax_mapa () {		        	
    	var center = map.getCenter();
			
		latitud 	= center.lat();
		longitud 	= center.lng();
												    
	    jQuery.ajax({
	        url: ajax_object.ajax_url,
	        type: "POST",
	        //dataType: "JSON",
	        
	        data: { 
				'action'	: 'closest_services',
				'latitud'	: latitud,
				'longitud'	: longitud,
				'categoria' : categoria,
				'servicios_repetidos' : servicios_repetidos,
				'unit'		: 'km',
				'radius'	: 10
            },      
	         
	        success: function( data, textStatus, jqXHR ) { // Si todo salio bien se ejecuta esto
				
				var servicios = [];
				var precio;
				var moneda;
				var url_imangen;
				var html_marker;
				
				jQuery.each( data, function( id_servicio, info_servicio ) {

					servicios_repetidos.push(id_servicio);
					
					if( jQuery("#lista_servicios_geolocation").length != 0) { 
						
						/* ************************************ */
						/* AGREGO EL MARKER DE CADA SERVICIO */
						/* ************************************ */
						servicio = {
						    position : new google.maps.LatLng( info_servicio.meta_fields.et_meta_latitud[0] , info_servicio.meta_fields.et_meta_longitud[0] ),
						    type: info_servicio.category[0].slug,
						    info_servicio: info_servicio
						};

						addMarker(servicio);
						/* ************************************ */
						
						
						/* ************************************ */
						/* AGREGO EL CADA SERVICIO */
						/* ************************************ */										
						if ( info_servicio.precio_servicio_visibilidad == 'public') {
							precio = info_servicio.meta_fields.et_meta_precio[0];
							moneda = info_servicio.meta_fields.et_meta_precio_moneda[0];
						} else {
							precio = "-"
							moneda = "";
						}
						
						if (info_servicio.thumb_url != false) {
							url_imangen = info_servicio.thumb_url[0];
						} else {	
							url_imangen ='https://www.estilotu.com/wp-content/uploads/2016/11/estilotu-imageplaceholder-2.png';
						}
						
						html =  "<div class='listing-card-wrapper small-12 medium-6 large-6 columns' id='card_"+ id_servicio +"'>";
						html += 	"<div class='listing' itemscope='' itemtype='http://schema.org/Enumeration'>";
						
						html +=			"<div class='panel-image listing-img listing-img--hide-carousel-controls'>";
						
						html += 			"<div>";
						
						html += 				"<a href='"+ info_servicio.service_url +"' class='media-photo media-cover' target='_blank' aria-hidden='false'>";
						
						html +=						"<div class='listing-img-container media-cover text-center'>";
						html +=							"<img src='"+ url_imangen +"' itemprop='image' class='img-responsive-height'>";
						html +=						"</div>";
						
						html += 				"</a>";
						
						html += 				"<a href='"+ info_servicio.service_url +"' target='_blank' >";
						html += 					"<div class='panel-overlay-bottom-left panel-overlay-label panel-overlay-listing-label'>";
						html += 						"<div class='price-label'>";
						html += 							"<span>";
						html += 								"<sup class='currency-prefix'>"+ moneda +"</sup>";
						html += 								"<span class='price-amount'>"+ precio +"</span>";
						html += 								"<span></span>";
						html += 							"</span>";
						html += 						"</div>";
						html += 					"</div>";
						html += 				"</a>";

						html += 			"</div>";
						html += 		"</div>";
						
						
						html +=			"<div class='panel-body panel-card-section'>";
						html +=				"<div class='media'>";
						
						html +=					"<a target='_blank' href='"+ info_servicio.service_author_url +"' class='pull-right media-photo-badge card-profile-picture card-profile-picture-offset' rel='nofollow' >";
						html +=						"<div class='media-photo media-round'>";
						html +=							info_servicio.service_author_avatar;
						html +=						"</div>";
						html +=					"</a>";
						
						html +=					"<h3 title='"+ info_servicio.service_title +"' class='h5 listing-name text-truncate space-top-1'>";
						html +=						"<a href='"+ info_servicio.service_url +"' target='_blank' class='text-normal'>";
						html +=							"<span class='listing-name--display'>"+ info_servicio.service_title +"</span>";
						html +=						"</a>";
						html +=					"</h3>";
						
						html +=					"<h6 title='"+ info_servicio.category[0]['name'] +"' class='h6 listing-name text-truncate space-top-1'>";
						html +=						"<a href='"+ info_servicio.service_url +"' target='_blank' class='text-normal'>";
						html +=							"<span class='listing-category--display'>"+ info_servicio.category[0]['name'] +"</span>";
						html +=						"</a>";
						html +=					"</h6>";
						
						
						html += 			"</div>";

						html += 		"</div>";
						
						html += 	"</div>";
						html += "</div>";
						
						jQuery("#lista_servicios_geolocation").prepend(html);
					}
						
				});
											
			}
        })
        
        .fail(function( jqXHR, textStatus, errorThrown, data ) { // Si todo salio MAL se ejecuta esto
			console.log('Ocurrio un error y no se pudo procesar su solicitud correctamente.');
			
			html = "<h2>Algo no salió bien.</h2>";
			
			jQuery("#lista_servicios_geolocation").append(html);

        });
    }
    /* ******************************** */
   
   jQuery("body").on("mouseenter", ".listing-card-wrapper" , function () {
		var id_card = jQuery(this).attr('id');
		var id = id_card.split("_");

		changeMarker(id[1] , "seleccionado");
	});
	
	jQuery("body").on("mouseleave", ".listing-card-wrapper" , function () {
		var id_card = jQuery(this).attr('id');
		var id = id_card.split("_");
		
		changeMarker( id[1] , "desecleccionado" );
	});
    
    function changeMarker(record_id , accion ){
	    
	    if ( accion == "seleccionado" ) {
	    				    
		    for (i in markers){
			    
		        if( markers[i].record_id == "marker_" + record_id ) {
					
					icono_original = markers[i].icon;
					
					markers[i].setIcon('http://maps.google.com/mapfiles/kml/pushpin/purple-pushpin.png');
					
					var latLng = markers[i].getPosition();
					map.panTo(latLng);

		            return false;
		        }
		    }
		    
		    
		} else {
			
			markers[i].setIcon(icono_original);
			
		}
	}
    
    /* ******************************** */
	/* ESCUCHA CUANDO EL MAPA CAMBIA */
	/* ******************************** */
	/*
    map.addListener('idle', function() {
	    searchBox.setBounds(map.getBounds() );
	    
	    var idleTimeout = window.setTimeout( ajax_mapa , 2000);

		google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
			window.clearTimeout(idleTimeout);
		});


	});
	*/
    /* ******************************** */
    
    /* ******************************** */
	/* ESCUCHA CUANDO EL MAPA CAMBIA PARA SABER LOS MARKERS VISIBLES */
	/* ******************************** */
	/*
    google.maps.event.addListener(map, 'bounds_changed', function() {
        
        id_elemento = "";
        
        for (var i = 0; i < markers.length; i++) {
            
            id_elemento = markers[i].record_id.split("_");
            id_elemento = id_elemento[1];
            
            if (map.getBounds().contains(markers[i].getPosition())) {
                // markers[i] in visible bounds			                
                jQuery( "#card_" + id_elemento ).removeClass("invisible").addClass("visible");
                
            } 
            else {
                // markers[i] is not in visible bounds
                jQuery( "#card_" + id_elemento ).removeClass("visible").addClass("invisible");
            }
        }
    });
	*/
    /* ******************************** */
    
    // [START region_getplaces]
	// Listen for the event fired when the user selects a prediction and retrieve
	// more details for that place.
	searchBox.addListener('places_changed', function() {
							
		var places = searchBox.getPlaces();
		
		if (places.length == 0) {
		  return;
		}
		
		// Clear out the old markers.
		/*
		markers.forEach(function(marker) {
		  marker.setMap(null);
		});
		markers = [];
		*/
		
		// For each place, get the icon, name and location.
		var bounds = new google.maps.LatLngBounds();
		
		places.forEach(function(place) {
		  var icon = {
		    url: place.icon,
		    size: new google.maps.Size(71, 71),
		    origin: new google.maps.Point(0, 0),
		    anchor: new google.maps.Point(17, 34),
		    scaledSize: new google.maps.Size(25, 25)
		  };
		
		  // Create a marker for each place.
		  markers.push(new google.maps.Marker({
		    map: map,
		    icon: icon,
		    title: place.name,
		    position: place.geometry.location
		  }));
		
		  if (place.geometry.viewport) {
		    // Only geocodes have viewport.
		    bounds.union(place.geometry.viewport);
		  } else {
		    bounds.extend(place.geometry.location);
		  }
		});
		map.fitBounds(bounds);
		
		ajax_mapa ();
	});
	// [END region_getplaces]
    
    /* ******************************** */
	/* AL CAMBIAR UNA CATEGORIA */
	/* ******************************** */
    jQuery("body").on("change", "#lista-categoria" , function () {
        
        categoria = jQuery(this).val();
        
        servicios_repetidos = [];

        markers.forEach(function(marker) {
		  marker.setMap(null);
		});
		markers = [];
        			        
        jQuery("#lista_servicios_geolocation").empty();
        
        ajax_mapa ();
        
    });
	/* ******************************** */
    
    /* ******************************** */
	/* AGREGO LOS MARKERS DEL MAPA */
	/* ******************************** */
    function addMarker(servicio) {
		
		var mapicon;
		
		switch(servicio.type) {
			
			case 'entrenamiento-y-fisioterapia':
				mapicon = '/wp-content/uploads/2016/11/location-icon-training-64.png';
				break;
			
			case 'nutricion-y-alimentacion':
				mapicon = '/wp-content/uploads/2016/11/location-icon-nutrition-64.png';
				break;
			
			default:
				//mapicon = '/wp-content/uploads/2016/10/Marker_ET_32.png'
				mapicon = '/wp-content/uploads/2015/03/Marker_ET_64.png';
				//mapicon = 'http://maps.google.com/mapfiles/kml/pushpin/purple-pushpin.png';
		}			
							
		var marker = new google.maps.Marker({
			position: servicio.position,
			icon: mapicon,
			record_id: "marker_" + servicio.info_servicio.service_ID,
			animation: google.maps.Animation.DROP,
			map: map
		});
		
		markers.push(marker);

		google.maps.event.addListener(marker, 'click', function(){
	        
	        if ( servicio.info_servicio.precio_servicio_visibilidad == 'public') {
				precio = servicio.info_servicio.meta_fields.et_meta_precio[0];
				moneda = servicio.info_servicio.meta_fields.et_meta_precio_moneda[0];
			} else {
				precio = "-"
				moneda = "";
			}
	        
	        html_marker = 	"<div class='marker_content listing-map-popover'>";

	        html_marker += 		"<div class='marker_service_image panel-image listing-img listing-img--hide-carousel-controls'>";
	        html_marker +=			"<a target='_blannk' href='"+ servicio.info_servicio.service_url  +"'>";
	        html_marker +=				"<img class='img-responsive-height' itemprop='image' src='"+ servicio.info_servicio.thumb_url[0] +"'>";
	        html_marker +=			"</a>";
	        
	        html_marker +=			"<a target='_blannk' href='"+ servicio.info_servicio.service_url  +"'>";
	        html_marker +=				"<div class='panel-overlay-bottom-left panel-overlay-label panel-overlay-listing-label'>";
			
			html_marker +=					"<div class='price-label'>";
			html_marker +=						"<span>";
			html_marker +=							"<sup class='currency-prefix'>"+ moneda +"</sup>";
			html_marker +=							"<span class='price-amount'>"+ precio +"</span>";
			html_marker +=						"</span>";
			html_marker +=					"</div>";
			
			html_marker +=				"</div>";
	        html_marker +=			"</a>";
	        
	        html_marker +=		"</div>";

	        html_marker +=		"<div class='marker_service_title'>";
	        html_marker +=			"<h3 class='h5 listing-name text-truncate space-top-1'>";
	        html_marker +=				"<a target='_blannk' href='"+ servicio.info_servicio.service_url  +"'>"+ servicio.info_servicio.service_title +"</a>";
	        html_marker +=			"</h3>";
	        html_marker +=		"</div>";

	        html_marker +=	"</div>";

	        infowindow.close(); // Close previously opened infowindow
	        infowindow.setContent( html_marker );
	        infowindow.open(map, marker);
	    });
	}
	/* ******************************** */
    
}

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
	infoWindow.setPosition(pos);
	infoWindow.setContent(browserHasGeolocation ?
	'Error: The Geolocation service failed.' :
	'Error: Your browser doesn\'t support geolocation.');
}