<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://estoesweb.com
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
 * @author     Carlos Carmona <ccarmona@estoesweb.com>
 */
class Estilotu_Citas extends Estilotu_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	private $tablename_citas;
	
	private $is_member;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( ) {
		
		global $wpdb;
		$this->tablename_citas = $wpdb->prefix . "bb_appoinments";

	}

	/**
	* Register the stylesheets for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	
	public static function cantidad_citas( $user_id = null , $fecha = null , $status = "confirm" ) {
		global $wpdb;
		global $wp_query;
		
		if ( $fecha == null ):

			$time_period = "appoinment_date >= CURDATE() ";
			
		elseif ( $fecha == "all" ): 
			
			$time_period = "appoinment_date >= CURDATE() OR appoinment_date <= CURDATE() ";
		
		else:
		
			$time_period = "appoinment_date = '$fecha' ";
		
		endif;
		
		$query = "SELECT COUNT(*) FROM {$wpdb->prefix}bb_appoinments WHERE ($time_period) AND appoinment_status = '$status' ";
		
		if ($user_id == null):
			
			$user_id = get_current_user_id();
			$user_case = $wpdb->prepare(' AND appoinment_provider_id = %d ', $user_id);
			
			$query .= $user_case;
		
		elseif ($user_id != "all"):
			
			$user_case = $wpdb->prepare(' AND appoinment_provider_id = %d ', $user_id);
			
			$query .= $user_case;
			
		endif;
		
		return $wpdb->get_var( $query );
	}
	
	
	/* **************************************** */
	/* OBTENER CITAS							*/
	/* **************************************** */
	public function obtener_citas( $args = array() ) {  
		
		global $wpdb;
		global $wp_query;
		global $current_user;
		
		/* **************************************** */
		/* FILTRO POR FECHA							*/
		/* **************************************** */
		if ( !isset( $args['fecha'] ) ):

			$from_date 	= "appoinment_date >= CURDATE() ";
			$to_date 	= "AND appoinment_date <= '" . date("Y-m-d", strtotime("+1 week") ) . "' "  ;
			
		elseif ( $args['fecha'] == "all" ):

			$from_date 	= "appoinment_date >= CURDATE() ";
			$to_date 	= "OR appoinment_date <= CURDATE() ";
		
		else:
		
			if ( !isset( $args['fecha']['from'] ) ) :

				$from_date = "appoinment_date >= CURDATE() ";
				
			else:

				$from_date = "appoinment_date >= '" . $args['fecha']['from'] . "' " ;
			
			endif;
			
			if ( !isset( $args['fecha']['to'] ) ):
			
				$to_date 	= "AND appoinment_date <= '" . date("Y-m-d", strtotime("+1 week") ) . "' "  ;
			
			else:

				$to_date 	= "AND appoinment_date <= '". $args['fecha']['to'] . "' ";
			
			endif;
		
		endif;
		
		$time_period = $from_date . $to_date;		
		/* **************************************** */
		
		/* **************************************** */
		/* FILTRO POR STATUS						*/
		/* **************************************** */
		if ( !isset( $args['status'] ) ):
			$status = "confirm";
		
		else:
			
			$status = $args['status'];
			
		endif; 
		/* **************************************** */
		
		
		/* **************************************** */
		/* TIPO DE RETORNO 							*/
		/* **************************************** */
		if ( !isset( $args['type'] ) ):
			$type = "OBJECT";
		
		else:
			
			$type = $args['type'];
			
		endif;
		/* **************************************** */
		
		/* **************************************** */
		/* PAGINA ACTUAL							*/
		/* **************************************** */
		if ( !isset( $args['page_number'] ) ):
			
			$page_number = 1 ;
		
		else:
			
			$page_number = $args['page_number'];
			
		endif;
		/* **************************************** */
		
		/* **************************************** */
		/* CANTIDAD POR PAGINA						*/
		/* **************************************** */
		if ( !isset( $args['per_page'] ) ):
			
			$per_page = 200 ;
		
		else:
			
			$per_page = $args['per_page'];
			
		endif;
		/* **************************************** */
		
		$query = "SELECT * FROM $this->tablename_citas WHERE ($time_period) AND appoinment_status = '$status' ";
		
		
		/* **************************************** */
		/* USUARIO A BUSCAR							*/
		/* **************************************** */			
		if ( ! isset( $args['user_id'] ) ):
			
			$user_id 	= get_current_user_id();
			$user_case 	= $wpdb->prepare('AND appoinment_user_id = %d ', $user_id);
			
			$query .= $user_case;
		
		elseif ( $args['user_id'] == "all" ) :
		
		else:
			
			$user_id	= $args['user_id'];
			$user_case 	= $wpdb->prepare(' AND appoinment_user_id = %d ', $user_id);
			
			$query .= $user_case;
			
		endif;
		/* **************************************** */
		
		/* **************************************** */
		/* PROVIDER A BUSCAR						*/
		/* **************************************** */			
		if ( isset( $args['provider_id'] ) && $args['provider_id'] != "all"):
			
			$provider_id 	= $args['provider_id'];
			$provider_case 	= $wpdb->prepare('AND appoinment_provider_id = %d ', $provider_id);
			
			$query .= $provider_case;
			
		endif;
		/* **************************************** */			
		
		$query 	.= " ORDER BY appoinment_date ASC, appoinment_time ASC ";
		$query 	.= " LIMIT $per_page";
		$query 	.= " OFFSET " . ( $page_number - 1 ) * $per_page;
						
		$result = $wpdb->get_results( $query , $type );
						
		return $result;
	}
	/* *********************************************** */
	
	public function validar_accion ( $args ) {
		
		// si viene con los datos para guardar
		if ( isset( $args['guardar_cita_nonce'] ) ): 
		
			if (wp_verify_nonce( $args['guardar_cita_nonce'] , 'guardar_cita' ) ): 

				$token_id = stripslashes( $args['et_token'] );
				
				// si ya existe un token que indica que se guardo el post
				if ( get_transient( 'token_' . $token_id ) ) {
	
				    _e("Usted ya guardo esta cita");
				    return;
				
				} 
				
				// si el post es nuevo
				else {				
					
					$user_id 		= get_current_user_id();
					$id_servicio 	= $args['id_servicio'];
					$fecha_servicio	= $args['servicio_dia_seleccionado'];
					$hora_servicio	= $args['et_meta_hora_inicio'];
					
					$servicio		= get_post( $id_servicio );
					$id_provider 	= $servicio->post_author;
					
					$provider_info 	= get_userdata( $id_provider );
					$user_info 		= get_userdata( $user_id );
					
					if ( $this->guardar_cita( $id_servicio , $id_provider , $fecha_servicio , $hora_servicio ) ):
					
						_e("Su cita se ha guardado con éxito");
						set_transient( 'token_' . $token_id , 'cita-guardada', 86400 );
						
						$email_proveedor = (new Estilotu_Email)
						    ->to($provider_info->user_email)
						    ->subject("Nueva cita el $fecha_servicio a las $hora_servicio de $user_info->first_name")
						    ->template( 'cita_nueva_proveedor.php' , [
						        'name_provider' 	=> $provider_info->first_name,
						        'servicio'  		=> $servicio->post_title,
						        'fecha'				=> $fecha_servicio,
						        'hora'				=> $hora_servicio,
						        'name_user' 		=> $user_info->first_name,
						        'email_user' 		=> $user_info->user_email,
						        'url'				=> ESTILOTU_URL
						    ] )
						    ->send();
						
						$email_cliente = (new Estilotu_Email)
						    ->to($user_info->user_email)
						    ->subject("$user_info->first_name, has realizado una nueva cita el $fecha_servicio a las $hora_servicio")
						    ->template( 'cita_nueva_cliente.php' , [
						        'name_provider' 	=> $provider_info->first_name,
						        'email_provider' 	=> $provider_info->user_email,
						        'servicio'  		=> $servicio->post_title,
						        'fecha'				=> $fecha_servicio,
						        'hora'				=> $hora_servicio,
						        'name_user' 		=> $user_info->first_name,
						        'email_user' 		=> $user_info->user_email,
						        'url'				=> ESTILOTU_URL
						    ] )
						    ->send();
						
					else:
					
						_e("Ocurrió un error al editar su servicio");
					
					endif;	
				}
				
			endif;
		
		elseif ( isset( $args['cancelar_cita_nonce'] ) ):
		
			if (wp_verify_nonce( $args['cancelar_cita_nonce'] , 'cancelar_cita' ) ):
				
				if ( isset( $args["fecha"] ) )
					$fecha = $args["fecha"];
				
				if ( isset( $args["hora"] ) )	
					$hora = $args["hora"];
				else	
					$hora = null;
				
				if ( $this->cambiar_status_cita( $args["status"] , $fecha , $hora  ) ):
				
					echo "<h2>";
						_e("La citas se modificaron con éxito");
					echo "</h2>";
					
				endif;
				
			endif;
			
		endif;
		
		return ;
	
	} 
	
	public function citas_recibidas_listar(){
		global $bp;	
		global $wp_query;
		global $current_user;
		
		$args = array(
			"user_id"	=> "all",
			"provider_id"	=> get_current_user_id()
		);
		
		$this->is_member = Estilotu_Buddypress::validar_miebro();
		
		$this->validar_accion($_POST);
		
		if ( isset( $_POST['filtros_citas_nonce'] ) ): 
		
			if (wp_verify_nonce( $_POST['filtros_citas_nonce'] , 'filtros_citas' ) ): 
				
				if ( isset($_POST['date_from-filter'] ) )
					$args['fecha']["from"] = $_POST['date_from-filter'] ;
				
				if ( isset($_POST['date_to-filter'] ) ) 
					$args['fecha']["to"] = $_POST['date_to-filter'] ;
				
				if ( isset($_POST['status-filter'] ) ) 
					$args['status'] = $_POST['status-filter'] ;
				
			endif;

		endif;

		$fecha_from 	= !empty( $_POST['date_from-filter'] ) 	? $_POST['date_from-filter'] : date("Y-m-d");
		$fecha_to 		= !empty( $_POST['date_to-filter'] ) 	? $_POST['date_to-filter'] : date("Y-m-d", strtotime("+1 month") );
		$selected 		= !empty( $_POST['status-filter'] ) 	? $_POST['status-filter'] : "confirm";
		
		$args['fecha']['from'] = $fecha_from;
		$args['fecha']['to'] = $fecha_to;
		
		$citas_recibidas = $this->obtener_citas( $args );

		wp_enqueue_style( 'estilotu-citas' );
		wp_enqueue_script( 'estilotu_citas_opciones' );
		wp_enqueue_script( 'jquery-ui-datepicker');
		wp_enqueue_style( 'jquery-ui-datepicker-style' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
		
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/citas/recibidas_listar.php' ;
		
		return;	
	}
	
	public function citas_realizadas_listar(){
		global $bp;	
		global $wp_query;
		global $current_user;

		$args = array();
		
		$this->validar_accion($_POST);
		
		if ( isset( $_POST['filtros_citas_nonce'] ) ): 
		
			if (wp_verify_nonce( $_POST['filtros_citas_nonce'] , 'filtros_citas' ) ): 
				
				if ( isset($_POST['date_from-filter'] ) )
					$args['fecha']["from"] = $_POST['date_from-filter'] ;
				
				if ( isset($_POST['date_to-filter'] ) ) 
					$args['fecha']["to"] = $_POST['date_to-filter'] ;
				
				if ( isset($_POST['status-filter'] ) ) 
					$args['status'] = $_POST['status-filter'] ;
				
			endif;

		endif;
		
		$fecha_from 	= !empty( $_POST['date_from-filter'] ) 	? $_POST['date_from-filter'] : date("Y-m-d");
		$fecha_to 		= !empty( $_POST['date_to-filter'] ) 	? $_POST['date_to-filter'] : date("Y-m-d", strtotime("+1 month") );
		$selected 		= !empty( $_POST['status-filter'] ) 	? $_POST['status-filter'] : "confirm";
		
		$args['fecha']['from'] = $fecha_from;
		$args['fecha']['to'] = $fecha_to;
		
		$citas_recibidas = $this->obtener_citas( $args );

		wp_enqueue_style( 'estilotu-citas' );
		wp_enqueue_script( 'estilotu_citas_opciones' );
		wp_enqueue_script( 'jquery-ui-datepicker');
		wp_enqueue_style( 'jquery-ui-datepicker-style' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
		
		
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/citas/realizadas_listar.php' ;
		
	}
	
	private function guardar_cita( $id_servicio , $id_provider , $fecha_servicio , $hora_servicio ) {
		
		global $wpdb;
		
		do_action( 'notificar_cita' , $id_servicio );
		
		$data = array( 
					'appoinment_date' 			=> $fecha_servicio, 
					'appoinment_time' 			=> $hora_servicio, 
					'appoinment_provider_id' 	=> $id_provider, 
					'appoinment_user_id' 		=> get_current_user_id(), 
					'appoinment_service_id'		=> $id_servicio, 
					'appoinment_status'			=> "confirm",
					'update_time'	 			=> current_time("Y-m-d H:i:s")
				);

		$result = $wpdb->insert( $this->tablename_citas , $data);
				
		if ($result != false ):

			//$this->add_to_stream($id_servicio);
			
			//$enviar_email_usuario 		= new Estilotu_Emails( $current_user->user_email , "reserva_usuario" , $data );
			
			//$provider_info = get_userdata( $id_provider );
			//$enviar_email_profesional 	= new Estilotu_Emails( $provider_info->user_email , "notificacion_profesional" , $data );
			
			return $result;

		endif;
		
		return false;
		
	}
	
	/* ************************************************* */ 
	/* CANCELAR CITA AJAX								 */
	/* ************************************************* */ 
	public function cambiar_status_cita_ajax() {
		
		global $wpdb;
		
		$id_cita = $_POST['id_cita'];
		
		if ( $_POST['status'] == "Confirmar" )
			$status_cita = "confirm";			
		
		elseif ($_POST['status'] == "Cancelar" )
			$status_cita = "cancel";	
		
		else
			$status_cita = "on hold";
		
		$data = array(
			'appoinment_status' => $status_cita
		);
		
		$where = array(
			'appoinment_id' => $id_cita
		);

		wp_send_json( $wpdb->update( $this->tablename_citas , $data, $where ) );
	
	}
	/* ************************************************* */ 
	
	/* ************************************************* */ 
	/* CANCELAR CITA								 */
	/* ************************************************* */ 
	public function cambiar_status_cita( $status_cita = null , $fecha = null , $hora = null ) {
		
		global $wpdb;
		//$tablename = $wpdb->prefix . "bb_appoinments";
		$data = array();
		$where = array();
		
		$data = array(
			'appoinment_status' => $status_cita
		);
		
		if ( $fecha != null )
			$where['appoinment_date'] = $fecha;
		
		if ( $hora != null )
			$where['appoinment_time'] = $hora ;

		
		return $wpdb->update( $this->tablename_citas , $data, $where ) ;
	
	}
	/* ************************************************* */ 
	
	/* ************************************************* */ 
	/* REGISTRAR ASISTENCIA DE PARTICIPANTE */
	/* ************************************************* */ 
	public function registrar_asistencia_y_pago_participante() {
		
		/* *********************** */
		/* REVISO SEGURIDAD */
		/* *********************** */
	
		// check_ajax_referer( 'et_ajax_asistencia_y_pago_participante_nonce' , 'security' );
	
		/* *********************** */
		
		global $wpdb;
		//$tablename = $wpdb->prefix . "bb_appoinments";
		
		$id_cita = $_POST['id_cita'];
		

		$data = array(
				'appoinment_user_assist' => $_POST['appoinment_user_assist'],
				'appoinment_user_paid' => $_POST['appoinment_user_paid']
			);
		
		$where = array(
			'appoinment_id' => $id_cita
		
		);

		wp_send_json( $wpdb->update( $this->tablename_citas , $data, $where ) );
		
	}
	/* ************************************************* */ 
	
	/* ************************************************* */ 
	/* SHORTCODE */
	/* ************************************************* */
	public function listar_citas_func( $atts ) {

		$cita_atts = shortcode_atts( array(
        	'type'	=> 'OBJECT',
        	'desde'	=> date("Y-m-d"),
        	'hasta'	=> date("Y-m-d", strtotime("+1 month") ),
        	'size'	=> "size0_75x",
        	'class_lista'	=> "shortcode_citas lista_citas",
        	'class_fecha'	=> "date-as-calendar inline-flex"
               	
	    ) , $atts );

		$cita_atts["fecha"]["from"] = $cita_atts['desde']; 
		$cita_atts["fecha"]["to"] 	= $cita_atts['hasta']; 
				
		$citas = $this->obtener_citas( $cita_atts );
		
		if ( empty( $citas) )
			return _e("No hay citas cercanas" , "estilotu");

		
			
		$html = "";
		
		foreach ( $citas as $cita ):
			
			$servicio = get_post( $cita->appoinment_service_id );
			
			$html .= "<div class='row'>";
			$html .= 	"<div class='col-sm-2 wpb_column column_container'>";
			$html .= 		"<time datetime='" . $cita->appoinment_date . " " . $cita->appoinment_time . "' class='" . $cita_atts['class_fecha'] . " " . $cita_atts['size'] . "'>";
			$html .= 			"<span class='weekday'>" . date( 'l', strtotime($cita->appoinment_date ) ) . "</span>";
			$html .= 			"<span class='day'>" . date( 'd', strtotime($cita->appoinment_date ) ) . "</span>";
			$html .= 			"<span class='month'>" . date( 'F', strtotime($cita->appoinment_date ) ) . "</span>";
			$html .= 			"<span class='year'>" . date( 'Y', strtotime($cita->appoinment_date ) ) . "</span>";
			$html .= 		"</time>";
			$html .= 	"</div>";
					 	
			$html .= 	"<div class='col-sm-10 wpb_column column_container'>";
			$html .= 		"<ul class='" . $cita_atts['class_lista'] . "'>";
			$html .= 			"<li class='servicio'><a href='" . esc_url( get_permalink( $cita->appoinment_service_id ) ) . "'>$servicio->post_title</a></li>";
			$html .= 			"<li class='hora'>" . date( 'h:i A', strtotime($cita->appoinment_time ) ) . "</li>";
			$html .= 		"</ul>";
			$html .= 	"</div>";
			$html .= "</div>";
			
			$html .= "<br>";
		endforeach;

		return $html;
	
	}
	
}