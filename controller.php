<?php

/**
 * A base controller class for shortcut application controllers
 * @author Ron Masas <ronmasas@gmail.com>
 */
class Controller
{

    protected $db;
    protected $get;
    protected $post;
    protected $server;
    protected $shortcut;

    /**
     * @param Shortcut $shortcut
     * @return void
     */
    public function __construct($shortcut) {
        global $wpdb;
        // Database connection
        $this->db = $wpdb;
        // Requests get params
        $this->get = $_GET;
        // Requests post params
        $this->post = $_POST;
        // Global server array
        $this->server = $_SERVER;
        // Shortcut instace
        $this->shortcut = $shortcut;
    }

    /**
     * Wrap the shortcut instace view function for quick aceess
     * @param string $template
     * @param array $vars
     * @return string
     */
    protected function view($template, $vars = array()) {
        return $this->shortcut->view($template, $vars);
    }

}
