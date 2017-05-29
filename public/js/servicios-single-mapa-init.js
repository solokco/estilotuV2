function initMap() {
	
	var lat = parseFloat( php_vars[0] );
	var lng = parseFloat( php_vars[1] );
	
	var myLatLng = { lat: lat , lng: lng }; 
	
	var map_canvas = document.getElementById('map');
	
	var mapOtions = {
		zoom: 13,
		center: myLatLng
	}
	
	// creo el mapa
	var map = new google.maps.Map( map_canvas , mapOtions );
    
    // agrego el marker
    var marker = new google.maps.Marker({
		map: map,
		draggable:false,
		position: myLatLng
    });

}