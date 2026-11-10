<?php
// Template to show the HTML for layout options
/**
 * @var $print_options Avpvh\PrintWebsite\domain\PrintOptions
 * @var $displayer Avpvh\PrintWebsite\Twine\services\display\FormInputs
 * @var $upsells boolean
 */

use Avpvh\PrintWebsite\domain\PrintOptions;
use Avpvh\PrintWebsite\services\display\FormInputs;

?>
<h2><?php esc_html_e('Page Layout','avpvh-print-website' );?></h2>
<table class="form-table">
    <tbody>
    <?php echo $displayer->getHtmlForTabledOptions($print_options->pageLayoutOptions($upsells));?>
    </tbody>
</table>