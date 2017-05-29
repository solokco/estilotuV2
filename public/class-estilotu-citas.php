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
	
	private function obtener_citas( $user_id = null, $status = null , $fecha = null ) {  
		
		global $wpdb;
		global $wp_query;
		global $current_user;
		
		// $tablename_citas = $wpdb->prefix . "bb_appoinments";
						
		$this->es_historial = isset($wp_query->query_vars['servicios']) && ($wp_query->query_vars['servicios'] == "historial"); 
				
		if ($this->es_historial):
			$time_period = "appoinment_date < CURDATE()";
			
		else: 
			$time_period = "appoinment_date >= CURDATE()";
		
		endif;
					
		if ($user_id == null)
			$user_id = get_current_user_id();
			
		if ( !isset($fecha) )	
			$fecha = date("Y-m-d");	
		
		$sql = $wpdb->prepare( "SELECT * FROM $this->tablename_citas WHERE appoinment_provider_id = %d ORDER BY appoinment_date ASC, appoinment_time ASC", $user_id );
		// es proveedor
/*
		if ( $this->ver_citas == "recibidas" && $this->es_proveedor ):
			if ($status == null)	
				$sql = $wpdb->prepare( "SELECT * FROM $this->tablename_citas WHERE appoinment_provider_id = %d AND $time_period  ORDER BY appoinment_date ASC, appoinment_time ASC", $user_id );
			else 
				$sql = $wpdb->prepare( "SELECT * FROM $this->tablename_citas WHERE appoinment_provider_id = %d AND appoinment_status = %s AND $time_period ORDER BY appoinment_date ASC, appoinment_time ASC", $user_id , $status );

		//es usuario 
		else:
			if ($status == null)	
				$sql = $wpdb->prepare( "SELECT * FROM $this->tablename_citas WHERE appoinment_user_id = %d AND $time_period  ORDER BY appoinment_date ASC, appoinment_time ASC", $user_id );
			else 
				$sql = $wpdb->prepare( "SELECT * FROM $this->tablename_citas WHERE appoinment_user_id = %d AND appoinment_status = %s AND $time_period ORDER BY appoinment_date ASC, appoinment_time ASC", $user_id , $status);
		endif;
*/
									
		$result = $wpdb->get_results( $sql, OBJECT );
						
		return $result;
	}
	/* *********************************************** */
	 
	public function citas_recibidas_listar(){
		global $bp;	
		global $wp_query;
		global $current_user;
		
		$this->is_member = Estilotu_Buddypress::validar_miebro();
		
		// si viene con los datos para guardar
		if ( isset( $_POST['guardar_cita_nonce'] ) ): 
		
			if (wp_verify_nonce( $_POST['guardar_cita_nonce'] , 'guardar_cita' ) ): 
			
				$user_id 		= get_current_user_id();
				$id_servicio 	= $_POST['id_servicio'];
				$fecha_servicio	= $_POST['servicio_dia_seleccionado'];
				$hora_servicio	= $_POST['et_meta_hora_inicio'];
				
				$servicio		= get_post( $id_servicio );
				$id_provider 	= $servicio->post_author;
	
				$token_id = stripslashes( $_POST['et_token'] );
				
				// si ya existe un token que indica que se guardo el post
				if ( get_transient( 'token_' . $token_id ) ) {
	
				    _e("Usted ya guardo esta cita");
				    return;
				
				} 
				
				// si el post es nuevo
				else {				
					
					if ( $this->guardar_cita( $id_servicio , $id_provider , $fecha_servicio , $hora_servicio ) ):
					
						_e("Su cita se ha guardado con éxito");
						set_transient( 'token_' . $token_id , 'cita-guardada', 86400 );
					
					else:
					
						_e("Ocurrió un error al editar su servicio");
					
					endif;	
				}
				
			endif;
			
		endif;
		
		$citas_recibidas = $this->obtener_citas();

		wp_enqueue_style( 'estilotu-citas' );
		wp_enqueue_script( 'estilotu_citas_opciones' );
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/citas/recibidas_listar.php' ;
		
		return;	
	}
	
	public function citas_realizadas_listar(){
		global $bp;	
		global $wp_query;
		global $current_user;
		
		$id_provider = $bp->displayed_user->id;
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/citas/realizadas_listar.php' ;
		
	}
	
	private function guardar_cita( $id_servicio , $id_provider , $fecha_servicio , $hora_servicio ) {
		
		global $wpdb;

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

}