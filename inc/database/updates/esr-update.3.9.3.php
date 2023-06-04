<?php
if (version_compare(get_site_option('esr_db_version'), '3.9.3', '<')) {
    global $wpdb;

    $wpdb->query("ALTER TABLE {$wpdb->prefix}esr_log RENAME COLUMN system TO main_plugin;");
    $wpdb->query("ALTER TABLE {$wpdb->prefix}esr_log RENAME COLUMN insert_tme TO insert_time;");
}
