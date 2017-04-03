<?php

/**
 * @link              www.froala.com
 * @since             1.0.0
 * @package           Froala
 *
 * @wordpress-plugin
 * Plugin Name:       Froala-Wysiwyg
 * Plugin URI:        http://froala.com
 * Description:       This plugin will add advance rich text editing capabilities to your WordPress CMS.
 * On activation will replace the default Tiny MCE editor on the admin area. The plugin is easily scalable
 * using the public part of the plugin.
 * Version:           1.0.1
 * Author:            Radu
 * Author URI:        www.froala.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       Froala
 * Domain Path:       /wysiwyg-editor
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-froala-activator.php
 */
function activate_froala() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-froala-activator.php';
	Froala_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-froala-deactivator.php
 */
function deactivate_froala() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-froala-deactivator.php';
	Froala_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_froala' );
register_deactivation_hook( __FILE__, 'deactivate_froala' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-froala.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_froala() {

	$plugin = new Froala();
	$plugin->run();

}
run_froala();
