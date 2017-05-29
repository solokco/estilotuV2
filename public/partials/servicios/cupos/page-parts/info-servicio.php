<?php
/** 
 * Displays Service info
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */
global $tipo;
global $servicio_meta;

wp_enqueue_style( 'et_servicios');

// *********************
// SI ES CUPO O EVENTO
// *********************
if ($tipo == "cupos" || $tipo == "evento" ): 
							
	if ( isset($servicio_meta['et_meta_precio'][0]) )
		$precio = $servicio_meta['et_meta_precio'][0];
		
	if ( isset($servicio_meta['et_meta_precio_moneda'][0]) ):
		$moneda = $servicio_meta['et_meta_precio_moneda'][0];
		
		switch ($moneda){
			case "VEF":
				$moneda = "Bsf";
			break;
			
			case "USD":
				$moneda = "$";
			break;
			
			case "EU":
				$moneda = "&euro;";
			break;
			
			default:
				$moneda = "Bsf";
		}
	
	else:

		$moneda = "Bsf";

	endif;
		
	if ( isset($servicio_meta['et_meta_precio_visibilidad'][0]) )
		$visibilidad_precio = $servicio_meta['et_meta_precio_visibilidad'][0];			
	
	?>
	
	<section class="container-wrap">
		<div class="container">
<!-- 			<div class="hr-title hr-long"><abbr>Precio del servicio</abbr></div> -->
			
			<div id="contenedor_precio">
				<?php if ($visibilidad_precio == "private"): ?>
					
					<p>El precio de este servicio es privado</p>
					
				<?php elseif ($visibilidad_precio == "hidden"): 
					
					if ( is_user_logged_in() ):
						wp_enqueue_script( 'et_front_end_servicios');
						?>
						
						<script>
							jQuery(document).ready(function() {
								jQuery(".mostrar_precio").click(function( event ) {
									event.preventDefault();
									
									
									
									jQuery("#precio_evento").show();
									jQuery(".mostrar_precio").hide();
								});
							});
						</script>
						
						<a href="#" class="btn btn-primary btn-lg mostrar_precio" id="mostrar_precio_<?php echo get_the_ID (); ?>">Mostrar el precio</a>
						<h2 style="display:none;" id="precio_evento"><?php echo $moneda ?> <?php echo (number_format($precio ,2,",",".") ); ?> </h2>
	
						<p>Al hace click en "mostrar precio" se le comunicará al profesional sobre su solicitud</p>
					<?php 
					else:
						
						echo "<h4>El precio de este servicio es visible solo para miembros de EstiloTú</h4>";
						
					endif;	
					?>
					
				<?php else: ?>
	
					<h2><?php echo $moneda ?> <?php echo (number_format($precio ,2,",",".") ); ?> </h2>
	
				<?php endif; ?>	
			</div>
			
		</div>
	</section>

	<br>
	
<?php endif;
// *********************

// *********************
// SI ES EVENTO
// *********************
if ($tipo == "evento" ): 
	global $fecha_inico;
	global $fecha_fin;
		
	$fecha_inico 	= Estilotu_Public::et_date_translate( $servicio_meta["et_date_from"][0] . " " . $servicio_meta["et_time_from"][0] );
	$fecha_fin 		= Estilotu_Public::et_date_translate( $servicio_meta["et_date_to"][0] . " " . $servicio_meta["et_time_to"][0] );
	
	?>	
	<section class="container-wrap">
		<div class="container">
			<div class="hr-title hr-long"><abbr>Fecha del servicio</abbr></div>
			
			<div id="contenedor_fecha">
			
				<div id="fecha_inicio">	
					<time datetime="<?php echo $fecha_inico["pass_var"]; ?>" class="icon">
						<em class="dia"><?php echo $fecha_inico["dia_semana_txt"]; ?></em>
						<strong class="mes"><?php echo $fecha_inico["mes_txt"]; ?></strong>
						<span class="txt">desde</span>
						<span class="dia_numero"><?php echo $fecha_inico["dia_num"]; ?></span>
						
						<em class="hora"><?php echo $fecha_inico["format_time_12"]; ?></em>
						
					</time>
					
				</div>
				
				<div id="fecha_fin">	
					<time datetime="<?php echo $fecha_fin["pass_var"]; ?>" class="icon">
						<em class="dia"><?php echo $fecha_fin["dia_semana_txt"]; ?></em>
						<strong class="mes"><?php echo $fecha_fin["mes_txt"]; ?></strong>
						<span class="txt">hasta</span>
						<span class="dia_numero"><?php echo $fecha_fin["dia_num"]; ?></span>
						
						<em class="hora"><?php echo $fecha_fin["format_time_12"]; ?></em>
					</time>
				</div>
	
			</div>
			
		</div>
	</section>

<?php endif;
// *********************