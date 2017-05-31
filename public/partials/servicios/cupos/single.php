<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */
get_header(); ?>
	
	<style>
		
		#titulo_servicio{background-color: #703090;border-radius: 0px 0px 40px 0px;color:#FFF !important;padding:10px 10px 10px 120px;margin-left:10px;}
		#titulo_servicio_simple {margin-bottom:0px;}
		#heading_direccion {margin-top:0px;}
		#body_direccion p {margin: 0px;}
		#conteido_servicio {background:rgba(1,1,1,0);position:relative;}
		#conteido_servicio #avatar-author {position:absolute;left:0px;top:-10px;}
		#conteido_servicio #avatar-author img {border-radius:100%;}
		.dia_circulo{margin:1px;padding:5px;}
		#lista_dias_disponibles {padding-top: 12px;}
		#costo_servicio {margin-top: -56px;background-color: rgba(0,0,0,0.8);color:#FFF !important;padding:10px;}
		
		.container-contenido-servicio {padding:0px 20%;}
		
		#imagen_autor img {border-radius: 50%;}
		
		.table-condensed, .datepicker-inline {width:100%;}
		
		.datepicker.datepicker-inline td,
		.datepicker table tr td.new:not(.disabled) {background: var(--et-main-color);color:#FFF;border:1px solid #FFF;}
		
		.datepicker table tr td.day:hover:not(.disabled) {background: #8a1488;color:#FFF}
		.datepicker table tr td.day.active {background: #8a1488;border:none;}
		
		.datepicker table tr td.today {background: #8a1488;border:none;color:#FFF !important;}
		.datepicker table tr td.today.disabled {background: #bfc1cd !important;}
		
		#map {height:500px;}
	</style>
	
<?php include_once ( ESTILOTU_PATH . 'public/partials/servicios/cupos/page-parts/general-before-wrap-servicios.php' ) ;?>
			
<?php while ( have_posts() ) : 
	
	the_post(); 
	
	$servicio 					= new Estilotu_Servicio();
	
	$servicio_id 				= $post->ID; 
	$servicio_meta 				= get_post_meta($servicio_id); 				
	$servicios_categoria		= get_the_terms($servicio_id , 'servicios-categoria');
	$disponible					= $servicio->horarios_servicio($servicio_id);
	
	$url_guardar 				= bp_core_get_user_domain( get_current_user_id() ) . "citas" ;	
	$nonce_field				= wp_nonce_field( 'guardar_cita', 'guardar_cita_nonce' );
	$token_id 					= md5( uniqid( "", true ) );
	
	$precio 					= isset($servicio_meta['et_meta_precio'][0]) ? $servicio_meta['et_meta_precio'][0] : '' ;
	$moneda 					= isset($servicio_meta['et_meta_precio_moneda'][0]) ? $servicio_meta['et_meta_precio_moneda'][0] : '' ;
	$moneda_visibilidad 		= isset($servicio_meta['et_meta_precio_visibilidad'][0]) ? $servicio_meta['et_meta_precio_visibilidad'][0] : 'public' ;
	$tiempo_maximo_reserva 		= isset($servicio_meta['et_meta_max_time'][0]) ? $servicio_meta['et_meta_max_time'][0] : '' ;
	$tiempo_maximo_cierre 		= isset($servicio_meta['et_meta_close_time'][0]) ? $servicio_meta['et_meta_close_time'][0] : '' ; 
	
	$direccion1 				= isset($servicio_meta['et_meta_direccion_1'][0]) ? $servicio_meta['et_meta_direccion_1'][0] : '' ;
	$direccion2 				= isset($servicio_meta['et_meta_direccion_2'][0]) ? $servicio_meta['et_meta_direccion_2'][0] : '' ;
	$ciudad		 				= isset($servicio_meta['et_meta_ciudad'][0]) ? $servicio_meta['et_meta_ciudad'][0] : '' ;
	$estado		 				= isset($servicio_meta['et_meta_estado'][0]) ? $servicio_meta['et_meta_estado'][0] : '' ;
	$pais		 				= isset($servicio_meta['et_meta_pais'][0]) ? $servicio_meta['et_meta_pais'][0] : '' ;
	$zip_code	 				= isset($servicio_meta['et_meta_zipcode'][0]) ? $servicio_meta['et_meta_zipcode'][0] : '' ;
	
	$latitud	 				= isset($servicio_meta['et_meta_latitud'][0]) ? $servicio_meta['et_meta_latitud'][0] : '' ;
	$longitud	 				= isset($servicio_meta['et_meta_longitud'][0]) ? $servicio_meta['et_meta_longitud'][0] : '' ;
	$latlon						= array($latitud, $longitud);
	
	$ubicacion_servicio			= new Estilotu_Geolocation_Public();
	
	$config_vars				= array( "disponible" => $disponible , "fecha_tope" => $tiempo_maximo_reserva , "hora_cierre" => $tiempo_maximo_cierre , "id_servicio" => $servicio_id , "url_user" => $url_guardar , "nonce_field" => $nonce_field , "token_id" => $token_id);
	
	//envio variables a jquery
	wp_register_script ( 'bootstrap-calendar' 			, plugin_dir_url( __FILE__ )		. 'js/calendar/bootstrap-datepicker.min.js'				, array( 'jquery' ), null , false );
	wp_register_script ('bootstrap-calendar-espanol' 	, plugin_dir_url( __FILE__ )		. 'js/calendar/locales/bootstrap-datepicker.es.min.js'	, array( 'bootstrap-calendar' ), null , false );
	wp_enqueue_script  ('bootstrap-calendar-config' 	, plugin_dir_url( __FILE__ )		. 'js/calendar/bootstrap-datepicker-config.js'			, array( 'bootstrap-calendar-espanol' ), null , false );

	wp_localize_script ( 'bootstrap-calendar-config', 'config_vars', $config_vars );
	wp_localize_script ( 'bootstrap-calendar-config', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );	
	?>
	
	<style>
		.fondo_imagen_servicio {
			background-image: url(<?php echo ( get_the_post_thumbnail_url( get_the_ID() , 'large') ); ?>) !important;
			background-attachment: fixed;
			background-position: center;
			background-repeat: no-repeat;
			background-size: cover;
			
		}
	</style>
	
	<article id="post-<?php the_ID(); ?>" class="servicio-single">
		
		<section class="container-wrap  main-color ">
			<div class="section-container container-full">
				<div class="vc_row vc_row-fluid fondo_imagen_servicio vc_row-has-fill row">
					<div class="wpb_column vc_column_container vc_col-sm-12">
						<div class="vc_column-inner ">
							<div class="wpb_wrapper">
								<div class="vc_empty_space" style="height: 500px">
									<span class="vc_empty_space_inner"></span>
								</div>
							</div>
						</div>
						
						<div class="vc_parallax-inner skrollable skrollable-between" data-bottom-top="top: -50%;" data-top-bottom="top: 0%;" style="height: 150%; background-image: url(&quot;http://wordpress.dev/wp-content/uploads/2017/05/18673307_10155103867170289_6295631514902123616_o.jpg&quot;); top: -22.3429%;"></div>
						
					</div>
				</div>
			</div>
		</section><!-- end section -->
		
		<section class="container-wrap  main-color container-contenido-servicio">
			<div class="section-container container-full">
				<div class="vc_row vc_row-fluid row">
					
					<div class="wpb_column vc_column_container col-sm-8">
						<div class="vc_column-inner ">
							<div class="wpb_wrapper">
								
								<div class="row">
									<div class="col-sm-10">
										<h2 style="text-align: left;" class="vc_custom_heading" id="titulo_servicio_simple"><?php the_title(); ?></h2>
										<h4 id="heading_direccion"><?php echo $ciudad . ", " . $estado . ", " . $pais; ?></h4>
									</div>
								
									<div class="col-sm-2" id="imagen_autor">
										<?php echo get_avatar( get_the_author_meta( 'ID' ), 64 ); ?>
									</div>
								</div>
								
								
								<div class="kleo_text_column wpb_content_element ">

									<div class="wpb_wrapper">

										<?php the_content(); ?>

									</div> 
									
								</div> 
								
								<hr>
								
								<div class="kleo_text_column wpb_content_element" id="body_direccion">
									
									<div class="wpb_wrapper">
										
										<h3>Dirección</h3>
										<p><?php echo $direccion1 . " " . $direccion2; ?></p>
										<p><?php echo $ciudad . ", " . $estado; ?></p>
										<p><?php echo $pais . ", " . $zip_code; ?></p>
										
										<?php $ubicacion_servicio->show_map( "single" , $latlon ); ?>
							
									</div> 
									
								</div> 
		
							</div>
						
						</div>
					
					</div>
				
					<div class="wpb_column vc_column_container col-sm-4">
						<div class="vc_column-inner ">
							<div class="wpb_wrapper">
					
								<div class="kleo_text_column wpb_content_element ">
									
									<div class="wpb_wrapper">
										
										<h2 id="costo_servicio"><?php echo $moneda . " " . $precio; ?></h2>
				
									</div> 
									
									<div id="datepicker"></div>
									<div class="lista_cupos_disponibles"></div>
									
								</div> 
							
							</div>
			
						</div>
					</div>
					
				</div>
			</div>
		</section><!-- end section --><p></p>
	
	</article>	
	

		
<?php endwhile; ?>
	
<?php include_once ( ESTILOTU_PATH . 'public/partials/servicios/cupos/page-parts/general-after-wrap-servicios.php');?>

<?php get_footer(); ?>