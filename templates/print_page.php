<?php
/**
 * The header for our printing
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */
/**
 * @var string $avpvh_prwb_download_file_name
 */
if(apply_filters('pmb-print-page-treat-as-single', true)){
    $wp_query->is_home = false;$wp_query->is_single = true;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <meta name="robots" content="noindex,nofollow">

    <?php wp_head(); ?>
</head>

<body class="<?php echo str_replace('has-sidebar', '', implode(' ',get_body_class('pmb-print-page pmb-format-' . $avpvh_prwb_format))); ?>">
<!-- AV-PvH Print Website Version <?php echo AVPVH_PRWB_VERSION;?>-->
<div class="pmb-waiting-message-fullpage pmb-extra-content">
    <div class="pmb-waiting-message-outer-container">
        <div class="pmb-window-buttons pmb-top-left pmb-small-instructions">
            <span class="pmb-loading-content">
                <a href="javascript:history.back();">❌
                    <?php esc_html_e('Cancel', 'avpvh-print-website'); ?>
                </a>
            </span>
            <span class="pmb-print-ready">
                <a href="javascript:history.back();">✅
                    <?php esc_html_e('Return', 'avpvh-print-website'); ?>
                </a>
            </span>
        </div>
        <div class="pmb-waiting-area">
            <h1 id='pmb-in-progress-h1' class="pmb-waiting-h1"><?php _e('Initializing', 'avpvh-print-website'); ?></h1>
        </div>
        <div class="pmb-print-ready pmb-print-instructions">
            <?php do_action('avpvh_prwb_print_page_ready_instructions_start');?>
            <?php
            if ($avpvh_prwb_format === 'pdf') {
                if(in_array($avpvh_prwb_browser, ['chrome','desktop_safari'])) {
                    ?>
                    <input disabled="disabled" class="pmb-print-page-print-button" type="submit" onclick="window.print()"
                           value="<?php esc_attr_e('Print to PDF', 'avpvh-print-website'); ?>"/><?php
                } else {
                    ?>
                    <p><?php esc_html_e('Use your browser to print to PDF.', 'avpvh-print-website'); ?></p>
                    <?php
                }
                // Browser-specific "how to save as PDF" hints.
                {
                    if ($avpvh_prwb_browser === 'firefox') {
                        ?>
                        <details class="pmb-details">
                            <summary class="pmb-reveal-options pmb-inline"><?php esc_html_e('How?', 'avpvh-print-website'); ?>
                            </summary>
                            <div class="pmb-reveal-details">
                                <ol>
                                    <li><a href="https://addons.mozilla.org/en-US/firefox/addon/print-to-pdf-document/" target="_blank" title="<?php esc_html_e('opens in new tab', 'avpvh-print-website'); ?>"><?php esc_html_e('Download Print to PDF Browser Extension', 'avpvh-print-website'); ?></a></li>
                                    <li><?php esc_html_e('Click the extension’s button.', 'avpvh-print-website'); ?></li>
                                </ol>
                            </div>
                        </details>
                        <?php
                    } elseif($avpvh_prwb_browser === 'chrome') {
                        ?>
                        <details class="pmb-details">
                            <summary class="pmb-reveal-options pmb-inline"><?php esc_html_e('How?', 'avpvh-print-website'); ?>
                            </summary>
                            <div class="pmb-reveal-details">
                                <p><?php esc_html_e('After clicking "Print to PDF", set the "Destination" to "Save as PDF."', 'avpvh-print-website'); ?></p>
                            </div>
                        </details>
                        <?php
                    } elseif($avpvh_prwb_browser === 'desktop_safari') {
                        ?>
            <details class="pmb-details">
                <summary
                        class="pmb-reveal-options pmb-inline"><?php esc_html_e('How on Safari on Desktop', 'avpvh-print-website'); ?>
                </summary>
                <div class="pmb-reveal-details">
                    <ol>
                        <li><?php esc_html_e('Click "Print to PDF"', 'avpvh-print-website'); ?></li>
                        <li><?php esc_html_e('Choose to "Save as PDF."', 'avpvh-print-website'); ?></li>
                    </ol>
                </div>
            </details>
            <?php
                    } elseif( $avpvh_prwb_browser === 'mobile_safari'){
                        ?>
                        <details class="pmb-details">
                            <summary class="pmb-reveal-options pmb-inline"><?php esc_html_e('How on Mobile Safari', 'avpvh-print-website'); ?>
                            </summary>
                            <div class="pmb-reveal-details">
                                <ol>
                                    <li>
                                        <?php esc_html_e('Click the share button', 'avpvh-print-website'); ?>
                                    </li>
                                    <li>
                                        <?php esc_html_e('Click "Options" and choose PDF', 'avpvh-print-website'); ?>
                                    </li>
                                    <li>
                                        <?php esc_html_e('Save to Files', 'avpvh-print-website'); ?>
                                    </li>
                                </ol>
                            </div>
                        </details>
                        <?php
                    }
                }
                ?><p class="pmb-help"><?php esc_html_e('Note: Google Chrome and Microsoft Edge are recommended for producing PDFs.', 'avpvh-print-website');?></_E></_E></_E></p><?php
            } elseif($avpvh_prwb_format === 'html'){// HTML
                ?>
                <input type="submit" onclick="avpvh_prwb_copy()" value="<?php esc_attr_e('Copy to Clipboard', 'avpvh-print-website'); ?>"/>
                <?php
            } elseif ($avpvh_prwb_format === 'epub') {
                ?>
                <a class="button button-primary pmb-disabled" id="download_link" download="<?php echo esc_attr($avpvh_prwb_download_file_name);?>" href="javascript:void(0)"><?php esc_html_e('Download ePub', 'avpvh-print-website'); ?></a>
                <div class="pmb-small-instructions"><?php esc_html_e('Build an ePub file locally in your browser.', 'avpvh-print-website'); ?></div>
                <div style="display:none" class="pmb-warning" id="pmb-print-page-warnings"></div>
                <?php
            } elseif ($avpvh_prwb_format === 'word') {
                ?>
                <a class="button button-primary pmb-disabled" id="download_link" download="<?php echo esc_attr($avpvh_prwb_download_file_name);?>" href="javascript:void(0)"><?php esc_html_e('Download Word Document', 'avpvh-print-website'); ?></a>
                <div class="pmb-small-instructions"><?php esc_html_e('Build a Word-compatible document locally in your browser.', 'avpvh-print-website'); ?></div>
                <div style="display:none" class="pmb-warning" id="pmb-print-page-warnings"></div>
                <?php
            } else { // default: print
                ?>
                <input type="submit" disabled="disabled" class="pmb-print-page-print-button"  onclick="window.print()" value="<?php esc_attr_e('Print', 'avpvh-print-website'); ?>"/>
                <div class="pmb-small-instructions"><?php esc_html_e('Use your browser to print.', 'avpvh-print-website'); ?></div>
                <?php
            }
            ?>
        </div>
        <div class="pmb-posts-placeholder pmb-extra-content">
            <div class="pmb-spinner-container">
                <div class="pmb-spinner"></div>
            </div>
            <p class="pmb-status"><span class="pmb-posts-count"></span></p>
        </div>
    </div>
</div>

<div class="pmb-posts site pmb-project-content" id="content">
    <?php
    if (in_array($avpvh_prwb_format,['print','pdf'] )){
    ?>
    <div class="pmb-preview-note"><?php esc_html_e('Use your browser’s "print preview" for the best preview.', 'avpvh-print-website'); ?></div>
    <?php } ?>
    <div class="pmb-posts-header">
        <?php
        ?>

            <?php if ($avpvh_prwb_show_site_title) { ?>
                <h1 class="site-title"><?php echo $avpvh_prwb_site_name; ?></h1>
            <?php } ?>
            <?php if ($avpvh_prwb_show_site_tagline) { ?>
                <p class="site-description"><?php echo $avpvh_prwb_site_description; ?></p>
            <?php } ?>
            <?php
            if ($avpvh_prwb_show_filters) {
                // If they specified an after date, show it
                if ($avpvh_prwb_after_date && $avpvh_prwb_before_date) {
                    if($avpvh_prwb_after_date === $avpvh_prwb_before_date){
                        $date_range_string = sprintf('published on %1$s,', $avpvh_prwb_after_date);
                    } else {
                        $date_range_string = sprintf('published from %1$s to %2$s,', $avpvh_prwb_after_date, $avpvh_prwb_before_date);
                    }
                } elseif ($avpvh_prwb_after_date && !$avpvh_prwb_before_date) {
                    $date_range_string = sprintf(
                    // translators: 1: date string
                        esc_html__('published on %1$s and later,', 'avpvh-print-website'),
                        $avpvh_prwb_after_date
                    );
                } elseif (!$avpvh_prwb_after_date && $avpvh_prwb_before_date) {
                    $date_range_string = sprintf(
                    // translators: 1: date string
                        esc_html__('published on %1$s and earlier,', 'avpvh-print-website'),
                        $avpvh_prwb_before_date
                    );
                } else {
                    $date_range_string = '';
                }

                // Figure out taxonomy filters used and how to display them.
                $taxonomy_filters_strings = array();
                foreach ($avpvh_prwb_taxonomy_filters as $taxonomy_filter) {
                    $taxonomy = $taxonomy_filter['taxonomy'];
                    $terms = $taxonomy_filter['terms'];
                    $taxonomy_filters_strings[] = sprintf(
                    // translators: 1 taxonomy name, 2: list of terms that apply to this post
                        esc_html__('%1$s: %2$s', 'avpvh-print-website'),
                        count($terms) > 1 ? $taxonomy->labels->name : $taxonomy->labels->singular_name,
                        implode(', ', $terms)
                    );

                }
                $filters_string = count($taxonomy_filters_strings) ?
                    sprintf(
                    // @translators: $s, the categories and terms used to filter this print-out,
                    //  eg "Category: WordPress, Blogging; Term: Computers, Headphones".
                        esc_html__('with %s,', 'avpvh-print-website'),
                        implode('; ', $taxonomy_filters_strings)
                    ) :
                    '';
                if($avpvh_prwb_author){
                    $filters_string .= ' ' . sprintf(esc_html__('by %s', 'avpvh-print-website'), $avpvh_prwb_author->display_name);
                }
                $content_description = $avpvh_prwb_post_type . ' ' . $date_range_string . ' ' . $filters_string;

                ?>
                <?php
            } else {
                $content_description = '';
            }
            ?>
            <p class="pmb-printout-meta">
                <?php
                if ($avpvh_prwb_show_site_url && $content_description) {
                    printf(
                    // translators: 1 description of printout, 2: site URL
                        esc_html__('%1$s from %2$s.', 'avpvh-print-website'),
                        $content_description,
                        $avpvh_prwb_site_url
                    );
                } elseif ($avpvh_prwb_show_site_url && ! $content_description) {
                        echo $avpvh_prwb_site_url;
                } elseif ($content_description) {
                    // if we're not showing the site's URL, but there's a content description to show, by all means...
                    // it should still be shown.
                    printf(
                        _x(
                            '%s.',
                            'Description of what\'s in the printout. Eg the post type, dante range, and filters.',
                            'avpvh-print-website'),
                        $content_description
                    );
                }
                ?><?php
                //give it some space
                echo ' ';
                if ($avpvh_prwb_show_date_printed && $avpvh_prwb_show_credit) {

                    printf(
                    // translators: 1: date, 2: opening link tag, 3: closing link tag
                        esc_html__('Printed on %1$s using %2$sAV-PvH Print Website%3$s', 'avpvh-print-website'),
                        date_i18n(get_option('date_format')),
                        '',
                        ''
                    );
                } elseif ($avpvh_prwb_show_date_printed) {
                    // translators: 1: date
                    printf(
                        esc_html__('Printed on %1$s', 'avpvh-print-website'),
                        date_i18n(get_option('date_format'))
                    );
                } elseif ($avpvh_prwb_show_credit) {
                    printf(
                        esc_html__('Printed using %1$sAV-PvH Print Website%2$s', 'avpvh-print-website'),
                        '',
                        ''
                    );
                }
                do_action('avpvh_prwb_print_page_after_printed_on', $avpvh_prwb_show_date_printed, $avpvh_prwb_show_credit);
                ?></p>
            <?php
            // Close the print page header and open the posts body.
            ?>
        </div>
        <div class="pmb-posts-body">
            <?php
            ?>


        </div>
        <?php do_action('avpvh_prwb_print_page_after_posts');?>
    </div>
    <?php wp_footer(); ?>

</body>
</html>
