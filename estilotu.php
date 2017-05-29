<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://estoesweb.com
 * @since             1.0.0
 * @package           Estilotu
 *
 * @wordpress-plugin
 * Plugin Name:       EstiloTu
 * Plugin URI:        http://estilotu.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           2.0.0
 * Author:            Carlos Carmona
 * Author URI:        http://estoesweb.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       estilotu
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-estilotu-activator.php
 */
function activate_estilotu() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-estilotu-activator.php';
	Estilotu_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-estilotu-deactivator.php
 */
function deactivate_estilotu() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-estilotu-deactivator.php';
	Estilotu_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_estilotu' );
register_deactivation_hook( __FILE__, 'deactivate_estilotu' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-estilotu.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_estilotu() {

	$plugin = new Estilotu();
	$plugin->run();

}
run_estilotu();
