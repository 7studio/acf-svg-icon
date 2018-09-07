<?php
/*
Plugin Name: Advanced Custom Fields: SVG Icon
Plugin URI: https://github.com/7studio/acf-svg-icon
Description: Add a new ACF field type: "SVG Icon" which allows you to select icon(s) from a SVG sprite.
Version: 1.0.1
Author: Xavier Zalawa
Author URI: http://www.7studio.fr
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: swp-acf-si
Domain Path: /lang
*/



// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die( 'Something went wrong.' );
}



define( 'SWP_ACF_SVG_ICON_VERSION', '1.0.1' );
define( 'SWP_ACF_SVG_ICON_FILE', __FILE__ );
define( 'SWP_ACF_SVG_ICON_URL', plugin_dir_url( SWP_ACF_SVG_ICON_FILE ) );
define( 'SWP_ACF_SVG_ICON_DIR', plugin_dir_path( SWP_ACF_SVG_ICON_FILE ) );



if ( ! class_exists( 'swp_acf_plugin_svg_icon' ) ) {
    class swp_acf_plugin_svg_icon {
        // vars
        var $settings;

        /**
         * __construct
         *
         * This function will setup the class functionality
         *
         * @type   function
         * @date   17/02/2016
         * @since  1.0.0
         *
         * @param  void
         * @return void
         */
        function __construct() {
            $this->settings = array(
                'version'   => SWP_ACF_SVG_ICON_VERSION,
                'url'       => SWP_ACF_SVG_ICON_URL,
                'path'      => SWP_ACF_SVG_ICON_DIR
            );

            // include field
            add_action( 'acf/include_field_types', array( $this, 'include_field' ) ); // v5
            add_action( 'acf/register_fields', array( $this, 'include_field' ) ); // v4
        }

        /**
         * include_field
         *
         * This function will include the field type class and load textdomain
         *
         * @type   function
         * @date   17/02/2016
         * @since  1.0.0
         *
         * @param  int  $version Major ACF version. Defaults to 4
         * @return void
         */
        function include_field( $version = 4 ) {
            // load textdomain
            load_plugin_textdomain( 'swp-acf-si', false, plugin_basename( SWP_ACF_SVG_ICON_DIR ) . '/lang' );

            include_once( "fields/class-swp-acf-svg-icon-v{$version}.php" );
        }
    }

    new swp_acf_plugin_svg_icon();
}
