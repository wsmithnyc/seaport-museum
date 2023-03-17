<?php
/**
* Seaport Museum.
 *
 * This file adds functions to the Seaport Museum Genesis Child Theme.
 *
 * @package Seaport Museum
 * @author  William Mallick
 */

// Starts the engine.
use Blocks\Constants;

require_once get_template_directory() . '/lib/init.php';
require_once 'lib/seaport_functions.php';
require_once 'lib/shortcode_functions.php';

// Defines constants to help enqueue scripts and styles.
define( 'CHILD_THEME_HANDLE', sanitize_title_with_dashes( wp_get_theme()->get( 'Name' ) ) );
define( 'CHILD_THEME_VERSION', wp_get_theme()->get( 'Version' ) );

// Sets up the Theme.
require_once get_stylesheet_directory() . '/lib/theme-defaults.php';

add_action( 'after_setup_theme', 'seaport_museum_localization_setup' );

//custom javascript header injection
add_action( 'wp_head', 'seaport_museum_header_javascript_cb', 101 );

//customer javascript footer injections
add_action( 'wp_footer', 'seaport_museum_footer_javascript_cb', 1000 );

//remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
add_filter( 'genesis_superfish_args_url', 'prefix_superfish_args_url' );


// Adds helper functions.
require_once get_stylesheet_directory() . '/lib/helper-functions.php';

// Adds image upload and color select to Customizer.
require_once get_stylesheet_directory() . '/lib/customize.php';

// Includes Customizer CSS.
require_once get_stylesheet_directory() . '/lib/output.php';

// Adds WooCommerce support.
//require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php';

// Adds the required WooCommerce styles and Customizer CSS.
//require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php';

// Adds the Genesis Connect WooCommerce notice.
//require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php';

//add_theme_support( 'genesis-custom-header', array( 'width' => 1200, 'height' => 125 ) );

add_action( 'after_setup_theme', 'genesis_child_gutenberg_support' );


add_action( 'wp_enqueue_scripts', 'seaport_museum_enqueue_scripts_styles' );

// Adds support for HTML5 markup structure.
add_theme_support( 'html5', genesis_get_config( 'html5' ) );
add_theme_support('html5', array('search-form'));

// Adds support for accessibility.
add_theme_support( 'genesis-accessibility', genesis_get_config( 'accessibility' ) );

// Adds viewport meta tag for mobile browsers.
add_theme_support( 'genesis-responsive-viewport' );

// Adds custom logo in Customizer > Site Identity.
add_theme_support( 'custom-logo', genesis_get_config( 'custom-logo' ) );

add_filter( 'genesis_seo_title', 'seaport_museum_header_title', 10, 3 );

//override custom post title
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

add_action( 'genesis_entry_header', 'custom_do_post_title' );

// Renames primary and secondary navigation menus.
add_theme_support( 'genesis-menus', genesis_get_config( 'menus' ) );

// Adds image sizes.
add_image_size( 'sidebar-featured', 75, 75, true );
add_image_size( 'widget-thumb', 600, 400, false );

// Adds support for after entry widget.
add_theme_support( 'genesis-after-entry-widget-area' );

// Adds the ShortCode for event description, which is used in SSSM Posts
add_shortcode( Constants::SHORTCODE_EVENT_DESC, 'get_event_desc_shortcode' );

// Add the ShortCode for Book Now button. This will render a standard button with the book now url
// This will draw a button with the text "Book Now"
add_shortcode( Constants::SHORTCODE_BOOK_NOW, 'get_event_book_now_shortcode' );

// Add the ShortCode for Book General Admission button. This will render a standard button with the book now url
// This will draw a button with the text "Book General Admission"
add_shortcode( Constants::SHORTCODE_BOOK_GENERAL_ADMISSION, 'get_event_book_now_general_admission_shortcode' );

// Adds the ShortCode for Museum Hours
add_shortcode( 'museum-hours', 'get_museum_hours_shortcode' );

// Adds support for 3-column footer widgets.
add_theme_support( 'genesis-footer-widgets', 4 );

// Removes header right widget area.
unregister_sidebar( 'header-right' );

// Removes secondary sidebar.
unregister_sidebar( 'sidebar-alt' );

// Removes site layouts.
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );
genesis_unregister_layout( 'content-sidebar' );

// Removes output of primary navigation right extras.
remove_filter( 'genesis_nav_items', 'genesis_nav_right', 10, 2 );
remove_filter( 'wp_nav_menu_items', 'genesis_nav_right', 10, 2 );

add_action( 'genesis_theme_settings_metaboxes', 'seaport_museum_remove_metaboxes' );

add_filter( 'genesis_customizer_theme_settings_config', 'seaport_museum_remove_customizer_settings' );

//show banner before header
add_action( 'genesis_header', 'add_widget_before_header', 5 );

// Displays top action buttons
add_action( 'genesis_header', 'seaport_museum_top_actions', 11 ); //'genesis_header'

// Displays custom logo.
add_action( 'genesis_site_title', 'seaport_museum_custom_logo', 0 );

// Repositions primary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

// Repositions the secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
//add_action( 'genesis_footer', 'genesis_do_subnav', 10 );

add_filter( 'wp_nav_menu_args', 'seaport_museum_secondary_menu_args' );

add_filter( 'genesis_author_box_gravatar_size', 'seaport_museum_author_box_gravatar' );

add_filter( 'genesis_comment_list_args', 'seaport_museum_comments_gravatar' );


//remove_action( 'genesis_footer', 'genesis_do_footer' );

add_action('widgets_init', 'seaport_museum_extra_widgets');

add_action('genesis_after_content', 'seaport_museum_footer_widget');

//add
add_action( 'enqueue_block_editor_assets', 'seaport_museum_gutenberg_styles' );

remove_shortcode('footer_copyright');

add_shortcode( 'footer_copyright', 'seaport_museum_copyright_shortcode' );

add_action( 'init', 'events_remove_entry_meta', 12 );

add_action( 'get_header', 'remove_titles_from_pages' );

add_action('admin_enqueue_scripts', 'seaport_museum_admin_theme_style' );

add_action( 'get_header', 'categories_archive_logic' );

//adjust the query before it runs
add_action( 'parse_tax_query', 'seaport_museum_category_query');  // parse_tax_query   pre_get_posts

if (isset($_GET['body'])) {
	seaport_museum_hide_body();
}
