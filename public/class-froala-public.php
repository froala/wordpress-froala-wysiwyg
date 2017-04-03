<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       www.froala.com
 * @since      1.0.0
 *
 * @package    Froala
 * @subpackage Froala/public
 */

/**
 *
 * @package    Froala
 * @subpackage Froala/public
 * @author     Radu <Radu@froala.com>
 */
class Froala_Editor {

	private $option_name = 'froala';

	/**
	 * Static method for easy initialization with the editor.
	 *
	 * @param null                          $element_selector        Represents the html element selector.
	 * @param array('option' => 'argument') $editor_options          Represents the options that the editor can load.
	 *
	 */
	public static function activate($element_selector = null, $editor_options = null) {

		if ( $element_selector !== null) {

			$licence_key = get_option('froala_fr_licence' );
			$active_plugins = get_option('froala_plugin_list');

			Froala_Editor::enqueue_styles();
			Froala_Editor::enqueue_scripts();

			foreach ($active_plugins as $script) {

				$suffix = '.min.js';
				$css_suffix = '.css';
				Froala_Editor::enque_editor_plugins($script,$suffix);
				Froala_Editor::enque_editor_plugins_css($script,$css_suffix);
			}

			if (is_array($editor_options)) {
				$editor_options['key']  =  $licence_key;
				$editor_options         = json_encode($editor_options);
			} else if (is_object($editor_options)) {
				$editor_options->key =  $licence_key;
				$editor_options         = json_encode($editor_options);
			}
			else {
				$editor_options = '{\'key\':\''.$licence_key.'\'}';
			}

			$content = "\t\t" . '<script> jQuery(window).on(\'load\', function(e){
						 jQuery(\''.$element_selector.'\').froalaEditor('.$editor_options.');
						}); </script>' . "\n";

			Froala_Editor::enque_editor_script($content);
		}
	}

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */


	public function __construct( $plugin_name = 'froala', $version = '1.0' ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles () {

		/**
		 * The Froala_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_style('froala_editor_css',plugin_dir_url( __FILE__ ) . 'css/froala_editor.css');
		wp_register_style('froala_style_css',plugin_dir_url( __FILE__ ) . 'css/froala_style.css');
		wp_register_style('froala_public_css',plugin_dir_url( __FILE__ ) . 'css/froala-public.css');
		wp_register_style('font_asm','https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');


		wp_enqueue_style('froala_editor_css');
		wp_enqueue_style('froala_style_css');
		wp_enqueue_style('froala_public_css');
		wp_enqueue_style('font_asm');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts () {

		/**
		 * The Froala_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script('froala_editor',plugins_url('public/js/froala_editor.min.js',dirname( __FILE__ )),array('jquery','editor-init'), true);
		wp_enqueue_script('froala_editor');

	}

	/**
	 *  Enques the editor javascript plugins
	 *
	 * @param null $name       *Will be the name of the file same as the plugin name
	 * @param null $suffix     *Will be the suffix of the file like: ".min.js", ".js"
	 */
	public function enque_editor_plugins ($name = null, $suffix = null) {

		wp_register_script($name,plugins_url('public/js/plugins/'.$name.$suffix,dirname( __FILE__ )),array('jquery'), true);
		wp_enqueue_script($name);
	}

	/**
	 * Enques the css files for the specific plugins, makes sure that plugins that don't have css file won't load.
	 *
	 * @param null $name        *Will be the name of the file same as the plugin name
	 * @param null $suffix      *Will be the suffix of the file like: ".min.css", ".css"
	 */
	public function enque_editor_plugins_css ($name = null, $suffix = null) {

		$path = plugin_dir_url( __FILE__ ) . 'css/plugins/'.$name.$suffix;
		stream_context_set_default( [
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false,
			],
		]);
		$headers = @get_headers($path);

		if (preg_match("|200|", $headers[0])) {
			wp_register_style('froala-'.$name,$path);
			wp_enqueue_style('froala-'.$name);
		}
	}

	/**
	 * Injects the main javascript file and loads the Froala editor.
	 *
	 * @param null $content
	 */
	public function enque_editor_script ($content=null) {

		wp_enqueue_script( 'editor-init', plugins_url('public/js/plugins/editor-init.js',dirname( __FILE__ )),array('jquery'), '1.0' );
		wp_add_inline_script( 'editor-init', $content );
	}

}

