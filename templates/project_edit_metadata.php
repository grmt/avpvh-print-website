<?php
/**
 * @var $project_form \Avpvh\PrintWebsite\Twine\forms\base\FormSection
 * @var $form_url string
 * @var $steps_to_urls array
 * @var $current_step string
 */
avpvh_prwb_render_template(
	'partials/project_header.php',
	[
		'project' => $project,
		'page_title' => __('Edit Project Metadata', 'avpvh-print-website'),
		'current_step' => $current_step,
		'steps_to_urls' => $steps_to_urls
	]
);
?>
<form method="POST" action="<?php echo esc_attr($form_url);?>">
    <?php echo $form->getHtmlAndJs();?>
    <button class="button button-primary pmb-save"><?php esc_html_e('Save & Proceed', 'avpvh-print-website');?></button>
</form>
<?php avpvh_prwb_render_template('partials/project_footer.php');