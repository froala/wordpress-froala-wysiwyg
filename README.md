# WordPress Froala WYSIWYG Editor


## Compatibility

Version: 3.3.0 up to present versions of WordPress.


## Manual Installation

Clone or [download the contents of this repo](https://github.com/froala/wordpress-froala-wysiwyg/archive/master.zip), make a new folder inside your WordPress installation under plugins folder.
Copy the contentes that you previously downloaded to the new folder.

The plugin will be available under plugins in your WordPress admin area.

## Installation

Enter your admin area of your WordPress installation, go to plugins and cick add new. Search for Froala Wysiwyg Editor
and follow the automated proccess. In most cases the "ftp://" credentials will be needed. This is default WordPress behaivour
when installing new plugins.

## Integration

Go to your plugins page inside the admin area of your WordPress installation and activate the plugin.
**The plugin will replace the default editor**.


## Usage

The plugin can be used under the admin area as soon as it is active.

The plugin has a settings page that will be available after you activate the plugin. Under the settings page there will 
be an input for the licence key and a dropdown with all the available plugins that can be activated/deactivated.


Include plugins in file `class-froala-public.php` from [Available Plugin List](https://froala.com/wysiwyg-editor/docs/plugins)

```php
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
  'word_paste.min.js',
  '../third_party/font_awesome.min.js',
  '../third_party/spell_checker.min.js',
  '../third_party/image_tui.min.js'
];
```

```php
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
  'video.css',
  '../third_party/font_awesome.min.css',
  '../third_party/spell_checker.min.css',
  '../third_party/image_tui.min.css'
];
```

and update in the file `class-froala-admin.php` with corresponding to plugin added above

```php
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
array('name'=>'video'),
array('name'=>'../third_party/font_awesome'),
array('name'=>'../third_party/spell_checker'),
array('name'=>'../third_party/image_tui'));
```

```php
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

wp_register_style('font_awesome_css',plugin_dir_url( __FILE__ ) . 'css/third_party/font_awesome.min.css');
wp_register_style('spell_checker_css',plugin_dir_url( __FILE__ ) . 'css/third_party/spell_checker.min.css');
wp_register_style('image_tui_css',plugin_dir_url( __FILE__ ) . 'css/third_party/image_tui.min.css');

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
wp_enqueue_style('font_awesome_css');
wp_enqueue_style('spell_checker_css');
wp_enqueue_style('image_tui_css');
```


To use the Froala Editor on the front-end part of the website, the plugin must be initialized from themes folder.

The activate function accepts 2 parameters but the editor can be init using just one. The second param can be an array or object of options
that can be passed to the editor.

**For a complete list of options have a look over our [options list](https://www.froala.com/wysiwyg-editor/docs/options).**
#### Constants :

```php
define('FroalaCustomJSFolderPath', '/'.basename(__DIR__).'/custom/js');
define('FroalaCustomCSSFolderPath', '/'.basename(__DIR__).'/custom/css');

```

#### Public hooks:

```php

// There are 2 available hooks that work for the front-end part of the website.
// froala_before_public_init acts before the editor gets initialized and 
// froala_after_public_init acts after the editor and all the plugins are loaded.
// Callback function for these hooks acepts 4 params

/** Callback function for public hooks"
 *
 * @param null $path        * File path on server.
 * @param null $type        * Can be js or css
 * @param string $prop      * Can be inline|file
 * @param null $mix         * If prop = file, mix will be the file name else if prop = inline mix will be the data.
 *
 * @return array|WP_Error
 *
 *
* To use a public hook, it needs to be registered right after the editor get's instantiated. The propper way 
* would be to store it in a variable so you can have access to the debug log.
*
* This example includes a custom css file and load's it acordingly, because it's used after public init the css file
* will be at the very bottom of your head tag.

* To understand better, the params are in this way: 
* 1' st froala_after_public_init        => name of the hook.
* 2' nd $custom_css_path.'/test.css'    => path to the file.
* 3' rd 'css'                           => script type.
* 4' th 'file'                          => script property, can be file|inline.
* 5' th 'test'                          => the name of the file. 
*/
$custom_css_path = plugins_url(FroalaEditorCustomCSSFolderPath);
$custom_js_path = plugins_url(FroalaEditorCustomJSFolderPath);

$hook = apply_filters('froala_after_public_init', $custom_css_path.'/test.css', 'css', 'file','test');

if( is_wp_error( $hook ) ) {
	echo $hook->get_error_message();
}

// Same as the example above but it includes a javascript file and the action of the hook it's before Froala Editor's initialization.
$hook = apply_filters('froala_before_public_init', $custom_js_path.'/test.js', 'js', 'file','test');

if( is_wp_error( $hook ) ) {
  echo $hook->get_error_message();
}
// Example using inline script

$hook = apply_filters('froala_after_public_init', null, 'js', 'inline', 'console.log("test")');

if( is_wp_error( $hook ) ) {
  echo $hook->get_error_message();
}

// Example using inline css
$hook = apply_filters('froala_before_public_init', null, 'css', 'inline', 'h1 {background-color: #00ffff;}');


if( is_wp_error( $hook ) ) {
  echo $hook->get_error_message();
}

// Note!! 
//The hooks must be registered right after instantiating the FroalaEditor class.

$Froala_Editor = new Froala_Editor();
.
.
.
$hook = apply_filters('froala_before_public_init', null, 'css', 'inline', 'h1 {background-color: #00ffff;}');
.
.
$froala->activate('#comment',array('colorsBackground   '=> ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE'],
                                         'colorsText'         => ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE']
                                        ));
```

#### Admin hooks:

```php

// There are 2 available hooks that work for the admin part of the website.
// froala_before_init acts before the editor gets initialized and 
// froala_after_init acts after the editor and all the plugins are loaded.
// Callback function for these hooks acepts 4 params

/** Callback function for public hooks"
 *
 * @param null $path        * File path on server.
 * @param null $type        * Can be js or css
 * @param string $prop      * Can be inline|file
 * @param null $mix         * If prop = file, mix will be the file name else if prop = inline mix will be the data.
 *
 * @return array|WP_Error
 *
 *
* To use a private hook, it needs to be registered before the editor get's initialized. The propper way 
* would be to store it in a variable so you can have access to the debug log.
*
* This example includes a custom css file and load's it acordingly, because it's used after admin init the css file
* will be at the very bottom of your head tag.

* To understand better, the params are in this way: 
* 1' st froala_after_public_init        => name of the hook.
* 2' nd $custom_css_path.'/test.css'    => path to the file.
* 3' rd 'css'                           => script type.
* 4' th 'file'                          => script property, can be file|inline.
* 5' th 'test'                          => the name of the file. 
*/

$custom_css_path = plugins_url(FroalaEditorCustomCSSFolderPath);
$custom_js_path = plugins_url(FroalaEditorCustomJSFolderPath);

$hook = apply_filters('froala_after_init', $custom_css_path.'/test.css', 'css', 'file','test');

if( is_wp_error( $hook ) ) {
  echo $hook->get_error_message();
}
// Same as the example above but it includes a javascript file and the action of the hook it's before Froala Editor's initialization.

$hook = apply_filters('froala_before_init', $custom_js_path.'/test.js', 'js', 'file','test');

if( is_wp_error( $hook ) ) {
  echo $hook->get_error_message();
}
// Example using inline script

$hook = apply_filters('froala_after_init', null, 'js', 'inline', 'console.log("test")');

if( is_wp_error( $hook ) ) {
  echo $hook->get_error_message();
}
// Example using inline css

$hook = apply_filters('froala_before_init', null, 'css', 'inline', 'h1 {background-color: #00ffff;}');

if( is_wp_error( $hook ) ) {
 echo $hook->get_error_message();
}

```


#### Example of simple init :

```php
// Public method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.

$Froala_Editor = new Froala_Editor();
$Froala_Editor->activate('#comment');

```

#### Example of intializing using 2 params:

```php

// Static method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.
// 'array()'   Represents the list of options that are passed to the editor.

$Froala_Editor = new Froala_Editor();
$Froala_Editor->activate('#comment',array('colorsBackground' => ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE'],
                                         'colorsText'       => ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE']
                                        ));
                                        
```

#### Example for usage on the front-end but saving the images inside WordPress media library:

```php

// Public method for easy instantiation of the Froala WYSIWYG
// '#comment'  Represents the html element selector.
// 'array()'   Represents the list of options that are passed to the editor.

$Froala_Editor = new Froala_Editor();
$Froala_Editor->activate('#comment',array(
                                    'imageUploadParams'  => ['action' =>'froala_upload_files'],
                                    'imageUploadURL'     => admin_url( 'admin-ajax.php' ),
                                    'imageManagerLoadParams'   => ['action' =>'froala_image_manager'],
                                    'imageManagerLoadURL'=> admin_url( 'admin-ajax.php' )
                                    ));

```

#### Example for adding new plugin for Froala Editor
This will be visible in the admin under Froala WYSIWYG settings and can be activated/deactivated

```php

// FroalaEditorCustomJSFolderPath is a constant defined on plugin activation will return
// the path to the Custom JS folder e.g: /froala/custom/js    
// froala_new_plugin, custom hook that integrates the new plugin and registers the new script
// The hook takes 2 params, 1'st the path to the plugin and 2'nd the name of the plugin.
// The if statement check if there are any erros on registering the new plugin

$custom_plugin_path = plugins_url(FroalaEditorCustomJSFolderPath);
$new_plugin = apply_filters('froala_new_plugin', $custom_plugin_path . '/test.js', 'test');

if( is_wp_error( $new_plugin ) ) {
	echo $new_plugin->get_error_message();
}

After calling the hook inside the admin pannel under Froala WYSIWYG settings there will be a new plugin in the list
called "test".

```
Add the above code to froala.php file to add plugin inside admin panel.After adding a new plugin, it needs to be activated from the admin panel. For an easier understanding the plugin will come with a dummy file placed inside "froala/custom/js/". You can delete this file at any time it's just for demo purposes.

For checking out how plugin works without adding it to admin panel,use the following code inside functions file in your theme:

```php
$custom_plugin_path = plugins_url(FroalaEditorCustomJSFolderPath);
$new_plugin = apply_filters('froala_before_public_init', $custom_plugin_path . '/test.js','js','file', 'test');

if( is_wp_error( $new_plugin ) ) {
  echo $new_plugin->get_error_message();
}

```
This will help if you copy/paste the above code inside your functions file.
 


## License

The `WordPress Froala WYSIWYG Editor` project is under MIT license. However, in order to use WordPress Froala WYSIWYG Editor plugin you should purchase a license for it.

Froala Editor has [3 different licenses](https://www.froala.com/wysiwyg-editor/pricing) for commercial use. For details please see [License Agreement](https://www.froala.com/wysiwyg-editor/terms).

