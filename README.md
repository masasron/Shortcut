# Shortcut
Speed up your WordPress development.


### Installation

* Upload the entire `shortcut` folder to the `/wp-content/plugins/` directory.
* Activate the plugin through the 'Plugins' menu in WordPress.

#### Creating a new Plugin

You can use any folder structure you want, if you want to use the view functionality of Shortcut you must have a `views` folder in your plugin root.

```php

/*
  Plugin Name: Empty Plugin
  Description: An empty shortcut plugin.
  Version: 1.0.0
 */
 
// Make sure Shortcut is active
if (!class_exists('Shortcut'))
{
    return add_action('init', function() {
        deactivate_plugins(plugin_basename(__FILE__));
    });
}

/**
 * When your creating a new instance of Shortcut you can pass an array of a string of files. 
 * The array/string will be evaluated and included using the `glob` function.
 */
$sc = new Shortcut(['controllers/*.php'], __DIR__);

```

#### Controllers

If your using controllers make sure to use `extends Controller` on each one.

```php
/*
 * Main controller
 */
class MainController extends Controller
{
    // ...
}

```
