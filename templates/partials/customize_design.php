<?php
/**
 * @var $form \Avpvh\PrintWebsite\Twine\forms\base\FormSection
 * @var $form_url string
 * @var $design \Avpvh\PrintWebsite\orm\entities\Design
 */
?>
<?php if ($design->getDesignTemplate()->getDocs()) { ?>
<div class="pmb-right-help"><span class="dashicons dashicons-admin-site-alt3"></span><a target="_blank" href="<?php echo esc_url($design->getDesignTemplate()->getDocs());?>"><?php printf(esc_html__('Read %s Documentation Online', 'avpvh-print-website'), $design->getDesignTemplate()->getTitle());?></a></div>
<?php } ?>
<form method="POST" action="<?php echo esc_attr($form_url);?>">
    <?php echo $form->getHtmlAndJs();?>
    <button class="button button-primary pmb-save"><?php esc_html_e('Save & Proceed', 'avpvh-print-website');?></button>
    <!--    <button id="pmb-save-as" class="button button-primary pmb-save-as">--><?php //esc_html_e('Save As...', 'avpvh-print-website');?><!--</button>-->
</form>