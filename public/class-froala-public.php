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
	public $custom_scripts = array();
	public $custom_scripts_status = '';


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

	/** Helper function, gets active filters for a specific action
	 *
	 * @param string $hook      * The name of the hook.
	 *
	 * @return string|WP_Hook
	 */
	public function froala_get_filters_for( $hook = '' ) {
		global $wp_filter;
		if( empty( $hook ) || !isset( $wp_filter[$hook] ) ) {
			return 'none';
		}

		return $wp_filter[$hook];
	}


	/** Helper function to get filter arguments.
	 * @param null $object          * Wp_filter object for specific action.
	 * @param null $filter          * The callback function for action.
	 * @param null $priority        * Action priority that it's set on class Froala
	 *
	 * If priority for a hook it's changed it will impact this file too, so the change
	 * must be made also here. When calling this function make sure that the same priority
	 * is passed to the function.
	 *
	 * @return bool
	 */
	public function froala_get_filter_data ($object = null, $filter = null, $priority = null) {
		$active_filters = $object->callbacks[$priority];

		if (is_array($active_filters) || is_object($active_filters)) {

			foreach ($active_filters as $callback) {

				if ( in_array( $filter, $callback['function'] ) ) {
					$this->custom_scripts        = $callback['function'][0]->custom_scripts;
					$this->custom_scripts_status = $callback['function'][0]->custom_scripts_status;

					return true;
				} else {

					return false;
				}
			}
		}
	}

	/**
	 * Public method for easy initialization for the Froala Editor.
	 *
	 * @param null                          $element_selector        Represents the html element selector.
	 * @param array('option' => 'argument') $editor_options          Represents the options that the editor can load.
	 *
	 */

	public function activate($element_selector = null, $editor_options = null) {

		$filter = $this->froala_get_filters_for('froala_before_public_init');

		if ( $filter !== 'none') {
			$filter = $this->froala_get_filter_data($filter,'froala_editor_before','10');
		}

		if (isset($this->custom_scripts_status) && $this->custom_scripts_status == 'before') {
			$this->froala_set_custom_script();
		}

		if ( $element_selector !== null) {

			$licence_key = get_option('froala_fr_licence' );
			$active_plugins = get_option('froala_plugin_list');
			$this->enqueue_styles();
			$this->enqueue_scripts();

			foreach ($active_plugins as $script) {
				$suffix = '.min.js';
				$css_suffix = '.css';
				$this->froala_enque_editor_plugins($script,$suffix);
				$this->froala_enque_editor_plugins_css($script,$css_suffix);
			}

			if (is_array($editor_options)) {
				$editor_options['key']  =  $licence_key;
				$editor_options         = json_encode($editor_options);
			} else if (is_object($editor_options)) {
				$editor_options->key    =  $licence_key;
				$editor_options         = json_encode($editor_options);
			}
			else {
				$editor_options = '{\'key\':\''.$licence_key.'\'}';
			}

			$content = "\t\t" . '<script>function initFroalaEditor(){ new FroalaEditor(\''.$element_selector.'\','.$editor_options.');}
			window.onload = initFroalaEditor;
			    </script>' . "\n";

			$this->froala_enque_editor_script($content);
			$filter = $this->froala_get_filters_for('froala_after_public_init');

			if ( $filter !== 'none') {
				$filter = $this->froala_get_filter_data('froala_editor_after','10');
			}

			if (isset($this->custom_scripts_status) && $this->custom_scripts_status == 'after') {
				$this->froala_set_custom_script();
			}
		}
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

		wp_enqueue_style('froala_editor_css');
		wp_enqueue_style('froala_style_css');
		wp_enqueue_style('froala_public_css');
		
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

		wp_register_script('froala_editor',plugins_url('public/js/froala_editor.min.js',dirname( __FILE__ )), true);
		wp_enqueue_script('froala_editor');

	}

	/**
	 *  Enques the editor javascript plugins
	 *
	 * @param null $name       *Will be the name of the file same as the plugin name
	 * @param null $suffix     *Will be the suffix of the file like: ".min.js", ".js"
	 * Modified
	 * @since 1.0.2
	 */
	public function froala_enque_editor_plugins ($name = null, $suffix = null) {
		$path = plugins_url('public/js/plugins/'.$name.$suffix,dirname( __FILE__ ));

		$js_array_list = [
			'align.min.js',
			'char_counter.min.js',
			'code_beautifier.min.js',
			'code_view.min.js',
			'colors.min.js',
			'draggable.min.js',
			'emoticons.min.js',
			'entities.min.js',
			'file.min.js',
			'font_family.min.js',
			'font_size.min.js',
			'forms.min.js',
			'fullscreen.min.js',
			'help.min.js',
			'image.min.js',
			'image_manager.min.js',
			'inline_style.min.js',
			'line_breaker.min.js',
			'link.min.js',
			'lists.min.js',
			'paragraph_format.min.js',
			'paragraph_style.min.js',
			'print.min.js',
			'quick_insert.min.js',
			'quote.min.js',
			'save.min.js',
			'special_characters.min.js',
			'url.min.js',
			'video.min.js',
			'word_paste.min.js'
		];

		if (in_array($name.$suffix,$js_array_list)){

			wp_register_script('froala-'.$name,$path);
			wp_enqueue_script('froala-'.$name);
		}
		else {
			$path = plugins_url(FroalaEditorCustomJSFolderPath.'/'.$name.'.js');
			$headers = @get_headers($path);

			if (preg_match("|200|", $headers[0])) {
				wp_register_script('froala-'.$name,$path);
				wp_enqueue_script('froala-'.$name);
			}
		}
	}

	/**
	 * Enques the css files for the specific plugins, makes sure that plugins that don't have css file won't load.
	 *
	 * @param null $name        *Will be the name of the file same as the plugin name
	 * @param null $suffix      *Will be the suffix of the file like: ".min.css", ".css"
	 */
	public function froala_enque_editor_plugins_css ($name = null, $suffix = null) {

		$css_array_list = [
			'char_counter.css',
			'code_view.css',
			'colors.css',
			'draggable.css',
			'emoticons.css',
			'file.css',
			'fullscreen.css',
			'help.css',
			'image.css',
			'image_manager.css',
			'line_breaker.css',
			'quick_insert.css',
			'special_characters.css',
			'table.css',
			'video.css'
		];

		$path = plugin_dir_url( __FILE__ ) . 'css/plugins/'.$name.$suffix;

		if (in_array($name.$suffix,$css_array_list)) {
			wp_register_style('froala-'.$name,$path);
			wp_enqueue_style('froala-'.$name);
		}
	}

	/**
	 * Injects the main javascript file and loads the Froala editor.
	 *
	 * @param null $content
	 */
	public function froala_enque_editor_script ($content = null) {

		wp_enqueue_script( 'editor-init', plugins_url('public/js/plugins/editor-init.js',dirname( __FILE__ )));
		wp_add_inline_script( 'editor-init', $content );
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

	/** Callback function for public hook "froala_before_public_init"
	 *
	 * @param null $path        * File path on server.
	 * @param null $type        * Can be js or css
	 * @param string $prop      * Can be inline|file
	 * @param null $mix         * If prop = file, mix will be the file name else if prop = inline mix will be the data.
	 *
	 * @return array|WP_Error
	 */
	public function froala_editor_before ($path = null, $type = null, $prop = 'file', $mix = null) {

		return $this->froala_check_script_before_insert($path, $type, $prop, $mix, 'before');
	}

	/** Callback function for public hook "froala_after_public_init"
	 *
	 * @param null $path        * File path on server.
	 * @param null $type        * Can be js or css
	 * @param string $prop      * Can be inline|file
	 * @param null $mix         * If prop = file, mix will be the file name else if prop = inline mix will be the data.
	 *
	 * @return array|WP_Error
	 */
	public function froala_editor_after ($path = null, $type = null, $prop = 'file', $mix = null) {

		return $this->froala_check_script_before_insert($path, $type, $prop, $mix, 'after');
	}

	/** Helper function that adds js or css scripts to the page.
	 *
	 * @param null $path    * Script path
	 * @param null $type    * Can be js or css.
	 * @param string $prop  * Can be file|inline default file
	 * @param null $mix     * If prop = file, mix will be the file name else if prop = inline mix will be the data.
	 *
	 * @return array|WP_Error
	 */

	public function froala_check_script_before_insert ($path, $type, $prop, $mix, $when) {

		$allowed_types = [ 'js', 'css' ];
		$allowed_prop  = [ 'file', 'inline' ];

		if ( ! is_null( $type ) && ! in_array( strtolower( $type ), $allowed_types ) ) {
			return new WP_Error( 'broke', __( '<div class="error notice"><p>The type param for this hook can be "css" or "js", change accordingly.</p></div>' ) );
		}

		if ( ! is_null( $prop ) && ! in_array( strtolower( $prop ), $allowed_prop ) ) {
			return new WP_Error( 'broke', __( '<div class="error notice"><p>The property param for this hook can be "file" or "inline", change accordingly.</p></div>' ) );
		}

		if ( ! is_null( $prop ) && strtolower( $prop ) == 'file' ) {

			if ( is_null( $type ) ) {
				return new WP_Error( 'broke', __( '<div class="error notice"><p>When adding a new script as a file the file "type" can not be null, change accordingly.</p></div>' ) );
			}
			if ( is_null( $mix ) ) {
				return new WP_Error( 'broke', __( '<div class="error notice"><p>When adding a new script as a file the file "name" can not be null, change accordingly.</p></div>' ) );
			}
			if ( is_null( $path ) ) {
				return new WP_Error( 'broke', __( '<div class="error notice"><p>When adding a new script as a file the file "path" can not be null, change accordingly.</p></div>' ) );
			}
		}

		if ( ! is_null( $prop ) && strtolower( $prop ) == 'inline' ) {

			if ( is_null( $mix ) ) {
				return new WP_Error( 'broke', __( '<div class="error notice"><p>When adding inline scripts, the script must contain "data", change accordingly.</p></div>' ) );
			}
		}

		if ( ! is_null( $path ) ) {


			if ( $this->froala_check_plugin_path( $path ) ) {

				array_push( $this->custom_scripts, array(
						'path' => $path,
						'type' => strtolower( $type ),
						'prop' => strtolower( $prop ),
						'mix'  => $mix
					)
				);
				$this->custom_scripts_status = $when;

				return $this->custom_scripts;
			}
		} else if ( is_null( $path ) && ! is_null( $mix ) && strtolower( $prop ) == 'inline' ) {

			if ( is_null( $type ) ) {
				return new WP_Error( 'broke', __( '<div class="error notice"><p>When adding a new inline script the file "type" can not be null, change accordingly.</p></div>' ) );
			}

			array_push( $this->custom_scripts, array(
					'path' => $path,
					'type' => strtolower( $type ),
					'prop' => strtolower( $prop ),
					'mix'  => $mix
				)
			);
			$this->custom_scripts_status = $when;

			return $this->custom_scripts;
		}
		return new WP_Error( 'broke', __( '<div class="error notice"><p>Please check your path, the file was not found on the server. <br/> This may be from an improper htaccess config or read wright on that folder.</p></div>' ) );
	}

	/** Callback function that inserts inline|file scripts
	 *
	 */
	public function froala_set_custom_script () {


		if (isset($this->custom_scripts)) {

			foreach ($this->custom_scripts as $c_script) {

				if ($c_script['prop'] == 'file') {

					if (strtolower($c_script['type']) == 'css') {
						wp_register_style($c_script['mix'], $c_script['path']);
						wp_enqueue_style($c_script['mix']);
					}

					if (strtolower($c_script['type']) == 'js') {
						wp_register_script($c_script['mix'], $c_script['path']);
						wp_enqueue_script($c_script['mix']);
					}

				} else if ($c_script['prop'] == 'inline') {

					if (strtolower($c_script['type']) == 'css') {
						$this->froala_add_inline_css($c_script['mix']);
					}

					if (strtolower($c_script['type']) == 'js') {
						$this->froala_add_inline_js($c_script['mix']);
					}
				}
			}
		}

	}

	/** Adds inline javascript.
	 * @param null $content     * Must be the actual javascript content. e.g alert('test')
	 */
	public function froala_add_inline_js ($content = null) {
		wp_enqueue_script( 'froala-custom-scripts-js', plugins_url('public/js/plugins/editor-init.js',dirname( __FILE__ )), array(), '1' );
		wp_add_inline_script( 'froala-custom-scripts-js', $content );
	}

	/** Adds inline css.
	 * @param null $content     * Must be the actual css content e.g h1 {background-color: #00ffff;}
	 */
	public function froala_add_inline_css ($content = null) {
		wp_enqueue_style( 'froala-custom-scripts-css', '#', array(), '1.0' );
		wp_add_inline_style( 'froala-custom-scripts-css', $content );
	}

	/** Image File Upload
	 * Upload Files to WordPress Media Folder
	 *
	 * returns file path in json format
	 */
	public function froala_upload_files() {

		if ($_FILES) {

			// Let WordPress handle the upload.
			$attachment_id = media_handle_upload( 'file', 0 );

			if ( is_wp_error( $attachment_id ) ) {
				// There was an error uploading the image.
			} else {
				// The image was uploaded successfully!
				$file_path      = wp_get_attachment_url( $attachment_id );
				$response       = new StdClass;
				$response->link = $file_path;

				echo stripslashes( json_encode( $response ) );
			}
		}
		exit();
	}

	/** Image File Manger
	 * Pulls all the images from the Wordpress Media.
	 *
	 * returns object in json format
	 */
	public function froala_image_manager() {


		$query_images_args = array (
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_status'    => 'inherit',
			'posts_per_page' => - 1,
		);

		$query_images = new WP_Query( $query_images_args );

		$images = array();
		$obj = array();
		foreach ( $query_images->posts as $image ) {
			$images['url']   = wp_get_attachment_url( $image->ID );
			$images['thumb'] = wp_get_attachment_image_src( $image->ID, $size = 'thumbnail', $icon = false )[0];
			$images['tag']   = get_post_meta( $image->ID, '_wp_attachment_image_alt', true );
			$obj[]           = $images;
		}

		echo(stripslashes(json_encode($obj)));

		exit();
	}

}






