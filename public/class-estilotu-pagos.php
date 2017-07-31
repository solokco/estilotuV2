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
class Estilotu_Pagos extends Estilotu_Public {

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
	/* **************************************** */
	/* OBTENER CITAS							*/
	/* **************************************** */
	public function total_pagos( $status_pago = true , $proveedor_id = null ) { 
		
		global $wpdb;
		
		if ( $proveedor_id == null || !is_int($proveedor_id) )
			$proveedor_id = get_current_user_id();
		
		if ( $status_pago ):
		
			$status = " AND appoinment_user_paid = %d ";
		
		else:
		
			$status = " AND (appoinment_user_paid = %d OR appoinment_user_paid is NULL) ";
		
		endif;
		
		$query = $wpdb->prepare(" 
			
			SELECT 
			appoinment_currency AS currency, 
			SUM(appoinment_price) AS monto_pendiente
			
			FROM 
			$this->tablename_citas 
			
			WHERE 
			appoinment_provider_id = %d
			$status
			AND appoinment_price > %d 
			
			GROUP BY appoinment_currency" ,

			$proveedor_id,
			$status_pago,
			0
		);

		$result = $wpdb->get_results( $query );
		
		return $result;
		
	}
	
	
	/* **************************************** */
	/* OBTENER CITAS							*/
	/* **************************************** */
	public function obtener_pagos( $args = array() ) {  
		
		if ( current_user_can ( "manage_options" ) ):
		
		elseif ( bp_displayed_user_id() != get_current_user_id() ):
			return; 
		
		endif;
		
		global $wpdb;
		global $wp_query;
		global $current_user;
		
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
		
		$query = "	SELECT 
					appoinment_service_id AS id_servicio,
					appoinment_user_id AS id_user, 
					COUNT(appoinment_user_id) AS pagos_pendientes, 
					appoinment_user_paid AS status_pago, 
					appoinment_currency AS currency, 
					SUM(appoinment_price) AS monto_pendiente
					
					FROM $this->tablename_citas 
					
					WHERE appoinment_currency <> '' 
					AND appoinment_price > 0 
					AND appoinment_status = '$status' ";
		
		/* **************************************** */
		/* FILTRO POR FECHA							*/
		/* **************************************** */
		if ( isset( $args['fecha'] ) ):
		
			if ( !isset( $args['fecha']['from'] ) ) :

				$from_date = " AND appoinment_date >= CURDATE() ";
				
			else:

				$from_date = " AND appoinment_date >= '" . $args['fecha']['from'] . "' " ;
			
			endif;
			
			if ( !isset( $args['fecha']['to'] ) ):
			
				$to_date 	= "AND appoinment_date <= '" . date("Y-m-d", strtotime("+1 week") ) . "' "  ;
			
			else:

				$to_date 	= "AND appoinment_date <= '". $args['fecha']['to'] . "' ";
			
			endif;
			
			$time_period = $from_date . $to_date;		
			
			$query .= $time_period;
		endif;
		/* **************************************** */
		
		/* **************************************** */
		/* STATUS PAGO								*/
		/* **************************************** */			
		if ( ! isset( $args['user_paid'] ) || $args['user_paid'] == 0 || empty($args['user_paid'] ) ) :
			
			$user_paid 	= $wpdb->prepare(' AND (appoinment_user_paid = %d OR appoinment_user_paid is NULL ) ', 0 );
			$query .= $user_paid;
		else:

			$user_paid 	= $wpdb->prepare(' AND (appoinment_user_paid = %d ) ', 1 );
			
			$query .= $user_paid;
			
		endif;
		/* **************************************** */
		
		/* **************************************** */
		/* USUARIO A BUSCAR							*/
		/* **************************************** */			
		if ( ! isset( $args['user_id'] ) ):
		
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
		
		else:
			
			$provider_id 	= get_current_user_id();
			$provider_case 	= $wpdb->prepare('AND appoinment_provider_id = %d ', $provider_id);
			
			$query .= $provider_case;
			
		endif;
		/* **************************************** */	
		
		/* **************************************** */
		/* FILTRO POR ASISTENCIA						*/
		/* **************************************** */
		if ( isset( $args['asistencia'] ) ):

			$query .= $wpdb->prepare('AND appoinment_user_assist = %d ', $args['asistencia'] );
			
		endif; 
		/* **************************************** */		
		
		$query 	.= " GROUP BY id_servicio, appoinment_user_id, appoinment_currency, status_pago";
		$query 	.= " ORDER BY appoinment_date ASC, appoinment_time ASC ";
		$query 	.= " LIMIT $per_page";
		$query 	.= " OFFSET " . ( $page_number - 1 ) * $per_page;
						
		$result = $wpdb->get_results( $query , $type );
		
		//echo $query;
						
		return $result;
	}
	/* *********************************************** */
	
	public function listar_pagos_profesional() {
		
		$total_generado = $this->total_pagos();
		$total_pendiente = $this->total_pagos( false );
		
		$args = array(
			"fecha" => array(
				"from" => '2016-01-01',
				"to" => '2018-01-01'
			)
		);
		
		$pagos = $this->obtener_pagos($args);
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/pagos/list.php' ;
	}
	
}