<?php
/** 
 * Displays Service info
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */
global $tipo;
global $servicio_meta;

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
			<div class="hr-title hr-long"><abbr>Precio del servicio</abbr></div>
			
			<div id="contenedor_precio">
				<?php if ($visibilidad_precio == "private"): ?>
					
					<p>El precio de este servicio es privado</p>
					
				<?php elseif ($visibilidad_precio == "hidden"): ?>
					
					<script>
						jQuery(document).ready(function() {
							jQuery(".mostrar_precio").click(function( event ) {
								event.preventDefault();
								//alert("Se notifica al proveedor");
								
								jQuery("#precio_evento").show();
								jQuery(".mostrar_precio").hide();
							});
						});
					</script>
					
					<a href="#" class="btn btn-primary btn-lg mostrar_precio">Mostrar el precio</a>
					<h2 style="display:none;" id="precio_evento"><?php echo $moneda ?> <?php echo (number_format($precio ,2,",",".") ); ?> </h2>

					<p>Al hace click en "mostrar precio" se le comunicará al profesional sobre su solicitud</p>
					
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
if ($tipo == "evento" ): ?>

	<section class="container-wrap">
		<div class="container">
			<div class="hr-title hr-long"><abbr>Fecha del servicio</abbr></div>
			
			<div id="contenedor_fecha">
			
				<div id="fecha_inicio">	
					<h2><?php echo $servicio_meta["et_date_from"][0] ?></h2>
					<?php 
					if ($servicio_meta['tipo-evento'][0] == "tipo-evento-corrido" ): ?>
					
						<h2><?php echo $servicio_meta["inicio_horario"][0] ?></h2>	
					
					<?php endif; ?>
				</div>
				
				<div id="fecha_fin">	
					<h2><?php echo $servicio_meta["et_date_to"][0] ?></h2>
					
					<?php
					if ($servicio_meta['tipo-evento'][0] == "tipo-evento-corrido" ): ?>
					
						<h2><?php echo $servicio_meta["fin_horario"][0] ?></h2>	
					
					<?php endif; ?>
				</div>
	
			</div>
			
		</div>
	</section>

<?php endif;
// *********************