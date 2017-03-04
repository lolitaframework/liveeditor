<?php
namespace liveeditor;

use liveeditor\LolitaFramework\Core\Arr;
use liveeditor\LolitaFramework\Core\Img;
use liveeditor\LolitaFramework\Core\View;

class ModelFilters
{
    /**
     * Add data marker data-post-title="YES" to the_title
     *
     * @param  [string] $title The post title.
     * @param  [integer] $id The post ID.
     * @return [string] filtered title.
     */
    public static function filterTheTitle($title, $id)
    {
        if (is_admin()) {
            return $title;
        }
        $post = get_post($id);
        $attributes = Arr::join(
            array(
                'class'           => 'live-editor-title live-editor-title-'. $id,
                'data-post-id'    => $id,
                'data-post-date'  => $post->post_date,
            )
        );
        return View::make(
            'title',
            array(
                'attributes' => $attributes,
                'title'      => $title,
            )
        );
    }

    /**
     * Add data marker ot the_content
     *
     * @param  [string] $content Content of the current post.
     * @return [stirng] content with marker.
     */
    public static function filterTheContent($content)
    {
        if (is_admin()) {
            return $content;
        }
        $post = get_post();

        $attributes = Arr::join(
            array(
                'class'             => 'live-editor-content live-editor-content' . $post->ID,
                'data-post-id'      => $post->ID,
                'data-post-date'    => $post->post_date,
            )
        );
        return View::make(
            'content',
            array(
                'attributes' => $attributes,
                'content'    => $content,
            )
        );
    }

    /**
     * Image downsize
     *
     * @param  boolean $downsized
     * @param  integer $id
     * @param  string $size
     * @return mixed
     */
    public static function imageDownsize($downsized, $id, $size)
    {
        $post_id = Img::getPostID($id);
        $img_url = wp_get_attachment_url($id);
        $meta = wp_get_attachment_metadata($id);
        $width = $height = 0;
        $is_intermediate = false;
        $img_url_basename = wp_basename($img_url);

        // try for a new style intermediate size
        if ($intermediate = image_get_intermediate_size($id, $size)) {
            $img_url = str_replace($img_url_basename, $intermediate['file'], $img_url);
            $width = $intermediate['width'];
            $height = $intermediate['height'];
            $is_intermediate = true;
        } elseif ($size == 'thumbnail') {
            // fall back to the old thumbnail
            if (($thumb_file = wp_get_attachment_thumb_file($id)) && $info = getimagesize($thumb_file)) {
                $img_url = str_replace($img_url_basename, wp_basename($thumb_file), $img_url);
                $width = $info[0];
                $height = $info[1];
                $is_intermediate = true;
            }
        }
        if (!$width && !$height && isset($meta['width'], $meta['height'])) {
            // any other type: use the real image
            $width = $meta['width'];
            $height = $meta['height'];
        }

        if ($img_url) {
            // we have the actual image size, but might need to further constrain it if content_width is narrower
            list($width, $height) = image_constrain_size_for_editor($width, $height, $size);

            return array($img_url.'#id='.$id . '&post_id=' . $post_id, $width, $height, $is_intermediate);
        }
        return false;
    }
}
