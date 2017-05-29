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
		#conteido_servicio {background:rgba(1,1,1,0);position:relative;}
		#conteido_servicio #avatar-author {position:absolute;left:0px;top:-10px;}
		#conteido_servicio #avatar-author img {border-radius:100%;}
		.dia_circulo{margin:1px;padding:5px;}
		#lista_dias_disponibles {padding-top: 12px;}
		
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
	
	<article id="post-<?php the_ID(); ?>" class="servicio-single">
		
		<div class="article-content">

			<!-- 
				<section class="container-wrap main-color">
					<div class="section-container container"> 
			-->
		
				<div class="row">

					<div class="col-sm-8">
						<h2><?php the_title(); ?></h2>
					</div>

					<div class="col-sm-4">
						<h2><?php echo $moneda . " " . $precio; ?></h2>
					</div>
				</div>
				
				<div class="row">
			
					<div class="col-sm-4">
						
						<?php echo ( get_the_post_thumbnail( get_the_ID() , 'medium') ); ?>

					</div>
					
					<div class="col-sm-4">
						<?php the_content(); ?>
						
					</div>
					
					<div class="col-sm-4">
						<div class="input-group date" data-provide="datepicker">
							<input type="text" class="form-control">
							<div class="input-group-addon">
								<span class="glyphicon glyphicon-th"></span>
							</div>
						</div>
					</div>
					
					<div class="lista_cupos_disponibles"></div>
				</div>
				
				<div class="row">
					<div class="col-sm-12">
						<h3>Dirección</h3>
						<p><?php echo $direccion1 . ", " . $direccion2; ?></p>
						<p><?php echo $ciudad . ", " . $estado; ?></p>
						<p><?php echo $pais . ", " . $zip_code; ?></p>
						
						<?php $ubicacion_servicio->show_map( "single" , $latlon ); ?>
						
					</div>
				</div>
				
			
			<!-- 
				</div> 
			</div>
			-->
			
			
		</div>
	
	</article>	
	

		
<?php endwhile; ?>
	
<?php include_once ( ESTILOTU_PATH . 'public/partials/servicios/cupos/page-parts/general-after-wrap-servicios.php');?>

<?php get_footer(); ?>