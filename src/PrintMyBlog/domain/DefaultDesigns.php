<?php

namespace Avpvh\PrintWebsite\domain;

use Avpvh\PrintWebsite\entities\DesignTemplate;
use WP_User;

/**
 * Class DefaultDesigns
 * @package Avpvh\PrintWebsite\domain
 */
class DefaultDesigns
{
    /**
     * Registers designs.
     */
    public function registerDefaultDesigns()
    {

        avpvh_prwb_register_design(
            'classic_digital',
            'classic_digital',
            function (DesignTemplate $design_template) {
                return [
                    'title' => __('Classic Digital PDF', 'avpvh-print-website'),
                    'quick_description' => esc_html__('A simple but flexible design intended mainly for reading from screens, inspired by AV-PvH Print Website Quick Print.', 'avpvh-print-website'),
                    'description' => avpvh_prwb_get_contents($design_template->getDir() . 'description.php'),
                    'author' => [
                        'name' => 'Mike Nelson',
                    ],
                    'previews' => [
                        [
                            'url' => $design_template->getUrl() . 'assets/preview1.jpg',
                            'desc' => __('Title page, with working hyperlinks.', 'avpvh-print-website'),
                        ],
                        [
                            'url' => $design_template->getUrl() . 'assets/preview2.jpg',
                            'desc' => __('Main matter, showing hyperlinks and large images.', 'avpvh-print-website'),
                        ],
                    ],
                    'design_defaults' => [
                        'use_title' => true,
                        'image_size' => 800,
                        'font_style' => 'times',
                        'header_font_style' => 'arial',
                    ],
                    'project_defaults' => [
                        'title' => get_bloginfo('name'),
                    ],
                ];
            }
        );
        avpvh_prwb_register_design(
            'classic_print',
            'editorial_review',
            function (DesignTemplate $design_template) {
                $preview_folder_url = AVPVH_PRWB_ASSETS_URL . '/images/design_previews/pdf/print/edit/';
                return [
                    'title' => __('Editorial Review', 'avpvh-print-website'),
                    'quick_description' => __('Your writing in an easy-to-review format for editors.', 'avpvh-print-website'),
                    'description' => avpvh_prwb_get_contents($design_template->getDir() . 'descriptions/edit.php'),
                    'author' => [
                        'name' => 'Mike Nelson',
                    ],
                    'previews' => [
                        [
                            'url' => $preview_folder_url . '/preview1.jpg',
                            'desc' => __('Title page, showing the double-spaced text.', 'avpvh-print-website'),
                        ],
                        [
                            'url' => $preview_folder_url . '/preview2.jpg',
                            'desc' => __('Main matter, showing smaller images and double-spaced text.', 'avpvh-print-website'),
                        ],
                    ],
                    'design_defaults' => [
                        'header_content' => [
                            'title',
                            'subtitle',
                            'url',
                            'date_printed',
                        ],
                        'post_content' => [
                            'title',
                            'id',
                            'author',
                            'url',
                            'published_date',
                            'categories',
                            'featured_image',
                            'excerpt',
                            'content',
                        ],
                        'page_per_post' => true,
                        'image_size' => 200,
                        'custom_css' => 'article{line-height:2;}',
                    ],
                    'project_defaults' => [
                        'title' => get_bloginfo('name'),
                    ],
                ];
            }
        );
        avpvh_prwb_register_design(
            'classic_print',
            'classic_print',
            function (DesignTemplate $design_template) {
                return [
                    'title' => __('Classic Print PDF', 'avpvh-print-website'),
                    'quick_description' => __('A simple but flexible design intended for printing, inspired by AV-PvH Print Website Quick Print', 'avpvh-print-website'),
                    'description' => avpvh_prwb_get_contents($design_template->getDir() . 'descriptions/classic.php'),
                    'author' => [
                        'name' => 'Mike Nelson',
                    ],
                    'previews' => [
                        [
                            'url' => $design_template->getUrl() . 'assets/preview1.jpg',
                            'desc' => __('Title page, showing removed hyperlinks.', 'avpvh-print-website'),
                        ],
                        [
                            'url' => $design_template->getUrl() . 'assets/preview2.jpg',
                            'desc' => __('Main matter, showing external hyperlinks automatically converted into footnotes. Page numbers are always on the bottom-outside corner, and each article’s title is shown at the top of right pages.', 'avpvh-print-website'),
                        ],
                    ],
                    'design_defaults' => [
                        'use_title' => true,
                        'image_size' => 400,
                        'font_style' => 'times',
                        'header_font_style' => 'palatino linotype',
                    ],
                    'project_defaults' => [
                        'title' => get_bloginfo('name'),
                    ],
                ];
            }
        );
        avpvh_prwb_register_design(
            'classic_print',
            'economical_print',
            function (DesignTemplate $design_template) {
                $preview_folder_url = AVPVH_PRWB_ASSETS_URL . 'images/design_previews/pdf/print/economical/';
                return [
                    'title' => __('Economical Print PDF', 'avpvh-print-website'),
                    'quick_description' => __('Compact design meant to save paper but still deliver all the content.', 'avpvh-print-website'),
                    'description' => avpvh_prwb_get_contents($design_template->getDir() . 'descriptions/economical.php'),
                    'author' => [
                        'name' => 'Mike Nelson',
                    ],
                    'previews' => [
                        [
                            'url' => $preview_folder_url . 'preview1.jpg',
                            'desc' => __('Title page, showing smaller text.', 'avpvh-print-website'),
                        ],
                        [
                            'url' => $preview_folder_url . 'preview2.jpg',
                            'desc' => __(
                                'Main matter, showing smaller text and images to reduce ink usage.',
                                'avpvh-print-website'
                            ),
                        ],
                    ],
                    'design_defaults' => [
                        'header_content' => [
                            'title',
                            'url',
                            'date_printed',
                        ],
                        'post_content' => [
                            'title',
                            'featured_image',
                            'content',
                        ],
                        'page_per_page' => false,
                        'font_size' => '9pt',
                        'image_size' => 150,
                        // purposefully leave hyperlink defaults dynamic
                    ],
                    'project_defaults' => [
                        'title' => get_bloginfo('name'),
                    ],
                ];
            }
        );
        avpvh_prwb_register_design(
            'buurma',
            'buurma',
            function (DesignTemplate $design_template) {
                $current_user = wp_get_current_user();
                if ($current_user instanceof WP_User && $current_user->exists()) {
                    $name = $current_user->first_name . ' ' . $current_user->last_name;
                } else {
                    $name = '';
                }
                return [
                    'title' => __('Buurma Whitepaper', 'avpvh-print-website'),
                    'quick_description' => __('Stylized and branded PDF designed mainly for reading from a device, great for organizations', 'avpvh-print-website'),
                    'description' => avpvh_prwb_get_contents($design_template->getDir() . 'description.php'),
                    'author' => [
                        'name' => 'Mike Nelson',
                    ],
                    'previews' => [
                        [
                            'url' => $design_template->getUrl() . 'assets/preview1.jpg',
                            'desc' => __('Title page, showing a stylzed upper margin for a company name, background gradient and logo, among other things.', 'avpvh-print-website'),
                        ],
                        [
                            'url' => $design_template->getUrl() . 'assets/preview2.jpg',
                            'desc' => __('Main matter, showing working hyperlinks (which also each get an automatic footnote), and page number and logo in bottom-right corner.', 'avpvh-print-website'),
                        ],

                    ],
                    'design_defaults' => [],
                    'project_defaults' => [
                        'title' => get_bloginfo('name'),
                        'byline' => $name,
                        'issue' => __('Issue 01', 'avpvh-print-website'),
                        'cover_preamble' => __('Text explaining the purpose of the paper and gives a brief summary of it, so folks know they’re reading the right thing.', 'avpvh-print-website'),
                    ],
                ];
            }
        );
        avpvh_prwb_register_design(
            'mayer',
            'mayer',
            function (DesignTemplate $design_template) {
                return [
                    'title' => __('Mayer Magazine', 'avpvh-print-website'),
                    'quick_description' => __('Digital 2-column magazine inspired by the defunct Zinepal', 'avpvh-print-website'),
                    'description' => avpvh_prwb_get_contents($design_template->getDir() . 'description.php'),
                    'author' => [
                        'name' => 'Mike Nelson',
                    ],
                    'previews' => [
                        [
                            'url' => $design_template->getUrl() . 'assets/preview1.jpg',
                            'desc' => __(
                                'Title page and table of contents both fit on the first page.',
                                'avpvh-print-website'
                            ),
                        ],
                        [
                            'url' => $design_template->getUrl() . 'assets/preview2.jpg',
                            'desc' => __(
                                'Two column layout which compactly shows content and images.',
                                'avpvh-print-website'
                            ),
                        ],
                    ],
                    'design_defaults' => [
                        'page_per_post' => false,
                        'post_header_in_columns' => false,
                    ],
                    'project_defaults' => [
                        'title' => get_bloginfo('name'),
                    ],
                ];
            }
        );

        avpvh_prwb_register_design(
            'classic_epub',
            'classic_epub',
            function (DesignTemplate $design_template) {
                return [
                    'title' => __('Classic ePub', 'avpvh-print-website'),
                    'quick_description' => __('Simple ePub for using when uploading as an eBook to Amazon and other epub marketplaces', 'avpvh-print-website'),
                    'description' => avpvh_prwb_get_contents($design_template->getDir() . 'description.php'),
                    'author' => [
                        'name' => 'Mike Nelson',
                    ],
                    'previews' => [
                        [
                            'url' => $design_template->getUrl() . 'assets/preview1.jpg',
                            'desc' => __(
                                'Title page and table of contents both fit on the first page.',
                                'avpvh-print-website'
                            ),
                        ],
                        [
                            'url' => $design_template->getUrl() . 'assets/preview2.jpg',
                            'desc' => __(
                                'Two column layout which compactly shows content and images.',
                                'avpvh-print-website'
                            ),
                        ],
                    ],
                    'design_defaults' => [
                        'header_content' => [
                            'title',
                            'subtitle',
                            'url',
                            'date_printed',
                        ],
                        'post_content' => [
                            'title',
                            'id',
                            'author',
                            'url',
                            'published_date',
                            'categories',
                            'featured_image',
                            'excerpt',
                            'content',
                        ],
                    ],
                    'project_defaults' => [],
                ];
            }
        );
        avpvh_prwb_register_design(
            'classic_word',
            'classic_word',
            function (DesignTemplate $design_template) {
                return [
                    'title' => __('Classic Word', 'avpvh-print-website'),
                    'quick_description' => __('Simple Microsoft Word document when that format is required.', 'avpvh-print-website'),
                    'description' => avpvh_prwb_get_contents($design_template->getDir() . 'description.php'),
                    'author' => [
                        'name' => 'Mike Nelson',
                    ],
                    'previews' => [
                        [
                            'url' => $design_template->getUrl() . 'assets/preview1.jpg',
                            'desc' => __(
                                'Title page and table of contents.',
                                'avpvh-print-website'
                            ),
                        ],
                        [
                            'url' => $design_template->getUrl() . 'assets/preview2.jpg',
                            'desc' => __(
                                'Simple layout',
                                'avpvh-print-website'
                            ),
                        ],
                    ],
                    'design_defaults' => [
                        'header_content' => [
                            'title',
                            'subtitle',
                            'url',
                            'date_printed',
                        ],
                        'post_content' => [
                            'title',
                            'id',
                            'author',
                            'url',
                            'published_date',
                            'categories',
                            'featured_image',
                            'excerpt',
                            'content',
                        ],
                    ],
                    'project_defaults' => [],
                ];
            }
        );

        avpvh_prwb_register_design(
            'haller',
            'haller',
            function (DesignTemplate $design_template) {
                return [
                    'title' => __('Haller Tabloid', 'avpvh-print-website'),
                    'quick_description' => __('Print-ready newspaper design', 'avpvh-print-website'),
                    'description' => avpvh_prwb_get_contents($design_template->getDir() . 'description.php'),
                    'author' => [
                        'name' => 'Mike Nelson',
                    ],
                    'previews' => [
                        [
                            'url' => $design_template->getUrl() . 'assets/preview1.jpg',
                            'desc' => __(
                                'Title page and table of contents both fit on the first page.',
                                'avpvh-print-website'
                            ),
                        ],
                        [
                            'url' => $design_template->getUrl() . 'assets/preview2.jpg',
                            'desc' => __(
                                'Two column layout which compactly shows content and images.',
                                'avpvh-print-website'
                            ),
                        ],
                    ],
                    'design_defaults' => [
                        'page_per_post' => false,
                        'post_header_in_columns' => false,
                    ],
                    'project_defaults' => [
                        'title' => get_bloginfo('name'),
                    ],
                ];
            }
        );


        do_action('avpvh_prwb_register_designs');
    }
}
