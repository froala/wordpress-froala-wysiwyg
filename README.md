# WordPress Froala WYSIWYG Editor

<h3>Compatibility</h3>

Version: 3.3.0 up to present versions of WordPress.


<h2>Manual Instalation</h2>

Clone or download the contents of this repo, make a new folder inside your WordPress installation under plugins folder.
Copy the contents that you previously downloaded to the new folder.

The plugin will be available under plugins in your WordPress admin area.

<h2>Instalation</h2>

Enter your admin area of your WordPress installation, go to plugins and click add new. Search for Froala Wysiwyg Editor
and follow the automated process. In most cases the "ftp://" credentials will be needed. This is default WordPress behavior
when installing new plugins.

<h2>Integration</h2>

Go to your plugins page inside the admin area of your WordPress installation and activate the plugin.
<br/><em>The plugin will replace the default editor</em>


<h2>Usage</h2>

The plugin can be used under the admin area as soon as it is active.

The plugin has a settings page that will be available after you activate the plugin. Under the settings page there will 
be an input for the licence key and a dropdown with all the available plugins that can be activated/deactivated.

To use the Froala Editor on the front-end part of the website, the plugin must be initialized from themes folder.

The activate function accepts 2 parameters but the editor can be init using just one. The second param can be an array or object of options
that can be passed to the editor.

<strong>For a complete list of options have a look over our <a href="https://www.froala.com/wysiwyg-editor/docs/options">options list</a> </strong>

Example of simple init :

```php
// Static method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.

Froala_Editor::activate('#comment');

```

Example of inti using 2 params:

```php

// Static method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.
// 'array()'   Represents the list of options that are passed to the editor.

Froala_Editor::activate('#comment',array('colorsBackground' => ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE'],
                                         'colorsText'       => ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE']
                                        ));
                                        
```

Example for usage on the front-end but saving the images inside WordPress media library:

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

Example of RTL LTR Direction Buttons:

```php

// Static method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.
// 'array()'   Represents the list of options that are passed to the editor.

Froala_Editor::activate('#comment',array('colorsBackground'=> ['#61BD6D', '#1ABC9C', '#54ACD2', 'REMOVE'],
                                         'direction' =>'rtl'
                                         ));
                                         
```

Example of setting the min height and limiting the Html attributes:

```php
// Static method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.
// 'array()'   Represents the list of options that are passed to the editor.

Froala_Editor::activate('#comment',array('htmlAllowedAttrs' => ['title', 'href', 'alt', 'src', 'style'],
	                                     'heightMin' => '200'
                                         ));

```

Example of limiting the Html tags and removing unwanted tags:

```php
// Static method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.
// 'array()'   Represents the list of options that are passed to the editor.

Froala_Editor::activate('#comment',array('htmlAllowedTags' => ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
	                                     'htmlRemoveTags'  => ['script', 'style', 'base']
                                         ));

```

Example of setting the toolbar buttons and toolbar position:

```php
// Static method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.
// 'array()'   Represents the list of options that are passed to the editor.

Froala_Editor::activate('#comment',array('toolbarButtons' => ['bold', 'italic', 'underline'],
                                         'toolbarBottom' => true
                                         ));

```

Example of code beautifier passing options as object:

```php
// Static method for easy instantiation for the editor.
// '#comment'  Represents the html element selector.
// 'array()'   Represents the list of options that are passed to the editor.


Froala_Editor::activate('#comment',(object)  ['codeBeautifierOptions' =>[
                                              'end_with_newline' => true,
                                              'indent_inner_html'=> true,
                                              'extra_liners' =>['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'pre', 'ul', 'ol', 'table', 'dl'],
                                              'brace_style' =>'expand',
                                              'indent_char' => ' ',
                                              'indent_size' => '4',
                                              'wrap_line_length'=> '0'
                                              ],]);

```

Example of using the editor in inline mode and allowing only some types of images: 

```php 

Froala_Editor::activate('#comment',array('imageAllowedTypes' => ['jpeg', 'jpg', 'png'],
                                         'toolbarInline' => true
                                         ));

```


Example of using the tolbar sticky and setting an offeset for it:

```php 

Froala_Editor::activate('#comment',array('toolbarSticky' => true,
                                         'toolbarStickyOffset' => '50'
                                         ));

```


<h2>License</h2>

The <code>WordPress Froala WYSIWYG Editor</code> project is under MIT license. However, in order to use WordPress Froala WYSIWYG Editor plugin you should purchase a license for it.

Froala Editor has <a href="https://www.froala.com/wysiwyg-editor/pricing"> 3 different</a> licenses for commercial use. For details please see <a href="https://www.froala.com/wysiwyg-editor/terms"> License Agreement.</a>

