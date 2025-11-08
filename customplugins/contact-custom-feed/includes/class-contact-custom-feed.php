<?php
/* ============================================
 * File: includes/class-contact-custom-feed.php
 * ============================================ */

/**
 * Main Plugin Class
 */
class Contact_Custom_Feed {

    protected $admin;
    protected $helper;

    public function __construct() {
        $this->load_dependencies();
    }

    private function load_dependencies() {
        $this->admin = new CCF_Admin();
        $this->helper = new CCF_Helper();
    }

    public function run() {
        $this->admin->init();
    }

    public static function activate() {
        // Activation code here
        flush_rewrite_rules();
    }

    public static function deactivate() {
        // Deactivation code here
        flush_rewrite_rules();
    }
}
