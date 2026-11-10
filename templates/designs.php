<?php
/**
 * @var $formats \Avpvh\PrintWebsite\entities\FileFormat[]
 * @var $designs \Avpvh\PrintWebsite\orm\entities\Design[]
 * @var $config \Avpvh\PrintWebsite\services\config\Config
 */
?>
<div class="wrap nosubsub">
    <h1 class="wp-heading-inline"><?php _e('Pro Print ― Designs', 'avpvh-print-website');?></h1>
    <p><?php _e('Designs in AV-PvH Print Website are like Themes in WordPress: their settings affect how the projects are displayed. Edit them below or change which ones are used by default.', 'avpvh-print-website');?></p>
<?php
foreach($formats as $format){
    ?>
    <h2><?php echo $format->coloredTitleAndIcon();?></h2>
    <p><?php echo $format->desc();?></p>
<?php
    $designs_for_format = $designs[$format->slug()];
    avpvh_prwb_render_template(
        'partials/select_designs.php',
        [
            'format' => $format,
            'designs'=> $designs_for_format,
            'chosen_design' => $config->getDefaultDesignFor($format),
            'active_text' => __('<span>Default:</span> %s', 'avpvh-print-website'),
            'select_button_text' => esc_html__('Make Default', 'avpvh-print-website'),
            'select_button_aria_label' => esc_html__('Make "%s" the Default Design for its Format', 'avpvh-print-website'),
            'customize_button_aria_label' => esc_html__(' Customize the Design "%s"', 'avpvh-print-website'),
            'context' => 'designs_list'
        ]
    );
}
?>
</div>
