<?php
/**
* Seaport Museum.
 *
 * This file adds the required CSS to the front end to the Genesis Sample Theme.
 *
 * @package Seaport Museum
 * @author  William Mallick
 */

add_action( 'wp_enqueue_scripts', 'seaport_museum_css' );
/**
 * Checks the settings for the link color, and accent colors.
 * If any of these value are set the appropriate CSS is output.
 *
 * @since 2.2.3
 */
function seaport_museum_css() {
	
	$color_link   = get_theme_mod( 'seaport_museum_link_color', seaport_museum_customizer_get_default_link_color() );
	$color_accent = get_theme_mod( 'seaport_museum_accent_color', seaport_museum_customizer_get_default_accent_color() );
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
	
	//Set the Link Color
	$css .= sprintf(
		'

		a, h1, aside .menu-item.current-page,
		.entry-title a:focus,
		.entry-title a:hover,
		.sub-menu-toggle:focus,
		.sub-menu-toggle:hover {
			color: %1s;
		}
		',
		$color_link
		);

	//Primary Accent Color: Nav Color
	$css .= sprintf(
		'
		
		button,
		input[type="button"],
		input[type="reset"],
		input[type="submit"],
		.site-container div.wpforms-container-full .wpforms-form input[type="submit"],
		.site-container div.wpforms-container-full .wpforms-form button[type="submit"],
		.button, .fasc-button  {
			border-color: %1$s;
			color: %1$s;
			background-color: transparent
		}

		button:focus,
		button:hover,
		input[type="button"]:focus,
		input[type="button"]:hover,
		input[type="reset"]:focus,
		input[type="reset"]:hover,
		input[type="submit"]:focus,
		input[type="submit"]:hover,
		input[type="reset"]:focus,
		input[type="reset"]:hover,
		input[type="submit"]:focus,
		input[type="submit"]:hover,
		.site-container div.wpforms-container-full .wpforms-form input[type="submit"]:focus,
		.site-container div.wpforms-container-full .wpforms-form input[type="submit"]:hover,
		.site-container div.wpforms-container-full .wpforms-form button[type="submit"]:focus,
		.site-container div.wpforms-container-full .wpforms-form button[type="submit"]:hover,
		.button:focus,
		.button:hover,
		.fasc-button:focus,
		.fasc-button:hover {
			background-color: %1$s;
			color: %2$s;
			text-decoration: none;
		}
		
		/* action buttons default for mobile, which has red header */
		#action-button-a, #action-button-b, #action-button-c {
            text-transform: uppercase;
        }
        
        #action-button-a, #action-button-b, #action-button-c {
            border-color: #FFF;
            color: #FFF;
        }
        
        #action-button-a:hover, #action-button-b:hover, #action-button-c:hover {
            color: %1$s;
            background-color: #FFF;
            border-color: #FFF;
        }
        
        /*
        #action-button-a:hover {
            color: #FFF;
            background-color: %1$s;
            border-color: #FFF;
        }*/
        /* end of action buttons default */
        
        		
		.nav-actions.nav-top button i {
			color: %1$s;
		}
		
		.nav-actions.nav-scrolled button i {
			color: #FFF;
		}
		
		.site-header.nav-scrolled, .site-header  {
			background-color: %1$s;
			color: #FFF;
		}
		
		.custom-logo-link, .custom-logo-home-container, .sub-accordion-section-title_tagline {
			background-color: %1$s;
		}
		
		.custom-logo, .custom-logo-home {
			border-color: %1$s;
		}
		
		.nav-scrolled button.solid {
		    background-color: #FFF;
		    color: %1$s;
		    border-color: #FFF;
		}
	
		.nav-scrolled button {
		    background-color: transparent;
		    color: %1$s;
		    border-color: %1$s;
		}
		
		.nav-top button.solid {
		    background-color: %1$s;
		    color: #fff;
		}
		
		.footer-widget-area h4, .widget-title {
			color: %1$s;
			margin: 0;
		}
		
		.accent-text, .footer-widgets h2, .entry-title  {
		    color: %1$s;
		}
		
		hr {
			border-color: %1$s;
		}
		
		.genesis-nav-menu .menu-item a {
			 color: #FFFFFF;
		}
		
		.genesis-nav-menu .sub-menu a {
			color: %1$s;
			background-color: #FFF;
		}
				
		.nav-top .genesis-nav-menu a:focus,
		.nav-top .genesis-nav-menu a:hover,
		.nav-top .genesis-nav-menu .current-menu-item > a
		.nav-top .genesis-nav-menu .sub-menu .current-menu-item > a:focus,
		.nav-top .genesis-nav-menu .sub-menu .current-menu-item > a:hover {
			background-color: %1$s;
			color: #FFF;
		}
		
		
		.nav-scrolled .nav-primary ul.sub-menu,
		.nav-scrolled .genesis-nav-menu a,
		.nav-scrolled .genesis-nav-menu a:focus,
		.nav-scrolled .genesis-nav-menu a:hover,
		.nav-scrolled .genesis-nav-menu .current-menu-item > a,
		.nav-scrolled .genesis-nav-menu .sub-menu .current-menu-item > a:focus,
		.nav-scrolled .genesis-nav-menu .sub-menu .current-menu-item > a:hover,
		 aside .menu-item a:hover, aside .menu-item a:focus,
		 aside .menu-item span:hover, aside .menu-item span:focus {
			color: #FFF;
			background-color: %1$s;
		}
		
		.nav-scrolled .sub-menu  li,
		.nav-scrolled .genesis-nav-menu .sub-menu .current-menu-item > a:focus,
		.nav-scrolled .genesis-nav-menu .sub-menu .current-menu-item > a:hover {
		   background-color: %1$s;
		   color: #FFF;
		}
		
		.nav-top .genesis-nav-menu>li.current-menu-item {
			border-color: %1$s;
		}
		
		.nav-scrolled nav.nav-primary>li.current-menu-item  {
			border-color: #FFF;
		}
		
		.nav-primary .nav-home a:link, .nav-primary .nav-home a:visited {
			color: %1$s;
			background-color: #FFF;
		}
		
		.nav-scrolled .nav-primary .nav-home a:link, .nav-primary .nav-home a:visited {
			background-color: %1$s;
			color: #FFF;
		}
		
		#menu-primary > .menu-item, #menu-primary > .menu-item > a {
			background-color: %1$s;
			border-color %1$s;
		}
		
		.footer-widgets .widget_text .widget-title {
			color: %1$s;
		}
		
		hr.wp-block-separator {
		  border-color: %1$s;
		}
		
		.sidebar__inner.show-border > ul {
		   border-color: %1$s;
		}
		
		.block-post-grid--post-content a.button-normal:link, 
		.block-post-grid--post-content a.button-normal:visited  {
		    color: %1$s;
		    border-color: %1$s;
		    background-color: #FFF;
		}
		
		.block-post-grid--post-content a.button-normal:hover,
		.block-post-grid--post-content a.button-normal:active {
		    color: #FFF;
		    background-color: %1$s;
		}
		
		.block-post-grid--post-content a.button-invert:link, 
		.block-post-grid--post-content a.button-invert:visited  {
		    color: #FFF;
		    border-color: %1$s;
		    background-color: %1$s;
		}
		
		.block-post-grid--post-content a.button-invert:hover,
		.block-post-grid--post-content a.button-invert:active {
		    color: %1$s;
		    background-color: #FFF;
		}
		
		.bock-post-grid--hover-indicator {
		    background-color: %1$s;
		}
		
		@media only screen and (min-width: 960px) {

			.nav-top .genesis-nav-menu .menu-item a {
				color: #000000;
			}
			
			.nav-top .genesis-nav-menu .sub-menu a {
				color: #000000;
				background-color: #FFF;
			}
		  
			.sub-menu-toggle::before {
				color: %1$s;
			}
			
			/* responsive nav button */
			.nav-top button {
			    background-color: transparent;
			    color: %1$s !important;
			}
			
			.nav-scrolled .menu-toggle {
				background-color: transparent;
			    color: #fff;
			}
			/* end of responsive nav button */
			
			/* action buttons default */
			#action-button-b,
			#action-button-c {
				border-color: %1$s;
				color: %1$s;
			}
			
			#action-button-a,
			#action-button-b:hover,
			#action-button-c:hover {
				color: #FFF;
				background-color: %1$s;
				border-color: %1$s;
			}
			
			#action-button-a:hover {
			    color: %1$s;
				background-color: #FFF;
				border-color: %1$s;
			}
			/* end of action buttons default */
			
			/* action buttons nav scrolled */
            .nav-scrolled #action-button-b,
            .nav-scrolled #action-button-c {
                border-color: #FFF;
                color: #FFF;
            }
            
            .nav-scrolled #action-button-a,
            .nav-scrolled #action-button-b:hover,
            .nav-scrolled #action-button-c:hover {
                color: %1$s;
                background-color: #FFF;
                border-color: #FFF;
            }
            
            .nav-scrolled #action-button-a:hover {
                color: #FFF;
                background-color: %1$s;
                border-color: #FFF;
            }
            /* end of action buttons nav scrolled */
						
			#menu-primary > .menu-item, #menu-primary > .menu-item > a {
				background-color: #FFF;
				border-color: #FFF;
			}
			
			.nav-scrolled #menu-primary > .menu-item, .nav-scrolled #menu-primary > .menu-item > a {
				background-color: %1$s;
				border-color: %1$s;
			}
			
			.nav-top .genesis-nav-menu a:focus,
			.nav-top .genesis-nav-menu a:hover,
			.nav-top .genesis-nav-menu .current-menu-item > a
			.nav-top .genesis-nav-menu .sub-menu .current-menu-item > a:focus,
			.nav-top .genesis-nav-menu .sub-menu .current-menu-item > a:hover {
			    background-color: #FFF;
				color: %1$s;
			}
			
			.nav-top .nav-primary ul.sub-menu {
				background-color: #FFF;
			}
			
			.site-header.nav-scrolled, .site-header  {
				background-color: #FFF;
				color:  %1$s;
			}

			.genesis-nav-menu > .menu-highlight > a:hover,
			.genesis-nav-menu > .menu-highlight > a:focus,
			.genesis-nav-menu > .sub-menu,
			.genesis-nav-menu > .menu-highlight.current-menu-item > a {
				background-color: %1$s;
				color: #FFF;
			}
			
			 .genesis-nav-menu .sub-menu a:hover {
				color: %1$s;
			}
						
			.site-header.nav-scrolled {
				background-color: %1$s;
				color: #FFF;
			}
			
			.nav-scrolled .genesis-nav-menu > .menu-highlight > a:hover,
			.nav-scrolled .genesis-nav-menu > .menu-highlight > a:focus,
			.nav-scrolled .genesis-nav-menu > .sub-menu,
			.nav-scrolled .genesis-nav-menu > .menu-highlight.current-menu-item > a {
				color: %1$s;
				background-color: #FFF;
			}
			
			.nav-scrolled .genesis-nav-menu .sub-menu a {
				color: #FFF;
			}
			
			#menu-primary > .menu-item, #menu-primary > .menu-item > a {
				background-color: transparent;
				border: inherit;
			}
			
			.nav-scrolled #menu-primary > .menu-item  {
				background-color: transparent;
				border: inherit;
			}
			
			.nav-scrolled #menu-primary > .menu-item:hover, .nav-scrolled #menu-primary > .menu-item:hover > a {
				background-color: #FFF;
				color: %1$s;
			}
			
			.nav-scrolled .genesis-nav-menu .sub-menu a:hover {
				color: %1$s;
				background-color: #FFF;
			}
			
			#menu-primary > .menu-item:hover, #menu-primary > .menu-item:hover > a {
				color: %1$s;
			}
			
		}
		/* end of min width 960px */
		
		
		',
		$color_accent,
		'#FFF'
	);

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
			display: inline-block;
		}
		',
		$logo_effective_height
	) : '';

	// Place menu below logo and center logo once it gets big.
	$css .= ( has_custom_logo() && ( 600 <= $logo_effective_width ) ) ?
		'
		.wp-custom-logo .title-area,
		.wp-custom-logo .menu-toggle,
		.wp-custom-logo .nav-primary {
			float: none;
		}

		.wp-custom-logo .title-area {
			margin: 0 auto;
			text-align: center;
		}

		@media only screen and (min-width: 960px) {
			.wp-custom-logo .nav-primary {
				text-align: center;
			}

			.wp-custom-logo .nav-primary .sub-menu {
				text-align: left;
			}
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
			 padding-top: %1$spx;
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
		
		
		@media only screen and (min-width: 960px) {
			.custom-logo-home-container {
				padding: %1$spx;
			}
			
			img.custom-logo-home {
				height: %2$spx;
				width: %3$spx;
				display: inherit;
			}
			
			a.custom-logo-link-home {
				width: %3$spx;
				display: block;
			}
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
