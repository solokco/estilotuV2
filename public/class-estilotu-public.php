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
class Estilotu_Public {

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
	
	public $miembro;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		$this->miembro = new Estilotu_Buddypress( );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) 	. 'css/estilotu-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'estilotu-servicios', plugin_dir_url( __FILE__ ) 	. 'css/estilotu-servicios.css', array(), $this->version, 'all' );
		wp_register_style( 'estilotu-citas', plugin_dir_url( __FILE__ ) 	. 'css/estilotu-citas.css', array(), $this->version, 'all' );
		
		wp_enqueue_style( 'bootstrap-calendar', plugin_dir_url( __FILE__ ) 	. 'css/calendar/bootstrap-datepicker.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'bootstrap-calendar-standalone', plugin_dir_url( __FILE__ ) 	. 'css/calendar/bootstrap-datepicker.standalone.min.css', array(), $this->version, 'all' );
		

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/estilotu-public.js', array( 'jquery' ), $this->version, false );
		
		//wp_register_script( 'geolocalizacion_center', plugin_dir_url( __FILE__ ) . 'assets/js/ajax/geolocalizacion_center.js', array( 'jquery' ), $this->version, true );
		//wp_localize_script( 'geolocalizacion_center', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
		
		wp_enqueue_script( 'jquery-validate' 	, plugin_dir_url( __FILE__ )		. 'js/jquery.validate.min.js'			, array( 'jquery' ), $this->version , true );
		wp_register_script( 'additional-methods' 	, plugin_dir_url( __FILE__ )	. 'js/additional-methods.min.js'		, array( 'jquery-validate' ), $this->version , true );
		
		wp_register_script( 'estilotu_servicios_list_map_init', plugin_dir_url( __FILE__ ) . 'js/map/servicios-list-mapa-init.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( 'estilotu_servicios_list_map_init', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
		wp_register_script( 'estilotu_servicios_add_map_init' 		, plugin_dir_url( __FILE__ )	. 'js/map/servicios-add-mapa-init.js'		, array( 'jquery' ), $this->version , true );
		wp_register_script( 'estilotu_servicios_single_map_init' 	, plugin_dir_url( __FILE__ )	. 'js/map/servicios-single-mapa-init.js'		, array( 'jquery' ), $this->version , true );
		
		wp_register_script( 'estilotu_map_google_api', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAvDBnBAIlKDWPmXIsKVx02VAt16_YkrEU&libraries=places&callback=initMap', array( 'jquery' ), $this->version, true );
		
		// En el archivo single.php se ha repetido esto ya que el filtro se ejecuta antes que la accion
		wp_register_script( 'bootstrap-calendar' 			, plugin_dir_url( __FILE__ )		. 'js/calendar/bootstrap-datepicker.min.js'				, array( 'jquery' ), $this->version , false );
		wp_register_script ('bootstrap-calendar-espanol' 	, plugin_dir_url( __FILE__ )		. 'js/calendar/locales/bootstrap-datepicker.es.min.js'	, array( 'bootstrap-calendar' ), $this->version , false );
		wp_register_script ('bootstrap-calendar-config' 	, plugin_dir_url( __FILE__ )		. 'js/calendar/bootstrap-datepicker-config.js'			, array( 'bootstrap-calendar-espanol' ), $this->version , false );
		
		wp_register_script( 'estilotu_duplicar_servicios' 	, plugin_dir_url( __FILE__ )	. 'js/servicios-agregar.js'		, array( 'jquery' ), $this->version );
		wp_localize_script( 'estilotu_duplicar_servicios', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
		wp_register_script( 'estilotu_citas_opciones' 	, plugin_dir_url( __FILE__ )	. 'js/citas/citas-opciones.js'		, array( 'jquery' ), $this->version );
		wp_localize_script( 'estilotu_citas_opciones', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
		wp_enqueue_script("jquery-effects-core");

	}

	/* ********************************************** */
	/* LEE LAS VARIABLES PASADAS POR _GET */
	/* ********************************************** */
	public function et_queryvars( $qvars ) {
		$qvars[] = 'id_prov';
		$qvars[] = 'id_servicio';
		$qvars[] = 'accion';
		$qvars[] = 'status';
		$qvars[] = 'id_cita';
		$qvars[] = 'categoria';
		$qvars[] = 'id_usuario';
		$qvars[] = 'tipo_servicio';
		$qvars[] = 'fecha';
		$qvars[] = 'hora';
		$qvars[] = 'redirect_to';
		$qvars[] = 'estado';
		$qvars[] = 'pais';
		$qvars[] = 'servicios';
		
		return $qvars;
	}
	/* ********************************************** */
	
	/* **************************************************** */
	/* ENVIO AL USUARIO AL HOME AL HACER LOGOUT */
	/* **************************************************** */
	public function et_go_home_on_logout() {
		wp_redirect( home_url() );
		exit();
	}
	/* **************************************************** */
	
	public function add_async_attribute($tag, $handle) {
	    
	    if ( 'estilotu_map_google_api' !== $handle )
	        return $tag;
	    
	    return str_replace( ' src', ' async="async" src', $tag );
	    
	}
	
	public function add_defer_attribute($tag, $handle) {
	    
	    if ( 'estilotu_map_google_api' !== $handle )
	        return $tag;
	    
	    return str_replace( ' src', ' defer="defer" src', $tag );
	    
	}

}