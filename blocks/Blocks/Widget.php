<?php

namespace Blocks;

class Widget
{
    /**
     * Renders the widget for a supplied post. Optionally provide a target date for ticket links
     *
     * If the post is not an 'event' type, then the ticket link will not be created.
     *
     * returns the html for the post's grid widget
     *
     * @param $post
     *
     * @return string
     */
    public static function getWidgetHtml( $post, $accentClass = '', $target_date = null )
    {
        //use the featured image for the post
        static $fallback_id = 0;

        if ( $fallback_id == 0 ) {
            //NOTE: Dependency on Plugin "display-featured-image-genesis
            if ( function_exists( 'displayfeaturedimagegenesis_get_setting' ) ) {
                $setting     = displayfeaturedimagegenesis_get_setting();
                $fallback_id = $setting['default'];
            }
        }

        $image_id = get_post_thumbnail_id( $post );

        if ( empty( $image_id ) ) {
            $image_id = $fallback_id;
        }

        //possibly use a custom field for the title when displaying the widget
        $custom_fields = get_post_custom( $post->ID );

        $hide_custom_data = hideCustomEventText($custom_fields[ Constants::CUSTOM_FIELD_HIDE_EVENT_TEXT ][0] ?? 'N');

        $thumbnail_img = wp_get_attachment_image( $image_id, 'medium_large' ); //widget-thumb   large medium_large

        if ( !$hide_custom_data && ( ! empty( trim( $custom_fields[ Constants::CUSTOM_FIELD_EVENT_SHORT_DESC ][0] ?? '' ) ) ) ) {
            //title custom field was added to the page and populated with non-empty text
            $excerpt = $custom_fields[ Constants::CUSTOM_FIELD_EVENT_SHORT_DESC ][0];
        } else {
            //use the posts title.
            $excerpt = $post->post_excerpt;
        }

        //Trim long excerpt down to approximately fit in the display area.
        $text_max_length = 600;
        if ( strlen( $excerpt ) > $text_max_length ) {
            $last_space = strpos( $excerpt, ' ', min( $text_max_length - 6, strlen( $excerpt ) ) );

            if ( ! empty( $last_space ) && ( $last_space < strlen( $excerpt ) ) ) {
                $excerpt = substr( $excerpt, 0, min( $last_space, $text_max_length ) );
            }
        }

        if ( !$hide_custom_data && ( ! empty( trim( $custom_fields[ Constants::CUSTOM_FIELD_EVENT_TITLE ][0] ?? '' )
            ) ) ) {
            //title custom field was added to the page and populated with non-empty text
            $title = $custom_fields[ Constants::CUSTOM_FIELD_EVENT_TITLE ][0];
        } else {
            //use the posts title.
            $title = get_the_title( $post );
        }

        //Trim long titles down to approximately fit in the display area.
        $text_max_length = 120;
        if ( strlen( $title ) > $text_max_length ) {
            $last_space = strpos( $title, ' ', min( $text_max_length - 6, strlen( $title ) - 1 ) );

            if ( ! empty( $last_space ) && ( $last_space < strlen( $title ) ) ) {
                $title = substr( $title, 0, min( $last_space, $text_max_length ) );
            }
        }

        $post_block_id = str_replace( '.', '', microtime( true ) );

        $block_head_class = $accentClass;


        $permalink = get_the_permalink( $post );

        $ticket_url = '';

        if ( ! empty( trim( $custom_fields[Constants::CUSTOM_FIELD_BOOK_NOW][0] ?? '' ) ) ) {
            //title custom field was added to the page and populated with non-empty text
            $ticket_url = $custom_fields[Constants::CUSTOM_FIELD_BOOK_NOW][0];
        }


        //build the ticket button, or ignore if the post type is not an event
        $ticket_button = self::getTicketButton( $ticket_url, $block_head_class );

        $page_button = self::getPageButton( $permalink, $block_head_class );

        $excerptHtml = ''; // "<p class='block-post-grid--post-content-excerpt'>&nbsp;</p>";


        //get the categories

        $categories = wp_get_post_categories( $post->ID );

        $category_data_tags = [];

        foreach ( $categories as $category ) {
            $category_data_tags[ $category ] = "data-category-$category='$category'";
        }

        $category_data_tags = implode( " ", $category_data_tags );


        if ( ! empty( $excerpt ) ) {
            $excerptHtml = "<p class='block-post-grid--post-content-excerpt'><a href='{$permalink}'>{$excerpt}</a></p>";
        }

        $html = "<div {$category_data_tags} class=\"block-post-grid--post\" id=\"block-{$post_block_id}\" data-url=\"{$permalink}\">
            <div class='bock-post-grid--hover-indicator'></div>
			<div onclick='window.location=\"{$permalink}\"' class=\"block-post-grid--post-thumbnail\" >\n{$thumbnail_img}\n</div>
			<div class=\"block-post-grid--post-content has-theme-white-color\">
                <h2><a href='{$permalink}'>{$title}</a></h2>
                <div id=\"extra-{$post_block_id}\" class=\"block-post-grid--post-content-extra  has-theme-white-color\">
                    {$excerptHtml}
                    <div class='block-post-grid--post-actions'>{$ticket_button} {$page_button}</div>
                </div>
            </div>
		</div>
		";

        return $html;

    }


    /**
     * Build the Ticket Link button
     * Will return empty space if the post is not an "event" type.
     *
     * @param string $block_head_class
     * @param null $target_date
     *
     * @return string
     */
    protected static function getTicketButton( $ticket_url, $block_head_class = '' )
    {
        if ( empty( trim( $ticket_url ) ) ) {
            return '';
        }

        return "<a class='event-button button ' href='{$ticket_url}'>BOOK NOW</a>";
    }

    /**
     *
     * Build the Page Link button
     * Will return empty space if the post is not an "event" type.
     *
     * @param $permalink
     * @param string $block_head_class
     *
     * @return string
     */
    protected static function getPageButton( $permalink, $block_head_class = '' )
    {
        if ( empty( trim( $permalink ) ) ) {
            return '';
        }

        return "<a class='event-button button' href='$permalink}'>LEARN MORE</a>";
    }

}
