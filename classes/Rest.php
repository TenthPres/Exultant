<?php

namespace tp;

abstract class Rest
{
    public static function init()
    {
        foreach (get_post_types() as $postType) {
            add_filter("rest_prepare_$postType", [self::class, 'prepareSearchResult'], 10, 3);
        }
    }

    public static function prepareSearchResult($response, $item, $request): \WP_REST_Response
    {
        // Check if the item is a post type that supports thumbnails
        if (!post_type_supports($item->post_type, 'thumbnail')) {
            $thumbnailId = get_post_thumbnail_id($item->ID);
            if ($thumbnailId) {
                $image = wp_get_attachment_image_src($thumbnailId, 'small');
                if ($image) {
                    $response->data['thumb'] = $image[0];
                } else {
                    $response->data['thumb'] = null;
                }
            }
        }

        return $response;
    }
}