<?php

namespace Avpvh\PrintWebsite\domain;

/**
 * Class DefaultSectionTemplates
 * @package Avpvh\PrintWebsite\domain
 */
class DefaultSectionTemplates
{
    /**
     * Registers default section templates.
     */
    public function registerDefaultSectionTemplates()
    {
        avpvh_prwb_register_section_template(
            'single_column',
            [
                'mayer',
                'haller',
            ],
            function () {
                return [
                    'title' => __('Single Column', 'avpvh-print-website'),
                    'fallback' => '',
                ];
            }
        );
        avpvh_prwb_register_section_template(
            'just_content',
            [
                'classic_digital',
                'buurma',
                'mayer',
                'classic_print',
                'classic_epub',
                'classic_word',
                'haller',
            ],
            function () {
                return [
                    'title' => __('Fullpage Content', 'avpvh-print-website'),
                    'fallback' => 'article',
                ];
            }
        );
        avpvh_prwb_register_section_template(
            'center_content',
            [
                'classic_digital',
                'buurma',
                'mayer',
                'classic_print',
            ],
            function () {
                return [
                    'title' => __('Centered Content', 'avpvh-print-website'),
                    'fallback' => 'just_content',
                ];
            }
        );

        avpvh_prwb_register_section_template(
            'just_content_in_columns',
            [
                'mayer',
                'haller',
            ],
            function () {
                return [
                    'title' => __('Full Column Content', 'avpvh-print-website'),
                    'fallback' => 'just_content',
                ];
            }
        );
        avpvh_prwb_register_section_template(
            'important_article',
            [
                'haller',
            ],
            function () {
                return [
                    'title' => __('Important', 'avpvh-print-website'),
                    'fallback' => '',
                ];
            }
        );
    }
}
