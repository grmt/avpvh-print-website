<?php

namespace Avpvh\PrintWebsite\system;

use Avpvh\PrintWebsite\services\SvgDoer;

/**
 * Class CustomPostTypes
 *
 * Description
 *
 * @package        AV-PvH Print Website
 * @author         Mike Nelson
 * @since          $VID:$
 *
 */
class CustomPostTypes
{
    const PROJECT = 'avpvh_prwb_project';
    const PROJECTS = 'avpvh_prwb_projects';
    const DESIGN = 'avpvh_prwb_design';
    const DESIGNS = 'avpvh_prwb_designs';
    const CONTENT = 'avpvh_prwb_content';
    const CONTENTS = 'avpvh_prwb_contents';
    /**
     * @var SvgDoer
     */
    protected $svg_doer;

    /**
     * @param SvgDoer $svg_doer
     */
    public function inject(SvgDoer $svg_doer)
    {
        $this->svg_doer = $svg_doer;
    }

    /**
     * This must not be done before init eh.
     */
    public function register()
    {
        register_post_type(
            self::PROJECT,
            [
                'label' => esc_html__('Saved Prints', 'avpvh-print-website'),
                'description' => esc_html__('Saved prints for printing with AV-PvH Print Website', 'avpvh-print-website'),
                'capability_type' => 'avpvh_prwb_project',
                'capabilities' => array(
                    'publish_posts' => 'publish_avpvh_prwb_projects',
                    'edit_posts' => 'edit_avpvh_prwb_projects',
                    'edit_others_posts' => 'edit_others_avpvh_prwb_projects',
                    'delete_posts' => 'delete_avpvh_prwb_projects',
                    'delete_others_posts' => 'delete_others_avpvh_prwb_projects',
                    'read_private_posts' => 'read_private_avpvh_prwb_projects',
                ),
            ]
        );
        $this->setupMapMetaCaps(self::PROJECT);

        register_post_type(
            self::DESIGN,
            [
                'label' => esc_html__('Designs', 'avpvh-print-website'),
                'description' => esc_html__('Designs for printing with AV-PvH Print Website', 'avpvh-print-website'),
                'capability_type' => 'avpvh_prwb_design',
                'capabilities' => array(
                    'publish_posts' => 'publish_avpvh_prwb_designs',
                    'edit_posts' => 'edit_avpvh_prwb_designs',
                    'edit_others_posts' => 'edit_others_avpvh_prwb_designs',
                    'delete_posts' => 'delete_avpvh_prwb_designs',
                    'delete_others_posts' => 'delete_others_avpvh_prwb_designs',
                    'read_private_posts' => 'read_private_avpvh_prwb_designs',
                ),
            ]
        );
        $this->setupMapMetaCaps(self::DESIGN);

        $labels = array(
            'name'                  => _x('Print Materials', 'Post type general name', 'avpvh-print-website'),
            'singular_name'         => _x('Print Material', 'Post type singular name', 'avpvh-print-website'),
            'menu_name'             => _x('Print Materials', 'Admin Menu text', 'avpvh-print-website'),
            'name_admin_bar'        => _x('Print Material', 'Add New on Toolbar', 'avpvh-print-website'),
            'add_new'               => __('Add New', 'avpvh-print-website'),
            'add_new_item'          => __('Add New Print Material', 'avpvh-print-website'),
            'new_item'              => __('New Print Material', 'avpvh-print-website'),
            'edit_item'             => __('Edit Print Material', 'avpvh-print-website'),
            'view_item'             => __('View Print Material', 'avpvh-print-website'),
            'all_items'             => __('All Print Materials', 'avpvh-print-website'),
            'search_items'          => __('Search Print Materials', 'avpvh-print-website'),
            'parent_item_colon'     => __('Parent Print Materials:', 'avpvh-print-website'),
            'not_found'             => __('No Print Materials found.', 'avpvh-print-website'),
            'not_found_in_trash'    => __('No Print Materials found in Trash.', 'avpvh-print-website'),
            'featured_image'        => _x('Print Material Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'avpvh-print-website'),
            'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'avpvh-print-website'),
            'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'avpvh-print-website'),
            'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'avpvh-print-website'),
            'archives'              => _x('Print Material archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'avpvh-print-website'),
            'insert_into_item'      => _x('Insert into Print Material', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'avpvh-print-website'),
            'uploaded_to_this_item' => _x('Uploaded to this Print Material', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'avpvh-print-website'),
            'filter_items_list'     => _x('Filter Print Materials list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'avpvh-print-website'),
            'items_list_navigation' => _x('Print Materials list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'avpvh-print-website'),
            'items_list'            => _x('Print Materials list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'avpvh-print-website'),
        );
        register_post_type(
            self::CONTENT,
            // WordPress CPT Options Start
            array(
                'labels' => $labels,
                'has_archive' => true,
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => AVPVH_PRWB_ADMIN_PROJECTS_PAGE_SLUG,
                'rewrite' => array('slug' => 'avpvh-print-website'),
                'show_in_rest' => true,
                'supports' => array('title', 'editor', 'revisions', 'author', 'thumbnail', 'custom-fields'),
                'taxonomies' => array('category', 'post_tag'),
                'menu_icon' => $this->svg_doer->getSvgDataAsColor(AVPVH_PRWB_DIR . 'assets/images/menu-icon.svg'),
                'capability_type' => self::CONTENT,
                'capabilities' => array(
                    'publish_posts' => 'publish_' . self::CONTENTS,
                    'edit_posts' => 'edit_' . self::CONTENTS,
                    'edit_others_posts' => 'edit_' . self::CONTENTS,
                    'delete_posts' => 'delete_' . self::CONTENTS,
                    'delete_others_posts' => 'delete_' . self::CONTENTS,
                    'read_private_posts' => 'read_private_' . self::CONTENTS,
                ),
            )
        );
        $this->setupMapMetaCaps(self::CONTENT);

        add_filter('wp_insert_post_data', [$this, 'makePrintMaterialsAlwaysPrivate']);
        add_filter('rest_post_search_query', [$this, 'includePrivatePrintMaterialsInSearch'], 10, 2);
    }

    /**
     * Sets up the filter to map meta caps
     * @param string $cap_slug
     */
    private function setupMapMetaCaps($cap_slug)
    {
        add_filter(
            'map_meta_cap',
            function ($caps, $cap, $user_id, $args) use ($cap_slug) {
                return $this->mapMetaCap($caps, $cap, $user_id, $args, $cap_slug);
            },
            10,
            4
        );
    }

    /**
     * We wanted print materials to not be public... but then again, we want them to have URLs for easy linking
     * and to appear in link searches. So instead we just make them all private...
     * unless they're a draft or trashed, in which case we leave them alone.
     * @param array $post
     * @return mixed
     */
    public function makePrintMaterialsAlwaysPrivate($post)
    {
        if ($post['post_type'] === self::CONTENT && $post['post_status'] === 'publish') {
            $post['post_status'] = 'private';
        }
        return $post;
    }

    /**
     * And we ask for WP search endpoint to show private posts as well (provided the user can see them).
     * @param array $query_args to pass into WP_Query
     * @param \WP_REST_Request $request
     */
    public function includePrivatePrintMaterialsInSearch($query_args, $request)
    {
        global $current_user;
        if ($current_user instanceof \WP_User && $current_user->ID && current_user_can('read_private_posts')) {
            $query_args['post_status'] = ['publish', 'private'];
        }
        return $query_args;
    }

    /**
     * Based on the post in question, determine which caps are required.
     * @param array $caps
     * @param string $cap
     * @param int $user_id
     * @param array $args
     * @param string $cap_slug
     * @return array
     */
    public function mapMetaCap($caps, $cap, $user_id, $args, $cap_slug)
    {

        /* If editing, deleting, or reading a project, get the post and post type object. */
        if ('edit_' . $cap_slug === $cap || 'delete_' . $cap_slug === $cap || 'read_' . $cap_slug === $cap) {
            $post = get_post($args[0]);
            $post_type = get_post_type_object($post->post_type);

            /* Set an empty array for the caps. */
            $caps = array();
        }

        /* If editing a project, assign the required capability. */
        if ('edit_' . $cap_slug === $cap) {
            if ($user_id === $post->post_author) {
                $caps[] = $post_type->cap->edit_posts;
            } else {
                $caps[] = $post_type->cap->edit_others_posts;
            }
        } elseif ('delete_' . $cap_slug === $cap) {
            /* If deleting a project, assign the required capability. */
            if ($user_id === $post->post_author) {
                $caps[] = $post_type->cap->delete_posts;
            } else {
                $caps[] = $post_type->cap->delete_others_posts;
            }
        } elseif ('read_' . $cap_slug === $cap) {
            /* If reading a private project, assign the required capability. */
            if ('private' !== $post->post_status) {
                $caps[] = 'read';
            } elseif ($user_id === $post->post_author) {
                $caps[] = 'read';
            } else {
                $caps[] = $post_type->cap->read_private_posts;
            }
        }

        /* Return the capabilities required by the user. */
        return $caps;
    }

    /**
     * @return string[]
     */
    public function getPostTypes()
    {
        return [
            self::CONTENT,
            self::DESIGN,
            self::PROJECT,
        ];
    }
}
