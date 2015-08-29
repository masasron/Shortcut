# Shortcut
Object oriented WordPress development.


### Installation

* Upload the entire `shortcut` folder to the `/wp-content/plugins/` directory.
* Activate the plugin through the 'Plugins' menu in WordPress.

### Working with Shortcut

Start by coping the `demo` folder to the `/wp-content/plugins/` directory.
Lets take a look at the `app.php` file, in here you can register all of the plugin functionality.
We also have two folders `controllers` where all of your plugins controllers will be. and `views` where all of your plugin views will be.

### Creating a Controller
When creating a controller you need to extends the base controller named Controller.
You can see an example for a controller on `demo/controllers/TestController.php`

```php

class TestContoller extends Controller {

    public function testing() {
        $this->db; // $wpdb
        $this->get; // $_GET
        $this->post; // $_POST
        $this->server; // $_SERVER
        $this->shortcut; // The current Shortcut object
    }

}

```

### Shortcut Api

You can use `$this` or `$plugin` when your in the `app.php` file.
if you want to use any of the Shortcut functions when your in a controller just use the `$this->shortcut->{function}`.

##### Shortcode

```php
// Link shortcode to a method on TestController
$this->shortcode('testing', 'TestController@shortcode');

$this->shortcode('testing', function (){
  return 'shortcode output.';
});
```


##### Ajax

```php
$this->ajax('test','TestController@firstTest');

$this->ajax('test',function (array $request) {
    return array(
      'testing' => true,
      'request' => $request // post & get request array
    );
});
```

##### Page
Add a WordPress page

```php
$this->page(array(
    'title' => 'Plugin Settings',
    'parent' => 'options-general',
    'request.get' => 'TestController@getPage', // Runs on a get request to the page
    'request.post' => 'TestController@postPage' // Runs on a post request to the page
));
```

##### Adding a filter

```php
$this->filter('body_class',function ($classes){
    return array_merge($classes,['injected_class']);
});

// You can also use controller
$this->action('template_redirect','TestController@filterBodyClasses');


// TestController.php
public function filterBodyClasses($classes){
  return array_merge($classes,['injected_class']);
}
```

##### Adding an action

```php
$this->action('template_redirect',function (){
    // ...
});

// You can also use controller
$this->action('template_redirect','TestController@templateRedirect');


// TestController.php
public function templateRedirect(){
  // ...
}
```


