var placeSearch, autocomplete;
var componentForm = {
	//street_number: 'short_name',
	//route: 'long_name',
	locality: 'long_name',
	administrative_area_level_1: 'short_name',
	country: 'short_name',
	postal_code: 'short_name'
};
  
var map;
var marker;
var myLatLng; 
var mapOtions; 

var lat;
var lng;
  
function initMap() {
	
	lat = parseFloat( jQuery('#et_meta_latitud').val() );
	lng = parseFloat( jQuery('#et_meta_longitud').val() );
		
	if( isNaN(lat) ) {
        lat = 0;
    }
	
	if( isNaN(lng) ) {
        lng = 0;
    }
	
	myLatLng = { lat: lat , lng: lng };
	
	if ( isNaN(lat) && isNaN(lng) ) {
		//jQuery('#table_address').hide();
	} else {
		jQuery('#table_address').show();
	}
	
	var input_ciudad 	= document.getElementById('locality');
	var input_direccion	= document.getElementById('main_address');
	var input_pais 		= jQuery('#country').val();
	var map_canvas 		= document.getElementById('map');
	
	mapOtions = {
		zoom: 13,
		center: myLatLng
	}
	
	// creo el mapa
	map = new google.maps.Map( map_canvas , mapOtions );
    
    // agrego el marker
    marker = new google.maps.Marker({
		map: map,
		draggable:true,
		position: myLatLng
    });
    
    marker.setPosition(myLatLng);
    map.setCenter(myLatLng);
    
    // agrego listener
    marker.addListener('drag', handleEvent);
	marker.addListener('dragend', handleEvent);
	
	// disable enter
	google.maps.event.addDomListener(input_direccion, 'keydown', function(e) { 
		if (e.keyCode == 13) { 
			e.preventDefault(); 
		}
	}); 
	
	// cambio la limitante de la ciudad al cambiar pais
	jQuery('#country').on('change', function() {
		
		input_pais = jQuery(this).val();
		jQuery("#locality").val("");
		jQuery("#main_address").val("");
		jQuery("#administrative_area_level_1").val("");
		jQuery("#postal_code").val("");
		
		autocomplete.setComponentRestrictions({ country: input_pais });
		
	});

    autocomplete = new google.maps.places.Autocomplete( input_direccion , {types: ['geocode']} );
	autocomplete.addListener('place_changed', fillInAddress);
	
    // When the user selects an address from the dropdown, populate the address
    // fields in the form.
    
    //autocomplete.bindTo('bounds', map);
}
  
function handleEvent(event) {
		
	document.getElementById('et_meta_latitud').value = event.latLng.lat();
	document.getElementById('et_meta_longitud').value = event.latLng.lng();

}
  
// para llenar la info
function fillInAddress() {
    // Get the place details from the autocomplete object.
    var place = autocomplete.getPlace();
	
	for (var component in componentForm) {
		document.getElementById(component).value = '';
		document.getElementById(component).disabled = false;
    }
	
	
    // Get each component of the address from the place details
    // and fill the corresponding field on the form.
    for (var i = 0; i < place.address_components.length; i++) {
		
		var addressType = place.address_components[i].types[0];
		
		if (componentForm[addressType]) {
				
			var val = place.address_components[i][componentForm[addressType]];
			document.getElementById(addressType).value = val;
	
		}
		
    }
    
    var latlng = new google.maps.LatLng( place.geometry.location.lat() , place.geometry.location.lng() );
	
	console.log(latlng);
	
    map.setCenter(latlng);
    marker.setPosition(latlng);
    
    document.getElementById('et_meta_latitud').value = place.geometry.location.lat();
    document.getElementById('et_meta_longitud').value = place.geometry.location.lng();
     
}

// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function geolocate() {
    if (navigator.geolocation) {
		
		navigator.geolocation.getCurrentPosition(function(position) {
			var geolocation = {
				lat: position.coords.latitude,
				lng: position.coords.longitude
        	};
        
            var circle = new google.maps.Circle({
				center: geolocation,
				radius: position.coords.accuracy
            });
        
			autocomplete.setBounds(circle.getBounds());
		
		});
    } else {
        
        console.log("no se pudo obtener locacion");
        
    }
}