# WordPress Froala WYSIWYG Editor


<h3>Compatibility</h3>

Version: 3.3.0 up to present versions of WordPress.


<h2>Manual Instalation</h2>

Clone or download the contents of this repo, make a new folder inside your WordPress instalation under plugins folder.
Coppy the contentes that you previously downloaded to the new folder.

The plugin will be available under plugins in your WordPress admin area.

<h2>Instalation</h2>

Enter your admin area of your WordPress instalation, go to plugins and cick add new. Search for Froala Wysiwyg Editor
and follow the automated proccess. In most cases the "ftp://" credentials will be needed. This is default WordPress behaivour
when installing new plugins.

<h2>Integration</h2>

Go to your plugins page inside the admin area of your WordPress instalation and activate the plugin.
<br/><em>The plugin will replace the default editor</em>


<h2>Usage</h2>

The plugin can be used under the admin area as soon as it is active.

The plugin has a settings page that will be availalbe after you activate the plugin. Under the settings page there will 
be an input for the licence key and a dropdown with all the available plugins that can be activated/deactivated.

To use the Froala Editor on the front-end part of the website, the plugin must be initialized from themes folder.

The activate function accepts 2 paramenters but the editor can be init using just one. The second param can be an array or object of options
that can be passed to the editor.

<strong>For a complete list of options have a look over our <a href="https://www.froala.com/wysiwyg-editor/docs/options">options list</a> </strong>

Simple init example:
```php
// Static method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.

Froala_Editor::activate('#comment'); </code>
```

Two paramenters init example:
```php
// Static method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.
// 'array()'   Represents the list of options that are passed to the editor.

Froala_Editor::activate('#comment',array('colorsBackground' => ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE'],
                                         'colorsText'       => ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE']
                                        ));
```

Init example for usage on the front-end but saving the images inside WordPress media library:

```php

// Static method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.
// 'array()'   Represents the list of options that are passed to the editor.

$path = plugins_url('includes/froala-upload-to-server.php', dirname( __FILE__ ));
Froala_Editor::activate('#comment',array('colorsBackground   '=> ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE'],
                                         'colorsText'         => ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE'],
                                         'imageUploadURL'     => $path.'?upload_image=1',
                                         'imageManagerLoadURL'=> $path.'?view_images=1
                                        ));

```

<h2>License</h2>

The <code>WordPress Froala WYSIWYG Editor</code> project is under MIT license. However, in order to use WordPress Froala WYSIWYG Editor plugin you should purchase a license for it.

Froala Editor has <a href="https://www.froala.com/wysiwyg-editor/pricing"> 3 different</a> licenses for commercial use. For details please see <a href="https://www.froala.com/wysiwyg-editor/terms"> License Agreement.</a>

