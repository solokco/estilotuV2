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
class Estilotu_Servicio {
	
	private $en_edicion;
	private	$id_servicio;
	private $servicio;
	private $servicio_meta;
	private $servicios_categoria;
	private $tipo_de_servicio;
	private $post_id;
	private	$fecha_seleccionada;
	public	$table_name;
	public 	$token_id;
	private $facilities_selected 	= array();
	private $facilities_list 		= array( "area_techada" => "Area Techada" , "Sanitarios" => "Sanitarios" , "Duchas" => "Duchas" , "Estacionamiento" => "Estacionamiento" , "area_infantil" => "Area para niños" , "Wifi" => "Wifi" , "Hidratacion" => "Hidratación" , "Cafetin" => "Cafetin");
	private $locacion_tipo 			= array( "Calle" => "Calle" , "Plaza" => "Plaza" , "Piscina" => "Piscina" , "Gimnasio" => "Gimnasio" , "Parque" => "Parque" , "Cancha_deportiva" => "Cancha Deportiva" );
	private $locacion_selected;
	
	public function __construct() {
		
		//global $wpdb;
		

	}
	
	/* *************************************************************************** */
	/* CARGO TEMPLATE PARA SINGLE */
	/* *************************************************************************** */
	public function cpt_servicios_template_single ( $single ) {
	    global $wp_query, $post;
		
	    /* Checks for single template by post type */
	    if ( $post->post_type == "servicios" ) {

	        if ( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/servicios/cupos/single.php' ) ) {

	            $single =  plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/servicios/cupos/single.php';
	            
			}
	            
	    }
	    
	    return $single;
	}
	
	/* ************************************************************************ */
	/* RECIBO UNA FECHA Y LA CONVIERTO A ESPANOL					 			*/
	/* ************************************************************************ */
	public static function convertir_fecha ($fecha) {
	
		$fecha_final  = date("l, d F Y", strtotime($fecha) );
		
		$days = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
		$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
		
		$fecha_final = str_ireplace($days, $dias, $fecha_final);
		
		$month = array("January","February","March","April","May","June","July","August","September","October","November","December");
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
	
		$fecha_final = str_ireplace($month, $meses, $fecha_final);
		
		return $fecha_final;
		
	}
	/* ************************************************************************ */
	
	
	/* ************************************************************************ */
	/* CREO UN ARRAY DE HORAS Y MINUTOS								 			*/
	/* ************************************************************************ */
	public function crear_horario ( $steps = 15 ) {
	
		$bloque_horario = array();
		
		for ( $hora = 0 ; $hora < 24 ;  $hora++ ):	
															
			for ( $minuto = 0 ; $minuto < 60 ;  $minuto += $steps ):
					
				$bloque_horario["value"][] 		= str_pad($hora,2,'0',STR_PAD_LEFT) . ":" 	. str_pad($minuto,2,'0',STR_PAD_LEFT) . ":00";
				$bloque_horario["militar"][] 	= str_pad($hora,2,'0',STR_PAD_LEFT) . ":" 	. str_pad($minuto,2,'0',STR_PAD_LEFT) ;
				$bloque_horario["ampm"][] 		= str_pad($hora % 12 ? $hora % 12 : 12 , 2 , '0' , STR_PAD_LEFT ) . ":" 		. str_pad($minuto,2,'0',STR_PAD_LEFT) . ($hora >= 12 ? " pm" : " am");
					
			endfor;		
		
		endfor;
		
		return $bloque_horario;
	}
	/* ************************************************************************ */
	
	/* ************************************************************************ */
	/* LISTA LOS SERVICIOS DEL USUARIO QUE SE MUESTRA EN BUDDYPRESS 			*/
	/* ************************************************************************ */
	public function listar_servicios_profesional() {
		global $bp;
		global $current_user;
		global $post;
		
		$id_provider = $bp->displayed_user->id;
		
		$args = array(
			'posts_per_page'   => 5,
			'offset'           => 0,
			'category'         => '',
			'category_name'    => '',
			'orderby'          => 'date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'servicios',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'author'	   => '',
			'author_name'	   => '',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		
		$the_query = new WP_Query( $args );
		
		require_once ESTILOTU_PATH . 'public/partials/servicios/cupos/_list.php' ;
		
		//echo do_shortcode( '[vc_posts_grid loop="size:100|order_by:date|post_type:servicios|authors:'.$id_provider.'" post_layout="grid" columns="4" show_switcher="no" switcher_layouts="masonry,small,standard" show_meta="yes" show_excerpt="yes"]' );
		
	}
	/* ************************************************************************ */
	
	/* ************************************************************************ */
	/* LISTA LOS SERVICIOS DEL USUARIO QUE SE MUESTRA EN BUDDYPRESS 			*/
	/* ************************************************************************ */
	public function agregar_servicio () {
		global $bp;	
		global $wp_query;
		global $current_user;
		
		$id_provider = $bp->displayed_user->id;
		
		/* ************************************ */
		/* SI NO VIENE POST NONCE  */
		/* ************************************ */
		if ( !isset( $_POST['publicar_servicio_nonce'] ) || !wp_verify_nonce( $_POST['publicar_servicio_nonce'] , 'publicar_servicio' ) ): 
			
			/* ************************************ 	*/
			/* SI NO HAY TIPO DE EVENTO SELECCIONADO 	*/
			/* ************************************ 	*/
			if ( !isset($wp_query->query_vars['tipo_servicio']) ):
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/servicios/select_to_add.php' ;

			/* ************************************ 	*/
			/* SI EL TIPO SELECCIONADO ES CUPOS 		*/
			/* ************************************ 	*/
			elseif ( $wp_query->query_vars['tipo_servicio'] == 'cupos' ):
						
				$this->tipo_de_servicio = $wp_query->query_vars['tipo_servicio'];
				$this->token_id 		= md5( uniqid( "", true ) );
				
				$max_time 			= null;
				$moneda 			= null;
				$moneda_visibilidad = null;
				$et_meta_close_time	= null;
				$intensidad			= '1' ; 
				
				$mapa 				= new Estilotu_Geolocation_Public(); 
				$lista_paises		= $mapa->lista_paises();

				wp_enqueue_script('estilotu-imagesuploader');
				wp_enqueue_script('estilotu_duplicar_servicios');
				
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/servicios/cupos/add.php' ;
			
			/* ************************************ 	*/
			/* SI EL TIPO SELECCIONADO ES EVENTOS 		*/
			/* ************************************ 	*/
			elseif ( $wp_query->query_vars['tipo_servicio'] == 'evento' ):
				
				$this->tipo_de_servicio = $wp_query->query_vars['tipo_servicio'];
				
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/servicios/eventos/add.php' ;
			
			
			/* ************************************ 	*/
			/* SI EL TIPO SELECCIONADO ES ONLINE 		*/
			/* ************************************ 	*/
			elseif ( $wp_query->query_vars['tipo_servicio'] == 'online' ):
				
				$this->tipo_de_servicio = $wp_query->query_vars['tipo_servicio'];
				
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/servicios/servicios-agregar-online-display.php' ;
				
			endif;
		
		else:

			$this->token_id = stripslashes( $_POST['et_token'] );
				
			// si ya existe un token que indica que se guardo el post
			if ( get_transient( 'token_' . $this->token_id ) ) { ?>

			    <h2 class='alert alert-warning'> <?php _e( "Usted ya guardo este servicio" , "estilotu" ); ?> </h2>
			    
			    <?php 
			    return;
			
			} 
			
			// si el post es nuevo
			else {
				
				$post_id = $this->guardar_servicio( );
				
				if ( $post_id ): ?>
					
					<h2 class='alert alert-success'> <?php _e("Su servicio se ha guardado con éxito" , "estilotu") ?> </h2>;
					<a class="et_button" href="<?php echo get_permalink( $post_id ); ?>" title="">Ver publicación</a>
					
					<?php _e("<h2 class=''>Ver servicio</h2>" , "estilotu");
					
					set_transient( 'token_' . $this->token_id , 'servicio-agregar', 3600 );

					
				else: ?>
					
					<h2 class='alert alert-danger'> <?php _e("Ocurrió un error al guardar su servicio" , "estilotu"); ?> </h2>
				
				<?php	
				endif;	
			}
			
		endif;	
		
	}
	/* ************************************************************************ */
	
	/* ************************************************************************ */
	/* LISTA LOS SERVICIOS DEL USUARIO QUE SE MUESTRA EN BUDDYPRESS 			*/
	/* ************************************************************************ */
	public function editar_servicio () {
		
		global $bp;	
		global $wp_query;
		
		$this->post_id  	= get_query_var( "id_servicio" );		
		$post_author_id 	= get_post_field( 'post_author', $this->post_id  );
		
		// valido que el id del servicio sea del autor
		if ( $post_author_id == bp_displayed_user_id() ):
						
			// valido que no venga la funcion de guardar
			if ( !isset( $_POST['publicar_servicio_nonce'] ) || !wp_verify_nonce( $_POST['publicar_servicio_nonce'] , 'publicar_servicio' ) ): 
				
					$this->en_edicion 			= true;
					$this->servicio 			= get_post($this->post_id); 
					$this->servicio_meta 		= get_post_meta($this->post_id); 				
					$this->servicios_categoria	= get_the_terms($this->post_id, 'servicios-categoria');
					$this->token_id 			= md5( uniqid( "", true ) );
					//$disponible 				= unserialize($this->servicio_meta["disponibilidad_servicio"][0]) ;			
					
					$moneda 					= isset($this->servicio_meta['et_meta_precio_moneda'][0]) ? $this->servicio_meta['et_meta_precio_moneda'][0] : '' ;
					$moneda_visibilidad 		= isset($this->servicio_meta['et_meta_precio_visibilidad'][0]) ? $this->servicio_meta['et_meta_precio_visibilidad'][0] : '' ;
					$max_time 					= isset($this->servicio_meta['et_meta_max_time'][0]) ? $this->servicio_meta['et_meta_max_time'][0] : '' ;
					$et_meta_close_time 		= isset($this->servicio_meta['et_meta_close_time'][0]) ? $this->servicio_meta['et_meta_close_time'][0] : '' ; 
					$intensidad					= isset($this->servicio_meta['intensidad'][0]) ? $this->servicio_meta['intensidad'][0] : '1' ; 
					
					$horarios_servicio = $this->horarios_servicio( $this->post_id );
					
					$mapa 				= new Estilotu_Geolocation_Public(); 
					$lista_paises		= $mapa->lista_paises();

					$this->facilities_selected 	= isset($this->servicio_meta['facilities'][0]) ? unserialize($this->servicio_meta['facilities'][0]) : array() ;
					$this->locacion_selected 	= isset($this->servicio_meta['et_meta_tipo_locacion'][0]) ? $this->servicio_meta['et_meta_tipo_locacion'][0] : null ;
					
					wp_enqueue_script('estilotu-imagesuploader');
					wp_enqueue_script('estilotu_duplicar_servicios');
					
					require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/servicios/cupos/add.php' ;	
				
			// si viene con la funcion de guardar
			else:			

				$this->token_id = stripslashes( $_POST['et_token'] );
				
				// si ya existe un token que indica que se guardo el post
				if ( get_transient( 'token_' . $this->token_id ) ) { ?>

				    <h2 class='alert alert-warning'> <?php _e("Usted ya guardo este post</h2>" , "estilotu" ); ?> </h2>
					
					<?php
				    return;
				
				} 
				
				// si el post es nuevo
				else {
					
					$post_id = $this->guardar_servicio( "editar" );			
					
					if ( $post_id ): ?>
						
						<h2 class='alert alert-success'> <?php _e( "Su servicio se ha editado con éxito" , "estilotu" ); ?> </h2>

						<a class="et_button" href="<?php echo get_permalink( $post_id ); ?>" title="">Ver publicación</a>
						
						<?php
							
						
						set_transient( 'token_' . $this->token_id , 'servicio-editar', 3600 );
						
					else: ?>
						
						<h2 class='alert alert-danger'> <?php _e( "Ocurrió un error al editar su servicio" , "estilotu" ) ; ?> </h2>
					
					<?php	
					endif;	
				}

			endif;	
		
		// si no hay un servicio para editar	
		elseif ( empty($this->post_id) ):
			
			_e("Debe seleccionar un servicio a editar" , "estilotu");
		
		// si el usuario no es dueno de ese servicio	
		else:
			
			_e("No estás autorizado para editar este servicio" , "estilotu");
			
		endif; 	
	}
	
	
	
	/* *********************************************** */
	/* AGREGAR O EDITAR UN SERVICIO NUEVO */ 
	/* *********************************************** */
	private function guardar_servicio ( $accion = "" ) {  
		
		
		/* *********************************************** 	*/
		/* SI EL USER NO ESTA LOGEADO 	*/
		/* *********************************************** 	*/
		if ( !is_user_logged_in() )
			return;
				
		/* *********************************************** 	*/
		/* VALIDO QUE VENGA EL CODIGO NONCE DEL FORMULARIO	*/
		/* *********************************************** 	*/
		if ( !isset( $_POST['publicar_servicio_nonce'] ) || !wp_verify_nonce( $_POST['publicar_servicio_nonce'], 'publicar_servicio' ) ) {
		   print 'Se ha producido una falla, por favor intente m&aacute;s tarde';
		   exit;
		} 
					
		global $current_user;
		wp_get_current_user();
					
		$user_id			= $current_user->ID;
		$post_title     	= wp_strip_all_tags( $_POST['nombre_servicio'] );
		$post_content		= $_POST['description'] ;
		$tipo 				= wp_strip_all_tags( $_POST['et_meta_tipo']	 	);
		$categoria			= wp_strip_all_tags($_POST['categoria_servicio']) ;  
		//$tags				= wp_strip_all_tags ($_POST['servicios-etiquetas']	);
		//$tags 			= explode(',', $tags);
		$precio				= wp_strip_all_tags( $_POST['et_meta_precio']	 );
		$precio_moneda		= wp_strip_all_tags( $_POST['et_meta_precio_moneda']  );  
		$precio_visibilidad	= wp_strip_all_tags( $_POST['et_meta_precio_visibilidad']  );  
		$intensidad			= wp_strip_all_tags( $_POST['intensidad']	 );
		$max_time 			= wp_strip_all_tags( $_POST['et_meta_max_time']  );
		$facilities		 	= $_POST['facilities'] ;
		$tipo_locacion		= wp_strip_all_tags( $_POST['et_meta_tipo_locacion'] ) ;

		
		//ubicacion
		$ubicacion 		= array();
		foreach ( $_POST['ubicacion'] as $key => $value ):
			
			$ubicacion[$key] = wp_strip_all_tags($value);
			
		endforeach;
		
		/*
		$direccion_1 	= wp_strip_all_tags( $_POST['ubicacion']['et_meta_direccion_1']  );  
		$direccion_2 	= wp_strip_all_tags( $_POST['ubicacion']['et_meta_direccion_2']  );  
		$pais 			= wp_strip_all_tags( $_POST['ubicacion']['et_meta_pais']  );  
		$ciudad			= wp_strip_all_tags( $_POST['ubicacion']['et_meta_ciudad']  );  
		$estado			= wp_strip_all_tags( $_POST['ubicacion']['et_meta_estado']  );  
		$zipcode		= wp_strip_all_tags( $_POST['ubicacion']['et_meta_zipcode']  );  
		$usar_mapa		= wp_strip_all_tags( $_POST['ubicacion']['et_meta_usar_mapa']  );  
		$latitud		= wp_strip_all_tags( $_POST['ubicacion']['et_meta_latitud']  );  
		$longitud		= wp_strip_all_tags( $_POST['ubicacion']['et_meta_longitud']  );  
		*/
		
		$cerrar_cupo	= wp_strip_all_tags( $_POST['et_meta_close_time']  );  
		$disponibilidad	= $_POST['disponible'] ;  
		
		if ( $accion == "editar" ):

			$post_id = wp_update_post( array(
				'ID'			=> $this->post_id,
				'post_title'	=> $post_title,
				'post_content'	=> $post_content
				) );
			
		
		else:
			$post_id = wp_insert_post( array(
				'post_author'	=> $user_id,
				'post_title'	=> $post_title,
				'post_type'     => 'servicios',
				'post_content'	=> $post_content,
				'post_status'	=> 'publish'
				) );
		endif;

		wp_set_object_terms( $post_id, array($categoria), 'servicios-categoria' );
		
		/* ****************************************************************** 	*/ 
		/* GUARDA LOS META DATOS	*/ 
		/* ****************************************************************** 	*/ 
		update_post_meta($post_id, 'et_meta_tipo',		$tipo  );
		update_post_meta($post_id, 'et_meta_precio',	$precio  );
		update_post_meta($post_id, 'et_meta_precio_moneda',	$precio_moneda  );
		update_post_meta($post_id, 'et_meta_precio_visibilidad',	$precio_visibilidad  );
		update_post_meta($post_id, 'facilities',	$facilities  );
		update_post_meta($post_id, 'intensidad',	$intensidad  );
		update_post_meta($post_id, 'et_meta_tipo_locacion',	$tipo_locacion  );
		
		
		foreach ($ubicacion as $key => $value):
			
			update_post_meta($post_id, $key , $value  );
			
		endforeach;
		
/*
		update_post_meta($post_id, 'et_meta_usar_mapa',	$usar_mapa  );
		update_post_meta($post_id, 'et_meta_direccion_1',	$direccion_1  );
		update_post_meta($post_id, 'et_meta_direccion_2',	$direccion_2  );
		update_post_meta($post_id, 'et_meta_pais',		$pais  );
		update_post_meta($post_id, 'et_meta_zipcode',	$zipcode  );
		update_post_meta($post_id, 'et_meta_ciudad',	$ciudad  );
		update_post_meta($post_id, 'et_meta_estado',	$estado  );
		update_post_meta($post_id, 'et_meta_latitud',	$latitud  );
		update_post_meta($post_id, 'et_meta_longitud',	$longitud  )
*/;
		update_post_meta($post_id, 'et_meta_close_time',$cerrar_cupo  );
		update_post_meta($post_id, 'et_meta_max_time',	$max_time  );
		update_post_meta($post_id, 'disponibilidad_servicio', $disponibilidad  );
		/* ****************************************************************** 	*/ 
		
		/* ******************************************************* */
		/* GUARDA LA IMAGEN DEL POST */
		/* ******************************************************* */
		if( ! empty( $_FILES ) ) {
			foreach( $_FILES as $key=>$file ) {
				if( is_array( $file ) ) {
		
					if ($key == "imagen_destacada")
						$portada = true;
					else
						$portada = false;
						
					$attachment_id = $this->upload_user_file( $file , $portada );
						
					if ($portada)
						set_post_thumbnail( $post_id, $attachment_id );
					
				}
			}
		}
		
		return $post_id;
		
	}
	/* *********************************************** */
	
	/* *********************************************** */
	/* PROCESAR IMAGEN SUBIDA */
	/* *********************************************** */
	private function upload_user_file( $file = array() ) {
	
		require_once( ABSPATH . 'wp-admin/includes/admin.php' );
		
		$file_return = wp_handle_upload( $file, array('test_form' => false ) );
		
		if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
		
			return false;
		
		} else {
		
			$filename = $file_return['file'];
			
			$attachment = array(
				'post_mime_type' => $file_return['type'],
				'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content' => '',
				'post_status' => 'inherit',
				'guid' => $file_return['url']
			);
			
			$attachment_id = wp_insert_attachment( $attachment, $file_return['url'] );
			
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
			wp_update_attachment_metadata( $attachment_id, $attachment_data );
			
			if( 0 < intval( $attachment_id ) ) {
				return $attachment_id;
			}
		
		}
		
		return false;
	}
	/* *********************************************** */
	
	
	private function ibenic_file_upload() {
		
		$usingUploader = 1;
		
		$fileErrors = array(
			0 => "There is no error, the file uploaded with success",
			1 => "The uploaded file exceeds the upload_max_files in server settings",
			2 => "The uploaded file exceeds the MAX_FILE_SIZE from html form",
			3 => "The uploaded file uploaded only partially",
			4 => "No file was uploaded",
			6 => "Missing a temporary folder",
			7 => "Failed to write file to disk",
			8 => "A PHP extension stoped file to upload" 
		);
		
		$posted_data 	=  isset( $_POST ) ? $_POST : array();
		$file_data 		= isset( $_FILES ) ? $_FILES : array();
		
		$data 			= array_merge( $posted_data, $file_data );
		
		if( $usingUploader == 1 ) {
			$uploaded_file = wp_handle_upload( $data['ibenic_file_upload'], array( 'test_form' => false ) );
			if( $uploaded_file && ! isset( $uploaded_file['error'] ) ) {
				$response['response'] = "SUCCESS";
				$response['filename'] = basename( $uploaded_file['url'] );
				$response['url'] = $uploaded_file['url'];
				$response['type'] = $uploaded_file['type'];
			} else {
				$response['response'] = "ERROR";
				$response['error'] = $uploaded_file['error'];
			}
		} elseif ( $usingUploader == 2) {
			$attachment_id = media_handle_upload( 'ibenic_file_upload', 0 );
			
			if ( is_wp_error( $attachment_id ) ) { 
				$response['response'] = "ERROR";
				$response['error'] = $fileErrors[ $data['ibenic_file_upload']['error'] ];
			} else {
				$fullsize_path = get_attached_file( $attachment_id );
				$pathinfo = pathinfo( $fullsize_path );
				$url = wp_get_attachment_url( $attachment_id );
				$response['response'] = "SUCCESS";
				$response['filename'] = $pathinfo['filename'];
				$response['url'] = $url;
				$type = $pathinfo['extension'];
				if( $type == "jpeg"
				|| $type == "jpg"
				|| $type == "png"
				|| $type == "gif" ) {
					$type = "image/" . $type;
				}
				$response['type'] = $type;
			}
		}
		
		wp_send_json( $response );

	}
	
	
	private function ibenic_file_delete() {
		
		if( isset( $_POST ) ){
			
			global $wpdb;
			
			$fileurl = $_POST['fileurl'];
			$response = array();
			
			$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $fileurl ));
			
			if( $attachment ){
				$attachmentID = $attachment[0];
				if ( false === wp_delete_attachment( $attachmentID ) ) {
		
					$response['response'] = "ERROR";
					$response['error'] = 'File could not be deleted';
		
				} else {
					$response['response'] = "SUCCESS";
				}
			} else {
				$filename = basename( $fileurl );
				$upload_dir = wp_upload_dir();
		    		$upload_path = $upload_dir["basedir"]."/custom/";
		    		$uploaded_file = $upload_path . $filename;
				if(file_exists($uploaded_file)){
				
					@unlink($uploaded_file);
					$response['response'] = "SUCCESS";
				
				} else {
					$response['response'] = "ERROR";
					$response['error'] = 'File does not exist';
				}
			}
			
			//wp_send_json( $response );
		} 
		
		wp_die();
	}
	
	
	
	
	/* ************************************************************************ */
	/* LISTA LOS SERVICIOS DEL USUARIO QUE SE MUESTRA EN BUDDYPRESS 			*/
	/* ************************************************************************ */
	public function horarios_servicio ( $servicio_id ) {
		
	    //$this->provider_id = get_the_author_meta('ID');
		
		$servicio_meta = get_post_custom( $servicio_id ) ;
		
		$disponibilidad = unserialize($servicio_meta['disponibilidad_servicio'][0]);
		
		/* ******************************************** */
		/* VERSION 1.0 	*/
		/* ******************************************** */
		if ( isset($servicio_meta['bloque_lunes'][0]) )	
			$cuposLunes 	= unserialize( unserialize( $servicio_meta['bloque_lunes'][0] ) ) ;
		
		if ( isset($servicio_meta['bloque_martes'][0]) )	
			$cuposMartes 	= unserialize( unserialize( $servicio_meta['bloque_martes'][0] ) ) ;
	
		if ( isset($servicio_meta['bloque_miercoles'][0]) )		
			$cuposMiercoles = unserialize( unserialize( $servicio_meta['bloque_miercoles'][0] ) ) ;
		
		if ( isset($servicio_meta['bloque_jueves'][0]) )	
			$cuposJueves 	= unserialize( unserialize( $servicio_meta['bloque_jueves'][0] ) ) ;
		
		if ( isset($servicio_meta['bloque_viernes'][0]) )	
			$cuposViernes 	= unserialize( unserialize( $servicio_meta['bloque_viernes'][0] ) ) ;
		
		if ( isset($servicio_meta['bloque_sabado'][0]) )	
			$cuposSabado 	= unserialize( unserialize( $servicio_meta['bloque_sabado'][0] ) ) ;
		
		if ( isset($servicio_meta['bloque_domingo'][0]) )	
			$cuposDomingo 	= unserialize( unserialize( $servicio_meta['bloque_domingo'][0] ) ) ;	
		/* ******************************************** */
		
		/* ****************************************** 		*/
		/* BUSCO LOS DIAS LIBRES Y OCUPADOS DEL SERVICIO	*/
		/* ******************************************		*/
		$dias = array("domingo","lunes","martes","miercoles","jueves","viernes","sabado" );
		$dias_activados = array();
		$dias_desactivados = array();
				
		/* ****************************************** 	*/
		/* SI SE ESTA USANDO LA VERSION 1.0 			*/
		/* ******************************************	*/
		if ( !isset($servicio_meta['disponibilidad_servicio']) && ( isset($cuposDomingo) || isset($cuposLunes) || isset($cuposMartes) || isset($cuposMiercoles) || isset($cuposJueves) || isset($cuposViernes) || isset($cuposSabado) ) ):
		
			$this->old_version = true;

			foreach ( $dias as $key_dia => $dia_disponible ):
				
				if ( $servicio_meta['et_meta_dias_activo_'.$dia_disponible][0] == "on" ):
				
					$dias_activados[] = $key_dia;
				
				else:
					
					$dias_desactivados[] = $key_dia;
					
				endif;	

			endforeach;
		/* ******************************************	*/
		
		/* ****************************************** */
		/* SI SE ESTA USANDO LA VERSION 2.0 */
		/* ****************************************** */
		elseif ( isset( $servicio_meta["disponibilidad_servicio"][0]  ) ):
			
			$disponibilidad = unserialize($servicio_meta['disponibilidad_servicio'][0]);
				
				foreach ( $dias as $key_dia => $dia_disponible ):
					
					if ( array_key_exists ("activo" , $disponibilidad[$dia_disponible]) ):
						
						foreach ( $disponibilidad[$dia_disponible]["bloque"] as $key_bloque => $info_bloque ):
	
							$dias_activados[$key_dia][] = $info_bloque;
						
						endforeach;
					
					else:
						
						$dias_desactivados[] = $key_dia;
						
					endif;	
	
	
				endforeach;
		
		endif;
		/* ****************************************** */
			
		return array( "dias_activados" => $dias_activados , "dias_desactivados" => $dias_desactivados );
	}
	
	/* ************************************************************************ */
	/* OBTENERR CUPOS DE UN SERVICIO								 			*/
	/* ************************************************************************ */
	public function cargar_cupos ( ) {
		
		global $wpdb;
		
		$fecha_seleccionada = $_POST['fecha_seleccionada'] ;
		$id_servicio 		= $_POST['id_servicio'];
		$current_user_id	= get_current_user_id();
		
		$table_name 		= $wpdb->prefix . "bb_appoinments";
		
		$citas = $wpdb->prepare("SELECT appoinment_time , appoinment_user_id FROM $table_name WHERE appoinment_date = %s AND appoinment_service_id = %d AND (appoinment_status = 'confirm' OR appoinment_status = 'hold' )" , $fecha_seleccionada , $id_servicio ); 						
		$citas = $wpdb->get_results($citas , ARRAY_A);
		
		$horarios_servicio 	= $this->horarios_servicio( $id_servicio );
		$dayofweek 			= date('w', strtotime($fecha_seleccionada));
		$bloque_seleccionado = $horarios_servicio['dias_activados'][$dayofweek];
		
		
		if ( isset($citas) && is_array($citas) ):
				
			// Creo un array para las horas duplicadas de este dia
			$ocupado = array();
			foreach ($citas as $key => $value){
			    
			    foreach ($value as $key2 => $value2){
			        
			        if ( $key2 == "appoinment_time") {
				        $index = $value2;
				        if (array_key_exists($index, $ocupado)){
				            $ocupado[$index]++;
				        } else {
				            $ocupado[$index] = 1;
				        }	
					}
			    
			    }   
			}
			
			// Creo un array para las horas que el usuario ya tiene reserva
			$reservado = array();
			foreach ($citas as $key => $value){
			    
			    foreach ($value as $key2 => $value2){
			        
			        if ( $key2 == "appoinment_user_id" && $value2 == $current_user_id ) {
				        $index = $value["appoinment_time"];
				        if (!array_key_exists($index, $reservado)){
				            $reservado[$index] = true;
				        }	
					}
				}	
			}
		 
			
			$disponibilidad[$dia]["ocupado"] 	= $ocupado;
			$disponibilidad[$dia]["reservado"] 	= $reservado;
							
			ob_start("ob_gzhandler");
				$return = array( "cupos" => $disponibilidad[$dia] , "bloque_seleccionado" => $bloque_seleccionado );
			ob_end_clean();
			
		endif;	
		
		wp_send_json($return) ;
	}
	
	public function closest_services() {
		
		$servicios_repetidos = $_POST['servicios_repetidos'];
		
		$lat = $_POST['latitud'];
		$lng = $_POST['longitud'];
		$categoria = array();
		$unit = $_POST['unit'];
		$distancia = $_POST['radius'];;
		
		if ( $unit == 'km' ) { $radius_earth = 6371.009; }
		elseif ( $unit == 'mi' ) { $radius_earth = 3958.761; }

		
		$maxLat = ( float ) $lat + rad2deg($distancia / $radius_earth);
	    $minLat = ( float ) $lat - rad2deg($distancia / $radius_earth);
	    
	    $maxLng = ( float ) $lng + rad2deg( $distancia / $radius_earth ) / cos( deg2rad( ( float ) $lat) ) ;
	    $minLng = ( float ) $lng - rad2deg( $distancia / $radius_earth ) / cos( deg2rad( ( float ) $lat) ) ;
		
		//if ( !empty( $_POST['categoria'] ) ):
		
			$categoria = array(
		        array(
		        'taxonomy' => 'servicios-categoria',
		        'field' => 'slug',
		        'terms' => $_POST['categoria'],
		        'operator'  => ( empty( $_POST['categoria'] ) ? 'EXISTS' : 'IN' )
		        )
		    );
		
		//endif;
				
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
		    'tax_query' => $categoria
		);
 
		$closest_services_query = new WP_Query( $closest_services_args );
		
		//echo $closest_services_query->post_count;

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
		
		//return ;
		wp_send_json($servicio);
		
	}
	
}