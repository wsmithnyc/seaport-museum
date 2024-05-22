<?php
/**
 * Adds front-end inline styles for the custom Gutenberg color palette.
 *
 * @package Seaport Museum
 * @author  William Mallick
 */

add_action( 'wp_enqueue_scripts', 'seaport_museum_custom_gutenberg_css' );
/**
 * Outputs front-end inline styles based on colors declared in config/block-editor-settings.php.
 *
 * @since 2.9.0
 */
function seaport_museum_custom_gutenberg_css() {

	$block_editor_settings = genesis_get_config( 'block-editor-settings' );

	$css = <<<CSS
.ab-block-post-grid .ab-post-grid-items h2 a:hover {
	color: {$block_editor_settings['default-link-color']};
}

.site-container .wp-block-button .wp-block-button__link {
	background-color: {$block_editor_settings['default-link-color']};
	border: 2px solid {$block_editor_settings['default-button-color']};
}

.wp-block-button .wp-block-button__link:not(.has-background)
.wp-block-button.is-style-squared .wp-block-button__link:not(.has-background),
.event-button .wp-block-button__link:not(.has-background) {
    background-color: {$block_editor_settings['default-link-color']};
	color: #FFF;
}

.event-button.wp-block-button__link:not(.has-background):focus,
.event-button.wp-block-button__link:not(.has-background):hover,
.wp-block-button .wp-block-button__link:not(.has-background):focus,
.wp-block-button .wp-block-button__link:not(.has-background):hover,
.wp-block-button.is-style-squared .wp-block-button__link:not(.has-background):focus,
.wp-block-button.is-style-squared .wp-block-button__link:not(.has-background):hover{
    background-color: #FFF;
	color: {$block_editor_settings['default-button-color']};
}

.site-container .wp-block-button.is-style-outline .wp-block-button__link {
	color: {$block_editor_settings['default-button-color']};
	border: 2px solid {$block_editor_settings['default-button-color']};
}

.site-container .wp-block-button.is-style-outline .wp-block-button__link:focus,
.site-container .wp-block-button.is-style-outline .wp-block-button__link:hover {
	color: {$block_editor_settings['default-button-outline-hover']};
}

.site-container .wp-block-button .wp-block-button__link:focus,
.site-container .wp-block-button .wp-block-button__link:hover,
.site-container .wp-block-button.is-style-squared .wp-block-button__link:focus,
.site-container .wp-block-button.is-style-squared .wp-block-button__link:hover {
	color: {$block_editor_settings['default-button-color']};
	background-color: #FFF;
	border: 2px solid {$block_editor_settings['default-button-color']};
}

.site-container .wp-block-button.is-style-outline .wp-block-button__link:focus,
.site-container .wp-block-button.is-style-outline .wp-block-button__link:hover {
	background-color: {$block_editor_settings['default-button-color']};
	color: #FFF;
}

.wp-block-media-text .wp-block-media-text__content {
	padding: 0;
}

@media only screen and (min-width: 480px) {
	.wp-block-media-text .wp-block-media-text__content {
		padding: 0 8%;
	}
}

CSS;

	$css .= seaport_museum_inline_font_sizes();
	$css .= seaport_museum_inline_color_palette();

	wp_add_inline_style( CHILD_THEME_HANDLE . '-gutenberg', $css );

}

add_action( 'enqueue_block_editor_assets', 'seaport_museum_custom_gutenberg_admin_css' );
/**
 * Outputs back-end inline styles based on colors declared in config/block-editor-settings.php.
 *
 * Note this will appear before the style-editor.css injected by JavaScript,
 * so overrides will need to have higher specificity.
 *
 * @since 2.9.0
 */
function seaport_museum_custom_gutenberg_admin_css() {

	$block_editor_settings = genesis_get_config( 'block-editor-settings' );

	$css = <<<CSS
.ab-block-post-grid .ab-post-grid-items h2 a:hover,
.block-editor__container .editor-block-list__block a {
	color: {$block_editor_settings['default-link-color']};
}

.editor-styles-wrapper .editor-rich-text .button,
.editor-styles-wrapper .wp-block-button .wp-block-button__link:not(.has-background),
.editor-styles-wrapper .wp-block-button.is-style-squared .wp-block-button__link:not(.has-background) {
	background-color: {$block_editor_settings['default-button-color']};
	color: #FFF;
	border: 2px solid {$block_editor_settings['default-button-color']};
}

.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link {
	background-color: #FFF;
	color: {$block_editor_settings['default-button-color']};
	border-radius: 3px;
}

.editor-styles-wrapper .wp-block-button .wp-block-button__link{
	background-color: {$block_editor_settings['default-link-color']};
	border: 2px solid {$block_editor_settings['default-button-color']};
}

.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link:focus,
.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link:hover {
	color: {$block_editor_settings['default-button-bg']};
	background-color: {$block_editor_settings['default-button-color']};
}

.editor-styles-wrapper .wp-block-button .wp-block-button__link:not(.has-background)
.editor-styles-wrapper .wp-block-button.is-style-squared .wp-block-button__link:not(.has-background) {
    background-color: {$block_editor_settings['default-button-color']};
	color: #FFF;
}

.editor-styles-wrapper .wp-block-button .wp-block-button__link:focus,
.editor-styles-wrapper .wp-block-button .wp-block-button__link:hover,
.editor-styles-wrapper .wp-block-button.is-style-squared .wp-block-button__link:focus,
.editor-styles-wrapper .wp-block-button.is-style-squared .wp-block-button__link:hover {
	color: {$block_editor_settings['default-button-color']};
	background-color: #FFF;
	border: 2px solid {$block_editor_settings['default-button-color']};
}

.editor-styles-wrapper .has-theme-primary-background-color {
		  background-color: {$block_editor_settings['default-button-color']};
		}

.editor-styles-wrapper .block-post-grid--content-subsection .block-post-grid--post,
.editor-styles-wrapper .block-editor-block-list__block .block-post-grid--post {
  max-height: 400px;
  overflow: hidden;
}

.editor-styles-wrapper .block-post-grid--sidebar h2, .editor-styles-wrapper .block-post-grid--post-content h2 {
  text-decoration: none;
  background-color: transparent;
  color: {$block_editor_settings['default-link-color']};
}

.editor-styles-wrapper  .block-post-grid--post-thumbnail {
  height: 150px;
}

.editor-styles-wrapper .block-post-grid--content-subsection .block-post-grid--post-thumbnail {
    height: 106px;
}

.editor-styles-wrapper .block-post-grid--sidebar h2, .editor-styles-wrapper .block-post-grid--post-content h2 {
  font-size: 24px;
  margin: 0;
}

.editor-styles-wrapper .block-post-grid--content-subsection .editor-styles-wrapper .block-post-grid--post-content h2 {
  font-size: 18px;
  margin: 0;
}

.bock-post-grid--hover-indicator {
    background-color: {$block_editor_settings['default-button-color']};
}

.editor-styles-wrapper .block-post-grid--post-content a.event-button:link, .editor-styles-wrapper .block-post-grid--post-content a.event-button:visited {
  text-decoration: none;
  cursor: pointer;
  height: 2.9em;
  line-height: 2.7em;
  margin: 0;
  padding: 0 15px;
  border: 2px solid {$block_editor_settings['default-button-color']};
  color: {$block_editor_settings['default-button-color']};
}

.editor-styles-wrapper .block-post-grid--content-subsection .block-post-grid--post-content a.event-button:link {
  font-weight: 600;
  font-size: 13px;
  min-width: 100px;
  overflow: hidden;
  width: 44%;
  height: 2.9em;
  line-height: 2.7em;
  margin: 0;
  padding: 0 15px;
  border-radius: 3px;
  max-width: 180px;
}

.editor-styles-wrapper .block-post-grid--content-subsection .block-post-grid--post-content a.event-button:link {
  min-width: unset;
  font-size: 11px;
}

CSS;

	wp_add_inline_style( 'seaport-museum-gutenberg-fonts', $css );

}

/**
 * Generate CSS for editor font sizes from the provided theme support.
 *
 * @since 2.9.0
 *
 * @return string The CSS for editor font sizes if theme support was declared.
 */
function seaport_museum_inline_font_sizes() {

	$css               = '';
	$editor_font_sizes = get_theme_support( 'editor-font-sizes' );

	if ( ! $editor_font_sizes ) {
		return '';
	}

	foreach ( $editor_font_sizes[0] as $font_size ) {
		$css .= <<<CSS
		
		.site-container .has-{$font_size['slug']}-font-size {
			font-size: {$font_size['size']}px;
		}
CSS;
	}

	return $css;

}

/**
 * Generate CSS for editor colors based on theme color palette support.
 *
 * @since 2.9.0
 *
 * @return string The editor colors CSS if `editor-color-palette` theme support was declared.
 */
function seaport_museum_inline_color_palette() {

	$css                   = '';
	$block_editor_settings = genesis_get_config( 'block-editor-settings' );
	$editor_color_palette  = $block_editor_settings['editor-color-palette'];

	foreach ( $editor_color_palette as $color_info ) {
		$css .= <<<CSS
		.site-container .has-{$color_info['slug']}-color,
		.site-container .wp-block-button .wp-block-button__link.has-{$color_info['slug']}-color,
		.site-container .wp-block-button.is-style-outline .wp-block-button__link.has-{$color_info['slug']}-color {
			color: {$color_info['color']};
		}

		.site-container .has-{$color_info['slug']}-background-color,
		.site-container .wp-block-button .wp-block-button__link.has-{$color_info['slug']}-background-color,
		.site-container .wp-block-pullquote.is-style-solid-color.has-{$color_info['slug']}-background-color {
			background-color: {$color_info['color']};
		}

		.site-container .wp-block-button.is-style-outline .wp-block-button__link.has-{$color_info['slug']}-color {
			color: {$color_info['color']};
			border-color: {$color_info['color']};
		}
		
		.site-container .wp-block-button .wp-block-button__link.has-{$color_info['slug']}-background-color {
			background-color: {$color_info['color']};
			border: 2px solid {$color_info['color']};
		}
		
		.site-container .wp-block-button .wp-block-button__link.has-{$color_info['slug']}-background-color:hover,
		.site-container .wp-block-button .wp-block-button__link.has-{$color_info['slug']}-background-color:focus {
			color: {$color_info['color']};
			border: 2px solid {$color_info['color']};
			background-color: #FFF;
		}
		
		.event-button.wp-block-button__link.has-{$color_info['slug']}-background-color:hover,
		.event-button.wp-block-button__link.has-{$color_info['slug']}-background-color:focus {
			color: {$color_info['color']};
			border: 2px solid #FFF;
			background-color: #FFF;
		
		}

		.site-container .wp-block-button.is-style-outline .wp-block-button__link.has-{$color_info['slug']}-color:hover,
		 .site-container .wp-block-button.is-style-outline .wp-block-button__link.has-{$color_info['slug']}-color:focus{
			background-color: {$color_info['color']};
			color: #FFF;
			border-color: {$color_info['color']};
		}

		.editor-styles-wrapper .wp-block-button .wp-block-button__link.has-{$color_info['slug']}-background-color:hover,
		.site-container .wp-block-button .wp-block-button__link.has-{$color_info['slug']}-background-color:focus {
			background-color: {$color_info['color']};
			border: 2px solid {$color_info['color']};
			color: #FFF;
		}


		.block-post-grid--post-content.has-{$color_info['slug']}-background-color {
			background-color: {$color_info['color']};
		}

		.block-post-grid--post-content.has-{$color_info['slug']}-color {
			color: {$color_info['color']};
		}
CSS;
	}

	return $css;

}
