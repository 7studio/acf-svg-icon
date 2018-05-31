<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die( 'Something went wrong.' );
}

// Deletes plugin's transients.
global $wpdb;

$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE ('%_swp_acf_svg_icon_%')" );
