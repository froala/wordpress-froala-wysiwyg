<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.froala.com
 * @since      1.0.0
 *
 * @package    Froala
 * @subpackage Froala/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Froala
 * @subpackage Froala/admin
 * @author     Radu <Radu@froala.com>
 */
class Froala_Admin {


	/**
	 * The options name to be used in this plugin
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @var  	string 		$option_name 	Option name of this plugin
	 */
	private $option_name = 'froala';

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

	public $plugin_list = array();

	public $active_plugins = array();
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name ='froala', $version='1.0' ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		if ( !get_option( $this->option_name .'_plugin_list') ) {
			update_option( $this->option_name .'_plugin_list', array('align','char_counter'));
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Froala_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Froala_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		/*   REGISTER ALL CSS FOR SITE */

		wp_register_style('froala_editor_css',plugin_dir_url( __FILE__ ) . 'css/froala_editor.css');
		wp_register_style('froala_style_css',plugin_dir_url( __FILE__ ) . 'css/froala_style.css');
		wp_register_style('froala_admin_css',plugin_dir_url( __FILE__ ) . 'css/froala-admin.css');
		wp_register_style('font_asm','https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');


		wp_register_style('char_counter_css',plugin_dir_url( __FILE__ ) . 'css/plugins/char_counter.css');
		wp_register_style('colors_css',plugin_dir_url( __FILE__ ) . 'css/plugins/colors.css');
		wp_register_style('draggable_css',plugin_dir_url( __FILE__ ) . 'css/plugins/draggable.css');
		wp_register_style('emoticons_css',plugin_dir_url( __FILE__ ) . 'css/plugins/emoticons.css');
		wp_register_style('file_css',plugin_dir_url( __FILE__ ) . 'css/plugins/file.css');
		wp_register_style('fullscreen_css',plugin_dir_url( __FILE__ ) . 'css/plugins/fullscreen.css');
		wp_register_style('help_css',plugin_dir_url( __FILE__ ) . 'css/plugins/help.css');
		wp_register_style('image_css',plugin_dir_url( __FILE__ ) . 'css/plugins/image.css');
		wp_register_style('image_manager_css',plugin_dir_url( __FILE__ ) . 'css/plugins/image_manager.css');
		wp_register_style('line_breaker_css',plugin_dir_url( __FILE__ ) . 'css/plugins/line_breaker.css');
		wp_register_style('quick_insert_css',plugin_dir_url( __FILE__ ) . 'css/plugins/quick_insert.css');
		wp_register_style('special_characters_css',plugin_dir_url( __FILE__ ) . 'css/plugins/special_characters.css');
		wp_register_style('table_css',plugin_dir_url( __FILE__ ) . 'css/plugins/table.css');
		wp_register_style('video_css',plugin_dir_url( __FILE__ ) . 'css/plugins/video.css');

		wp_enqueue_style('froala_editor_css');
		wp_enqueue_style('froala_style_css');
		wp_enqueue_style('froala_admin_css');
		wp_enqueue_style('font_asm');

		wp_enqueue_style('char_counter_css');
		wp_enqueue_style('colors_css');
		wp_enqueue_style('draggable_css');
		wp_enqueue_style('emoticons_css');
		wp_enqueue_style('file_css');
		wp_enqueue_style('fullscreen_css');
		wp_enqueue_style('help_css');
		wp_enqueue_style('image_css');
		wp_enqueue_style('image_manager_css');
		wp_enqueue_style('line_breaker_css');
		wp_enqueue_style('quick_insert_css');
		wp_enqueue_style('special_characters_css');
		wp_enqueue_style('table_css');
		wp_enqueue_style('video_css');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Froala_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Froala_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script('froala_admin',plugin_dir_url( __FILE__ ) . 'js/froala-admin.js');
		wp_register_script('froala_editor',plugin_dir_url( __FILE__ ) . 'js/froala_editor.min.js');

		wp_enqueue_script('froala_admin');
		wp_enqueue_script('froala_editor');
	}

	/**
	 * Add an options page
	 *
	 * @since  1.0.0
	 */
	public function add_options_page() {

		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'Froala Settings', 'froala' ),
			__( 'Froala Wysiwyg', 'froala' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_options_page' )
		);

	}
	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_options_page () {
		include_once 'partials/froala-admin-display.php';
	}
	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	public function register_setting () {

		// Add a General section
		add_settings_section(
			$this->option_name . '_general_settings',
			__( 'Change the settings for Froala Wysiwyg', 'froala' ),
			array( $this, $this->option_name . '_general' ),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name .'_fr_licence',
			__( 'Licence Key', $this->option_name),
			array($this, $this->option_name.'_licence_input'),
			$this->plugin_name,
			$this->option_name . '_general_settings',
			array('label_for' => $this->option_name . '_fr_licence')
		);
		add_settings_field(
			$this->option_name .'_plugin_list',
			__( 'Plugin List', $this->option_name),
			array($this, $this->option_name.'_plugin_list'),
			$this->plugin_name,
			$this->option_name . '_general_settings',
			array('label_for' => $this->option_name . 'plugin_list')
		);

		register_setting( $this->plugin_name, 'pluginPage' );
		register_setting( $this->plugin_name, $this->option_name . '_fr_licence');
		register_setting( $this->plugin_name, $this->option_name . '_plugin_list');


	}
	/**
	 * Render the text for the general section
	 *
	 * @since  1.0.0
	 */
	public function froala_general () {
		echo '<p>' . __( 'Please change the settings accordingly.', 'froala' ) . '</p>';
	}

	/**
	 * Render the froala licence input
	 *
	 * @since 1.0.0
	 */
	public function froala_licence_input () {
		$licence_key = get_option( $this->option_name . '_fr_licence' );

		echo '<input type="text" name="' . $this->option_name . '_fr_licence' . '" id="' . $this->option_name . '_fr_licence' . '" value="' . $licence_key . '">';
	}

	/**
	 * Render the froala plugin list
	 *
	 * @since 1.0.0
	 */
	public function froala_plugin_list () {

		$options = get_option( $this->option_name .'_plugin_list');

		array_push($this->plugin_list,
			array('name'=>'align'),
			array('name'=>'char_counter' ),
			array('name'=>'code_beautifier'),
			array('name'=>'code_view'),
			array('name'=>'colors'),
			array('name'=>'draggable'),
			array('name'=>'emoticons'),
			array('name'=>'entities'),
			array('name'=>'file'),
			array('name'=>'font_family'),
			array('name'=>'font_size'),
			array('name'=>'forms'),
			array('name'=>'fullscreen'),
			array('name'=>'help'),
			array('name'=>'image'),
			array('name'=>'image_manager'),
			array('name'=>'inline_style'),
			array('name'=>'line_breaker'),
			array('name'=>'link'),
			array('name'=>'lists'),
			array('name'=>'paragraph_format'),
			array('name'=>'paragraph_style'),
			array('name'=>'print'),
			array('name'=>'quick_insert'),
			array('name'=>'quote'),
			array('name'=>'save'),
			array('name'=>'special_characters'),
			array('name'=>'table'),
			array('name'=>'url'),
			array('name'=>'video'));
		?>


        <ul class="fr-admin-dropdown triple" id="fr_admin_dropdown_ul">
			<?php foreach ($this->plugin_list as $plugin) {
				$selected = in_array( $plugin['name'], $options) ? ' checked="checked" ' : '';
				?>
                <li id="fr_admin_dropdown_li">
                    <input <?php echo $selected; ?> type="checkbox" id="<?php echo 'fr_'.$plugin['name'] ?>" name="<?php echo $this->option_name .'_plugin_list[]' ?>" value="<?php echo $plugin['name']; ?>">
                    <label for=<?php echo 'fr_'.$plugin['name'] ?>><?php echo ucfirst($plugin['name']) ?></label>
                </li>
				<?php
			}
			?>
        </ul>
		<?php
	}

	/**
	 * Initialize the froala editor on the admin panel default html tag has id #content
	 *
	 * @param $settings                  *Settings for wp_editor() function.
	 * @param null $editor_id            *Selector on which the Froala Editor will be initialized.
	 *
	 * @return mixed                     *Returns the settings array for the wp_editor.
	 */
	public function froala_editor ($settings, $editor_id = null) {

		$this->active_plugins = get_option( $this->option_name .'_plugin_list');
		$settings['tinymce']   = false;
		$settings['quicktags'] = false;
		$settings['media_buttons'] = false;
		$suffix = '.min.js';
		$path = plugins_url('includes/froala-upload-to-server.php', dirname( __FILE__ ));

		if ($custom_plugins = $this->froala_check_custom_plugins($this->active_plugins)) {

			foreach ($custom_plugins as $plugin) {
				echo "\t\t" . '<script type="text/javascript" src="' . $plugin['path'] . '"></script>' . "\n"; // xss ok
			}
		}

		foreach ($this->active_plugins as $script) {
			echo "\t\t" . '<script type="text/javascript" src="' . plugins_url( 'admin/js/plugins/' . $script . $suffix, dirname( __FILE__ ) ) . '"></script>' . "\n"; // xss ok
		}

		if ($editor_id == null) {
			$editor_id ='#content';
		}

		echo "\t\t" . '<script> jQuery(document).ready(function(){
						 jQuery(\''.$editor_id.'\').froalaEditor({\'imageUploadURL\':\''.$path.'?upload_image=1\',
                                                                  \'imageManagerLoadURL\':\''.$path.'?view_images=1\'});
						}); </script>' . "\n";

		return $settings;
	}

	/** Callback function to add new plugin
	 * @param null $path                 *Path for the file
	 * @param null $name                 *File name
	 *
	 * @return array|WP_Error
	 * @since 1.0.2
	 */
	public function froala_editor_before ($path = null, $name = null) {

		if ($this->froala_check_plugin_path($path)) {

			array_push($this->plugin_list, array('name' => $name , 'path' => $path));
			return $this->plugin_list;
		}
		return new WP_Error( 'broke', __( '<div class="error notice"><p>Please check your plugin path because the file was not found on the server. <br/> This may be from an improper htaccess config or read wright on that folder.</p></div>'));

	}

	/** Helper function checks if the file path is correct if not it will return error message.
	 * @param null $path
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public function froala_check_plugin_path ($path = null) {

		stream_context_set_default( [
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false,
			],
		]);
		$headers = @get_headers($path);

		if (preg_match("|200|", $headers[0])) {
			return true;
		}
		return false;
	}

	/** Helper function checks if custom plugins are active and if they it will add them
	 * to the loading que with the correct file path.
	 * @param null $active_plugins
	 *
	 * @return array|bool
	 * @since 1.0.2
	 */
	public function froala_check_custom_plugins ($active_plugins = null) {

		if ($active_plugins !== null) {
			$custom_plugins = array();

			for ($i = 0; $i < count($active_plugins); $i++) {

				if (in_array($this->plugin_list[$i]['name'],$active_plugins)) {
					array_push( $custom_plugins, $this->plugin_list[$i]);
					unset($this->active_plugins[$i]);
				}
			}
			return $custom_plugins;
		}
		return false;

	}

}
