<?php

namespace Avpvh\PrintWebsite\domain;

use Avpvh\PrintWebsite\system\CustomPostTypes;
use WP_Error;
use WP_Post;

/**
 * Class DefaultProjectContents
 * @package Avpvh\PrintWebsite\domain
 */
class DefaultProjectContents
{
    /**
     * Adds default content to db.
     */
    public function addDefaultContents()
    {
        foreach ($this->getDefaultContents() as $slug => $postargs) {
            $post = get_page_by_path($slug, OBJECT, CustomPostTypes::CONTENT);
            if (! $post instanceof WP_Post) {
                $postargs['post_type'] = CustomPostTypes::CONTENT;
                $postargs['post_name'] = $slug;
                $postargs['post_status'] = 'private';
                wp_insert_post($postargs, true);
            }
        }
    }

    /**
     * Returns an array describing all the default PMB content posts. Keys are their post_name,
     * values will be passed into wp_insert_post() (plus we'll automatically set the post_type to the PMB content type,
     * and set post_name using the array key.)
     * @return array
     */
    protected function getDefaultContents()
    {
        return apply_filters(
            'Avpvh\PrintWebsite\domain\DefaultProjectContents->getDefaultContents()',
            [
                'pmb-title-page' => [
                    'post_title' => __('Title Page', 'avpvh-print-website'),
                    'post_content' => '[avpvh_print_website_title_page]',
                ],
                'pmb-toc' => [
                    'post_title' => __('Table of Contents', 'avpvh-print-website'),
                    'post_content' => '[avpvh_print_website_toc]',
                ],
            ]
        );
    }
}
