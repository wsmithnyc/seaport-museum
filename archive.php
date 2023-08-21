<?php
/*
* Archive Events Template - archive-sssm-page.php
*/

require_once ('blocks/blocks_autoload.php');

use Blocks\Widget;

function seaport_museum_archive_output() {
    //This sets the custom post archive to display the fill content from each post.
	add_filter( 'genesis_pre_get_option_content_archive', 'seaport_museum_cp_content_display' );
	
	function seaport_museum_cp_content_display() {
		return 'excerpts';
	}

	//This returns the post thumbnail
	add_filter( 'genesis_pre_get_option_content_archive_thumbnail', 'seaport_museum_cp_image_display' );
	
	function seaport_museum_cp_image_display() {
		return 0;
	}

	//Set the image thumbnail size (options: thumbnail, medium, large, and featured-image if defined in your theme)
	add_filter( 'genesis_pre_get_option_image_size', 'seaport_museum_cp_image_size' );
	
	function seaport_museum_cp_image_size() {
		return 'featured-image';
	}

	//Changes the image alignment.
	add_filter( 'genesis_pre_get_option_image_alignment', 'seaport_museum_cp_image_alignment' );
	
	function seaport_museum_cp_image_alignment() {
		return 'aligncenter';
	}

	// Remove default post title (with link)
	remove_action( 'genesis_entry_header', 'custom_do_post_title' );

    // Remove the default header opening tags
    remove_action( 'genesis_archive_title_descriptions', 'genesis_do_archive_headings_open' );
	
	//* Reposition the secondary navigation menu
	remove_action( 'genesis_after_header', 'genesis_do_subnav' );

    //remove the default header closing tags
	remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
	
	// Add the new post title without a link
	add_action( 'genesis_entry_content', 'seaport_museum_cp_archive_content' );
	
	function seaport_museum_cp_archive_content() {
		global $post;
		
		$widget = Widget::getWidgetHtml($post, 'has-theme-primary-background-color');
		
		echo $widget;
	}
	
	remove_action('post_class', 'genesis_entry_post_class');
	
	add_action('post_class', 'seaport_museum_cp_post_class');
	
	function seaport_museum_cp_post_class() {
		return  ['archive-widget'];
	}
	
}

add_action('genesis_before_loop', 'seaport_museum_archive_output' );


genesis();
