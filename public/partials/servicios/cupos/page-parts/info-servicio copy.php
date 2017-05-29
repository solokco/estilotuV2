<?php
/** 
 * Displays Service info
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */
?>
<section class="container-wrap">
	<div class="container">
		<div class="hr-title hr-long" id="reserva_contenedor"><abbr>Realiza tu cita</abbr></div>
		<?php 
		$servicio_meta = get_post_custom() ;
		$tipo = $servicio_meta['et_meta_tipo'][0];							
			
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
		
		if ($tipo == "cupos"):

			do_action('et_mostrar_calendario');

		//**************
		// EVENTO
		//**************
		elseif ($tipo == "evento"): 
			global $wpdb;
			global $current_user;

			$token_id = md5( uniqid( "", true ) );

			$meta = get_post_custom();
			
			$fecha_evento = $meta['et_date_from'][0];
			$hora_evento = $meta['inicio_horario'][0];
				
			$table_name  = $wpdb->prefix . "bb_appoinments";
			$id_provider = get_the_author_meta( 'ID' );
			$id_servicio = get_the_ID();
			
			if ( !is_user_logged_in() ): ?>
			
				<div id="mensaje_registro">
					<h2 class="Centrar">Debes iniciar tu sesi&oacute;n para poder reservar un cupo</h2>
					<h3 class="Centrar">Si no tienes una cuenta, puedes registrarte haciendo <a href="/registro">clic aqu&iacute;</a></h3>
				</div>
			
			<?php 
			else: 
				$reservado = $wpdb->get_results("SELECT appoinment_user_id FROM $table_name appoinment_service_id = '$id_servicio' AND appoinment_user_id = '$current_user->ID' "); 
																	
				if ( !empty( $reservado) )
					$reservado = true;
				else
					$reservado = false; 
					
				if (!$reservado): ?>
				
					<div style="text-align: center;"> 
						<form class="formulario_reserva_evento" id="hacer_reserva_<?php echo $id_servicio ?>" action="/reservar" method="post" onsubmit="return confirm('Por favor confirma que deseas hacer esta reserva:');">
							<input type="submit" value="Reserva tu cupo" class="button btn-morado" id="boton_reservar" name="is_reserve">
							<input type="hidden" value="evento" name="tipo_servicio">
							
							<input type="hidden" value="<?php echo $fecha_evento ?>" id="fecha_evento" name="fecha_evento">
							<input type="hidden" value="<?php echo $hora_evento ?>" id="hora_evento" name="hora_evento">
							<input type="hidden" value="<?php echo $id_provider ?>" id="id_provider" name="id_provider">
							<input type="hidden" value="<?php echo $id_servicio ?>" id="id_servicio" name="id_servicio">
							<input type="hidden" name="token" value="<?php echo $token_id; ?>" />
						</form>
					</div>
				
				<?php 
				else:
				?>
					<div id="mensaje_registro">
						<h2 class="Centrar">Usted ya tiene un cupo reservado</h2>
					</div>
				
				<?php 
				endif;
				

			endif;
		//**************
		
		//**************
		// ONLINE
		//**************
		elseif ($tipo == "online"): 
			//do_action('et_listar_citas');
			global $current_user;

			?>
			<div style="text-align: center;"> 
				<a href="<?php echo add_query_arg('id_servicio' , get_the_ID() , '/usuarios/'.$current_user->user_login.'/consultas-online/#consulta' ) ?>" class="btn btn-primary btn-lg">Hacer consulta Online</a>	
			</div>
			<?php
		endif; 
		?>
	</div>
</section>

<?php if ($tipo == "cupos" || $tipo == "evento" ): ?>
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
							
							<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDFoZZgI13xJEg7Jb2DaCiPk_ahZBb3Qqw"></script>
							<script>
								var geocoder;
								var map;
								var image_et;
								
								function codeAddress(address) {
								    geocoder = new google.maps.Geocoder();
								    geocoder.geocode({
								        'address': address
								    }, function(results, status) {
								        if (status == google.maps.GeocoderStatus.OK) {
								            var myOptions = {
								                zoom: 16,
								                center: results[0].geometry.location,
								                mapTypeId: google.maps.MapTypeId.ROADMAP
								            }
								            
								            image_et = 'http://estilotu.com/wp-content/uploads/2015/03/Marker_ET_64.png';
								            
								            map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
								
								            var marker = new google.maps.Marker({
								                map: map,
								                icon: image_et,
								                position: results[0].geometry.location
								            });
								        }
								    });
								}			
					
								google.maps.event.addDomListener(window, 'load', codeAddress( "<?php echo (string)$direccion; ?>" ) );
						
						    </script>
							
							<style>
								#map-canvas img {max-width: none;}
							</style>
							
							<div id="map-canvas" style="height:500px;"></div>
						</div>
					</div>
				</div>
			<?php endif; ?>
			
		</div>
	</section>
<?php endif; ?>