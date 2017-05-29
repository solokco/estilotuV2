<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */
?>

<?php
$postclass = '';
if( is_single() && get_cfield('centered_text') == 1) { $postclass = 'text-center'; }
global $tipo;
global $servicio_meta;

$servicio_meta = get_post_custom() ;
$tipo = $servicio_meta['et_meta_tipo'][0];
$categories_servicios = get_the_term_list( get_the_ID() , 'servicios-categoria' , '' , ', ' , '');

$dataToJS = array(
	'id_servicio' 		=> get_the_ID(),
	'url_user'			=> add_query_arg( 'accion', 'guardar', bp_core_get_user_domain( get_current_user_id() ) . "citas" )
);

wp_enqueue_script( 'calendario_horitzontal');
wp_localize_script( 'calendario_horitzontal', 'php_vars', $dataToJS );
?>

<style>
	
	#contenido_servicio {margin:0px !important;}
	#conteido_servicio {margin:0px !important;}
	#calendario_horizontal {width:100%;}
	#header_fecha {margin:0px !important;background:#CCC;padding:10px 0px 20px;}
	
	.bloque_semana.oculto {display:none;}
	
	#calendario_horizontal .mes {color:#999;font-size: 1.6em;margin-left:10px;margin-bottom:10px;}
	#calendario_horizontal ul {width:100%;margin:0px;padding:0px;}
	li.bloque_dia{font-size: 1.8em;display:inline;float:left;width: 11%;text-align: center;}

	li.bloque_dia.activo {cursor:pointer;}
	li.bloque_dia.desactivo {color: #999;}
	
	li .calendario_fecha {font-size:0.8em;color:#777;}
	
	span.calendario_dia {padding-bottom:2px;}
	li.bloque_dia.activo span.calendario_dia:hover {border-bottom: 2px solid purple;cursor:pointer;color:purple;  }
	
	#descripcion_servicio{padding:0px 40px 0px 0px;}
	
	#titulo_servicio {margin-top: 0px;}
	
</style>

<!-- Begin Article -->
<article id="post-<?php the_ID(); ?>" <?php post_class(array($postclass)); ?>>

	<div class="article-content">
		
		<section class="container-wrap  main-color ">
			
			<div class="section-container container-full">
				
				<div class="row" id="conteido_servicio">
					
					<div class="row" id="header_fecha">	
						<?php
						
						$dt = new DateTime;
						if (isset($_GET['year']) && isset($_GET['week'])) {
						    $dt->setISODate($_GET['year'], $_GET['week']);
						} else {
						    $dt->setISODate($dt->format('o'), $dt->format('W'));
						}
						$year = $dt->format('o');
						$week = $dt->format('W');
						$week_current = $dt->format('W');
						
						$dias_disponibles = Estilotu_Servicios::horarios_servicio( get_the_ID() ) ; 
						
						function dia_semana($numero_dia , $dias_disponibles ) {
							
							if ( in_array( $numero_dia , $dias_disponibles['dias_activados'] ) ):
								$clase_dia = "bloque_dia activo";
							else:
								$clase_dia = "bloque_dia desactivo";
							endif;
							
							
							switch( $numero_dia ) {	
								case 1:
									$dia['inicial'] = "L";
									$dia['class'] = $clase_dia;
									break;
								
								case 2:
									$dia['inicial'] = "M";
									$dia['class'] = $clase_dia;
									break;
								
								case 3:
									$dia['inicial'] = "M";
									$dia['class'] = $clase_dia;
									break;
									
								case 4:
									$dia['inicial'] = "J";
									$dia['class'] = $clase_dia;
									break;
									
								case 5:
									$dia['inicial'] = "V";
									$dia['class'] = $clase_dia;
									break;
									
								case 6:
									$dia['inicial'] = "S";
									$dia['class'] = $clase_dia;
									break;
									
								case 7:
									$dia['inicial'] = "D";
									$dia['class'] = $clase_dia;
									break;
							}
						
							return $dia;
						}
						?>
			
						<div id="calendario_horizontal">
						    <div class="mes">
							    <span id="mes"><?php echo $dt->format('F'); ?></span> <span><sup id="year"><?php echo $dt->format('o'); ?></sup></span>
						    </div>
						    
						    <div id="dias_horizontales">
								<ul>
									
									<li class="controles bloque_dia">
										<a id="semana_anterior" data-semana="<?php echo ($week_current - 1) ?>" data-year="<?php echo $year ?>" href="#">&#60;</a>
									</li>
									
									
									<?php
									
									for ($x = 0; $x <= 10; $x++) { ?>
										
										<div class="bloque_semana <?php echo $x == 0 ? "" : "oculto"  ?>" id="semana_<?php echo $week; ?>">
										
											<?php
											do { ?>
											    <li class='<?php echo dia_semana( $dt->format('N') , $dias_disponibles )['class'] ?>' data-dia-servicio="<?php echo $dt->format('Y-m-d') ?>" data-dia-semana="<?php echo $dt->format('w') ?>">
												    <span class='calendario_dia'><?php echo dia_semana( $dt->format('N') , $dias_disponibles )['inicial'] ?></span>
												    <span class='calendario_fecha'> <sub> <?php echo $dt->format('d') ?> </sub></span>
												
												</li>
											    <?php
											    $dt->modify('+1 day');
											} while ($week == $dt->format('W')); ?>
											
										</div>

										<?php 	
										$week++;
										
									}	
									?>
								
									<li class="controles bloque_dia">
										<a id="semana_siguiente" data-semana="<?php echo ($week_current + 1) ?>" data-year="<?php echo $year ?>" href="#">&#62;</a>
									</li>

								</ul>
						    </div>
						</div>
					
					</div>
					
					
					<div class="row" id="header_servicio">	
						<div class="col-sm-8">
							<h2 id="titulo_servicio"><?php echo kleo_title(); ?></h2>
			
							<small id="avatar-author">
								<?php $autor_id = get_the_author_meta("ID"); ?>
								<?php echo get_avatar( $autor_id); ?> 
							</small>
						</div>
			
						<div class="col-sm-4" id="lista_dias_disponibles">
							
							<?php get_template_part( 'page-parts/precio-servicio' ); ?>
							
			<!--
							<?php $dias_disponibles = Estilotu_Servicios::horarios_servicio( get_the_ID() ) ; 
								
								for ($dia = 1 ; $dia <= 7 ; $dia++):
									
									if ( in_array( $dia , $dias_disponibles['dias_activados'] ) ):
										$clase_dia = "dia_activo";
									else:
										$clase_dia = "dia_inactivo";
									endif;
															
									switch ($dia) {
										
										case 1:
											echo "<span class='dia_circulo $clase_dia'>LU</span>";
											break;
											
										case 2:
											echo "<span class='dia_circulo $clase_dia'>MA</span>";
											break;
											
										case 3:
											echo "<span class='dia_circulo $clase_dia'>MI</span>";
											break;
											
										case 4:
											echo "<span class='dia_circulo $clase_dia'>JU</span>";
											break;
											
										case 5:
											echo "<span class='dia_circulo $clase_dia'>VI</span>";
											break;
											
										case 6:
											echo "<span class='dia_circulo $clase_dia'>SA</span>";
											break;
											
										case 7:
											echo "<span class='dia_circulo $clase_dia'>DO</span>";
											break;						
										
									}
								
								endfor;
			
							?>
							-->
							
						</div>	
						
					</div>
					
					<div class="row" id="contenido_servicio">	
						
						<div class="col-sm-4">
							
							<!--
							<?php if ( kleo_postmedia_enabled() && kleo_get_post_thumbnail() != '' ) : ?>
								<div class="article-media">
									<?php echo kleo_get_post_thumbnail( null, 'kleo-full-width' );?>
								</div>
							<?php endif; ?>
							-->
							
							<h3>Descripción</h3>
							<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'kleo_framework' ) ); ?>
							<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'kleo_framework' ), 'after' => '</div>' ) ); ?>
							
							
						
						</div>
						
						<div class="col-sm-4">
							
							<?php get_template_part( 'page-parts/calendario-servicio' ); ?>
					
						</div>
						
						<div class="col-sm-4" id="descripcion_servicio">

							
							<?php get_template_part( 'page-parts/mapa-servicio' ); ?>
							
						</div>
					</div>
					
					
				</div>

			</div>
			
		</section>
		
	
			
			<div class="row">
				<div class="col-sm-12">
					
				</div>
			</div>
			
			<div class="row">
				
			</div>
	
	</div>
</article><!--end article-->