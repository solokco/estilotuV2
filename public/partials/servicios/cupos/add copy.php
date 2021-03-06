<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       mingoagency.com
 * @since      1.0.0
 *
 * @package    Estilotu
 * @subpackage Estilotu/public/partials
 */
?>

<script>

jQuery(document).ready(function( $ ) {	
	
	//jQuery time
	var current_fs, next_fs, previous_fs; //fieldsets
	var left, opacity, scale; //fieldset properties which we will animate
	var animating; //flag to prevent quick multi-click glitches
	
	jQuery("#msform").validate({
		
		rules: {
			nombre_servicio: {
				required: true, 
				minlength: 5,
				maxlength: 50
			},
			
			et_meta_precio: {
				required: true, 
				number: true
			},
			
			imagen_destacada: {
				required: {
					depends: function(element) {
						return !jQuery(".imagen_post").length > 0 ;
		        	}
				},
				
				extension: {
					param: "jpg|png",
					depends: function(element) {
						return !jQuery(".imagen_post").length > 0 ;
		        	}
				}

			},
			
			disponible: {
				required: true,
                minlength: 1
			},
			
			generalTerms: {
				required: true
			}
		},
		
		messages: {
			nombre_servicio: {
				required: "Debes agregar un nombre a tu servicio",
				minlength: jQuery.validator.format("El nombre del servicio no puede ser menor a {0} caracteres"),
				maxlength: jQuery.validator.format("El nombre del servicio no puede ser mayor a {0} caracteres")
			},
			
			et_meta_precio: {
				required: "Por favor indique el costo de su servicio"
			},
			
			imagen_destacada: {
				required: "Debes seleccionar una imagen", 
				extension: "Recuerda que el formato debe ser JPG o PNG"
			},
			
			disponible: {
				required: "Debes seleccionar por lo menos un día con cupos",
                minlength: 1
			},
			
			generalTerms: {
				required: "Debes aceptar los términos y condiciones"
			}
			
		}

/*
		errorPlacement: function(error, element) { 
	      element.addClass('error');
	    },        

	    highlight: function (element) {
	        $(element).addClass('nonvalid')
	          .closest('.form-group').removeClass('error');
	    }
*/
		
/*
		submitHandler: function(form) {
			form.submit();
		}
*/
		
	});
	
	$(".next").click(function(){
		if(animating) return false;
		animating = true;
		
		current_fs = $(this).parent();
		next_fs = $(this).parent().next();
		
		//activate next step on progressbar using the index of next_fs
		$("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
		
		//show the next fieldset
		next_fs.show(); 
		//hide the current fieldset with style
		current_fs.animate({opacity: 0}, {
			step: function(now, mx) {
				//as the opacity of current_fs reduces to 0 - stored in "now"
				//1. scale current_fs down to 80%
				scale = 1 - (1 - now) * 0.2;
				//2. bring next_fs from the right(50%)
				left = (now * 50)+"%";
				//3. increase opacity of next_fs to 1 as it moves in
				opacity = 1 - now;
				current_fs.css({'transform': 'scale('+scale+')'});
				next_fs.css({'left': left, 'opacity': opacity});
			}, 
			duration: 800, 
			complete: function(){
				current_fs.hide();
				animating = false;
			}, 
			//this comes from the custom easing plugin
			easing: 'easeInOutBack'
		});
	});
	
	$(".previous").click(function(){
		if(animating) return false;
		animating = true;
		
		current_fs = $(this).parent();
		previous_fs = $(this).parent().prev();
		
		//de-activate current step on progressbar
		$("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");
		
		//show the previous fieldset
		previous_fs.show(); 
		//hide the current fieldset with style
		current_fs.animate({opacity: 0}, {
			step: function(now, mx) {
				//as the opacity of current_fs reduces to 0 - stored in "now"
				//1. scale previous_fs from 80% to 100%
				scale = 0.8 + (1 - now) * 0.2;
				//2. take current_fs to the right(50%) - from 0%
				left = ((1-now) * 50)+"%";
				//3. increase opacity of previous_fs to 1 as it moves in
				opacity = 1 - now;
				current_fs.css({'left': left});
				previous_fs.css({'transform': 'scale('+scale+')', 'opacity': opacity});
			}, 
			duration: 800, 
			complete: function(){
				current_fs.hide();
				animating = false;
			}, 
			//this comes from the custom easing plugin
			easing: 'easeInOutBack'
		});
	});
	
	( function ( document, window, index )
	{
		var inputs = document.querySelectorAll( '.inputfile' );
		Array.prototype.forEach.call( inputs, function( input )
		{
			var label	 = input.nextElementSibling,
				labelVal = label.innerHTML;
	
			input.addEventListener( 'change', function( e )
			{
				var fileName = '';
				if( this.files && this.files.length > 1 )
					fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
				else
					fileName = e.target.value.split( '\\' ).pop();
	
				if( fileName )
					label.querySelector( 'span' ).innerHTML = fileName;
				else
					label.innerHTML = labelVal;
			});
	
			// Firefox bug fix
			input.addEventListener( 'focus', function(){ input.classList.add( 'has-focus' ); });
			input.addEventListener( 'blur', function(){ input.classList.remove( 'has-focus' ); });
		});
	}( document, window, 0 ));

});
</script>


<section>
	<form method="post" action="<?php the_permalink(); ?>" id="msform" enctype="multipart/form-data">
		<ul id="progressbar">
			<li class="active">Datos Básicos</li>
			<li>Opciones Reservas</li>
			<li>Ubicación</li>
			<li>Cupos</li>
			<li>Publicar</li>
		</ul>
		
		<div class="row">
			
			<div class="section col-xs-12 col-md-6">
			sdfasdf
			</div>
			
			<div class="section col-xs-12 col-md-6">
			fasdfs
			</div>	
			
		</div>
		
		<fieldset>
			
			<div class="row">
				
				<div class="section col-xs-12 col-md-6">
					
				<h2 class="fs-title">Agregar Servicio tipo Cupos</h2>
				<h3 class="fs-subtitle">Información del Servicio</h3>
				
				<input type="hidden" name="et_meta_tipo" value="cupos">
				<input type="hidden" name="et_token" value="<?php echo $this->token_id; ?>">
			
			
				
					<div class="section col-xs-12 col-md-8">
						<label for="nombre_servicio" class="field-label"> <h4>Nombre del servicio <em> * </em></h4> </label>
						<input class="required" type="text" name="nombre_servicio" id="nombre_servicio" placeholder="Nombre del servicio" maxlength="65" value="<?php echo isset($this->servicio->post_title) ? $this->servicio->post_title : '' ?>">
						<span class="small-text">El nombre no debe tener más de 50 caracteres</span>
					</div>
					
					<div class="section col-xs-12 col-md-4">
						<label for="categoria-servicio" class="field-label"> <h4>Categoria: <em> * </em></h4> </label>
						<?php 
				        $args = array(
							'show_option_all'    => '',
							'show_option_none'   => '',
							'option_none_value'  => '-1',
							'orderby'            => 'name', 
							'order'              => 'ASC',
							'show_count'         => 0,
							'hide_empty'         => 0, 
							'child_of'           => 0,
							'exclude'            => '',
							'echo'               => 1,
							'selected'           => isset($this->servicios_categoria[0]->slug) ? $this->servicios_categoria[0]->slug : '0' ,
							'hierarchical'       => 1, 
							'name'               => 'categoria_servicio',
							'id'                 => 'categoria-servicio',
							'class'              => 'categorias',
							'depth'              => 0,
							'tab_index'          => 1,
							'taxonomy'           => 'servicios-categoria',
							'hide_if_empty'      => false,
							'value_field'	     => 'slug'
							); 
				        wp_dropdown_categories( $args );
						?>
					</div>
					
				</div>
				
				<div class="section col-xs-12 col-md-6"></div>
			</div>
			
			<br>
			
			<div class="row">
				
				<div class="section col-xs-12 col-md-8">
					
					<label for="description" class="field-label"><h4>Descripci&oacute;n <em> * </em> </h4></label>
			    	<?php $content = isset($this->servicio->post_content) ? $this->servicio->post_content : '' ;
			    	
					$editor_id = 'description';
					$editro_settings = array (
						//'media_buttons' => false,
						'quicktags'		=> false,
						'wpautop'		=> false,
						'teeny'			=> true
					);
					
					wp_editor( $content, $editor_id, $editro_settings );
			    	?>
				
				</div>
				
				<div class="section col-xs-12 col-md-4">
					
					<label for="description" class="field-label"><h4>Imagen: <em> * </em></h4> </label>

					<br>
						
					<div class="box">
						<input type="file" name="imagen_destacada" accept=".jpg, .png" id="file-1" class="inputfile inputfile-1" data-multiple-caption="{count} files selected" <?php if ( empty( $this->post_id ) ) echo "required";  ?> />
						<label for="file-1"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg> <span>Seleccionar portada&hellip;</span></label>
						<span class="small-text"> Solo se permiten imágenes tipo JPG y PNG - Máximo de 1MB </span> 
					</div>
					
					<?php 
					if ( !empty( $this->post_id ) && has_post_thumbnail ( $this->post_id ) ): ?>
						<br>
						
						<h4>Imagen actual</h4>
						<div class="imagen_post">
							<?php echo ( get_the_post_thumbnail( $this->post_id , 'medium') ); ?>
						</div>
					<?php 
					endif;
					?>
	
				</div>

			</div>
			
			<input type="button" name="next" class="next action-button" value="Next" />
			
		</fieldset>
		
		<fieldset>
			<h2 class="fs-title">Opciones de reserva</h2>
			<h3 class="fs-subtitle">Datos para la reserva</h3>	
			
			<div class="row">
				<div class="section col-xs-12 col-md-6">
			        <label>D&iacute;as de anticipaci&oacute;n para aceptar reservas</label>
			        
			        <select name="et_meta_max_time" id="et_meta_max_time">
			            <option value="7" <?php selected( $max_time, 7 ); ?>>7 d&iacute;as</option>
			            <option value="14" <?php selected( $max_time, 14 ); ?>>14 d&iacute;as</option>
			            <option value="21" <?php selected( $max_time, 21 ); ?>>21 d&iacute;as</option>
			            <option value="30" <?php selected( $max_time, 30 ); ?>>30 d&iacute;as</option>
			            <option value="60" <?php selected( $max_time, 60 ); ?>>60 d&iacute;as</option>
			            <option value="90" <?php selected( $max_time, 90 ); ?>>90 d&iacute;as</option>
			            <option value="120" <?php selected( $max_time, 120 ); ?>>120 d&iacute;as</option>
			            <option value="180" <?php selected( $max_time, 180 ); ?>>180 d&iacute;as</option>
			        </select>
				</div>
				
				<div class="section col-xs-12 col-md-6">
					
					<label>Tiempo previo para cerrar las reservas</label>
					
					<select name="et_meta_close_time" id="et_meta_close_time">
			            <option value="60" <?php selected( $et_meta_close_time,  60 ); ?>>01 minuto</option>
			            <option value="300" <?php selected( $et_meta_close_time,  300 ); ?>>05 minutos</option>
			            <option value="600" <?php selected( $et_meta_close_time,  600 ); ?>>10 minutos</option>
			            <option value="900" <?php selected( $et_meta_close_time,  900 ); ?>>15 minutos</option>
			            <option value="1800" <?php selected( $et_meta_close_time,  1800 ); ?>>30 minutos</option>
			            <option value="3600" <?php selected( $et_meta_close_time,  3600 ); ?>>1 hora</option>
			            <option value="5400" <?php selected( $et_meta_close_time,  5400 ); ?>>1 hora 30 minutos</option>
			            <option value="7200" <?php selected( $et_meta_close_time,  7200 ); ?>>2 horas</option>
			            <option value="9000" <?php selected( $et_meta_close_time,  9000 ); ?> >2 horas 30 minutos</option>
			            <option value="10800" <?php selected( $et_meta_close_time, 10800 );?>>3 horas</option>
			            <option value="14400" <?php selected( $et_meta_close_time, 14400 ); ?>>4 horas </option>
			            <option value="18000" <?php selected( $et_meta_close_time, 18000 ); ?>>5 horas</option>
			            <option value="21600" <?php selected( $et_meta_close_time, 21600 ); ?>>6 horas</option>
			            <option value="28800" <?php selected( $et_meta_close_time, 28800 ); ?>>8 horas</option>
			            <option value="36000" <?php selected( $et_meta_close_time, 36000 ); ?>>10 horas</option>
			            <option value="43200" <?php selected( $et_meta_close_time, 43200 ); ?>>12 horas</option>
			            <option value="86400" <?php selected( $et_meta_close_time, 86400 ); ?>>24 horas</option>
			            <option value="172800" <?php selected( $et_meta_close_time, 172800 ); ?>>48 horas</option>
			            <option value="604800" <?php selected( $et_meta_close_time, 604800 ); ?>>1 semana</option>
			        </select>
				
				</div>

			</div>

			<br>        

	        <div class="row">
				
				<h3 class="fs-title">Costos del servicio</h3>
				
				<div class="section col-xs-12 col-md-4">
			        
			        <label>Tipo de moneda</label>

			        <select name="et_meta_precio_moneda" id="et_meta_precio_moneda">
			            <option value="VEF" <?php selected( $moneda, "VEF" ); ?>>Bolivares</option>
			            <option value="USD" <?php selected( $moneda, "USD" ); ?>>Dolares Americanos</option>
			            <option value="EU" <?php selected( $moneda, "EU" ); ?>>Euros</option>
			        </select>

				</div>
				
				<div class="section col-xs-12 col-md-4">
					
					<label>Precio</label>
					<input type="number" min="0" name="et_meta_precio" id="et_meta_precio" aria-required="true" placeholder="Precio del servicio..." value="<?php echo isset($this->servicio_meta['et_meta_precio'][0]) ? $this->servicio_meta['et_meta_precio'][0] : '' ?>" required>
					
				</div>
			     
			    <div class="section col-xs-12 col-md-4">
			    
			    	<label>Visibilidad</label>
			     
			        <select name="et_meta_precio_visibilidad" id="et_meta_precio_visibilidad">
			            <option value="public" <?php selected( $moneda_visibilidad, "public" ); ?>>Público</option>
			            <option value="private" <?php selected( $moneda_visibilidad, "private" ); ?>>Privado</option>
			            <option value="hidden" <?php selected( $moneda_visibilidad, "hidden" ); ?>>Oculto</option>
			        </select>
			    </div>
			    
	        </div>
	 
	        <br>
	        <input type="button" name="previous" class="previous action-button" value="Previous" />
			<input type="button" name="next" class="next action-button" value="Next" />
	        
		</fieldset>
		
		<fieldset>
			<h2 class="fs-title">Detalles adicionales</h2>
			<h3 class="fs-subtitle">Información de interés para tus alumnos</h3>

			<input type="checkbox" name="vehicle" value="Bike"> Baños<br>
			<input type="checkbox" name="vehicle" value="Bike"> Duchas<br>
			<input type="checkbox" name="vehicle" value="Bike"> Estacionamiento<br>
			<input type="checkbox" name="vehicle" value="Bike"> Area para niños <br>
			<input type="checkbox" name="vehicle" value="Bike"> Wifi<br>
			<input type="checkbox" name="vehicle" value="Bike"> Hidratación<br>
			<input type="checkbox" name="vehicle" value="Bike"> Cafetin<br>
			
		</fieldset>
		
		<fieldset>
			<h2 class="fs-title">Ubicación</h2>
			<h3 class="fs-subtitle">¿Cómo llegarán a tu servicio?</h3>
			
			
			<h4 class="fs-subtitle">Por favor indique la ubicación de su servicio de modo que los usuarios de EstiloTú lo puedan ubicar con facilidad.</h4>
			<input type="hidden" name="ubicacion[et_meta_usar_mapa]" id="et_meta_usar_mapa" value="1">   
			
			<div id="locationField">
				<label for="autocomplete">Escriba la dirección para ubicarla en el mapa</label>
				<input id="autocomplete" placeholder="Seleccione la dirección de la lista y completa luego los detalles abajo" onFocus="" type="text" name="direccion"></input>
			</div>
			
			<hr>
	
		    <table id="table_address">

				<tr>
					<td class="label"><?php _e("Dirección" , "estilotu") ?></td>
					<td class="slimField"><input class="field" placeholder="Numero Establecimiento" id="street_number" name="ubicacion[et_meta_direccion_1]" value="<?php echo isset( $this->servicio_meta['et_meta_direccion_1'][0] ) ? $this->servicio_meta['et_meta_direccion_1'][0] : '' ;  ?>"></input></td>
					<td class="wideField" colspan="2"><input placeholder="Calle / Avenida" class="field" id="route"  name="ubicacion[et_meta_direccion_2]<<div>div</div>></div>" value="<?php echo isset( $this->servicio_meta['et_meta_direccion_2'][0] ) ? $this->servicio_meta['et_meta_direccion_2'][0] : '' ;  ?>"></input></td>
				</tr>
				
				<tr>
					<td class="label">City</td>
					<!-- Note: Selection of address components in this example is typical.
					 You may need to adjust it for the locations relevant to your app. See
					 https://developers.google.com/maps/documentation/javascript/examples/places-autocomplete-addressform
					-->
					<td class="wideField" colspan="3"><input name="ubicacion[et_meta_ciudad]" class="field" id="locality"  value="<?php echo isset( $this->servicio_meta['et_meta_ciudad'][0] ) ? $this->servicio_meta['et_meta_ciudad'][0] : '' ;  ?>"></input></td>
				</tr>
			      
				<tr>
					<td class="label"><?php _e("Estado" , "estilotu") ?></td>
					<td class="slimField"><input name="ubicacion[et_meta_estado]" class="field" id="administrative_area_level_1"  value="<?php echo isset( $this->servicio_meta['et_meta_estado'][0] ) ? $this->servicio_meta['et_meta_estado'][0] : '' ;  ?>"></input></td>
				
					<td class="label"><?php _e("Código Postal" , "estilotu") ?></td>
					<td class="wideField"><input name="ubicacion[et_meta_zipcode]" class="field" id="postal_code"  value="<?php echo isset( $this->servicio_meta['et_meta_zipcode'][0] ) ? $this->servicio_meta['et_meta_zipcode'][0] : '' ;  ?>"></input></td>
				</tr>
	
				<tr>
					<td class="label"><?php _e("País" , "estilotu") ?></td>
					<td class="wideField" colspan="3"><input name="ubicacion[et_meta_pais]" class="field" id="country"  value="<?php echo isset( $this->servicio_meta['et_meta_pais'][0] ) ? $this->servicio_meta['et_meta_pais'][0] : '' ;  ?>"></input></td>
				</tr>

		    </table>
							
			<h3>Puedes arrastrar la marca de la ubicaci&oacute;n en el mapa para mayor presici&oacute;n</h3>
			<!-- 			<div id="map" style="height:500px;"></div> -->
			<?php 
			$mapa = new Estilotu_Geolocation_Public(); 
			
			$mapa->show_map("add");	
				
			?>
			
			<table>					
				<tr>
					<td><label for="et_meta_latitud" class="field-label">Latitud</label></td>
					<td><label for="et_meta_longitud" class="field-label">Longitud</label></td>
				</tr>
				
				<tr>
					<td><input type="text" name="ubicacion[et_meta_latitud]" id="et_meta_latitud" class="gui-input" placeholder="Ingrese la latitud" value="<?php echo isset( $this->servicio_meta['et_meta_latitud'][0] ) ? $this->servicio_meta['et_meta_latitud'][0] : '' ;  ?>" required></td>
					<td><input type="text" name="ubicacion[et_meta_longitud]" id="et_meta_longitud" class="gui-input" placeholder="Ingrese la longitud" value="<?php echo isset( $this->servicio_meta['et_meta_longitud'][0] ) ? $this->servicio_meta['et_meta_longitud'][0] : '' ;  ?>" required></td>
				</tr>
			</table>
			
			<br>
			
			
			<input type="button" name="previous" class="previous action-button" value="Previous" />
			<input type="button" name="next" class="next action-button" value="Next" />
			
			
		</fieldset>
		
		
	    <fieldset>
		    <h2 class="fs-title">Disponibilidad</h2>
			<h3 class="fs-subtitle">¿Qué días estarás disponible?</h3>
			
		    <div class="section">
		        <p class="small-text fine-grey">Seleccione los días que su servicio estará disponible </p>
		    </div><!-- end section -->
		    
		    <div id="contenedor_disponibilidad">
		       
				<?php 
				$key = 0;
					
				for ($dia = 0; $dia <= 6; $dia++): 
					
					switch ($dia) {
						
						case 0:
							$dia_txt = "lunes";
							break;
							
						case 1:
							$dia_txt = "martes";
							break;
							
						case 2:
							$dia_txt = "miercoles";
							break;
							
						case 3:
							$dia_txt = "jueves";
							break;
							
						case 4:
							$dia_txt = "viernes";
							break;
							
						case 5:
							$dia_txt = "sabado";
							break;
							
						case 6:
							$dia_txt = "domingo";
							break;
					}
					
					?>
					
					<div class='checkbox turn_switch'>
						<label><?php echo $dia_txt ?></label>
						<label class='checkbox__container'>
							<input class='checkbox__toggle ShowHideReset' type='checkbox' name="disponible[<?php echo $dia_txt ?>][activo]" id="<?php echo "check_" . $dia_txt ?>" <?php if (isset($horarios_servicio) ) echo array_key_exists( $dia , $horarios_servicio["dias_activados"] ) ? "checked" : ''; ?> >
							<span class='checkbox__checker'></span>
							<span class='checkbox__cross'></span>
							<span class='checkbox__ok'></span>
	
							<svg class='checkbox__bg' space='preserve' style='enable-background:new 0 0 110 43.76;' version='1.1' viewbox='0 0 110 43.76'>
								<path class='shape' d='M88.256,43.76c12.188,0,21.88-9.796,21.88-21.88S100.247,0,88.256,0c-15.745,0-20.67,12.281-33.257,12.281,S38.16,0,21.731,0C9.622,0-0.149,9.796-0.149,21.88s9.672,21.88,21.88,21.88c17.519,0,20.67-13.384,33.263-13.384,S72.784,43.76,88.256,43.76z'></path>
							</svg>
						</label>
					</div>
					
		            <div id="contenedor_<?php echo $dia_txt; ?>" <?php if (isset($horarios_servicio) ) echo array_key_exists( $dia , $horarios_servicio["dias_activados"] ) ? '' : 'style="display:none;"'; else echo 'style="display:none;"';  ?> >
			            
			            <?php if ( isset($horarios_servicio) && array_key_exists( $dia , $horarios_servicio["dias_activados"] ) ):  ?>
				            
				            <?php foreach( $horarios_servicio["dias_activados"][$dia] as $id_bloque => $info_bloque ): ?>
				            
					            <table class="clone_<?php echo $dia_txt; ?> clone frm-row" id="<?php echo $dia_txt . "_" . $dia . "_" . $id_bloque ?>">
									
									<tr>
										<th>Hora de inicio<em> * </em></th>
										<th>Duración<em> * </em></th>
										<th>Cupos <em> * </em></th>
									</tr>
									
									<tr>
										
										<td>
											<?php $bloque_horas = $this->crear_horario(); ?>

											<select name="disponible[<?php echo $dia_txt ?>][bloque][<?php echo $id_bloque ?>][et_meta_hora_inicio]" class="hora_inicio">
													
												<?php foreach ($bloque_horas["ampm"] as $tipo => $bloque_hora): ?>		
												
													<option value="<?php echo $bloque_horas["value"][$tipo]; ?>" <?php selected( $bloque_horas["value"][$tipo] , $info_bloque['et_meta_hora_inicio'] ); ?> ><?php echo $bloque_hora; ?></option>
												
												<?php endforeach; ?>

											</select>
										</td>
										
										<td>
											<select name="disponible[<?php echo $dia_txt ?>][bloque][<?php echo $id_bloque ?>][et_meta_duracion]" class="duracion" id="duracion_<?php echo $dia_txt . "_" . $dia . "_" . $id_bloque ?>">
										    <?php for ($i=1; $i < 21 ; $i++):
												
												$minutos = $i * 15;
												$horas = floor($minutos / 60);
												
												if ($minutos % 60 != 0)
													$cuarto_hora = ($minutos % 60) + " minutos";
												else 
													$cuarto_hora = ""; 
												
												if ($horas < 1) 
													$result = $minutos . " minutos" ; 
													
												if ($horas == 1) 
													$result = $horas . " hora " . $cuarto_hora;
											
												else if ($horas > 1) 
												 	$result = $horas . " horas " . $cuarto_hora;
												?>
												
												<option value="<?php echo $minutos ?>" <?php selected( $minutos , $info_bloque['et_meta_duracion'] ); ?>>
													<?php echo $result ?>
												</option>
												
											<?php endfor; ?>
											</select>
										</td>
										
										<td><input type='number' name="disponible[<?php echo $dia_txt ?>][bloque][<?php echo $id_bloque ?>][et_meta_cupos]" class='gui-input required et_meta_cupos' id="cupos_<?php echo $dia_txt . "_" . $dia . "_" . $id_bloque ?>" placeholder='Cupos disponibles por evento...' min="1" max="999" value="<?php echo $info_bloque['et_meta_cupos']; ?>"></td>
									</tr>
									
									<tr>
										<td colspan="3">
											<a href="#" class="clone button_action"><img src="<?php echo ESTILOTU_URL . 'public/img/plus.png'; ?>"></a>
											
											<a href="#" class="delete button_action"><img src="<?php echo ESTILOTU_URL . 'public/img/minus.png'; ?>"></a>
										</td>
									</tr>
									
								</table>
				            <?php endforeach; ?>
						
						<?php else: 
							
							$id_bloque = 0; ?>
						
							<table class="clone_<?php echo $dia_txt; ?> clone frm-row" id="<?php echo $dia_txt . "_" . $dia . "_" . $id_bloque ?>">
									
									<tr>
										<th>Hora de inicio<em> * </em></th>
										<th>Duración<em> * </em></th>
										<th>Cupos <em> * </em></th>
									</tr>
									
									<tr>
										
										<td>
											<?php $bloque_horas = $this->crear_horario(); ?>
											<select name="disponible[<?php echo $dia_txt ?>][bloque][<?php echo $id_bloque ?>][et_meta_hora_inicio]" class="hora_inicio">
													
												<?php foreach ($bloque_horas["ampm"] as $tipo => $bloque_hora): ?>		
												
													<option value="<?php echo $bloque_horas["value"][$tipo]; ?>" <?php selected( $bloque_horas["value"][$tipo] , "08:00:00" ); ?>  ><?php echo $bloque_hora; ?></option>
												
												<?php endforeach; ?>
												
												
											</select>
										</td>
										
										<td>
											<select name="disponible[<?php echo $dia_txt ?>][bloque][<?php echo $id_bloque ?>][et_meta_duracion]" class="duracion" id="duracion_<?php echo $dia_txt . "_" . $dia . "_" . $id_bloque ?>">
										    <?php for ($i=1; $i < 21 ; $i++):
												
												$minutos = $i * 15;
												$horas = floor($minutos / 60);
												
												if ($minutos % 60 != 0)
													$cuarto_hora = ($minutos % 60) + " minutos";
												else 
													$cuarto_hora = ""; 
												
												if ($horas < 1) 
													$result = $minutos . " minutos" ; 
													
												if ($horas == 1) 
													$result = $horas . " hora " . $cuarto_hora;
											
												else if ($horas > 1) 
												 	$result = $horas . " horas " . $cuarto_hora;
												?>
												
												<option value="<?php echo $minutos ?>">
													<?php echo $result ?>
												</option>
												
											<?php endfor; ?>
											</select>
										</td>
										
										<td><input type='number' name="disponible[<?php echo $dia_txt ?>][bloque][<?php echo $id_bloque ?>][et_meta_cupos]" class='gui-input required et_meta_cupos' id="cupos_<?php echo $dia_txt . "_" . $dia . "_" . $id_bloque ?>" placeholder='Cupos disponibles por evento...' min="1" max="999" value="1"></td>
									</tr>
									
									<tr>
										<td colspan="3">
											<a href="#" class="clone button_action"><img src="<?php echo ESTILOTU_URL . 'public/img/plus.png'; ?>"></a>
											
											<a href="#" class="delete button_action"><img src="<?php echo ESTILOTU_URL . 'public/img/minus.png'; ?>"></a>
										</td>
									</tr>
									
								</table>

						
						<?php endif; ?>
			            
		            </div>
		            
		        <?php endfor; ?>  
		            
		    </div><!-- end #clone-animate --> 
		    
			<input type="button" name="previous" class="previous action-button" value="Previous" />
			<input type="button" name="next" class="next action-button" value="Next" />
	    
		</fieldset>                            
		
		
		<fieldset>
			
			<h3>Por favor <a href="#"> lea y acepte </a> nuestros términos y condiciones</h3>
			
			<div class='checkbox turn_switch'>
				<label>Acepto</label>
				<label class='checkbox__container'>
					<input class='checkbox__toggle' type='checkbox' name="generalTerms" required >
					<span class='checkbox__checker'></span>
					<span class='checkbox__cross'></span>
					<span class='checkbox__ok'></span>
	
					<svg class='checkbox__bg' space='preserve' style='enable-background:new 0 0 110 43.76;' version='1.1' viewbox='0 0 110 43.76'>
						<path class='shape' d='M88.256,43.76c12.188,0,21.88-9.796,21.88-21.88S100.247,0,88.256,0c-15.745,0-20.67,12.281-33.257,12.281,S38.16,0,21.731,0C9.622,0-0.149,9.796-0.149,21.88s9.672,21.88,21.88,21.88c17.519,0,20.67-13.384,33.263-13.384,S72.784,43.76,88.256,43.76z'></path>
					</svg>
				</label>
			</div>
	
			
			
			<br>
			<input type="button" name="previous" class="previous action-button" value="Previous" />
			<input type="submit" name="submit" class="submit action-button" value="Submit" />
			
		</fieldset>
		
		<?php wp_nonce_field( 'publicar_servicio', 'publicar_servicio_nonce' ); ?>
		
	</form>
</section>