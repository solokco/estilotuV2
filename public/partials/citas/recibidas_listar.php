<div class="row" id="Lista_Citas">
	
	<div class="col-sm-4 wpb_column column_container">
		
		<h3>Filtros</h3>
		
	</div>
	
	<div class="col-sm-8 wpb_column column_container">
	
		<div class="Citas">
			
			<div id="mensaje" style="display:none">
				<h2 class="Centrar">No hay citas pautadas para este d&iacute;a.</h2>
			</div>
			
			<?php
			// SEPARA EN GRUPOS DE DIA
	
			$group_date=array();
			foreach($citas_recibidas as $key => $item) {
			   $group_date[$item->appoinment_date][$item->appoinment_time][] = $item;
			}
			// SEPARA EN GRUPOS DE DIA
							
			foreach ($group_date as $key_bloque => $bloque):
				
				$dia_servicio = Estilotu_Servicio::convertir_fecha($key_bloque); ?>
				
				<div id="Bloque_<?php echo $key_bloque ?>" class="Bloque">
					
					<header class="header_dia"><meta http-equiv="Content-Type" content="text/html; charset=gb18030">
					
						<h2><?php echo $dia_servicio ?></h2>
						
						<?php if ( $this->is_member ): ?>
					
							<form method="post" name="eliminar_cita_dia" action="<?php the_permalink(); ?>" id="formulario_eliminar_cita" enctype="multipart/form-data">
								<input type="hidden" name="fecha" value="<?php echo $key_bloque ?>">
								<input type="hidden" name="status" value="cancel">
								<input type="submit" class="button btn-purple" value="Cancelar las clases del dia <?php echo $dia_servicio ?>">
							</form>
							
						<?php endif; ?>
						
					</header>
					
					<?php 
					foreach ( $bloque as $key_hora => $hora ): ?>
										
						<div class="Hora <?php echo $key_hora ?>">
							
							<header class="header_hora">
								
								<h3><?php echo date('H:i A', strtotime($key_hora)); ?></h3>
								
								<?php if ( $this->is_member ): ?>
						
									<form method="post" name="eliminar_cita_hora" action="<?php the_permalink(); ?>" id="formulario_eliminar_cita" enctype="multipart/form-data">
										<input type="hidden" name="fecha" value="<?php echo $key_bloque ?>">
										<input type="hidden" name="hora" value="<?php echo $key_hora ?>">
										<input type="hidden" name="status" value="cancel">
										<input type="submit" class="button btn-purple" value="Cancelar la clase de las <?php echo date('H:i A', strtotime($key_hora)); ?>">
									</form>
									
								<?php endif; ?>
								
							</header>
							
							<div class="citas">
								
								<?php foreach ( $hora as $key_cita => $cita ):							
									
									if ($cita->appoinment_status == "confirm" )
										$status_cita = "Confirmada";			
									elseif ($cita->appoinment_status == "cancel" )
										$status_cita = "Cancelada";	
									else
										$status_cita = "En Espera";
																			
									$user = get_userdata($cita->appoinment_user_id); ?>
									<div class="usuario">
										<div class="usuario_avatar">
											<a href="/usuarios/<?php echo $user->user_login ?>"><?php echo get_avatar( $cita->appoinment_user_id, 80 ); ?></a>
										</div>
										
										<div class="usuario_datos">								
											<h5> <?php echo ($user->first_name . " " . $user->last_name); ?> - <a href="/usuarios/<?php echo $user->user_login ?>"><?php echo $user->user_login ?></a></h5>
											<h6> <?php echo $user->user_email; ?></h6>
											<p>Status: <?php echo $status_cita; ?></p>
											<p>Servicio: <a href="<?php echo get_permalink($cita->appoinment_service_id); ?>"><?php echo get_the_title($cita->appoinment_service_id); ?></p></a>
									
										</div>
										
										<div class="usuario_opciones">
											
											<div class="usuario_status">
												
												<?php if ($this->es_historial == false): ?>
													<?php if ($status_cita == "Cancelada" || $status_cita == "En Espera" ): ?>
																								
														<form method="post" name="confirmar_cita_individual" action="<?php the_permalink(); ?>" id="formulario_eliminar_cita" enctype="multipart/form-data">
															<input type="hidden" name="fecha" value="<?php echo $key_bloque ?>">
															<input type="hidden" name="hora" value="<?php echo $key_hora ?>">
															<input type="hidden" name="id_cita" value="<?php echo $cita->appoinment_id ?>">
															<input type="hidden" name="status" value="confirm">
															<input type="submit" class="button btn-purple" value="Confirmar">
														</form>
			
													<?php endif; ?>
													
													<?php if ($status_cita == "Confirmada" || $status_cita == "En Espera"): ?>
														
														<form method="post" name="cancelar_cita_individual" action="<?php the_permalink(); ?>" id="formulario_eliminar_cita" enctype="multipart/form-data">
															<input type="hidden" name="fecha" value="<?php echo $key_bloque ?>">
															<input type="hidden" name="hora" value="<?php echo $key_hora ?>">
															<input type="hidden" name="id_cita" value="<?php echo $cita->appoinment_id ?>">
															<input type="hidden" name="status" value="cancel">
															<input type="submit" class="button btn-purple" value="Cancelar">
														</form>
													<?php endif; ?>
												<?php endif; ?>
												
											</div>
																					
											<?php if ( $this->is_member && get_post_field( 'post_author', $cita->appoinment_service_id ) == get_current_user_id() ): ?>
												 <div class="smart-forms">											
													
													<div class="usuario_opciones">
														
														<form method="post" name="formulario_usuario_opciones" class="formulario_usuario_opciones" id="formulario_usuario_opciones_<?php echo $cita->appoinment_id ?>" enctype="multipart/form-data">
		
															<div class="section">
															    
															    <div class='checkbox turn_switch'>
																	<label>&iquest;Asisti&oacute;&#63;</label>
																	<label class='checkbox__container'>
																		<input class='checkbox__toggle appoinment_user_assist boton_<?php echo $cita->appoinment_id ?>' type='checkbox' name="asistencia" id="asistencia_<?php echo $cita->appoinment_id ?>" <?php checked( $cita->appoinment_user_assist , 1 ); ?> >
																		<span class='checkbox__checker'></span>
																		<span class='checkbox__cross'></span>
																		<span class='checkbox__ok'></span>
														
																		<svg class='checkbox__bg' space='preserve' style='enable-background:new 0 0 110 43.76;' version='1.1' viewbox='0 0 110 43.76'>
																			<path class='shape' d='M88.256,43.76c12.188,0,21.88-9.796,21.88-21.88S100.247,0,88.256,0c-15.745,0-20.67,12.281-33.257,12.281,S38.16,0,21.731,0C9.622,0-0.149,9.796-0.149,21.88s9.672,21.88,21.88,21.88c17.519,0,20.67-13.384,33.263-13.384,S72.784,43.76,88.256,43.76z'></path>
																		</svg>
																	</label>
																</div>
															    
															</div>
															
															<div class="section">
															   
															    <div class='checkbox turn_switch'>
																	<label>&iquest;Pag&oacute;&#63;</label>
																	<label class='checkbox__container'>
																		<input class='checkbox__toggle appoinment_user_pay boton_<?php echo $cita->appoinment_id ?>' type='checkbox' name="payment" id="pay_<?php echo $cita->appoinment_id ?>" <?php checked( $cita->appoinment_user_paid , 1 ); ?> >
																		<span class='checkbox__checker'></span>
																		<span class='checkbox__cross'></span>
																		<span class='checkbox__ok'></span>
														
																		<svg class='checkbox__bg' space='preserve' style='enable-background:new 0 0 110 43.76;' version='1.1' viewbox='0 0 110 43.76'>
																			<path class='shape' d='M88.256,43.76c12.188,0,21.88-9.796,21.88-21.88S100.247,0,88.256,0c-15.745,0-20.67,12.281-33.257,12.281,S38.16,0,21.731,0C9.622,0-0.149,9.796-0.149,21.88s9.672,21.88,21.88,21.88c17.519,0,20.67-13.384,33.263-13.384,S72.784,43.76,88.256,43.76z'></path>
																		</svg>
																	</label>
																</div>

															</div>
																												
														</form>
														
													</div>
													
												 </div>
												 
											<?php endif; ?>
											
										</div>	
											
									</div>
									
								<?php endforeach; ?>		
								
							</div>
							
						</div>
		
					<?php endforeach; ?>
					
				</div>
				
			<?php endforeach; ?>
	
		</div>
		
	</div>

</div>