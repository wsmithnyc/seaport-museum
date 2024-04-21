<?php

require_once (realpath(dirname(__FILE__) . '/..') . '/blocks/blocks_autoload.php');

//custom functions for theme.
//these are the functions that are called from functions.php
//separation of concerns: execution vs function definition
//WordPress calls functions.php to execute code
//this file contains the functions only
//so that "functions.php" only execute code and has no function definitions.
use Blocks\Constants;

/**
 * Adds Gutenberg opt-in features and styling.
 *
 * @since 2.7.0
 */
function genesis_child_gutenberg_support() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- using same in all child themes to allow action to be unhooked.
	require_once get_stylesheet_directory() . '/lib/gutenberg/init.php';
}

/**
 * Removes the link from the hidden site title if a custom logo is in use.
 *
 * Without this filter, the site title is hidden with CSS when a custom logo
 * is in use, but the link it contains is still accessible by keyboard.
 *
 * @param string $title The full title.
 * @param string $inside The content inside the title element.
 * @param string $wrap The wrapping element name, such as h1.
 *
 * @return string The site title with anchor removed if a custom logo is active.
 * @since 1.2.0
 *
 */
function seaport_museum_header_title( $title, $inside, $wrap ) {
	
	if ( has_custom_logo() ) {
		$inside = get_bloginfo( 'name' );
	}
	
	return sprintf( '<%1$s class="site-title">%2$s</%1$s>', $wrap, $inside );
}

/**
 * Enqueues scripts and styles.
 *
 * @since 1.0.0
 */
function seaport_museum_enqueue_scripts_styles() {
	
	/*
	wp_enqueue_script(
		'sticky-sidebar',
		get_stylesheet_directory_uri() . "/js/sticky-sidebar/sticky-sidebar.js",
		array( 'jquery' ),
		CHILD_THEME_VERSION,
		true
	);
	*/
	
	// load Libre Franklin Font
	wp_enqueue_style(
		'seaport-museum-fonts-libre-franklin',
		'//fonts.googleapis.com/css?family=Libre+Franklin:400,600,700',
		[],
		CHILD_THEME_VERSION
	);
	
	//load Knockout fonts
	wp_enqueue_style(
		'seaport-museum-fonts-knockout',
		"//cloud.typography.com/6567296/7069612/css/fonts.css",
		[],
		CHILD_THEME_VERSION
	);

	wp_enqueue_style( 'dashicons' );
	
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script(
		'seaport-museum',
		get_stylesheet_directory_uri() . "/js/seaport-museum.js",
		array( 'jquery' ),
		CHILD_THEME_VERSION,
		true
	);
	
	wp_enqueue_style(
		'widget-block-styles',
		get_stylesheet_directory_uri() . '/blocks/widget-block.css',
		[],
		CHILD_THEME_VERSION
	);
	
	$suffix = '';

	wp_enqueue_script(
		'seaport-museum-responsive-menu',
		get_stylesheet_directory_uri() . "/js/responsive-menus{$suffix}.js",
		array( 'jquery' ),
		CHILD_THEME_VERSION,
		true
	);

	wp_localize_script(
		'seaport-museum-responsive-menu',
		'genesis_responsive_menu',
		seaport_museum_responsive_menu_settings()
	);
}

/**
 * Defines responsive menu settings.
 *
 * @since 2.3.0
 */
function seaport_museum_responsive_menu_settings() {
	
	$settings = array(
		'mainMenu'         => __( '', 'seaport-museum' ),
		'menuIconClass'    => 'dashicons-before dashicons-menu',
		'subMenu'          => __( 'Submenu', 'seaport-museum' ),
		'subMenuIconClass' => 'dashicons-before dashicons-arrow-down-alt2',
		'menuClasses'      => array(
			'combine' => array(
				'.nav-primary',
			),
			'others'  => array(),
		),
	);
	
	return $settings;
}

/**
 * Removes output of unused admin settings metaboxes.
 *
 * @param string $_genesis_admin_settings The admin screen to remove meta boxes from.
 *
 * @since 2.6.0
 *
 */
function seaport_museum_remove_metaboxes( $_genesis_admin_settings ) {
	remove_meta_box( 'genesis-theme-settings-header', $_genesis_admin_settings, 'main' );
	remove_meta_box( 'genesis-theme-settings-nav', $_genesis_admin_settings, 'main' );
}

/**
 * Removes output of header and front page breadcrumb settings in the Customizer.
 *
 * @param array $config Original Customizer items.
 *
 * @return array Filtered Customizer items.
 * @since 2.6.0
 *
 */
function seaport_museum_remove_customizer_settings( $config ) {
	unset( $config['genesis']['sections']['genesis_header'] );
	unset( $config['genesis']['sections']['genesis_breadcrumbs']['controls']['breadcrumb_front_page'] );
	
	return $config;
}

/**
 * Reduces secondary navigation menu to one level depth.
 *
 * @param array $args Original menu options.
 *
 * @return array Menu options with depth set to 1.
 * @since 2.2.3
 *
 */
function seaport_museum_secondary_menu_args( $args ) {
	
	if ( 'secondary' !== $args['theme_location'] ) {
		return $args;
	}
	
	$args['depth'] = 1;
	
	return $args;
}

/**
 * Modifies size of the Gravatar in the author box.
 *
 * @param int $size Original icon size.
 *
 * @return int Modified icon size.
 * @since 2.2.3
 *
 */
function seaport_museum_author_box_gravatar( $size ) {
	return 90;
}

/**
 * Modifies size of the Gravatar in the entry comments.
 *
 * @param array $args Gravatar settings.
 *
 * @return array Gravatar settings with modified size.
 * @since 2.2.3
 *
 */
function seaport_museum_comments_gravatar( $args ) {
	$args['avatar_size'] = 60;
	
	return $args;
}

genesis_register_sidebar(
    [
        'id' => 'before-header',
        'name' => __('Top Banner', '$text_domain'),
        'description' => __('Top Banner is displayed above the header area.', '$text_domain'),
    ]
);

/**
 * outputs the string returned from seaport_museum_get_custom_logo()
 */
function seaport_museum_custom_logo() {
	echo seaport_museum_get_custom_logo();
}

/**
 * Returns a custom logo, linked to home.
 *
 * @param int|null $blog_id Optional. ID of the blog in question. Default is the ID of the current blog.
 *
 * @return string Custom logo markup.
 * @since 4.5.0
 */
function seaport_museum_get_custom_logo( ?int $blog_id = 0 ): string
{

	$switched_blog = false;
	
	if ( is_multisite() && ! empty( $blog_id ) && (int) $blog_id !== get_current_blog_id() ) {
		switch_to_blog( $blog_id );
		$switched_blog = true;
	}
	
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	
	$custom_logo_home_url = get_theme_mod( 'seaport_museum_logo_home' );
	
	$custom_logo_home_subtitle = get_theme_mod( 'seaport_museum_logo_home_text' );
	
	// We have a logo. Logo is go.
	if ( $custom_logo_id ) {
		$custom_logo_attr = array(
			'class' => 'custom-logo',
		);
		
		/*
		 * If the logo alt attribute is empty, get the site title and explicitly
		 * pass it to the attributes used by wp_get_attachment_image().
		 */
		$image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
		
		if ( empty( $image_alt ) ) {
			$custom_logo_attr['alt'] = get_bloginfo( 'name', 'display' );
		}
		
		/*
		 * If the alt attribute is not empty, there's no need to explicitly pass
		 * it because wp_get_attachment_image() already adds the alt attribute.
		 */
		$html = sprintf(
			'<div class="custom-logo-container"><a href="%1$s" class="custom-logo-link" rel="home">%2$s</a></div>',
			esc_url( home_url( '/' ) ),
			wp_get_attachment_image( $custom_logo_id, 'full', false, $custom_logo_attr )
		);
		
		//logo Home Position
		$custom_logo_home_subtitle = ( ! empty( $custom_logo_home_subtitle ) ) ? "<svg id='logo-home-subtitle' viewBox='-3 0 164 18'><text x='0' y='15'>$custom_logo_home_subtitle</text></svg>" : "";
		
		$html .= sprintf(
			'<div class="custom-logo-home-container"><a href="%1$s" class="custom-logo-link-home" rel="home"><img alt="Seaport Museum Logo" class="custom-logo-home" src="%2$s">%3$s</a></div>',
			esc_url( home_url( '/' ) ),
			$custom_logo_home_url,
			$custom_logo_home_subtitle
		);
		
		return $html;
		
	} elseif ( is_customize_preview() ) {
		// If no logo is set, but we're in the Customizer, leave a placeholder (needed for the live preview).
        return sprintf(
            '<a href="%1$s" class="custom-logo-link" style="display:none;"><img class="custom-logo"/></a>',
            esc_url( home_url( '/' ) )
        );
	}
	
	if ( $switched_blog ) {
		restore_current_blog();
	}

    return '';
}

/**
 * outputs the string returned from seaport_museum_get_top_actions()
 */
function seaport_museum_top_actions(): void
{
	echo seaport_museum_get_top_actions();
}

function seaport_museum_mobile_search(): void
{
    echo build_search_form('search-container-mobile test-class');
}

/**
 * Get the html for the top nav action buttons
 *
 * @return string
 */
function seaport_museum_get_top_actions(): string
{
	//start with the logo and search form
    $logo = seaport_museum_get_custom_logo();
	$search = build_search_form();

	//load the buttons from the settings
	$button_a = load_button_settings('a');
	$button_b = load_button_settings('b');
	$button_c = load_button_settings('c');

	//put the buttons in an ordered array
	$orderedButtons[ $button_a['position'] ] = $button_a;

	//load button b at a unique position
	$position = get_next_available_position_ordinal($orderedButtons, $button_b['position']);
	$orderedButtons[ $position ] = $button_b;

	//load button c at a unique position
	$position = get_next_available_position_ordinal($orderedButtons, $button_c['position']);
	$orderedButtons[ $position ] = $button_c;

	ksort($orderedButtons);

    //build the html output
    $html = "<div class='actions-grid-item actions-logo-container'>$logo</div>";
    $html .= "<div class='actions-grid-item actions-search-container'>$search</div>";

	//build the html for the buttons
	foreach ($orderedButtons as $button) {
		if ($button['show']) {
			$html .= "<div class='actions-grid-item actions-button-container'>{$button['html']}</div>";
		}
	}

    return "<div class='nav-actions'><div class='nav-actions-wrap'><div class='actions-container'>$html</div></div></div>";
}

/**
 * Gets an Action Button options from the settings.
 * Supply a, b or c only
 *
 * @param $selector
 *
 * @return array
 */
function load_button_settings($selector) {
	/* settings used by this function
	* action_button_X_title
	* action_button_X_url
	* action_button_X_position
	* show_action_button_X
	*/

	//map positions to ordinal
	$position = [ 'left' => 10, 'middle' => 20, 'right' => 30 ];
	$setting = get_theme_mod( "action_button_{$selector}_position" );

	$button = [
		'url' => get_theme_mod( "action_button_{$selector}_url" ),
		'title' => get_theme_mod( "action_button_{$selector}_title" ),
		'position' => $position[$setting] ?? 10,
		'show' => get_theme_mod( "show_action_button_{$selector}" ),
	];

	$button['html'] = "<a class='action-link button' id='action-button-{$selector}' target='_blank' href='{$button['url']}'>{$button['title']}</a> ";

	return $button;
}

function get_next_available_position_ordinal($orderedButtons, $position) {
	if (empty($orderedButtons[$position])) {
		return $position;
	}

	return get_next_available_position_ordinal($orderedButtons, $position + 1);
}

/**
 * build search form
 * The default class
 *
 * @param string|null $class
 *
 * @return string
 */
function build_search_form(?string $class = 'search-container'): string
{
	$searchSubmit = home_url( '/' );

	return '<div class="'. $class . '"><form role="search" method="get" id="searchform ' . $class . '" action="'. $searchSubmit .'">
    <div><label class="screen-reader-text" for="s">Search for:</label>
        <input class="search-field ' . $class . '" data-active="0" type="text" value="" name="s" id="s" />
        <button title="search" type="button" class="' . $class . '" id="search-submit-' . $class . '"><svg class="search-icon" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
		width="38" height="38"
		viewBox="0 0 92 92"
		style="vertical-align: middle;"><path d="M 31 11 C 19.973 11 11 19.973 11 31 C 11 42.027 19.973 51 31 51 C 34.974166 51 38.672385 49.821569 41.789062 47.814453 L 54.726562 60.751953 C 56.390563 62.415953 59.088953 62.415953 60.751953 60.751953 C 62.415953 59.087953 62.415953 56.390563 60.751953 54.726562 L 47.814453 41.789062 C 49.821569 38.672385 51 34.974166 51 31 C 51 19.973 42.027 11 31 11 z M 31 19 C 37.616 19 43 24.384 43 31 C 43 37.616 37.616 43 31 43 C 24.384 43 19 37.616 19 31 C 19 24.384 24.384 19 31 19 z"></path></svg></button>
    </div>
    </form></div>';
}

/**
 * Social Widget Footer Area
 */
function seaport_museum_extra_widgets() {
	genesis_register_sidebar( array(
		'id'            => 'seaport-after-content',
		'name'          => __( 'After Content', 'seaport_museum' ),
		'description'   => __( 'Widgets between content and Footer Widgets (Content Width)', 'seaport_museum' ),
		'before_widget' => '<div class="site-inner">',
		'after_widget'  => '</div>',
	) );
}

/**
 * Position the Social Widget Footer Area
 */
function seaport_museum_footer_widget() {
	genesis_widget_area( 'seaport-after-content',
		array(
			'before' => '<div class="site-inner"><div class="wrap">',
			'after'  => '</div></div>',
		) );
}

/**
 * Sets localization (do not remove).
 *
 * @since 1.0.0
 */
function seaport_museum_localization_setup() {
	load_child_theme_textdomain( 'seaport-museum', get_stylesheet_directory() . '/languages' );
}

/**
 * Outputs the javascript specified in theme settings javascript header
 */
function seaport_museum_header_javascript_cb() {
	$heder_js = get_theme_mod( 'seaport_museum_header_javascript' );
	
	echo "<script type='text/javascript' id='seaport_museum_header_javascript'>\n$heder_js\n</script>";
}

/**
 * Outputs the javascript specified in theme settings javascript header
 * This should be echoed before the <body> close
 */
function seaport_museum_footer_javascript_cb() {
	$heder_js = get_theme_mod( 'seaport_museum_footer_javascript' );
	
	echo "<script type='text/javascript' id='seaport_museum_header_javascript'>\n$heder_js\n</script>";
}

/**
 * Add custom colors to Gutenberg.
 */
function seaport_museum_gutenberg_colors() {
	// Retrieve the accent color frm mthe Customizer.
	$accent = get_theme_mod( 'accent_color', '#fff000' );
	// Build styles.
	$css = '';
	$css .= '.has-accent-color { color: ' . esc_attr( $accent ) . ' !important; }';
	$css .= '.has-accent-background-color { background-color: ' . esc_attr( $accent ) . '; }';
	
	return wp_strip_all_tags( $css );
}

/**
 * Enqueue theme styles within Gutenberg.
 */
function seaport_museum_gutenberg_styles() {
	// Load the theme styles within Gutenberg.
	/*
	 wp_enqueue_style( 'tabor-gutenberg',
		get_theme_file_uri( '/assets/css/gutenberg.css' ),
		false,
		'@@pkg.version',
		'all' );
	 */
	
	// Add custom colors to Gutenberg.
	wp_add_inline_style( 'tabor-gutenberg', seaport_museum_gutenberg_colors() );
}

/**
 * Adds the visual copyright notice.
 *
 * Supported shortcode attributes are:
 *   after (output after notice, default is empty string),
 *   before (output before notice, default is empty string),
 *   copyright (copyright notice, default is copyright character like (c) ),
 *   first(year copyright first applies, default is empty string).
 *
 * If the 'first' attribute is not empty, and not equal to the current year, then
 * output will be formatted as first-current year (e.g. 1998-2020).
 * Otherwise, output is just given as the current year.
 *
 * Output passes through `genesis_footer_copyright_shortcode` filter before returning.
 *
 * @since 1.1.0
 *
 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
 * @return string Output for `footer_copyright` shortcode.
 */
function seaport_museum_copyright_shortcode( $atts ) {
	
	$defaults = [
		'after'     => '',
		'before'    => '',
		'copyright' => '&#x000A9;',
		'first'     => '',
	];
	
	$atts     = shortcode_atts( $defaults, $atts, 'footer_copyright' );
	
	$output = $atts['before'] . $atts['copyright'] . '&nbsp;';
	
	if ( '' !== $atts['first'] && date( 'Y' ) !== $atts['first'] ) {
		$output .= $atts['first'];// . '&#x02013;'; //dash before the year
	}
	
	$output .= date( 'Y' ) . $atts['after'];
	
	return apply_filters( 'seaport_museum_copyright_shortcode', $output, $atts );
}

//Remove entry meta for the 'events' post type
function events_remove_entry_meta() {
	//remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
	//remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
	remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
	remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
	remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
	remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
}

function remove_titles_from_pages() {
	if ( is_page(array('home') ) ) {
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
	}
}

/**
 * Filter in URL for custom Superfish arguments.
 *
 * @link   http://code.garyjones.co.uk/change-superfish-arguments
 *
 * @param string $url Existing URL.
 *
 * @return string Amended URL.
 */
function prefix_superfish_args_url( $url ) {
	return get_stylesheet_directory_uri() . '/js/superfish-args.js';
}

/**
 * Loads the custom admin css for this theme
 */
function seaport_museum_admin_theme_style() {
	wp_enqueue_style(
		'widget-block-styles',
		get_stylesheet_directory_uri() . '/blocks/widget-block.css',
		[],
		CHILD_THEME_VERSION
	);
	
	wp_enqueue_style(
		'my-admin-theme',
		get_stylesheet_directory_uri() . '/wp-admin.css',
		[],
		CHILD_THEME_VERSION
     );
	
	//load Knockout fonts
	wp_enqueue_style(
		'seaport-museum-fonts-knockout',
		"//cloud.typography.com/6567296/7069612/css/fonts.css",
		[],
		CHILD_THEME_VERSION
	);
}

/**
 * Swap in a different sidebar instead of the default sidebar.
 *
 * @author Jennifer Baumann
 * @link http://dreamwhisperdesigns.com/?p=1034
 */
function categories_archive_logic() {
	global $post;
	
	if (!empty($post->post_type) && $post->post_type = 'post')
	{
		//return false;
	}
	
	if ( is_page_template( 'archive.php' ) || is_archive()  ) {
		remove_action( 'genesis_after_content', 'genesis_get_sidebar' );
		
		//remove the archive title
		remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );

		//add_action( 'genesis_before_loop', 'child_get_categories_sidebar', 8);
		
		//open the div wrapper: wraps the posts in a container that will be responsive sized
		add_action( 'genesis_before_loop', 'seaport_museum_archive_open', 8);
		
		//close the div wrapper
		add_action('genesis_after_loop', 'seaport_museum_archive_close', 20);
	}
}

/**
 * Retrieve blog sidebar
 */
function child_get_categories_sidebar() {
	//this includes file 'sidebar-categories'
	//get_sidebar( 'categories' );
}

/**
 *  Opens a Div tag around archive post widgets
 *
 * Be sure to call seaport_museum_archive_close()
 */
function seaport_museum_archive_open()
{
	echo seaport_museum_category_subnav();
	echo "\n<div class='block-post-grid--content-subsection two-thirds'>\n<div class='block-post-grid'>\n";
}

/**
 * Closes the Div tag around archive post widgets.
 *
 * Call after seaport_museum_archive_open()
 */
function seaport_museum_archive_close()
{
	echo "\n</div>\n</div>\n</div>\n";
}

/**
 * @param $object
 *
 * @return int
 */
function seaport_museum_get_root_category($object)
{
	if (empty($object)) return 0;
	
	if ($object->parent == 0) return $object->cat_ID;
	
	$object = get_category($object->parent);
	
	return seaport_museum_get_root_category($object);
}

/**
 * Builds the sidebar menu for category based archive pages
 *
 * @return string
 */
function seaport_museum_category_subnav() {
	global $wp;
	
	global $post;
	
	$show_sidebar = true;
	
	if (!empty($wp->request) && $wp->request = 'blog')
	{
		//$show_sidebar = false;
	}
	
	$object = get_queried_object();
	
	$root_id = seaport_museum_get_root_category($object);
	
	$current_url = home_url( add_query_arg( array(), $wp->request ) );
	
	$current_url = (substr($current_url, -1, 1) == "/") ? $current_url : $current_url . "/";
	
	$menu = wp_get_nav_menu_items('Primary');
	
	$html = '';
	
	$menu_title = $object->name ?? '';
	$menu_desc =  $object->description ?? '';
	
	$has_other_items = false;
	
	foreach($menu as $item) {
		if ($item->post_parent != $root_id && $item->object_id != $root_id) continue;
		
		$title = $item->title;
		
		if (strtolower($object->slug) == strtolower($item->title))
		{
		    $title = $object->name;
		}
		
		//'cat_ID' => 22
		if ($item->url == $current_url)
		{
			$html .= "<li class='menu-item-{$item->db_id} current-page menu-item menu-item-type-taxonomy menu-item-object-category '>{$title}</li>";
		}
		else
		{
			$has_other_items = true;
			
			$html .= "<li class='menu-item-{$item->db_id} menu-item menu-item-link menu-item-type-taxonomy menu-item-object-category'><span href='#' onclick='seaportMuseum.categoryNavigation(\"{$item->url}\")' itemprop='url'>{$title}</span></li>";
		}
	}
	
	$ul_class = ($has_other_items) ? 'show-border' : '';
	
	$html = ($show_sidebar)  ? "<aside id='sidebar' class='sidebar widget-area one-third first'><div id='sidebar-menu' class='sidebar__inner $ul_class'><ul>{$html}</ul></div><div>&nbsp;</div></aside>" : "";

	$class = ($show_sidebar) ? 'two-thirds archive-header' : 'two-thirds';
	
	$html =  "<div style='clear: both; overflow: hidden;'><div class='$class'><h1>$menu_title</h1><p>$menu_desc</p></div></div><div class='archive-wrapper'>" . $html;
	
	return $html;
}

function seaport_museum_category_header($menu_item) {
	return  category_description( $menu_item->object_id );
}

function seaport_museum_category_query($query) {
	global $wp_query;
	
	//override category query to always show all categories. Sub-categories use in-page filtering (like single page app)
	if( !is_admin() && $query->is_main_query() && !$query->is_feed() && !empty($query->is_category)) {
		
		$categories = explode('/', $query->query['category_name']);
		
		$query->tax_query->queries[0]['include_children'] = false;
		
		$query->query_vars['category_name'] = $categories[0];
	}
}

//filter to show only the inner content (no <html> <head> <body>, navigation, footers, etc)
//use to lazy load content into an existing page.
function seaport_museum_hide_body() {
	show_admin_bar(false);
	
	remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
	remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );
	remove_action( 'genesis_header', 'genesis_do_header' );
	
	//remove_action( 'genesis_header', 'seaport_museum_top_actions', 0 );
	
	remove_all_actions( 'genesis_before_header',  5 );
	
	remove_all_actions('wp_head');
	
	remove_all_actions('genesis_before_content');
	
	remove_all_actions('genesis_header');
	
	remove_all_actions('get_header');
	
	remove_theme_support('genesis-footer-widgets');
	
	remove_shortcode('footer_copyright');
	
	// Removes site footer elements.
	remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
	remove_action( 'genesis_footer', 'genesis_do_footer' );
	remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );
	
	remove_all_actions('wp_footer');

	add_action( 'get_header', 'categories_archive_logic' );
}

function add_widget_before_header()
{
    //antipattern: this link should be in a theme setting.
    $url = 'https://tickets.southstreetseaportmuseum.org/orders/548/calendar';

    if (is_active_sidebar('before-header')) {
        genesis_widget_area('before-header', [
            'before' => "<div class='before-header top-banner widget-area'><div class='top-banner-content'><a href='$url'>",
            'after' => '</a></div></div>',
        ]);
    }
}

/**
 * Add headline for archive headings to archive pages.
 *
 * @since 2.5.0
 *
 * @param string $heading    Optional. Archive heading, default is empty string.
 * @param string $intro_text Optional. Archive intro text, default is empty string.
 * @param string $context    Optional. Archive context, default is empty string.
 */
function custom_do_archive_headings_headline( $heading = '', $intro_text = '', $context = '' ) {
    if ( 'posts-page-description' === $context ) {
        $heading = 'Latest News'; // set your desired Posts page title here.
    }

    printf( '<h1 %s>%s</h1>', genesis_attr( 'archive-title' ), strip_tags( $heading ) );
}

/**
 * Custom function to replace the post's title with the value of custom field 'event-title'
 *
 * @return void
 */
function custom_do_post_title(): void
{
    if ( ! is_home() && genesis_entry_header_hidden_on_current_page() ) {
        return;
    }

    //check to see if we are hiding the custom text data
    $hide = get_post_meta( get_the_ID(), Constants::CUSTOM_FIELD_HIDE_EVENT_TEXT, true );
    $hide = hideCustomEventText($hide);

    //get the event title from custom fields
    $event_title = get_post_meta( get_the_ID(), Constants::CUSTOM_FIELD_EVENT_TITLE, true );
    $event_title = trim($event_title);

    if (!$hide && !empty($event_title)) {
        $title = $event_title;
    } else {
        $title = apply_filters('genesis_post_title_text', get_the_title());
    }

    if ( empty( trim( $title ) ) ) {
        return;
    }

    // Link it, if necessary.
    if ( ! is_singular() && apply_filters( 'genesis_link_post_title', true ) ) {
        $title = genesis_markup(
            [
                'open'    => '<a %s>',
                'close'   => '</a>',
                'content' => $title,
                'context' => 'entry-title-link',
                'echo'    => false,
            ]
        );
    }

    // Wrap in H1 on singular pages.
    $wrap = is_singular() ? 'h1' : 'h2';

    // Also, if HTML5 with semantic headings, wrap in H1.
    $wrap = genesis_get_seo_option( 'semantic_headings' ) ? 'h1' : $wrap;

    // Wrap in H2 on static homepages if Primary Title H1 is set to title or description.
    if (
        is_front_page()
        && ! is_home()
        && genesis_seo_active()
        && 'neither' !== genesis_get_seo_option( 'home_h1_on' )
    ) {
        $wrap = 'h2';
    }

    /**
     * Entry title wrapping element.
     *
     * The wrapping element for the entry title.
     *
     * @since 2.2.3
     *
     * @param string $wrap The wrapping element (h1, h2, p, etc.).
     */
    $wrap = apply_filters( 'genesis_entry_title_wrap', $wrap );

    // Build the output.
    $output = genesis_markup(
        [
            'open'    => "<{$wrap} %s>",
            'close'   => "</{$wrap}>",
            'content' => $title,
            'context' => 'entry-title',
            'params'  => [
                'wrap' => $wrap,
            ],
            'echo'    => false,
        ]
    );

    echo apply_filters( 'genesis_post_title_output', $output, $wrap, $title ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- title output is left unescaped to accommodate trusted user input. See https://codex.wordpress.org/Function_Reference/the_title#Security_considerations.
}


/**
 * Determine if we should hide the event custom text
 *
 * @param string|null $hideValue
 * @return bool
 */
function hideCustomEventText(?string $hideValue = null): bool
{
    if (empty($hideValue)) {
        return false;
    }

    if (str_contains(strtolower($hideValue), 'y')) {
        return true;
    }

    return false;
}


/**
 * Adds an Admin Menu item for Reusable Blocks
 *
 * @return void
 */
function reusable_blocks_link_wp_admin(): void
{
    add_menu_page( 'linked_url', 'Reusable Blocks', 'read', 'edit.php?post_type=wp_block', '', 'dashicons-editor-table', 22 );
}
