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
class Estilotu_Notificaciones {
	
	public function __construct(){}
	
	
	public function custom_filter_notifications_get_registered_components( $component_names = array() ) {
	 
	    // Force $component_names to be an array
	    if ( ! is_array( $component_names ) ) {
	        $component_names = array();
	    }
	 
	    // Add 'custom' component to registered components array
	    array_push( $component_names, 'custom' );
	 
	    // Return component's with 'custom' appended
	    return $component_names;
	}
	

	public function custom_format_buddypress_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' )  {
	
	    // New custom notifications
	    if ( 'guardar_cita' === $action ) {
	    
	        $post_info = get_post( $item_id );
			$user_info = get_userdata( $secondary_item_id );
			
	        $custom_title = $user_info->user_login . ' agendó una cita en ' . get_the_title( $item_id );
	        $custom_link  = get_post_permalink( $item_id );
	        $custom_text = $user_info->user_login . ' agendó una cita en ' . get_the_title( $item_id );
	 
	        // WordPress Toolbar
	        if ( 'string' === $format ) {
	            $return = apply_filters( 'custom_filter', '' . esc_html( $custom_text ) . '', $custom_text, $custom_link );
	 
	        // Deprecated BuddyBar
	        } else {
	            $return = apply_filters( 'custom_filter', array(
	                'text' => $custom_text,
	                'link' => $custom_link
	            ), $custom_link, (int) $total_items, $custom_text, $custom_title );
	        }
	        
	        return $return;
	        
	    }
	    
	}

	public function notificar_cita_func( $service_id ) {
		
		$post = get_post( $service_id );		
	    $author_id = $post->post_author; /* Post author ID. */
	    
	    if ( bp_is_active( 'notifications' ) ) {
		    
		    bp_notifications_add_notification( array(
		        'user_id'           => $author_id,
		        'item_id'           => $service_id,
		        'secondary_item_id' => get_current_user_id(), 
		        'component_name'    => 'custom',
		        'component_action'  => 'guardar_cita',
		        'date_notified'     => bp_core_current_time(),
		        'is_new'            => 1,
		    ) );
		}
	    
	}	

}