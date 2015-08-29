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
        
        return $this->view('settings'); // display a view
    }

}

```

### Shortcut Api

You can use `$this` or `$plugin` when your in the `app.php` file.
if you want to use any of the Shortcut functions when your in a controller just use the `$this->shortcut->{function}`.

##### Using views

I think the biggest problem with WordPress is there is no clear separation of views and controllers.
Usually plugins code looks like a mix of the two, the `view` command allows you to completely separate
the two in a clean and readable way.

You can create a view just create a new file in your plugin `views` folder.
To render a view from a controller just use the `$this->view` method and pass the file name without the `.php`

```php

public function getPage(){
    
    return $this->view('settings');

}

```

You can also pass variables to the view by passing a second argument

```php

public function getPage(){

    $title = 'Hello World';
    
    $options = $this->getOptions(['first','second']);
    
    return $this->view('settings',compact('title','options'));
    
}

```


##### Adding shortcodes

```php
// Link shortcode to a method on TestController
$this->shortcode('testing', 'TestController@shortcode');

$this->shortcode('testing', function (){
  return 'shortcode output.';
});
```


##### Using ajax

```php
$this->ajax('test','TestController@firstTest');

$this->ajax('test',function (array $request) {
    return array(
      'testing' => true,
      'request' => $request // post & get request array
    );
});
```

##### Adding pages
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

##### Displaying a notice
Sometimes you need to display an error or success notice to the user after some action.
You can do this by using the $this->shortcut->notice function.

```php

//TestController.php

public function postPage() {
    // ...
    $whitelist = ['website_public','website_secret'];
    $this->shortcut->updateOptions($this->post,$whitelist);
    $this->shortcut->notice('Saved.');
    // You can also do
    $this->shortcut->notice('Error.');
    // Or
    $this->shortcut->updateOptions($this->post,$whitelist)->notice('Saved.');
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

##### multiIsset

Allows you to check multible cells in an array at once

`$array` any array
`$children` an array of potential cells

```php

$response = Api::request('user/1/photos');

// Before
if ( isset($response['results']) && isset($response['results']['photos']) && ... )

// After
if ( $this->multiIsset($response, ['results','photos']['url'] ){
    // do something with $response['results']['photos']['url']
}


```



