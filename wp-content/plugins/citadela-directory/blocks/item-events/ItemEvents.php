<?php

namespace Citadela\Directory\Blocks;

use Citadela\Directory\Events;

class ItemEvents extends Block
{
    protected static $slug = 'item-events';
    public static function renderCallback($attributes, $content)
    {
        if (is_admin() || !function_exists('tribe_get_event')) {
            return;
        }
        $posts = get_posts([
            'numberposts' => -1,
            'post_type' => 'tribe_events',
            'meta_key' => '_EventDirectoryItem',
            'meta_value' => get_the_ID(),
	    'orderby' => 'start_date'
        ]);
        $nowUtc = gmdate('Y-m-d H:i:s');
        foreach ($posts as $post) {
            $event = tribe_get_event($post->ID);
            if ($event->end_date_utc > $nowUtc) {
                $events[$post->ID] = $event;
            }
        }
        if (!empty($events)) {
            usort($events, function($a, $b) {
                return strtotime($a->start_date_utc . ' UTC') - strtotime($b->start_date_utc . ' UTC');
            });
            
            $classes = [];
            if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 

            ob_start();
            include __DIR__ . '/../../plugin/cpt/item/templates/parts/single-item-events.php';
            return ob_get_clean();
        }
    }
}
