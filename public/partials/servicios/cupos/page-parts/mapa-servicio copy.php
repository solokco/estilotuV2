<?php
/** 
 * Displays Service info
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */
global $tipo;
global $servicio_meta;

if ($tipo == "cupos" || $tipo == "evento" ): 
					
	if ( isset($servicio_meta['et_meta_pais'][0]) )
		$pais = $servicio_meta['et_meta_pais'][0];
	
	if ( isset($servicio_meta['et_meta_ciudad'][0]) )
		$ciudad = $servicio_meta['et_meta_ciudad'][0];
	
	if ( isset($servicio_meta['et_meta_estado'][0]) )
		$estado = $servicio_meta['et_meta_estado'][0];
	
	if ( isset($servicio_meta['et_meta_direccion'][0]) )
		$calle = $servicio_meta['et_meta_direccion'][0];
		
	if ( isset($servicio_meta['et_meta_longitud'][0]) )
		$longitud = $servicio_meta['et_meta_longitud'][0];
		
	if ( isset($servicio_meta['et_meta_latitud'][0]) )
		$latitud = $servicio_meta['et_meta_latitud'][0];
	
	if ( isset($servicio_meta['et_meta_zipcode'][0]) )
		$zipcode = $servicio_meta['et_meta_zipcode'][0];			
	
	if ( isset($servicio_meta['et_meta_usar_mapa'][0]) )
		$usar_mapa = $servicio_meta['et_meta_usar_mapa'][0];
	
	if ( $latitud && $longitud )
		$direccion = $latitud . "," . $longitud;
	else
		$direccion = $calle . ", " . $estado . ", " . $ciudad . ", " . $pais . ", " . $zipcode ;
	
	$direccion = wp_strip_all_tags($direccion, true);

	?>
		
	<section class="container-wrap">
		<div class="container">
			<div class="kleo-gap" style="height:30px;line-height:30px;"></div>
			
			<?php if ( $usar_mapa || $usar_mapa === null ): ?>
			
				<div class="hr-title hr-long"><abbr>Ubicación del servicio</abbr></div>
				<br />
				
				<div class="panel panel-default panel-toggle  icons-to-right">
					<div class="panel-heading">
						<div class="panel-title">
							<a href="#acc-1-2-d" data-parent="#accordion-1" data-toggle="collapse" class="accordion-toggle">
								Ver ubicaci&oacute;n en 
								<b>el Mapa</b>
								<span class="icon-closed icon-plus-1 hide"></span> 
								<span class="icon-opened icon-minus-1"></span>
							</a>
						</div>
					</div>
					
					<div id="acc-1-2-d" class="panel-collapse in" style="height: auto;">
						<div class="panel-body">
							<div id="direccion_escrita"?
								<?php 
								if ( isset($servicio_meta['et_meta_direccion'][0]) )
									echo "<p>".$calle."</p>";
								
								echo "<p>";
								if ( isset($servicio_meta['et_meta_estado'][0]) )
									echo $estado . ", ";
								
								if ( isset($servicio_meta['et_meta_ciudad'][0]) )
									echo $ciudad . ", ";
									
								if ( isset($servicio_meta['et_meta_pais'][0]) )
									echo $pais . ", ";
									
								if ( isset($servicio_meta['et_meta_zipcode'][0]) )
									echo $zipcode;	
								echo "</p>";	
								?>
							</div>
							

							<script>
							var geocoder;
							var map;
							var image_et = 'http://estilotu.com/wp-content/uploads/2015/03/Marker_ET_64.png';
							var destino;
							var origen;
							
							
							function initMap() {
								
								var directionsService = new google.maps.DirectionsService;
								var directionsDisplay = new google.maps.DirectionsRenderer;
								
								// inicializo el mapa
								map = new google.maps.Map(document.getElementById('map'), {
									center: {lat: <?php echo $servicio_meta['et_meta_latitud'][0]; ?> , lng: <?php echo $servicio_meta['et_meta_longitud'][0] ?> },
									zoom: 14
								});
								
								directionsDisplay.setMap(map);
								
								// Pongo marca del servicio
								var marker = new google.maps.Marker({
					                map: map,
					                icon: image_et,
					                position: {lat: <?php echo $servicio_meta['et_meta_latitud'][0]; ?> , lng: <?php echo $servicio_meta['et_meta_longitud'][0] ?> }
					            });
					            
					            destino = new google.maps.LatLng(<?php echo $servicio_meta['et_meta_latitud'][0]; ?> , <?php echo $servicio_meta['et_meta_longitud'][0] ?>);
					            
					            // Pongo marca de la ubicacion de la persona
					            navigator.geolocation.getCurrentPosition(function(position) {
									origen = {
										lat: position.coords.latitude,
										lng: position.coords.longitude
									};
									
									var marker = new google.maps.Marker({
						                map: map,
						                position: origen
						            });
									
									origen = new google.maps.LatLng(position.coords.latitude , position.coords.longitude);
									
									calculateAndDisplayRoute(directionsService, directionsDisplay);
								});
								
								
							}
							
							function calculateAndDisplayRoute(directionsService, directionsDisplay) {

								directionsService.route({
									origin: origen,
									destination: destino,
									travelMode: 'DRIVING',
									drivingOptions: {
										departureTime: new Date( Date.now() ),
										trafficModel: 'pessimistic'
									}
								}, function(response, status) {
										if (status === 'OK') {
											directionsDisplay.setDirections(response);
										} else {
											window.alert('Hemos tenido un error consiguiendo la direccion de origen para generar la ruta: ' + status);
										}
									}
								);
							}
						
						    </script>
						    
						    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCS2GPxD9swlsRYSFrVcLjDPIseSECsCJs&callback=initMap"></script>
							
							<style>
								#map img {max-width: none;}
							</style>
							
							<div id="map" style="height:500px;"></div>
														
						</div>
					</div>
				</div>
			<?php endif; ?>
			
		</div>
	</section>

	<br>
	
<?php endif; ?>