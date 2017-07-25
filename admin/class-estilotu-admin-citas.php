<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://estoesweb.com
 * @since      1.0.0
 *
 * @package    Estilotu
 * @subpackage Estilotu/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Estilotu
 * @subpackage Estilotu/admin
 * @author     Carlos Carmona <ccarmona@estoesweb.com>
 */

if ( !class_exists( 'WP_List_Table' ) ){ require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ); }
	
class Estilotu_Admin_Citas extends WP_List_Table {

	public function __construct( ) {
			
		parent::__construct( array(
			'singular'=> __("Cita" , "estilotu") , //Singular label
			'plural' => __("Citas" , "estilotu") , //plural label, also this well be one of the table css class
			'ajax'   => false //We won't support Ajax for this table
			) 
		);
			
	}
	
	protected function get_views() { 
	    $status_links = array(
	        "all"       => __("<a href='#'>All</a>",'my-plugin-slug'),
	        "published" => __("<a href='#'>Published</a>",'my-plugin-slug'),
	        "trashed"   => __("<a href='#'>Trashed</a>",'my-plugin-slug')
	    );
	    return $status_links;
	}
	
	public function get_columns(){
	  $columns = array(
            'cb'        				=> '<input type="checkbox" />', //Render a checkbox instead of text
            'appoinment_id'         	=> 'ID',
            'appoinment_date'       	=> 'Fecha',
            'appoinment_time' 			=> 'Hora',
            'appoinment_provider_id'	=> 'Entrenador',
            'appoinment_service_id'    	=> 'Servicio',
            'appoinment_status'      	=> 'Status'
        );
        return $columns;
	}
	
	/** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
	public function prepare_items() {
		$columns 	= $this->get_columns();
		$hidden 	= array();
		$sortable 	= $this->get_sortable_columns();	
		
		$status_cita 	= !empty( $_REQUEST['status-filter'] ) ? $_REQUEST['status-filter'] : 'confirm';
		$proveedor_cita = !empty( $_REQUEST['provider-filter'] ) ? $_REQUEST['provider-filter'] : 'all';
		$user_id		= !empty( $_REQUEST['user_id-filter'] ) ? $_REQUEST['user_id-filter'] : 'all';
		$fecha_from 	= !empty( $_REQUEST['date_from-filter'] ) ? $_REQUEST['date_from-filter'] : date("Y-m-d");
		$fecha_to 		= !empty( $_REQUEST['date_to-filter'] ) ? $_REQUEST['date_to-filter'] : date("Y-m-d", strtotime("+1 week") );
		
		$fecha = array("from" => $fecha_from, "to" => $fecha_to);
		
		$data = new Estilotu_Citas();
		
		$per_page 	= 200;
		$current_page = $this->get_pagenum();
		$total_items = $data->cantidad_citas( $proveedor_cita , "" , $status_cita );
		
		$args = array(
		
			"fecha" => array(
				"from" 	=>	$fecha_from,
				"to" 	=> 	$fecha_to
				
			),
			
			"status" 	=> $status_cita,
			"provider_id"	=> $proveedor_cita,
			"user_id"	=> $user_id,
			"per_page"	=> $per_page,
			"type"		=> "ARRAY_A",	
			"page_number" => $current_page
		);
		
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		$this->process_bulk_action();
		
		$data = $data->obtener_citas( $args );
		
		 /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'appoinment_id'; //If no sort, default to ID
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        
		
		$this->set_pagination_args( array(
		    'total_items' => $total_items,                  //WE have to calculate the total number of items
	    	'per_page'    => $per_page ,                    //WE have to determine how many items to show on a page
	    	'total_pages' => ceil($total_items / $per_page) // páginas en total para los enlaces de la paginación
		) );
		
		$this->items = $data;
	}
	
	public function column_cb($item){
        
        return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['appoinment_id']
		);

    }
	
	public function column_appoinment_id( $item ) {
		
		$delete_nonce = wp_create_nonce( 'et_delete_cita' );
		
		$title = '<strong>' . $item['appoinment_id'] . '</strong>';
		
		$actions = array(
			'delete' => sprintf( '<a href="?page=%s&action=%s&cita=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['appoinment_id'] ), $delete_nonce )
		);
		
		return $title . $this->row_actions( $actions );
	}
	
	public function column_appoinment_provider_id( $item ) {
		
		$provider_info = get_userdata( (int) $item[ "appoinment_provider_id" ] );
            	
    	if ( ! $provider_info  )
    		$provider_name = "Usuario no existe";
    	else
    		$provider_name = $provider_info->user_login;

    	return $provider_name;
		
	}
	
	public function column_appoinment_service_id( $item ) {

		$titulo_servicio = get_the_title( $item[ "appoinment_service_id" ] );
            	
    	if ( empty($titulo_servicio) )
    		$titulo_servicio = "Servicio no existe";
    	
    	return $titulo_servicio;
		
	}
	
	public function column_default( $item, $column_name ) {
	  	
	  	switch( $column_name ) {
		  	
            case 'appoinment_date':
            case 'appoinment_time':
            case 'appoinment_status':
                return $item[ $column_name ];            	
                
            default:
                return print_r( $item, true ) ;
        }
	}
	
	public function get_sortable_columns() {
		$sortable_columns = array(
			'appoinment_date'  	=> array('appoinment_date',false),
			'appoinment_id'  	=> array('appoinment_id',false)
		);

		return $sortable_columns;
	}
	
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete'    => 'Delete'
		);
	
		return $actions;
	}
	
	public function no_items() {
		
			_e( 'No se consiguieron citas con este status' , 'estilotu' );
			
	}
	
	/** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
	            
        //Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {
	
			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );
			
			if ( ! wp_verify_nonce( $nonce , 'et_delete_cita' ) ) {
				die( 'Go get a life script kiddies' );
			}
			
			else {
				self::delete_cita( absint( $_GET['cita'] ) );
			
				wp_redirect( esc_url( add_query_arg() ) );
				exit;
			}
		
		}
	
		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		|| ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' ) ) {
	
	    	$delete_ids = esc_sql( $_POST['bulk-delete'] );
	
		    // loop over the array of record IDs and delete them
		    foreach ( $delete_ids as $id ) {
				
				self::delete_cita( $id );
		    }
	
			wp_redirect( esc_url( add_query_arg() ) );
			exit;
		
		}		
			
	}

	
	public function extra_tablenav( $which ) {
	    
	    global $wpdb;
	    $tablename_citas = $wpdb->prefix . "bb_appoinments";
	    
	    if ( $which === "top" && !is_singular() ) { ?>

	        <div class="alignleft actions bulkactions">

				<?php $providers = $wpdb->get_results( "SELECT DISTINCT appoinment_provider_id FROM $tablename_citas" );?>
				<select name="provider-filter" class="ewc-provider-status">
				
					<option value=""><? _e( "Filtrar por Entrenador" , "estilotu"); ?></option>
					<?php
	                foreach( $providers as $provider ) { 
						
						$provider_info = get_userdata( (int) $provider->appoinment_provider_id );
            	
				    	if ( ! $provider_info  )
				    		$provider_name = "Usuario no existe";
				    	else
				    		$provider_name = $provider_info->user_login; ?>
						
						<option value="<?php echo $provider->appoinment_provider_id; ?>" <?php isset( $_POST['provider-filter'] ) ? selected( $_POST['provider-filter'] , $provider->appoinment_provider_id ) : ""; ?>><?php echo $provider_name; ?></option>

	                <?php } ?>
	                
				</select>
				
				<?php $status = array( "confirm" => "confirm" , "cancel" => "cancel" , "on hold" => "on hold"); ?> 
	            <select name="status-filter" class="ewc-filter-status">
	                
	                <option value=""><? _e( "Filtrar por Status" , "estilotu"); ?></option>
	                
	                <?php
	                
	                foreach( $status as $value => $stat ) { ?>
	                    
						<option value="<?php echo $value; ?>" <?php isset( $_POST['status-filter'] ) ? selected( $_POST['status-filter'] , $stat ) : ""; ?>><?php echo $stat; ?></option>

	                <?php } ?>

	            </select>
					
				<?php 
					wp_enqueue_script( 'jquery-ui-datepicker');
					wp_enqueue_style( 'jquery-ui-datepicker-style' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css'); ?>
				
				<script>
				    jQuery(document).ready(function($) {

						var date_from 	= $("#date_from-filter").val();
						var date_to 	= $("#date_to-filter").val();
						
						console.log(date_from + " " + date_to);
						
					    var dateFormat = "yy-mm-dd",
							
							from = $( "#date_from-filter" ).datepicker({
								defaultDate: "+1w",
								changeMonth: true,
								dateFormat: "yy-mm-dd"
								//numberOfMonths: 3
							})
								.datepicker("setDate", new Date( date_from ) )
								.on( "change", function() {
									to.datepicker( "option", "minDate", getDate( this ) );
								}),
								
									
					      
							to = $( "#date_to-filter" ).datepicker({
								defaultDate: "+1w",
								changeMonth: true,
								dateFormat: "yy-mm-dd"
								//numberOfMonths: 3
							})
								.datepicker("setDate", new Date( date_to ) )
								.on( "change", function() {
									from.datepicker( "option", "maxDate", getDate( this ) );
								})
								
									
					 
						function getDate( element ) {
							var date;
							
							try {
								date = $.datepicker.parseDate( dateFormat, element.value );
							} catch( error ) {
								date = null;
							}
						
							return date;
				    	}
				        
				    });
				</script>
				
				<?php
				
				$fecha_from 	= !empty( $_POST['date_from-filter'] ) ? $_POST['date_from-filter'] : date("Y-m-d");
				$fecha_to 		= !empty( $_POST['date_to-filter'] ) ? $_POST['date_to-filter'] : date("Y-m-d", strtotime("+1 week") );
					
				?>
				
				<label for="date_from-filter">From</label>
				<input type="text" id="date_from-filter" name="date_from-filter" value="<?php echo $fecha_from; ?> ">
				<label for="date_to-filter">to</label>
				<input type="text" id="date_to-filter" name="date_to-filter" value="<?php echo $fecha_to; ?> ">
				
				
				<?php submit_button( __( 'Filter' ), '', 'filter_action', false ,  array( 'id' => 'post-query-submit' ) ); ?>		
	        </div>

	        
		        
	    <?php }
	    
	    if ( $which == "bottom" ){
	        //The code that goes after the table is there
	
	    }
	}
	
	public static function delete_cita( $id ) {
		
		global $wpdb;
		
		$wpdb->delete(
			"{$wpdb->prefix}bb_appoinments",
			[ 'appoinment_id' => $id ],
			[ '%d' ]
		);
	
	}
	

}
