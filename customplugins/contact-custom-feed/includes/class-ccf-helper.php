<?php

/**
 * Helper Class - Add your custom code here
 */
class CCF_Helper
{
    public function __construct()
    {
        // Constructor code here
    }

    /**
     * Get all form IDs that have entries
     *
     * @return array Array of form IDs
     */
    public function get_form_ids_with_entries()
    {
        global $wpdb;

        $table_entry = $wpdb->prefix . "cf7_vdata_entry";

        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_entry'") != $table_entry) {
            return [];
        }

        // Get all form IDs that have entries
        $results = $wpdb->get_results(
            "SELECT `cf7_id` FROM {$table_entry} GROUP BY `cf7_id`",
            ARRAY_N,
        );

        $form_ids = [];
        if (!empty($results)) {
            foreach ($results as $row) {
                $form_ids[] = (int) $row[0];
            }
        }

        return $form_ids;
    }

    /**
     * Get all Contact Form 7 entries from Advanced CF7 DB
     *
     * @param int $limit Number of entries to retrieve (default: all)
     * @param int $offset Offset for pagination (default: 0)
     * @return array Array of form entries
     */
    public function get_cf7_entries($limit = 0, $offset = 0)
    {
        global $wpdb;

        $table_entry = $wpdb->prefix . "cf7_vdata_entry";

        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_entry'") != $table_entry) {
            return [];
        }

        // Build query with optional limit
        $query = "SELECT `data_id`, `cf7_id` FROM {$table_entry} GROUP BY `data_id` ORDER BY `data_id` DESC";

        if ($limit > 0) {
            $query .= $wpdb->prepare(" LIMIT %d, %d", $offset, $limit);
        }

        // Get all unique entries
        $entries = $wpdb->get_results($query, ARRAY_A);

        if (empty($entries)) {
            return [];
        }

        // Get detailed data for each entry
        $detailed_entries = [];
        foreach ($entries as $entry) {
            $data_id = (int) $entry["data_id"];
            $entry_data = $this->get_entry_by_data_id($data_id);

            if (!empty($entry_data)) {
                $detailed_entries[] = $entry_data;
            }
        }

        return $detailed_entries;
    }

    /**
     * Get entry data by data_id
     *
     * @param int $data_id The data ID
     * @return array Entry data with parsed fields
     */
    public function get_entry_by_data_id($data_id)
    {
        global $wpdb;

        $table_entry = $wpdb->prefix . "cf7_vdata_entry";

        // Get all fields for this entry
        $fields = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_entry} WHERE `data_id` = %d",
                $data_id,
            ),
            ARRAY_A,
        );

        if (empty($fields)) {
            return [];
        }

        // Parse the fields into a structured array
        $entry = [
            "data_id" => $data_id,
            "cf7_id" => "",
            "submit_time" => "",
            "fields" => [],
        ];

        foreach ($fields as $field) {
            if ($field["name"] === "submit_time") {
                $entry["submit_time"] = $field["value"];
            }

            if (empty($entry["cf7_id"])) {
                $entry["cf7_id"] = $field["cf7_id"];
            }

            // Store all fields
            $entry["fields"][$field["name"]] = $field["value"];
        }

        return $entry;
    }
    public function get_all_cf7_forms()
    {
        global $wpdb;

        return $wpdb->get_results("
            SELECT DISTINCT cf7_id, form_name
            FROM {$wpdb->prefix}advanced_cf7_db
            ORDER BY form_name ASC
        ");
    }
    /**
     * Get entries by form ID
     *
     * @param int $form_id The Contact Form 7 form ID
     * @param int $limit Number of entries to retrieve (default: all)
     * @param int $offset Offset for pagination (default: 0)
     * @return array Array of form entries
     */
    public function get_cf7_entries_by_form($form_id, $limit = 0, $offset = 0)
    {
        global $wpdb;

        $table_entry = $wpdb->prefix . "cf7_vdata_entry";

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_entry'") != $table_entry) {
            return [];
        }

        // Build query with optional limit
        $query = $wpdb->prepare(
            "SELECT `data_id`, `cf7_id` FROM {$table_entry} WHERE `cf7_id` = %d GROUP BY `data_id` ORDER BY `data_id` DESC",
            $form_id,
        );

        if ($limit > 0) {
            $query .= $wpdb->prepare(" LIMIT %d, %d", $offset, $limit);
        }

        $entries = $wpdb->get_results($query, ARRAY_A);

        if (empty($entries)) {
            return [];
        }

        // Get detailed data for each entry
        $detailed_entries = [];
        foreach ($entries as $entry) {
            $data_id = (int) $entry["data_id"];
            $entry_data = $this->get_entry_by_data_id($data_id);

            if (!empty($entry_data)) {
                $detailed_entries[] = $entry_data;
            }
        }

        return $detailed_entries;
    }

    /**
     * Get total count of entries
     *
     * @param int $form_id Optional form ID to filter by
     * @return int Total count
     */
    public function get_entries_count($form_id = 0)
    {
        global $wpdb;

        $table_entry = $wpdb->prefix . "cf7_vdata_entry";

        if ($form_id > 0) {
            $count = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(DISTINCT `data_id`) FROM {$table_entry} WHERE `cf7_id` = %d",
                    $form_id,
                ),
            );
        } else {
            $count = $wpdb->get_var(
                "SELECT COUNT(DISTINCT `data_id`) FROM {$table_entry}",
            );
        }

        return (int) $count;
    }

    /**
     * Get form title by form ID
     *
     * @param int $form_id The form ID
     * @return string Form title
     */
    public function get_form_title($form_id)
    {
        $form = get_post($form_id);

        if ($form && $form->post_type === "wpcf7_contact_form") {
            return $form->post_title;
        }

        return "Unknown Form";
    }

    /**
     * Export entries to CSV
     *
     * @param int $form_id Optional form ID to filter by (0 = all forms)
     * @param string $filename Optional filename for the CSV
     * @return void Downloads CSV file
     */
    public function export_entries_to_csv($form_id = 0, $filename = "")
    {
        // ------------------------------------
        // 1. Get entries (all, or by form ID)
        // ------------------------------------
        $all_entries =
            $form_id > 0
                ? $this->get_cf7_entries_by_form($form_id)
                : $this->get_cf7_entries();

        // ------------------------------------
        // 2. Filter ONLY entries that match:
        //    profit == "Da"
        //    cifrafaceri == "Peste 3.000.000 RON"
        // ------------------------------------
        $entries = array_filter($all_entries, function ($entry) {
            return isset(
                $entry["fields"]["profit"],
                $entry["fields"]["cifrafaceri"],
            ) &&
                $entry["fields"]["profit"] === "Da" &&
                $entry["fields"]["cifrafaceri"] === "Peste 3.000.000 RON";
        });

        if (empty($entries)) {
            wp_die("No matching entries found to export.");
        }

        // ------------------------------------
        // 3. Build filename
        // ------------------------------------
        if (empty($filename)) {
            $date = date("Y-m-d_H-i-s");
            if ($form_id > 0) {
                $form_title = sanitize_file_name(
                    $this->get_form_title($form_id),
                );
                $filename = "cf7_entries_{$form_title}_{$date}.csv";
            } else {
                $filename = "cf7_entries_all_{$date}.csv";
            }
        }

        // ------------------------------------
        // 4. Collect CSV headers
        // ------------------------------------
        $all_fields = ["Entry ID", "Form Name", "Form ID", "Submit Time"];
        $field_keys = [];

        foreach ($entries as $entry) {
            foreach ($entry["fields"] as $key => $value) {
                // Skip CF7 internal/meta fields
                if (
                    in_array($key, [
                        "submit_time",
                        "_wpcf7",
                        "_wpcf7_version",
                        "_wpcf7_locale",
                        "_wpcf7_unit_tag",
                        "_wpcf7_container_post",
                    ])
                ) {
                    continue;
                }

                if (!in_array($key, $field_keys)) {
                    $field_keys[] = $key;
                    $all_fields[] = $key;
                }
            }
        }

        // ------------------------------------
        // 5. Send headers for CSV download
        // ------------------------------------
        header("Content-Type: text/csv; charset=utf-8");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header("Pragma: no-cache");
        header("Expires: 0");

        // ------------------------------------
        // 6. Output CSV with UTF-8 BOM
        // ------------------------------------
        $output = fopen("php://output", "w");
        fprintf($output, chr(0xef) . chr(0xbb) . chr(0xbf)); // UTF-8 BOM

        // Write CSV header row
        fputcsv($output, $all_fields);

        // ------------------------------------
        // 7. Write ONLY the filtered entries
        // ------------------------------------
        foreach ($entries as $entry) {
            $row = [
                $entry["data_id"],
                $this->get_form_title($entry["cf7_id"]),
                $entry["cf7_id"],
                $entry["submit_time"],
            ];

            // Add dynamic field values
            foreach ($field_keys as $field_key) {
                $row[] = $entry["fields"][$field_key] ?? "";
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit();
    }
}
