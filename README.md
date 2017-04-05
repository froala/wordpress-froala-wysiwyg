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

To use the Froala Editor on the front-end part of the website, the plugin must be initialized from themes folder.

The activate function accepts 2 parameters but the editor can be init using just one. The second param can be an array or object of options
that can be passed to the editor.

**For a complete list of options have a look over our [options list](https://www.froala.com/wysiwyg-editor/docs/options).**
#### New constants :

```php
define('PluginPath', '/'.basename(__DIR__).'/includes/froala-upload-to-server.php');
define('CustomJSFolderPath', '/'.basename(__DIR__).'/custom/js');
define('CustomCSSFolderPath', '/'.basename(__DIR__).'/custom/css');

```


#### Example of simple init :

```php
// Static method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.

Froala_Editor::activate('#comment');

```

#### Example of intializing using 2 params:

```php

// Static method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.
// 'array()'   Represents the list of options that are passed to the editor.

Froala_Editor::activate('#comment',array('colorsBackground' => ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE'],
                                         'colorsText'       => ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE']
                                        ));
                                        
```

#### Example for usage on the front-end but saving the images inside WordPress media library:

```php

// Static method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.
// 'array()'   Represents the list of options that are passed to the editor.
// PluginPath  Represents a constant defined on plugin activation.

$path = plugins_url(PluginPath);
Froala_Editor::activate('#comment',array('colorsBackground   '=> ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE'],
                                         'colorsText'         => ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE'],
                                         'imageUploadURL'     => $path.'?upload_image=1',
                                         'imageManagerLoadURL'=> $path.'?view_images=1
                                        ));

```


#### Example for adding new plugin

```php

// CustomJSFolderPath is a constant defined on plugin activation will return
// the path to the Custom JS folder e.g: /froala/custom/js    
// froala_before_init custom hook that integrates the new plugin and registers the new script
// The hook takes 2 params, 1'st the path to the plugin and 2'nd the name of the plugin.
// The if statement check if there are any erros on registering the new plugin

$custom_plugin_path = plugins_url(CustomJSFolderPath);
$new_plugin = apply_filters('froala_before_init', $custom_plugin_path . '/test.js', 'test');

if( is_wp_error( $new_plugin ) ) {
	echo $new_plugin->get_error_message();
}


```
Add the above code to your functions file inside your theme to see how it works. For an easier understanding 
the plugin will come with a dummy file placed inside "froala/custom/js/". This will help if you copy/paste the above code inside your functions file.
 
You can delete this file at any time it's just for demo purposes.

After adding a new plugin, it needs to be activated from the admin panel.

## License

The `WordPress Froala WYSIWYG Editor` project is under MIT license. However, in order to use WordPress Froala WYSIWYG Editor plugin you should purchase a license for it.

Froala Editor has [3 different licenses](https://www.froala.com/wysiwyg-editor/pricing) for commercial use. For details please see [License Agreement](https://www.froala.com/wysiwyg-editor/terms).

