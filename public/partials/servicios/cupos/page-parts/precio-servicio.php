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
	
<?php endif;
// *********************