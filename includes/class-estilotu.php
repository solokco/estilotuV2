<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://estoesweb.com
 * @since      1.0.0
 *
 * @package    Estilotu
 * @subpackage Estilotu/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Estilotu
 * @subpackage Estilotu/includes
 * @author     Carlos Carmona <ccarmona@estoesweb.com>
 */
class Estilotu {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Estilotu_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'estilotu';
		$this->version = "1.1.6";

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Estilotu_Loader. Orchestrates the hooks of the plugin.
	 * - Estilotu_i18n. Defines internationalization functionality.
	 * - Estilotu_Admin. Defines all hooks for the admin area.
	 * - Estilotu_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		define("ESTILOTU_PATH", plugin_dir_path( dirname( __FILE__ ) ) );
		define("ESTILOTU_URL", plugin_dir_url( dirname( __FILE__ ) ) );
		
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-estilotu-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-estilotu-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-estilotu-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-estilotu-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-estilotu-buddypress.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-estilotu-geolocalizacion.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-estilotu-servicios.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-estilotu-citas.php';
		
		//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/servicios/class-servicios-public.php'; 
		//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/citas/class-citas-public.php'; 

		$this->loader = new Estilotu_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Estilotu_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Estilotu_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Estilotu_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		/* *************************************************************************** */
		/* ACCIONES DE CUSTOM POST TYPE */
		/* *************************************************************************** */
		$this->loader->add_action( 'init', $plugin_admin, 'registrar_cpt_servicios' );
		$this->loader->add_action( 'init', $plugin_admin, 'registrar_cpt_servicios_taxonomies' );
		
		$this->loader->add_action( 'admin_init', $plugin_admin, 'registrar_cpt_servicios_metabox' );
		
		$this->loader->add_action('admin_footer-post.php', $plugin_admin , 'registrar_cpt_servicios_status_dropdown');
		
		$this->loader->add_action( 'save_post_servicios', $plugin_admin, 'registrar_cpt_servicios_save' );
		
		$this->loader->add_filter( 'archive_template', $plugin_admin, 'cpt_servicios_template_archive' );

		/* *************************************************************************** */
		
		// limito las fotos solo a sus duenos
		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'et_restrict_media_library' );
		
		$this->loader->add_action( 'wc_memberships_user_membership_saved', $plugin_admin, 'change_user_role_author' );
		//$this->loader->add_action( 'wc_memberships_user_membership_status_changed', $plugin_admin, 'change_user_role_subscriber' );
		
		
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public 	 = new Estilotu_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$this->loader->add_filter( 'query_vars', $plugin_public, 'et_queryvars' );

		$this->loader->add_filter( 'script_loader_tag', $plugin_public, 'add_async_attribute' , 10 , 2);
		$this->loader->add_filter( 'script_loader_tag', $plugin_public, 'add_defer_attribute' , 10 , 2);
		
		$this->loader->add_action( 'bp_setup_nav', $plugin_public->miembro , 'menu_buddypress' , 999);
		
		$this->loader->add_action( 'wp_logout', $plugin_public, 'et_go_home_on_logout' );
		
		//$this->loader->add_action( 'wp_ajax_insertar_bloque_cupos', $plugin_servicio , 'insertar_bloque_cupos' );
		//$this->loader->add_action( 'wp_ajax_nopriv_insertar_bloque_cupos', $plugin_servicio , 'insertar_bloque_cupos' );

		//SERIVICIOS
		$servicio = new Estilotu_Servicio();
		
		$this->loader->add_filter( 'single_template', $servicio, 'cpt_servicios_template_single' , 10, 1 );
		
		$this->loader->add_action( 'wp_ajax_cargar_cupos', $servicio, 'cargar_cupos' );
		$this->loader->add_action( 'wp_ajax_nopriv_cargar_cupos', $servicio, 'cargar_cupos' );
		
		// CITAS		
		$citas = new Estilotu_Citas();
		
		$this->loader->add_action( 'wp_ajax_registrar_asistencia_y_pago_participante', $citas, 'registrar_asistencia_y_pago_participante' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Estilotu_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}