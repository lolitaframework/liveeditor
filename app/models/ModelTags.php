<?php
namespace liveeditor;

use liveeditor\LolitaFramework\Core\Arr;
use liveeditor\LolitaFramework\Core\Img;
use liveeditor\LolitaFramework\Core\View;

class ModelTags
{
    /**
     * Get tag cloud by all tag
     *
     * @return array
     */
    public static function tagCloud()
    {
        $tags = get_tags(array('hide_empty' => false));
        return self::tagCloudData($tags);
    }

    /**
     * Tag cloud data
     *
     * @param  array $tags
     * @return array
     */
    public static function tagCloudData($tags)
    {
        if (!count($tags)) {
            return array();
        }

        $counts      = Arr::pluck($tags, 'count');
        $small       = 8;
        $large       = 22;
        $font_spread = $large - $small;
        $min         = min($counts);
        $max         = max($counts);
        $spread      = max($max - $min, 1);

        if ($font_spread < 0) {
            $font_spread = 1;
        }

        $font_step = $font_spread / $spread;
        foreach ($tags as &$tag) {
            $tag->font_size = $small + ($tag->count - $min) * $font_step;
        }
        return $tags;
    }
}
