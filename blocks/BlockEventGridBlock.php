<?php

class BlockEventGridBlock
{
    // Variables
    protected $post_count;
    protected $post_type;
    protected $categories;
    protected $start_day_value;
    protected $end_day_value;
    protected $startDate;
    protected $endDate;
    protected $activeDates;

    protected $show_sidebar;
    protected $section_title;
    protected $show_dates;
    protected $show_days;
    protected $post_selection;
    protected $grid_block_class;
    protected $accent_classes;

    protected $post_list;

    public function __construct()
    {
        $this->loadVariables();
    }


    /**
     * Call this method to get the html output for the widget grid
     *
     * @return string
     */
    public function getOutput(): string
    {
        //get the post data
        if ( $this->post_selection === 'posts' ) {
            $filterDates = false;
            $this->getPostsFromValuesList();
        } else {
            $filterDates = true;
            $this->getPostsFromWpQuery();
        }

        //if we have no posts, then return empty HTMl, this behavior may change in the future
        if ( count( $this->post_list ) == 0 ) {
            return '';
        }

        $grid_html = $this->getWidgetGrid( $filterDates );

        //no sidebar, return just he widget html
        if ( ! $this->show_sidebar ) {
            return $grid_html;
        }

        $sidebar_html = $this->getSideBarHtml();

        return "<div>$sidebar_html<div class='block-post-grid--content-subsection two-thirds'>$grid_html</div></div>";
    }

    /**
     * Initialization of local variables, pulled from the block's configuration
     */
    protected function loadVariables(): void
    {
        //grid setup
        $this->post_selection = ( block_value( 'series-selection' ) == 'metadata' ) ? 'metadata' : 'posts';

        $this->post_type = 'event';

        $this->post_count = (int) block_value( 'number-of-events' );
        $this->categories = block_value( 'categories' );

        //sidebar
        $this->show_sidebar = ( block_value( 'show-sidebar' ) );

        $this->show_sidebar = ( ! empty( $this->show_sidebar ) );

        $this->section_title = trim( block_value( 'section-title' ) );
        $this->show_dates    = ( block_value( 'show-dates' ) );
        $this->show_days     = ( block_value( 'show-day-names' ) );

        $this->show_dates = ( ! empty( $this->show_dates ) );
        $this->show_days  = ( ! empty( $this->show_days ) );

        $this->grid_block_class = block_field( 'className' );

        //accent colors
        $this->accent_classes = [
            'has-theme-primary-background-color',
            //'has-theme-accent-2-background-color',
            //'has-theme-accent-3-background-color',
            //'has-theme-accent-4-background-color',
        ];

        //date range
        $this->start_day_value = block_value( 'relative-start-day' );
        $this->end_day_value   = block_value( 'relative-end-day' );

        if ( empty( $this->start_day_value ) ) {
            $this->start_day_value = 1;
        }
        if ( empty( $this->end_day_value ) ) {
            $this->end_day_value = 1;
        }

        $this->calculateDates();
    }

    /**
     * Calculate the start and end dates, based on the relative days configuration
     *
     * @throws Exception
     */
    protected function calculateDates(): bool
    {
        //automatically use lowest and higher value
        $start_day = min( $this->start_day_value, $this->end_day_value ) - 1;
        $end_day   = max( $this->start_day_value, $this->end_day_value ) - 1;

        $start_date      = new DateTime();
        $this->startDate = $start_date->add( new DateInterval( "P{$start_day}D" ) )->setTime( 0, 0 );
        $activeDate      = clone $this->startDate;

        $end_date      = new DateTime();
        $this->endDate = $end_date->add( new DateInterval( "P{$end_day}D" ) )->setTime( 0, 0 );

        $this->activeDates = [];

        for ( $i = 0; $i <= ( $end_day - $start_day ); $i ++ ) {
            $activeDate->add( new DateInterval( "P{$i}D" ) );
            $formattedDate = $activeDate->format( 'Y-m-d' );

            $this->activeDates[ $formattedDate ] = true;
        }

        return true;
    }

    /**
     * fetch the list of posts that were manually specified.
     * populates $this->post_list
     */
    protected function getPostsFromValuesList(): void
    {
        $this->post_list = [];

        for ( $i = 1; $i < 7; $i ++ ) {
            $post = block_value( "series-selection-$i" );

            if ( ! empty( $post ) ) {
                $this->post_list[] = $post;
            }
        }
    }


    /**
     * get the sidebar HTML, using the options retrieved from the post
     *
     * @return string
     */
    protected function getSideBarHtml(): string
    {
        $sidebar_html = "<h2>$this->section_title</h2>\n";

        if ( $this->show_dates ) {
            $display_start = $this->startDate->format( 'l' );
            $display_end   = $this->endDate->format( 'l' );

            $date_display = $display_start;

            if ( $display_start != $display_end ) {
                $date_display .= ' - ' . $display_end;
            }

            $date_display = "<p>$date_display</p>\n";

            $sidebar_html .= $date_display;
        }

        if ( $this->show_days ) {
            $display_start = $this->startDate->format( 'F j' );

            $end_format = ( $this->startDate->format( 'F' ) == $this->endDate->format( 'F' ) ) ? 'j' : 'F j';

            $display_end = $this->endDate->format( $end_format );

            $date_display = $display_start;

            if ( $display_start != $display_end && $this->startDate != $this->endDate ) {
                $date_display .= ' - ' . $display_end;
            }

            $date_display = "<p>$date_display</p>\n";

            $sidebar_html .= $date_display;
        }

        return "<div class='block-post-grid--sidebar one-third first'>$sidebar_html</div>";
    }

    /**
     * get the html for the post's grid widget
     *
     * @param $post
     *
     * @return string
     */
    protected function getWidgetHtml( $post, $target_date = null ): string
    {
        $excerpt = $post->post_excerpt;

        $last_space = strpos( $excerpt, ' ', min( 80, strlen( $excerpt ) ) );

        $excerpt = substr( $excerpt, 0, min( $last_space, 90 ) );

        $post_block_id = str_replace( '.', '', microtime( true ) );

        $thumbnail_url = get_the_post_thumbnail_url( $post, 'medium' );

        $permalink = get_the_permalink( $post );

        $ticket_link = '';

        $block_head_class = $this->accent_classes[0];

        if ( ! empty( $target_date ) && ! empty( $post->dates[ $target_date ] ) ) {
            $ticket_link = $post->dates[ $target_date ]['link'];
        } elseif ( ! empty( $post->ovation_calendar_link ) ) {
            $ticket_link = $post->ovation_calendar_link;
        }

        $title = get_the_title( $post );

        $html = "<div class=\"block-post-grid--post\" id=\"block-{$post_block_id}\" data-url=\"{$permalink}\">
            <div class='bock-post-grid--hover-indicator'></div>
			<div class=\"block-post-grid--post-thumbnail\" style=\"background-image: url('{$thumbnail_url}');\"></div>
            <div class=\"block-post-grid--post-content has-theme-white-color\">
                <h2>{$title}</h2>
                <div id=\"extra-{$post_block_id}\" class=\"block-post-grid--post-content-extra  has-theme-white-color\">
                    <p>{$excerpt}</p>
                </div>
            </div>
			<div class=''>
                <a class=\"event-button wp-block-button__link {$block_head_class}\" href=\"{$permalink}\">LEARN MORE</a>
                <a class=\"event-button wp-block-button__link {$block_head_class}\" target='_blank' href=\"{$ticket_link}\">BUY TICKETS</a>
            </div>
		</div>
		";

        return $html;
    }

    /**
     * cycle through the loop of posts in $this->post_list,
     * builds the whole grid, looping through each post in the list
     *
     * @return string
     */
    protected function getWidgetGrid( $filterDates = false ): string
    {
        $html = "<div class='block-post-grid {$this->grid_block_class}'>";

        $block_count = 0;

        foreach ( $this->post_list as $post ) {
            //parse the deep links from custom field
            $ovation_links = get_post_custom_values( 'ovation_deep_links', $post->ID );

            $post->dates = $this->getEventDates( $ovation_links );

            //get the master calendar from custom field
            $ovation_calendar = get_post_custom_values( 'ovation_calendar_link', $post->ID );

            $post->ovation_calendar_link = $ovation_calendar[0] ?? '';

            //determine whether to skip this post based on grid criteria dates
            if ( $filterDates ) {
                if ( empty( array_intersect_key( $this->activeDates, $post->dates ) ) ) {
                    continue;
                }
            }

            $html .= $this->getWidgetHtml( $post );

            $block_count ++;

            if ( $block_count == $this->post_count ) {
                break;
            }
        }

        $html .= "</div>";

        return $html;
    }


    /**
     * builds query arguments from the block's configuration values
     * set $this->post_list to the output of the WordPress query
     */
    protected function getPostsFromWpQuery(): void
    {
        // WP_Query arguments
        $args = [
            'post_type'      => [ $this->post_type ],
            'posts_per_page' => $this->post_count,
        ];

        if ( ! empty( $this->categories ) ) {
            $args['category_name'] = implode( ',', $this->categories );
        }

        $this->post_list = get_posts( $args );
    }

    /**
     * parses the OvationTix deep link text to get the date and link in an array
     *
     * @param $ovation_link
     *
     * @return array
     * @throws Exception
     */
    protected function parseOvationLink( $ovation_link, $include_time = false ): array
    {
        //sample: Oct 04, 2019 @ 03:00PM	https://web.ovationtix.com/trs/pe/10449623

        $link = substr( $ovation_link, strpos( $ovation_link, 'https' ) );

        if ( $include_time ) {
            $date_text = substr( $ovation_link, 0, strpos( $ovation_link, 'https:' ) - 2 );

            $date_text = trim( str_replace( '@', '', $string ) );
        } else {
            $date_text = substr( $ovation_link, 0, strpos( $ovation_link, '@' ) - 1 );

        }

        $date = new DateTime( $date_text );

        return [ 'date' => $date->format( 'Y-m-d' ), 'link' => $link, 'date_object' => $date ];
    }

    /**
     * returns an array of dates derived from parse ovationTix deep links
     * pattern: [ 'date' => 'link' ]
     *
     * @param array $ovation_links
     *
     * @return array
     * @throws Exception
     */
    protected function getEventDates( $ovation_links = null )
    {
        if ( empty( $ovation_links[0] ) ) {
            return [];
        }

        $dates = [];

        $ovation_links_data = explode( "\n", $ovation_links[0] );

        foreach ( $ovation_links_data as $ovation_link ) {
            $data = $this->parseOvationLink( $ovation_link );

            $dates[ $data['date'] ] = $data['link'];
        }

        return $dates;
    }


}
