<?php
// Template to show the HTML for display options
/**
 * @var $print_options Avpvh\PrintWebsite\domain\PrintOptions
 * @var $displayer Avpvh\PrintWebsite\Twine\services\display\FormInputs
 * @var $upsells boolean
 */

avpvh_prwb_render_template('partials/content_options.php',['print_options' => $print_options, 'displayer' => $displayer, 'upsells' => $upsells]);
avpvh_prwb_render_template('partials/layout_options.php', ['print_options' => $print_options, 'displayer' => $displayer, 'upsells' => $upsells]);
include('debug_options.php');