<?php
use Avpvh\PrintWebsite\controllers\Admin;
/**
 * @var $form \Avpvh\PrintWebsite\Twine\forms\base\FormSection
 * @var $steps_to_urls array
 * @var $current_step string
 * @var $project \Avpvh\PrintWebsite\orm\entities\Project|null
 */
avpvh_prwb_render_template(
        'partials/project_header.php',
        [
                'project' => $project,
                'page_title' => __('Edit Saved Print', 'avpvh-print-website'),
                'current_step' => $current_step,
                'steps_to_urls' => $steps_to_urls
        ]
);
?>
    <form id="pmb-project-form" method="POST" action="">
        <?php wp_nonce_field( 'pmb-project-edit' );?>
        <?php echo $form->getHtmlAndJs();?>
        <input type="submit" class="button button-primary" value="<?php esc_html_e('Submit & Proceed', 'avpvh-print-website');?>">
    </form>
<?php avpvh_prwb_render_template('partials/project_footer.php');