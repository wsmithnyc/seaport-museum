<?php
/**
* Seaport Museum.
 *
 * This file adds the required CSS to the front end to the Genesis Sample Theme.
 *
 * @package Seaport Museum
 * @author  William Mallick
 */

//add_action( 'wp_enqueue_scripts', 'seaport_museum_css' );
/**
 * Checks the settings for the link color, and accent colors.
 * If any of these value are set the appropriate CSS is output.
 *
 * @since 2.2.3
 */
function seaport_museum_css() {

    return '';
	
	$logo         = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
	$logo_home    = get_theme_mod( 'seaport_museum_logo_home' );
	
	if ( $logo ) {
		$logo_effective_height       = get_theme_mod( 'seaport_museum_logo_height', 168 );
		$logo_effective_width       = get_theme_mod( 'seaport_museum_logo_width', 106 );
		
		$logo_padding          = max( 0, ceil(( 125 - $logo_effective_height ) / 2) );
	}
	
	/* logo_home is the alternate logo when the menu is at it's "home" position (page has not yet scrolled) */
	if ( $logo_home ) {
		$logo_home_effective_height = get_theme_mod( 'seaport_museum_logo_home_height', 155 );
		$logo_home_effective_width = get_theme_mod( 'seaport_museum_logo_home_width', 165 );
		$logo_home_padding          = get_theme_mod( 'seaport_museum_logo_home_padding', 50 );
	}
	
	$css = '';


	/**  header logo **/
	$css .= ( has_custom_logo() && ( 200 <= $logo_effective_height ) ) ?
		'
		.site-header {
			/* position: static; */
		}
		'
	: '';

	$css .= ( has_custom_logo() && ( 350 !== $logo_effective_height ) ) ? sprintf(
		'
		.wp-custom-logo .site-container .title-area {
			/*display: inline-block;*/
		}
		',
		$logo_effective_height
	) : '';

	// Place menu below logo and center logo once it gets big.
	$css .= ( has_custom_logo() && ( 600 <= $logo_effective_width ) ) ?
		'
		

		@media only screen and (min-width: 960px) {

		}
		'
	: '';

	$css .= ( has_custom_logo() ) ? "
	.site-title, .site-description { display: none; }
	.title-area { position: absolute; }
	" : "";
	
	$css .= "\n@media only screen and (min-width: 1600px) { \n";
	
	$css .= ( has_custom_logo() && $logo_padding && ( $logo_effective_height > 1 ) ) ? sprintf(
		'
		.wp-custom-logo .title-area {
			 /*padding-top: %1$spx;*/
		}
		
		.custom-logo {
			display: none;
			height: %2$spx;
			width: %3$spx;
		}
		
		.nav-scrolled .custom-logo {
			display: inherit;
		}
		
		',
		$logo_padding,
		$logo_effective_height,
		$logo_effective_width
	) : '';
	
	
	//"home page logo" - not scrolled
	$css .= ( $logo_home ) ? sprintf(
		'
		/* close of media query 1610px */
		}
		

		
		
		',
		$logo_home_padding,
		$logo_home_effective_height,
		$logo_home_effective_width

		) : '';
	

	if ( $css ) {
		wp_add_inline_style( CHILD_THEME_HANDLE, $css );
	}

}
