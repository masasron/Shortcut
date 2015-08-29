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

### Helpers

##### updateOptions

Allows you to quickly save options to the database.
`$options` an array of key value options usually the request.
`$whitelist` an array of values to function will try to create options for.
`$prefix` a prefix string that will be used for each option, default is an empty string.

```php

//TestController.php

public function postPage() {
    // ...
    $whitelist = ['website_public','website_secret'];
    $this->shortcut->updateOptions($this->post,$whitelist);
    // ...
}

```

##### getOptions

Allows you quickly get an array of key value pers of a given list.

`$options` an array of option keys.
`$prefix` a prefix string for each option, default is an empty string.

```php

//TestController.php

public function getPage() {
    // ...
    $options = $this->shortcut->getOptions(['website_public','website_secret']);
    return $this->view('settings',compact('options'));
}

```

#### multiIsset

Allows you to check multible cells at once

```php

$response = Api::request('user/1/photos');

// Before
if ( isset($response['results']) && isset($response['results']['photos']) && ... )

// After
if ( $this->multiIsset($response, ['results','photos']['url'] ){
    // do something with $response['results']['photos']['url']
}


```



