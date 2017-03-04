<?php
namespace liveeditor;

use \liveeditor\LolitaFramework\Core\Arr;
use \liveeditor\LolitaFramework\Core\View;

class ModelActions
{
    /**
     * Create switcher in wp_admin_bar
     * Action: admin_bar_menu
     *
     * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference
     */
    public static function createSwitcher($wp_admin_bar)
    {
        if (!is_admin()) {
            $user = wp_get_current_user();
            $allowed_roles = array('editor', 'administrator', 'author');
            if (array_intersect($allowed_roles, $user->roles)) {
                $buttons = array(
                    array(
                        'id'    => 'edit_mode',
                        'title' => View::make(
                            'toolbar_node',
                            array(
                                'title' => __('Edit mode', 'liveeditor'),
                            )
                        ),
                        'href'  => '#',
                        'meta'  => array( 'class' => 'edit_mode' )
                    ),
                    array(
                        'id'    => 'featured_image',
                        'title' => View::make(
                            'toolbar_node',
                            array(
                                'title' => __('Featured image', 'liveeditor'),
                            )
                        ),
                        'href'  => '#',
                        'meta'  => array( 'class' => 'featured_image hide' ),
                    ),
                    array(
                        'id'    => 'date',
                        'title' => View::make(
                            'toolbar_node',
                            array(
                                'title' => __('Date', 'liveeditor'),
                            )
                        ),
                        'href'  => '#popup_dat_and_time',
                        'meta'  => array( 'class' => 'date hide open-popup-link' ),
                    ),
                    array(
                        'id'    => 'tags',
                        'title' => View::make(
                            'toolbar_node',
                            array(
                                'title' => __('Tags', 'liveeditor'),
                            )
                        ),
                        'href'  => '#popup_tags',
                        'meta'  => array( 'class' => 'hide open-popup-link' ),
                    ),
                    array(
                        'id'    => 'categories',
                        'title' => View::make(
                            'toolbar_node',
                            array(
                                'title' => __('Categories', 'liveeditor'),
                            )
                        ),
                        'href'  => '#popup_categories',
                        'meta'  => array( 'class' => 'hide open-popup-link' ),
                    ),
                    array(
                        'id'    => 'remove',
                        'title' => View::make(
                            'toolbar_node',
                            array(
                                'title' => __('Remove', 'liveeditor'),
                            )
                        ),
                        'href'  => '#',
                        'meta'  => array( 'class' => 'remove hide' ),
                    ),
                );
                foreach ($buttons as $args) {
                    $wp_admin_bar->add_node($args);
                }
            }
        }
    }

    /**
     * Edit post AJAX
     * Actions: wp_ajax_save_title, wp_ajax_save_content
     */
    public static function savePost()
    {
        check_ajax_referer('Lolita Framework', 'nonce');
        $response         = $_POST;
        $data             = Arr::get($response, 'data', array());
        $post_id          = (int) Arr::get($data, 'postId', 0);
        $post             = get_post($post_id);
        $type             = str_replace('wp_ajax_save_', '', current_action());
        $type             = strtolower($type);
        $response['html'] = stripcslashes($response['html']);

        if (!current_user_can('edit_post', $post_id)) {
            $post = null;
        }

        if (null === $post) {
            wp_send_json_error();
        }

        if ('title' === $type) {
            $post->post_title = esc_attr($response['html']);
        }

        if ('content' === $type) {
            $post->post_content = $response['html'];
        }

        if ('date' === $type) {
            $post->post_date = $response['html'];
        }

        $result = wp_update_post($post);
        wp_send_json_success(
            array(
                'post'   => get_post($post),
                'get'    => $response,
                'result' => $result,
                'type'   => $type,
            )
        );
    }

    /**
     * Remove post AJAX
     * Action: wp_ajax_remove_post
     */
    public static function removePost()
    {
        check_ajax_referer('Lolita Framework', 'nonce');
        $response = $_POST;
        $result   = wp_delete_post($response['postId']);
        if (false === $result) {
            wp_send_json_error($response);
        }
        wp_send_json_success($response);
    }

    /**
     * Update featured image AJAX
     * Action: wp_ajax_update_featured_image
     */
    public static function updateFeaturedImage()
    {
        check_ajax_referer('Lolita Framework', 'nonce');
        $response = $_POST;
        $post_id  = Arr::get($response, 'postId');
        $attachment_id = Arr::get($response, 'attachmentId');
        if ('' !== $post_id && '' !== $attachment_id) {
            update_post_meta($post_id, '_thumbnail_id', $attachment_id);
            wp_send_json_success(array());
        }
        wp_send_json_error('Wrong post_id or attachment_id!');
    }

    /**
     * Add tool bar HTML to page
     */
    public static function addToolBar()
    {
        if (!defined('LF_TOOL_BAR_PRINTED')) {
            define('LF_TOOL_BAR_PRINTED', true);
            echo View::make(
                'toolbar',
                array(
                    'tag_cloud' => ModelTags::tagCloud(),
                )
            );
        }
    }
}
