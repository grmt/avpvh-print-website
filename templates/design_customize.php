<?php
/**
 * @var $form \Avpvh\PrintWebsite\Twine\forms\base\FormSection
 * @var $form_url string
 * @var $design \Avpvh\PrintWebsite\orm\entities\Design
 * @var $steps_to_urls array
 * @var $current_step string
 */
// outputs the form for the design
?>
<div class="wrap nosubsub">
    <h1 class="wp-heading-inline"><?php _e('Pro Print ― Customize Design', 'avpvh-print-website');?></h1>
<?php
avpvh_prwb_render_template(
'partials/customize_design.php',
    [
    'design'=> $design,
    'form' => $form,
    'form_url' => $form_url
    ]
);
?>
</div>
