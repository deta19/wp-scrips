<?php
/* ============================================
 * File: admin/views/admin-page.php
 * ============================================ */
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="ccf-admin-container">
        <div class="card">
            <h2>Contact Form 7 Entries</h2>

            <form method="get" action="">
                <input type="hidden" name="page" value="<?php echo esc_attr(
                    $_GET["page"],
                ); ?>">

                <label for="cf7_form"><strong>Select Form:</strong></label>
                <select name="cf7_form" id="cf7_form" onchange="this.form.submit()">
                    <option value="">-- Select Form --</option>
                    <?php foreach ($forms as $form): ?>
                        <option value="<?php echo $form->id(); ?>"
                            <?php selected($selected_form_id, $form->id()); ?>>
                            <?php echo esc_html(
                                $form->title(),
                            ); ?> (ID: <?php echo $form->id(); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <div class="ccf-buttons">
                <a href="<?php echo admin_url(
                    "admin-post.php?action=export_cf7_entries&form_id=" .
                        intval($selected_form_id),
                ); ?>"
                    class="button button-primary">
                    Export CSV
                </a>

            </div>
            <?php if (empty($entries)): ?>
                <p>No entries found. Make sure Advanced CF7 DB plugin is installed and you have form submissions.</p>
            <?php else: ?>
                <p>Total Entries: <strong><?php echo count(
                    $entries,
                ); ?></strong></p>

                <div class="ccf-entries-table">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Entry ID</th>
                                <th>Form Name</th>
                                <th>Submit Time</th>
                                <th>Form Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entries as $entry):
                                if (
                                    !empty($entry["fields"]) &&
                                    is_array($entry["fields"])
                                ) {
                                    if (
                                        $entry["fields"]["profit"] == "Da" &&
                                        $entry["fields"]["cifrafaceri"] ==
                                            "Peste 3.000.000 RON"
                                    ) { ?>
                                <tr>
                                    <td><?php echo esc_html(
                                        $entry["data_id"],
                                    ); ?></td>
                                    <td><?php echo esc_html(
                                        $helper->get_form_title(
                                            $entry["cf7_id"],
                                        ),
                                    ); ?> (ID: <?php echo esc_html(
     $entry["cf7_id"],
 ); ?>)</td>
                                    <td><?php echo esc_html(
                                        $entry["submit_time"],
                                    ); ?></td>
                                    <td>
                                        <?php if (
                                            !empty($entry["fields"]) &&
                                            is_array($entry["fields"])
                                        ) {
                                            echo '<ul class="ccf-form-data">';
                                            foreach (
                                                $entry["fields"]
                                                as $key => $value
                                            ) {
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

                                                if (
                                                    filter_var(
                                                        $value,
                                                        FILTER_VALIDATE_URL,
                                                    )
                                                ) {
                                                    echo "<li><strong>" .
                                                        esc_html($key) .
                                                        ':</strong> <a href="' .
                                                        esc_url($value) .
                                                        '" target="_blank">' .
                                                        esc_html(
                                                            basename($value),
                                                        ) .
                                                        "</a></li>";
                                                } else {
                                                    $display_value =
                                                        strlen($value) > 50
                                                            ? substr(
                                                                    $value,
                                                                    0,
                                                                    50,
                                                                ) . "..."
                                                            : $value;
                                                    echo "<li><strong>" .
                                                        esc_html($key) .
                                                        ":</strong> " .
                                                        esc_html(
                                                            $display_value,
                                                        ) .
                                                        "</li>";
                                                }
                                            }
                                            echo "</ul>";
                                        } else {
                                            echo '<span class="no-data">No data available</span>';
                                        } ?>
                                    </td>
                                </tr>
                            <?php }
                                }
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
.ccf-admin-container {
    margin-top: 20px;
}

.ccf-admin-container .card {
    max-width: 100%;
    padding: 20px;
}

.ccf-entries-table {
    margin-top: 20px;
    overflow-x: auto;
}

.ccf-form-data {
    margin: 0;
    padding: 0;
    list-style: none;
    font-size: 13px;
}

.ccf-form-data li {
    padding: 3px 0;
    border-bottom: 1px solid #f0f0f0;
}

.ccf-form-data li:last-child {
    border-bottom: none;
}

.no-data {
    color: #999;
    font-style: italic;
}

.wp-list-table td {
    vertical-align: top;
}
</style>
