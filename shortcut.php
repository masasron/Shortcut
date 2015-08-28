<?php

/**
  Plugin Name: Shortcut
  Description: Speed up your WordPress development.
  Version: 1.0
  Author: Ron Masas <ronmasas@gmail.com>
 */
include_once 'controller.php';

class Shortcut
{

    /**
     * @ver string
     */
    private $path;

    /**
     * @param array $dependencies
     * @param string $path
     * @return Shortcut
     */
    public function __construct($dependencies = false, $path = '') {
        /**
         * @ver string
         */
        $this->path = $path;

        if ($dependencies) {
            $this->requireAllOnce($dependencies, $path);
        }

        return $this;
    }

    /**
     * A better way to check if array cells are defined.
     *
     * @param Array $array - the array you want to test
     * @param Array $children - all the cells keys you want to test by order
     *
     * @return bool
     */
    public function multiIsset(array $array, array $children) {
        foreach ($children as $child) {
            if (!isset($array[$child])) {
                return false;
            }
            $array = $array[$child];
        }
        return true;
    }

    /**
     * Get options
     * @param array $options
     * @param string $prefix
     * @return array
     */
    public function getOptions($options, $prefix = '') {
        $output = array();
        foreach ($options as $option) {
            $output[$option] = get_option($prefix . $option);
        }
        return $output;
    }

    /**
     * Get options
     * @param array $options
     * @param array $whiteList
     * @param string $prefix
     * @return Shortcut
     */
    public function updateOptions($options, $whiteList = false, $prefix = '') {
        foreach ($options as $key => $value) {
            if (is_array($whiteList)) {
                if (!in_array($key, $whiteList)) {
                    continue;
                }
            }
            update_option($prefix . $key, $value);
        }
        return $this;
    }

    /**
     * Add shortcode
     * @param string $code
     * @param string  $controller
     * @return Shortcut
     */
    public function shortcode($code, $controller) {
        add_shortcode($code, $this->fetchCallback($controller));
        return $this;
    }

    /**
     * Add menu item to wordpress settings and bind get/post to controllers.
     * @param string $title
     * @param mixed $getController
     * @param mixed $postController
     * @param mixed $parent
     * @param mixed $capability
     * @return Shortcut
     */
    public function page($title, $getController, $postController, $parent = false, $capability = 'manage_options') {
        $args = [$title, $this->fetchCallback($getController), $this->fetchCallback($postController), $capability, $parent];

        add_action('admin_menu', function() use($args) {

            /**
             * @ver string
             */
            $slug = sanitize_title($args[0]);

            if (getenv('REQUEST_METHOD') === 'POST' && filter_input(INPUT_GET, 'page') === $slug) {
                $this->invoke($args[2], filter_input_array(INPUT_POST));
            }

            if (!$args[4]) {
                add_menu_page($args[0], $args[0], $args[3], $slug, function() use ($args) {
                    print($args[1]());
                });
            } else {
                add_submenu_page($args[4] . '.php', $args[0], $args[0], $args[3], $slug, function() use ($args) {
                    print($args[1]());
                });
            }
        });

        return $this;
    }

    /**
     * Register plugin activation hook
     * @param string $file
     * @param mixed $callback
     * @return Swift
     */
    public function activation($file, $callback) {
        register_activation_hook($file, $this->fetchCallback($callback));
        return $this;
    }

    /**
     * Add filter wrapper
     * @param string $name
     * @param mixed $callback
     * @return Shortcut
     */
    public function filter($name, $callback, $num1 = false, $num2 = false) {
        $args = array(
            $name,
            $this->fetchCallback($callback)
        );

        if ($num1) {
            $args[] = $num1;
        }

        if ($num2) {
            $args[] = $num2;
        }

        call_user_func_array('add_filter', $args);
        return $this;
    }

    /**
     * Render view with vars
     * @param string $template
     * @param array  $vars
     * @param array  $options
     * @param Swift  $self
     * @return string
     */
    public function view($template, $vars) {
        /**
         * @ver string
         */
        $path = $this->path . '/views/' . $template . '.php';

        if (file_exists($path)) {
            ob_start();
            // Convert array to variables
            extract($vars);
            // Require template file
            require $path;
            return ob_get_clean();
        } else {
            // View was not found
            throw new Exception("View on path: `{$path}` was not found.");
        }
    }

    /**
     * Create a new ajax action for privilege and none privilege users, action will be bind to a controller
     * @param string $action
     * @param mixed $controller
     * @return Shortcut
     */
    public function ajax($action, $controller) {
        $self = $this;
        $request = array_merge(filter_input_array(INPUT_GET), filter_input_array(INPUT_POST));

        $this->action(sprintf('wp_ajax_%s', $action), function() use ($controller, $request, $self) {
            return wp_send_json($self->invoke($self->fetchCallback($controller), $request));
        }, $self);

        $this->action(sprintf('wp_ajax_nopriv_%s', $action), function() use ($controller, $request, $self) {
            return wp_send_json($self->invoke($self->fetchCallback($controller), $request));
        }, $self);

        return $this;
    }

    /**
     * Create a notice with a custom message and class
     * @param string $message
     * @param string $class
     * @return Shortcut
     */
    public function notice($message, $class = 'updated') {
        add_action('admin_notices', function () use ($message, $class) {
            switch ($class) {
                case 'update-nag':
                    printf('<div class="%s">%s</div>', $class, $message);
                    break;
                default:
                    printf('<div class="%s"><p>%s</p></div>', $class, $message);
            }
        });

        return $this;
    }

    /**
     * Add action shortcut
     * @param string $name
     * @param mixed $callback
     * @return Shortcut
     */
    public function action($name, $callback) {
        add_action($name, $this->fetchCallback($callback));
        return $this;
    }

    /**
     * Invoke a function
     * @param mixed $args
     * @param mixed $params
     * @return mixed
     */
    private function invoke($args, $params = array()) {
        if (is_callable($args)) {
            return call_user_func($args, $params);
        } elseif (is_array($args)) {
            return call_user_method_array($args[1], $args[0], $params);
        }
    }

    /**
     * Require once using file patterns
     * @param mixed $patterns
     * @return void
     */
    private function requireAllOnce($patterns, $path) {
        if (!is_array($patterns)) {
            $patterns = array($patterns);
        }

        $includes = array_map(function ($n) use ($path) {
            return $path . '/' . $n;
        }, $patterns);

        foreach ($includes as $pattern) {
            $files = glob($pattern);
            foreach ($files as $file) {
                require_once $file;
            }
        }
    }

    /**
     * Fetch a method by string or object
     * @param mixed $args
     * @return mixed results of the invoked method
     */
    private function fetchCallback($args) {
        if (is_callable($args)) {
            return $args;
        } elseif (is_string($args)) {
            /**
             * Fetch controller and invoke sent method
             */
            list($class, $method) = explode('@', $args);

            if (!class_exists($class)) {
                throw new Exception("Controller `{$class}` was not found.");
            }

            $obj = new $class($this);

            if (!method_exists($obj, $method)) {
                throw new Exception("Method `{$method}` on class `{$class}` was not found.");
            }

            return array($obj, $method);
        }
    }

}
