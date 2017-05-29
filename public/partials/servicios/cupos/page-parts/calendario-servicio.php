<?php
/** 
 * Displays Calendar
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */
global $tipo;
global $servicio_meta;
?>

<section class="container-wrap">
	<div class="container">
		<div class="hr-title hr-long" id="reserva_contenedor"><abbr>Realiza tu cita</abbr></div>
		
		<div id="contenedor_calendario">
			<?php 		
			//**************
			// CUPOS
			//**************
			if ($tipo == "cupos"):
	
				if ( is_user_logged_in() ): ?>
				
					<div class='Contenedor_Cupos'>
						<h2 id="titulo_selecciona_cupo">Selecciona un día para ver los cupos disponibles</h2>
				
						<div class="lista_cupos_disponibles"></div>
					
					</div>
					
					<!--
					<?php if ($vacaciones): ?>
					vacaciones = <?php echo json_encode( $dias_vacaciones ); ?>;
					vacaciones = JSON.stringify(vacaciones);
					<?php endif; ?>
					-->
				
				<?php
				else: ?>
				
					<h3>Debes iniciar tu sesión o registrarte para poder reservar el servicio</h3>
				
				<?php 
				endif;
	
			//**************
			
			//**************
			// EVENTO
			//**************
			elseif ($tipo == "evento"): 
				
				if ( is_user_logged_in () ) :
					global $wpdb;
					global $current_user;
					global $fecha_inico;
					global $fecha_fin;
					
					// print_r($servicio_meta); 
					
					$disponible = Estilotu_Servicios_FrontEnd::et_contar_reservas( $fecha_inico["date"] , get_the_ID() ) ;
					$url_guardar = add_query_arg( 'accion', 'guardar', bp_core_get_user_domain( get_current_user_id() ) . "citas" );	
					
					if (isset($servicio_meta["evento_cupos"][0]) )
						$cupos_disponibles = $servicio_meta["evento_cupos"][0] - $disponible["ocupado"][$fecha_inico["format_time_24"]];
					
					else
						$cupos_disponibles = 1000;				
					?>
					
					<div class='cupoDisponible' id="cupoDisponible_<?php get_the_ID() ?>">
	        	
		        	   <form class='formulario_reserva_cupo' id="hacer_reserva_<?php get_the_ID() ?>" action="<?php echo $url_guardar; ?>" method='post' >
		        	
							<header>Reservar</header>
							<p>Cupos Maximo: <?php echo $servicio_meta["evento_cupos"][0]; ?></p>
							<p>Cupos Disponibles: <?php echo $cupos_disponibles;  ?> </p>
								        	
					        <input type='hidden' value="<?php the_ID(); ?>" id="id_servicio_<?php the_ID(); ?>" name='id_servicio'>
					        <input type='hidden' value="<?php echo $fecha_inico["date"]; ?>" name='servicio_dia_seleccionado'>
					        <input type='hidden' value="<?php echo $fecha_inico["format_time_24"]; ?>" name='et_meta_hora_inicio'>
							
							<?php if ( $disponible["reservado"] ): ?>
							
								<input disabled type='submit' value='Ya reservaste' class='button btn-morado servicio_reservado' id='boton_reservar' name='agotado'>
								
							<?php elseif ( $cupos_disponibles < 1 ): ?>
								
								<input disabled type='submit' value='Cupos agotados' class='button btn-morado servicio_agotado' id='boton_reservar' name='agotado'>
							
							<?php else: ?>
							
								<input type='submit' value='reservar' class='button btn-morado' id='boton_reservar' name='is_reserve'>
							
							<?php endif; ?>
						</form>
		        	
					</div>
				<?php 
				else: ?>
				
				<h3>Debes iniciar tu sesión o registrarte para poder reservar el servicio</h3>
				
				<?php 	
				endif;	
				?>
			
			<?php
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
	</div>
</section>