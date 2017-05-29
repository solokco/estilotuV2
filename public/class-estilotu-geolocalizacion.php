<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       mingoagency.com
 * @since      1.0.0
 *
 * @package    Estilotu
 * @subpackage Estilotu/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Estilotu
 * @subpackage Estilotu/public
 * @author     Carlos Carmona <ccarmona@mingoagency.com>
 */
class Estilotu_Geolocation_Public extends Estilotu_Public {

	// private $plugin_name;
	// public $version;

	public function __construct( ) {

		// $this->plugin_name = $plugin_name;
		// $this->version = $version;

		//$this->closest_services();
	}
	
	function add_decimal_params( $sqlarr ) {
	  
	  //remove_filter('get_meta_sql','add_decimal_params');
	 
	  $sqlarr['where'] = str_replace('DECIMAL','DECIMAL(11 , 7)' , $sqlarr['where']);
	  return $sqlarr;
	}
	
	public function closest_services () {
		
		$servicios_repetidos = $_POST['servicios_repetidos'];
		
		$lat = $_POST['latitud'];
		$lng = $_POST['longitud'];
		$categoria = $_POST['categoria'];
		$unit = $_POST['unit'];
		$distancia = $_POST['radius'];;
		
		if ( $unit == 'km' ) { $radius_earth = 6371.009; }
		elseif ( $unit == 'mi' ) { $radius_earth = 3958.761; }

		
		$maxLat = ( float ) $lat + rad2deg($distancia / $radius_earth);
	    $minLat = ( float ) $lat - rad2deg($distancia / $radius_earth);
	    
	    $maxLng = ( float ) $lng + rad2deg( $distancia / $radius_earth ) / cos( deg2rad( ( float ) $lat) ) ;
	    $minLng = ( float ) $lng - rad2deg( $distancia / $radius_earth ) / cos( deg2rad( ( float ) $lat) ) ;
				
		$closest_services_args = array(
			'post_type' 		=> 'servicios',
			'posts_per_page' 	=> -1,
			'post_status'    	=> 'publish',
			'post__not_in'		=> $servicios_repetidos,
			'meta_query' => array(
				
				'relation' => 'AND',
				array(
					'relation' => 'AND',
					array(
						'key' => 'et_meta_latitud',
						'value' => '',
						'compare' => '!='
					),
					
					array(
						'key' => 'et_meta_longitud',
						'value' => '',
						'compare' => '!='
					)
				),
				
				array(
					'relation' => 'AND',
					array(
						'key' => 'et_meta_latitud',
						'value' => array( $minLat , $maxLat  ),
						'type' => 'DECIMAL',
						'compare' => 'BETWEEN'
					),
					
					array(
						'key' => 'et_meta_longitud',
						'value' => array( $minLng , $maxLng ),
						'type' => 'DECIMAL',
						'compare' => 'BETWEEN'
					)
				)
			),
		    'tax_query' => array(
		        array(
		        'taxonomy' => 'servicios-categoria',
		        'field' => 'slug',
		        'terms' => $categoria,
		        'operator'  => ( empty($categoria) ? 'EXISTS' : 'IN' )
		        )
		    )
		);
		 
		$closest_services_query = new WP_Query( $closest_services_args );

		$servicio = array();		
		while ($closest_services_query->have_posts()) {
		    
		    $closest_services_query->the_post();
			
			$serv_id = get_the_ID();
		    
		    // traigo todos los custom
		    $servicio[$serv_id]["meta_fields"] = get_post_custom( );
		    
		    $servicio[$serv_id]["service_ID"] = $serv_id;
		    $servicio[$serv_id]["service_author"] = get_post_field ( 'post_author' , $serv_id);
		    $servicio[$serv_id]["service_author_avatar"] = get_avatar( $servicio[$serv_id]["service_author"], '64');
		    $servicio[$serv_id]["service_author_url"] = bp_core_get_user_domain( $servicio[$serv_id]["service_author"] );
		    
		    $servicio[$serv_id]["precio_servicio_visibilidad"] = get_post_meta($serv_id , 'et_meta_precio_visibilidad' , true);
		    
			/*
			$servicio[$serv_id]["lng"] = get_post_meta($serv_id, 'et_meta_longitud', true);
		    $servicio[$serv_id]["lat"] = get_post_meta($serv_id , 'et_meta_latitud' , true);
   		    $servicio[$serv_id]["precio_servicio"] = get_post_meta($serv_id , 'et_meta_precio' , true);
		    $servicio[$serv_id]["precio_servicio_visibilidad"] = get_post_meta($serv_id , 'et_meta_precio_visibilidad' , true);
		    $servicio[$serv_id]["precio_servicio_moneda"] = get_post_meta($serv_id , 'et_meta_precio_moneda' , true);
			*/
		    

		    $servicio[$serv_id]["service_title"] = get_the_title();
		    $servicio[$serv_id]["category"] = get_the_terms($serv_id , 'servicios-categoria');
		    $servicio[$serv_id]["service_url"] = get_post_permalink( $serv_id );
		    $servicio[$serv_id]["thumb_url"] = wp_get_attachment_image_src( get_post_thumbnail_id( $serv_id ) , 'medium' );

		    
		}
		
		wp_send_json($servicio);
	}
	
	public function show_map( $type , $latlon = "" ) { ?>
		
		<div id='map'></div>
		
		<?php 
		if ($type == "add"): 
			wp_enqueue_script( 'estilotu_servicios_add_map_init');

		elseif ($type == "list") : ?>
		
			<div id='legend_categories'></div>
			<div id='legend_distancia'>En KM<span class='distance green'> 1 </span><span class='distance yellow'> 2 </span><span class='distance red'> 4 </span></div>
			
			<?php wp_enqueue_script ('estilotu_servicios_list_map_init'); ?>
		
		<?php	
		elseif ($type == "single") :

			wp_enqueue_script( 'estilotu_servicios_single_map_init');
			wp_localize_script( 'estilotu_servicios_single_map_init', 'php_vars', $latlon );
				
		else:
		
			echo "No se puede mostrar el mapa";
		
		endif;
		wp_enqueue_script( 'estilotu_map_google_api');
		
		return;
		
	}

}