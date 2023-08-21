<?php

namespace Blocks;

use \DateTime;
use \DateInterval;
use Exception;
use WP_Term;

class WidgetGridBlock
{
    const SELECTION_POSTS = 'posts';
    const CUSTOM_FIELD_EVENT_DATES = 'event-date-list';
    const DATE_SORT_IGNORE = 'Ignore';
    const DATE_SORT_ASC = 'Ascending';
    const DATE_SORT_DESC = 'Descending';

    // Variables
    protected $post_count;
    protected $post_type;
    protected $page_repeater_list;
    protected $categories;
    protected $start_day_value;
    protected $end_day_value;
    protected $startDate;
    protected $endDate;
    protected $activeDates;

    protected $show_sidebar;
    protected $section_title;
    protected $section_description;
    protected $show_dates;
    protected $show_days;
    protected $post_selection;
    protected $grid_block_class;
    protected $accent_class;

    protected $post_list;

    protected ?WP_Term $tag;

    protected ?WP_Term $demoted_tag;
    protected string $dateSort;

    public function __construct()
    {
        $this->loadVariables();
    }

    /**
     * Initialization of local variables, pulled from the block's configuration
     */
    protected function loadVariables()
    {
        //grid setup
        //$this->post_selection = ( block_value('series-selection') == 'metadata') ? 'metadata' : 'posts';

        $this->post_selection = self::SELECTION_POSTS;

        $this->post_type = Constants::SSSM_POST_TYPE;

        //$this->post_count = (int)block_value( 'number-of-events' );
        //$this->categories = block_value('categories');

        //sidebar
        $this->show_sidebar = (block_value('show-sidebar'));

        $this->show_sidebar = (!empty($this->show_sidebar));

        $this->section_title = trim(block_value('section-title'));

        $this->section_description = trim(block_value('section-description'));

        $this->page_repeater_list = block_value('page-list');

        $this->tag = block_value('tag');

        $this->demoted_tag = block_value('demoted-tag');

        $this->dateSort = block_value('date-sort');

        //$this->show_dates    = (block_value('show-dates'));
        //$this->show_days     = (block_value('show-day-names'));

        $this->show_dates = false; // (!empty($this->show_dates));
        $this->show_days = false; //(!empty($this->show_days));

        $this->grid_block_class = block_field('className');

        //accent colors
        $this->accent_class = 'has-theme-primary-background-color';

        //date range
        //$this->start_day_value = block_value('relative-start-day');
        //$this->end_day_value   = block_value('relative-end-day');

        if (empty($this->start_day_value)) $this->start_day_value = 1;
        if (empty($this->end_day_value)) $this->end_day_value = 1;

        //$this->calculateDates();
    }


    /**
     * Call this method to get the html output for the widget grid
     *
     * @return string
     */
    public function getOutput(): string
    {
        $filterDates = false;
        $this->post_list = [];

        //get the post data
        if (!empty($this->tag)) {
            $this->getPostsByTagSortedByDemotedTag($this->tag->name, $this->demoted_tag?->name);
        } elseif ($this->post_selection === self::SELECTION_POSTS) {
            $this->getPostsFromRepeaterList();
        } else {
            $filterDates = true;
            $this->getPostsFromWpQuery();
        }

        //if we have no posts, then return empty HTMl, this behavior may change in the future
        if (count($this->post_list) == 0) {
            return '<div class="empty-grid-block"><p>Empty Widget Block: The current selection has no results. Click to edit the block to change the categories, or edit page categories as needed.</p></div>';
        }

        $grid_html = $this->getWidgetGrid();

        if (empty($grid_html)) {
            return '<div class="empty-grid-block"></div>';
        }

        //no sidebar, return just he widget html
        if (!$this->show_sidebar) {
            return $grid_html;
        }

        $sidebar_html = $this->getSideBarHtml();

        return "<div class='block-post-grid-wrapper'>$sidebar_html<div class='block-post-grid--content-subsection two-thirds'>$grid_html</div></div>";
    }

    /**
     * Use for Event Dates
     * We store the event dates as custom fields on the posts
     * A separate plugin or process will populate those dates
     *
     * Calculate the start and end dates, based on the relative days configuration
     *
     * @throws Exception
     */
    protected function calculateDates(): void
    {
        //automatically use lowest and higher value
        $start_day = min($this->start_day_value, $this->end_day_value) - 1;
        $end_day = max($this->start_day_value, $this->end_day_value) - 1;

        $start_date = new DateTime();
        $this->startDate = $start_date->add(new DateInterval("P{$start_day}D"))->setTime(0, 0);
        $activeDate = clone $this->startDate;

        $end_date = new DateTime();
        $this->endDate = $end_date->add(new DateInterval("P{$end_day}D"))->setTime(0, 0);

        $this->activeDates = [];

        for ($i = 0; $i <= ($end_day - $start_day); $i++) {
            $activeDate->add(new DateInterval("P{$i}D"));
            $formattedDate = $activeDate->format('Y-m-d');

            $this->activeDates[$formattedDate] = true;
        }
    }

    /**
     * fetch the list of posts that were manually specified.
     * populates $this->post_list
     */
    protected function getPostsFromRepeaterList(): void
    {
        $this->post_list = [];

        if (empty($this->page_repeater_list['rows'])) return;

        foreach ($this->page_repeater_list['rows'] as $item) {
            $post = get_post($item['sssm-page']['id']);

            if (!empty($post)) $this->post_list[] = $post;
        }
    }

    /**
     * Returns an array of posts selected by a given tag or tag list.
     * Pass an array of strings to narrow down selection to posts having all the tags in the list
     * Pass an array of integers to skips posts by ID. This can be used to eliminate a post already displayed.
     *
     *
     * @param string|array|null $tagList
     * @param array|null $skipIds
     * @return array
     */
    protected function getPostsByTagValue(string|array|null $tagList, ?array $skipIds = []): array
    {
        if (empty($tagList)) return [];

        if (!is_array($tagList)) {
            $tagList = [$tagList];
        }

        $taxQuery = [];

        foreach($tagList as $tag) {
            $taxQuery[] = [
                'taxonomy' => 'post_tag',
                'field' => 'slug',
                'terms' => sanitize_title($tag), // Constants::WP_TAG_THIS_WEEK
            ];
        }

        //get posts by tag name
        return get_posts(
            [
                'post_type' => Constants::SSSM_POST_TYPE,
                'post_status' => 'publish',
                'posts_per_page' => 24,
                'orderby' => 'name',
                'tax_query' => $taxQuery,
                'exclude' => $skipIds,
            ]
        );
    }

    /**
     * Returns an array of posts tagged with the required tag
     * Optionally provide a "demoted tag" - posts also containing the demoted tag will be pushed to the end of results
     * Use the demoted tag to group subsets of posts together, the non-demoted will appear first in the output
     *
     * @param string $requiredTag
     * @param string|null $demotedTagName
     * @return void
     */
    protected function getPostsByTagSortedByDemotedTag(string $requiredTag, ?string $demotedTagName = null): void
    {
        //get the posts with both the included and demoted tag names
        $demotedPosts = $this->getPostsByTagValue([$demotedTagName, $requiredTag]);

        //get the list of post ID for the excluded posts
        $excluded = WidgetGridBlock::getIdsFromPostsList($demotedPosts);

        //get the posts with the included tag name, excluding the demoted set
        $posts = $this->getPostsByTagValue($requiredTag, $excluded);

        //sort these posts by the first event date
        $posts = $this->sortPostsByFirstEventDate($posts);

        //merge the lists together, which will place the demoted posts at the end (lower down the page)
        $this->post_list = array_merge($posts, $demotedPosts);
    }

    /**
     * Returns an array of IDs from an array of Posts.
     * This can be used to prevent showing the same posts later on the page or to build a list for any reason.
     * null safe: an empty array of posts will return an empty array
     *
     * @param array|null $postsList
     * @return array
     */
    public static function getIdsFromPostsList(?array $postsList = []): array
    {
        if (empty($postsList)) return [];

        $return = [];

        foreach ($postsList as $post) {
            $return[] = $post->ID;
        }

        return $return;
    }

    public function sortPostsByFirstEventDate(array $posts): array
    {
        //if we aren't sorting by date, do nothing, return the input
        if ($this->dateSort == self::DATE_SORT_IGNORE) return $posts;

        $datedPosts = $return = [];

        //sub-sort the posts by post date, using a far future date if there is no post date set
        foreach ($posts as $post) {
            $eventDates = get_post_custom_values(self::CUSTOM_FIELD_EVENT_DATES, $post->ID);
            $date = $this->getFirstEventDateFromCustomField($eventDates[0] ?? '', '2080-01-01');
            $datedPosts[$date][] = $post;
        }

        //sort the groups of posts based on the grid configuration
        if ($this->dateSort == self::DATE_SORT_ASC) {
            ksort($datedPosts);
        } else {
            krsort($datedPosts);
        }

        //build the output array in order.
        foreach ($datedPosts as $list) {
            foreach ($list as $post) {
                $return[] = $post;
            }
        }

        return $return;
    }

    public function getFirstEventDateFromCustomField(string $eventDates, string $default): string
    {
        if (empty($eventDates)) return $default;

        $dates = explode("\n", $eventDates);

        $monday = date('Y-m-d', strtotime("this week"));

        //look for the first occurrence on or after Monday of the current week.
        foreach ($dates as $date) {
            if ($date >= $monday) {
                return $date;
            }
        }
        return $default;
    }

    /**
     * get the sidebar HTML, using the options retrieved from the post
     *
     * @return string
     */
    protected function getSideBarHtml(): string
    {
        $sidebar_html = "<h2>$this->section_title</h2>\n";

        if (!empty($this->section_description)) {
            $sidebar_html .= "<p>$this->section_description</p>\n";
        }

        return "<div class='block-post-grid--sidebar one-third first'>$sidebar_html</div>";
    }

    /**
     * @return string
     * @deprecated
     *
     * Ovation Tix no longer in use, saving for example
     *
     * get the sidebar HTML, using the options retrieved from the post
     *
     */
    protected function getDateBasedSideBarHtml()
    {
        $sidebar_html = "<h2>$this->section_title</h2>\n";

        if ($this->show_dates) {
            $display_start = $this->startDate->format('l');
            $display_end = $this->endDate->format('l');

            $date_display = $display_start;

            if ($display_start != $display_end) {
                $date_display .= ' - ' . $display_end;
            }

            $date_display = "<p>$date_display</p>\n";

            $sidebar_html .= $date_display;
        }

        if ($this->show_days) {
            $display_start = $this->startDate->format('F j');

            $end_format = ($this->startDate->format('F') == $this->endDate->format('F')) ? 'j' : 'F j';

            $display_end = $this->endDate->format($end_format);

            $date_display = $display_start;

            if ($display_start != $display_end && $this->startDate != $this->endDate) {
                $date_display .= ' - ' . $display_end;
            }

            $date_display = "<p>$date_display</p>\n";

            $sidebar_html .= $date_display;
        }

        return "<div class='block-post-grid--sidebar one-third first'>$sidebar_html</div>";

    }

    /**
     * cycle through the loop of posts in $this->post_list,
     * builds the whole grid, looping through each post in the list
     *
     * @return string
     */
    protected function getWidgetGrid(): string
    {
        $html = "<div class='block-post-grid {$this->grid_block_class}'>";

        $block_count = 0;

        foreach ($this->post_list as $post) {

            $html .= Widget::getWidgetHtml($post, $this->accent_class);

            $block_count++;

            if ($block_count == $this->post_count) break;
        }

        $html .= "</div>";

        if ($block_count == 0) return '';

        return $html;


    }

    /**
     * @return string
     * @throws Exception
     * @deprecated
     *
     * Ovation Tix no longer in use, saving for example
     *
     * cycle through the loop of posts in $this->post_list,
     * builds the whole grid, looping through each post in the list
     *
     */
    protected function getEventDatesWidgetGrid($filterDates = false)
    {
        $html = "<div class='block-post-grid {$this->grid_block_class}'>";

        $block_count = 0;

        foreach ($this->post_list as $post) {
            //parse the deep links from custom field
            $ovation_links = get_post_custom_values('ovation_deep_links', $post->ID);

            $post->dates = $this->getEventDates($ovation_links);

            //get the master calendar from custom field
            $event_calendar = get_post_custom_values(Constants::CUSTOM_FIELD_EVENT_DATES, $post->ID);

            $post->ovation_calendar_link = $event_calendar[0] ?? '';

            //determine whether to skip this post based on grid criteria dates
            if ($filterDates) {
                if (empty(array_intersect_key($this->activeDates, $post->dates))) continue;
            }

            $html .= Widget::getWidgetHtml($post, $this->accent_class);

            $block_count++;

            if ($block_count == $this->post_count) break;
        }

        $html .= "</div>";

        if ($block_count == 0) return '';

        return $html;
    }


    /**
     * builds query arguments from the block's configuration values
     * set $this->post_list to the output of the WordPress query
     */
    protected function getPostsFromWpQuery()
    {
        // WP_Query arguments
        $args = [
            'post_type' => [$this->post_type],
            'posts_per_page' => $this->post_count,
        ];

        if (!empty($this->categories)) {
            $args['category_name'] = implode(',', $this->categories);
        }

        $this->post_list = get_posts($args);

    }

    /**
     * @param $ovation_link
     *
     * @return array
     * @throws Exception
     * @deprecated
     * parses the OvationTix deep link text to get the date and link in an array
     *
     */
    protected function parseOvationLink($ovation_link, $include_time = false): array
    {
        //sample: Oct 04, 2019 @ 03:00PM	https://web.ovationtix.com/trs/pe/10449623

        $link = substr($ovation_link, strpos($ovation_link, 'https'));

        if ($include_time) {
            $date_text = substr($ovation_link, 0, strpos($ovation_link, 'https:') - 2);

            $date_text = trim(str_replace('@', '', $date_text));
        }
        else {
            $date_text = substr($ovation_link, 0, strpos($ovation_link, '@') - 1);

        }

        $date = new DateTime($date_text);

        return ['date' => $date->format('Y-m-d'), 'link' => $link, 'date_object' => $date];
    }

    /**
     * @param array $event_links
     *
     * @return array
     * @throws Exception
     * @deprecated
     *
     * returns an array of dates derived from parse ovationTix deep links
     * pattern: [ 'date' => 'link' ]
     *
     * Sample Data (from acme plugin) (split by newline)
     * 2023-08-19
     * 2023-08-26
     * 2023-08-27
     * 2023-09-02
     * 2023-09-03
     * 2023-09-09
     * 2023-09-10
     * 2023-09-16
     * 2023-09-17
     * 2023-09-23
     * 2023-09-24
     * 2023-09-30
     * 2023-10-01
     *
     */
    protected function getEventDates(?array $event_links = null)
    {
        if (empty($event_links[0])) return [];

        $dates = [];

        $ovation_links_data = explode("\n", $event_links[0]);

        foreach ($ovation_links_data as $ovation_link) {
            $data = $this->parseOvationLink($ovation_link);

            $dates[$data['date']] = $data['link'];
        }

        return $dates;
    }
}
