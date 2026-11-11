<?php

namespace Avpvh\PrintWebsite\domain;

/**
 * Class PrintOptions
 *
 * Description
 *
 * @package     AV-PvH Print Website
 * @author         Mike Nelson
 * @since         $VID:$
 *
 */
class PrintOptions
{
    /**
     * @param bool $upsells
     * @return array[]
     */
    public function headerContentOptions($upsells = false)
    {
        return [
            'show_site_title' => [
                'label' => esc_html__('Site Title', 'avpvh-print-website'),
                'default' => true,
            ],
            'show_site_tagline' => [
                'label' => esc_html__('Tagline', 'avpvh-print-website'),
                'default' => true,
            ],
            'show_site_url' => [
                'label' => esc_html__('Site URL', 'avpvh-print-website'),
                'default' => true,
            ],
            'show_filters' => [
                'label' => esc_html__('Filters Used', 'avpvh-print-website'),
                'default' => true,
                'help' => esc_html__('E.g. selected categories, taxonomies, and date range.', 'avpvh-print-website'),
            ],
            'show_date_printed' => [
                'label' => esc_html__('Date Printed', 'avpvh-print-website'),
                'default' => true,
            ],
            'show_credit' => [
                'label' => esc_html__('Credit AV-PvH Print Website Plugin', 'avpvh-print-website'),
                'default' => true,
                'help' => sprintf(
                    // translators: 1: heart emoji
                    esc_html__('Show some love and tell your readers about AV-PvH Print Website %1$s', 'avpvh-print-website'),
                    '❤️'
                ),
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function postContentOptions()
    {
        return [
            'show_title' => [
                'label' => esc_html__('Title', 'avpvh-print-website'),
                'default' => true,
            ],
            'show_id' => [
                'label' => esc_html__('ID', 'avpvh-print-website'),
                'default' => false,
            ],
            'show_author' => [
                'label' => esc_html__('Author', 'avpvh-print-website'),
                'default' => false,
            ],
            'show_url' => [
                'label' => esc_html__('URL', 'avpvh-print-website'),
                'default' => false,
            ],
            'show_date' => [
                'label' => esc_html__('Published Date', 'avpvh-print-website'),
                'default' => true,
            ],
            'show_categories' => [
                'label' => esc_html__('Categories and Tags', 'avpvh-print-website'),
                'default' => true,
            ],
            'show_featured_image' => [
                'label' => esc_html__('Featured Image', 'avpvh-print-website'),
                'default' => true,
            ],
            'show_excerpt' => [
                'label' => esc_html__('Excerpt', 'avpvh-print-website'),
                'default' => false,
            ],
            'show_content' => [
                'label' => esc_html__('Content', 'avpvh-print-website'),
                'default' => true,
            ],
            'show_comments' => [
                'label' => esc_html__('Comments', 'avpvh-print-website'),
                'default' => false,
            ],
            'show_divider' => [
                'label' => esc_html__('Extra dividing line at end of post', 'avpvh-print-website'),
                'default' => false,
            ],
        ];
    }

    /**
     * @param bool $upsells
     * @return array[]
     */
    public function pageLayoutOptions($upsells = false)
    {
        return [
            'post_page_break' => [
                'label' => esc_html__('Each Post Begins on a New Page', 'avpvh-print-website'),
                'default' => true,
                //phpcs:disable Generic.Files.LineLength.TooLong
                'help' => esc_html__('Whether to force posts to always start on a new page. Doing so makes the page more legible, but uses more paper.', 'avpvh-print-website'),
                //phpcs:enable
            ],
            'columns' => [
                'label' => esc_html__('Columns', 'avpvh-print-website'),
                'default' => 1,
                'options' => [
                    1 => esc_html__('1', 'avpvh-print-website'),
                    2 => esc_html__('2', 'avpvh-print-website'),
                    3 => esc_html__('3', 'avpvh-print-website'),
                ],
                //phpcs:disable Generic.Files.LineLength.TooLong
                'help' => esc_html__('The number of columns of text on each page. Not supported by some web browsers.', 'avpvh-print-website'),
                //phpcs:enable
            ],
            'font_size' => [
                'label' => esc_html__('Font Size', 'avpvh-print-website') . ($upsells ? avpvh_prwb_pro_better(__('Saved Prints lets you specify header and text size and font', 'avpvh-print-website')) : ''),
                'default' => 'normal',
                'options' => [
                    'tiny' => esc_html__('Tiny (1/2 size)', 'avpvh-print-website'),
                    'small' => esc_html__('Small (3/4 size)', 'avpvh-print-website'),
                    'normal' => esc_html__('Normal (theme default)', 'avpvh-print-website'),
                    'large' => esc_html__('Large (slightly larger than normal)', 'avpvh-print-website'),
                ],
            ],
            'image_size' => [
                'label' => esc_html__('Image Size', 'avpvh-print-website'),
                'default' => 'medium',
                'options' => [
                    'full' => esc_html__('Full (theme default)', 'avpvh-print-website'),
                    'large' => esc_html__('Large (3/4 size)', 'avpvh-print-website'),
                    'medium' => esc_html__('Medium (1/2 size)', 'avpvh-print-website'),
                    'small' => esc_html__('Small (1/4 size)', 'avpvh-print-website'),
                    'none' => esc_html__('None (hide images)', 'avpvh-print-website'),
                ],
                //phpcs:disable Generic.Files.LineLength.TooLong
                'help' => esc_html__('If you want to save paper, choose a smaller image size, or hide images altogether.', 'avpvh-print-website'),
                //phpcs:enable
            ],
            'links' => [
                'label' => esc_html__('Include Hyperlinks', 'avpvh-print-website') . ($upsells ? avpvh_prwb_pro_better(__('Saved Prints with Pro PDF Service supports changing hyperlinks to page references or footnotes', 'avpvh-print-website')) : ''),
                'default' => 'include',
                'options' => [
                    'include' => esc_html__('Include', 'avpvh-print-website'),
                    'remove' => esc_html__('Remove', 'avpvh-print-website'),
                    'parens' => esc_html__('Replace with URL in Parenthesis', 'avpvh-print-website'),
                ],
                //phpcs:disable Generic.Files.LineLength.TooLong
                'help' => esc_html__('Note: PDFs generated in Firefox and Safari automatically remove hyperlinks. If you want your PDF to include hyperlinks, please use another browser.', 'avpvh-print-website'),
                //phpcs:enable
            ],
        ];
    }

    /**
     * @since $VID:$
     * @return array
     */
    public function troubleshootingOptions()
    {
        return [
            'rendering_wait' => [
                'label' => esc_html__('Post Rendering Wait-Time', 'avpvh-print-website'),
                //phpcs:disable Generic.Files.LineLength.TooLong
                'default' => apply_filters('Avpvh\PrintWebsite\domain\PrintOptions->troubleshootingOptions rendering_wait default', 200),
                //phpcs:enable
                'after_input' => esc_html__('ms', 'avpvh-print-website'),
                //phpcs:disable Generic.Files.LineLength.TooLong
                'help' => esc_html__('Milliseconds to wait between rendering posts. If posts are rendered too quickly on the page, sometimes images won’t load properly. ', 'avpvh-print-website'),
                //phpcs:enable
            ],
            'include_inline_js' => [
                'label' => esc_html__('Include Inline Javascript', 'avpvh-print-website'),
                'default' => false,
                //phpcs:disable Generic.Files.LineLength.TooLong
                'help' => esc_html__('Sometimes posts contain inline javascript which can cause errors and stop the page from rendering.', 'avpvh-print-website'),
                //phpcs:enable
            ],
            'shortcodes' => [
                'label' => esc_html__('Include Unrendered Shortcodes', 'avpvh-print-website'),
                'default' => false,
                //phpcs:disable Generic.Files.LineLength.TooLong
                'help' => esc_html__('If you left shortcodes from deactivated plugins or themes in your posts or have other content enclosed by square brackets, they are automatically removed from printouts. Check this to leave them.', 'avpvh-print-website'),
                //phpcs:enable
            ],
            'exclude_external_content' => [
                'label' => esc_html__('Exclude External Content', 'avpvh-print-website'),
                'default' => false,
                'help' => esc_html__('Do not load external images, embeds, media, or external hyperlinks in the print page. Useful when you want a self-contained printout.', 'avpvh-print-website'),
            ],
        ];
    }

    /**
     * @param string $format
     * @return array
     */
    protected function defaultOverrides($format)
    {
        $overrides = [];
        switch ($format) {
            case 'pdf':
                break;
            case 'print':
            default:
                $overrides['links'] = 'parens';
        }
        return $overrides;
    }

    /**
     * Returns the print options
     * @return array
     */
    public function allPrintOptions()
    {
        return array_merge(
            $this->troubleshootingOptions(),
            $this->pageLayoutOptions(),
            $this->headerContentOptions(),
            $this->postContentOptions()
        );
    }

    /**
     * @since $VID:$
     * @param string $format
     * @return array keys are option names, values are their default values
     */
    public function allPrintOptionDefaults($format = 'print')
    {
        $all = $this->allPrintOptions();
        $defaults = [];
        foreach ($all as $name => $details) {
            $defaults[$name] = $details['default'];
        }
        return array_merge(
            $defaults,
            $this->defaultOverrides($format)
        );
    }
}
// End of file PrintOptions.php
// Location: ${NAMESPACE}/PrintOptions.php
