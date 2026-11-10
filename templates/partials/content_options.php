<?php
// Template to show the HTML for display options
/**
 * @var $print_options Avpvh\PrintWebsite\domain\PrintOptions
 * @var $displayer Avpvh\PrintWebsite\Twine\services\display\FormInputs
 * @var $upsells boolean
 */

use Avpvh\PrintWebsite\domain\PrintOptions;
use Avpvh\PrintWebsite\services\display\FormInputs;
?>

<h2><?php esc_html_e('Content','avpvh-print-website' );?></h2>
<table class="form-table">
    <tbody>
    <tr>
        <th scope="row">
            <label ><?php esc_html_e('Header Content to Print','avpvh-print-website' ); $upsells ? avpvh_prwb_pro_better_e(__('Pro Print supports full-page title pages, custom title pages, or no title page', 'avpvh-print-website')) : null?></label>
            <p class="description"><?php esc_html_e('Appears at the top of the first page.', 'avpvh-print-website'); ?></p>
        </th>
        <td>
            <?php
            echo $displayer->getHtmlForShortOptions($print_options->headerContentOptions($upsells));
            ?>
        </td>
    </tr>
    <tr>
        <th scope="row"> <?php esc_html_e('Post Content to Print','avpvh-print-website' );?></th>
        <td>
            <?php
            echo $displayer->getHtmlForShortOptions($print_options->postContentOptions());
            ?>
        </td>
    </tr>
    </tbody>
</table>