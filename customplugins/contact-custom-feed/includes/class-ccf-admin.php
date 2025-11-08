<?php
/* ============================================
 * File: includes/class-ccf-admin.php
 * ============================================ */

/**
 * Admin Class
 */
class CCF_Admin
{
    public function init()
    {
        add_action("admin_menu", [$this, "add_admin_menu"]);
        add_action("admin_enqueue_scripts", [$this, "enqueue_admin_styles"]);
        add_action("admin_post_export_cf7_entries", [
            $this,
            "myplugin_export_cf7_entries",
        ]);
    }

    public function add_admin_menu()
    {
        add_menu_page(
            "Contact Custom Feed", // Page title
            "Contact Feed", // Menu title
            "manage_options", // Capability
            "contact-custom-feed", // Menu slug
            [$this, "render_admin_page"], // Callback
            "dashicons-rss", // Icon
            30, // Position
        );
    }

    public function myplugin_export_cf7_entries()
    {
        // Read form ID from GET
        $form_id = isset($_GET["form_id"]) ? intval($_GET["form_id"]) : 0;

        if ($form_id <= 0) {
            wp_die("Invalid or missing form ID.");
        }

        // Load helper and export
        $helper = new CCF_Helper();
        $helper->export_entries_to_csv($form_id);

        exit();
    }
    public function render_admin_page()
    {
        if (!current_user_can("manage_options")) {
            return;
        }

        // Get helper instance
        $helper = new CCF_Helper();

        $selected_form_id = isset($_GET["cf7_form"])
            ? intval($_GET["cf7_form"])
            : 0;

        // Load all forms from Advanced CF7 DB
        $forms = vsz_cf7_get_the_form_list(); // get all CF7 forms // You must create this method

        // var_dump($forms);
        // If a form is selected, load only entries from that form
        if ($selected_form_id) {
            $entries = $helper->get_cf7_entries_by_form($selected_form_id);
        }
        // Get all CF7 entries
        // $entries = $helper->get_cf7_entries();

        include CCF_PLUGIN_DIR . "admin/views/admin-page.php";
    }

    public function enqueue_admin_styles($hook)
    {
        // Only load on our plugin page
        if ($hook !== "toplevel_page_contact-custom-feed") {
            return;
        }

        // Enqueue custom styles if needed
        // wp_enqueue_style('ccf-admin', CCF_PLUGIN_URL . 'admin/css/admin-style.css', array(), CCF_VERSION);
    }
}
