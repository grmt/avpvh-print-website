<?php
// Template to show the HTML for layout options
/**
 * @var $print_options Avpvh\PrintWebsite\domain\PrintOptions
 * @var $displayer Avpvh\PrintWebsite\Twine\services\display\FormInputs
 */

use Avpvh\PrintWebsite\domain\PrintOptions;
use Avpvh\PrintWebsite\services\display\FormInputs;

?>
<details class="pmb-details">
    <summary class="pmb-reveal-options"><?php esc_html_e('Troubleshooting Options','avpvh-print-website' );?></summary>
    <div class="pmb-reveal-details">
        <table class="form-table">
            <tbody>
            <?php echo $displayer->getHtmlForTabledOptions($print_options->troubleshootingOptions());?>
            </tbody>
        </table>
    </div>
</details>
