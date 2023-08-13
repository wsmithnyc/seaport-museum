<?php

require_once (realpath(dirname(__FILE__) . '/..') . '/blocks/blocks_autoload.php');

use Blocks\Constants;

function get_event_desc_shortcode(): string
{
    $hide = get_post_meta( get_the_ID(), Constants::CUSTOM_FIELD_HIDE_EVENT_TEXT, true );
    $id = get_post_meta( get_the_ID(), Constants::CUSTOM_FIELD_TEMPLATE, true );
    $desc = get_post_meta( get_the_ID(), Constants::CUSTOM_FIELD_EVENT_DESC, true );

    $hide = hideCustomEventText($hide);
    $id = trim($id);

    //if there is no templateId or we set hide descriptions to "y" then return empty string
    if(empty($id) || ($hide)) {
        return '';
    }

    return "<div data-shortcode='event-desc' data-template-id='{$id}-desc'>$desc</div>";
}

function get_event_book_now_shortcode(): string
{
    $url = get_post_meta( get_the_ID(), Constants::CUSTOM_FIELD_BOOK_NOW, true );

    $url = trim($url);

    //if there is no templateId or we set hide descriptions to "y" then return empty string
    if(empty($url)) {
        return '';
    }

    return "<div class='is-layout-flex wp-block-buttons'>\n
            <div data-shortcode='" . Constants::SHORTCODE_BOOK_NOW . "' class='wp-block-button'><p><a class='wp-block-button__link wp-element-button' href='$url'>BOOK NOW</a></p></div>\n
            </div>";
}

function get_event_book_now_general_admission_shortcode(): string
{
    $url = get_post_meta( get_the_ID(), Constants::CUSTOM_FIELD_BOOK_NOW, true );

    $url = trim($url);

    //if there is no templateId or we set hide descriptions to "y" then return empty string
    if(empty($url)) {
        return '';
    }

    return "<div class='is-layout-flex wp-block-buttons'>\n
            <div data-shortcode='" . Constants::SHORTCODE_BOOK_GENERAL_ADMISSION . "' class='wp-block-button'>
            <p><a class='wp-block-button__link wp-element-button' href='$url'>BOOK GENERAL ADMISSION</a></p></div>\n
            </div>";
}

function get_museum_hours_shortcode(): string
{
    if (function_exists('get_museum_hours_shortcode_data')) {
        $data = get_museum_hours_shortcode_data();

        $open = $data['open'];
        $close = $data['close'];
        $open_today = $data['open_today'];
        $next_open_day = $data['next_open_day'];

        if ($open_today) {
            $message =  "The Seaport Museum is open today {$open}–{$close}.";
        } else {
            $message = "The Seaport Museum will open $next_open_day {$open}–{$close}.";
        }

        return "<div data-shortcode='museum-hours'><span class='dashicons dashicons-clock'></span>$message</div>";
    }

    //todo: need a fallback message in case this data is not available.
    $message = '';

    return "<div data-shortcode='museum-hours'>$message</div>";
}