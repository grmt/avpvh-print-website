<?php
/**
 * @var $avpvh_prwb_project \Avpvh\PrintWebsite\orm\entities\Project
 * @var $avpvh_prwb_design \Avpvh\PrintWebsite\orm\entities\Design
 *
 */
?>
<div class="pmb-haller-frontpage-header">
<div class="pmb-haller-frontpage-above-header">
    <span class="pmb-haller-frontpage-date"><?php $avpvh_prwb_project->echoSetting('date');?></span>
    <span class="pmb-haller-frontpage-issue"><?php printf(__('Issue %s', 'avpvh-print-website'), $avpvh_prwb_project->getSetting('issue'));?></span>
</div>
<div class="pmb-haller-frontpage-main">
    <?php $left_side = $avpvh_prwb_project->getSetting('frontpage_left_side');
    if($left_side){
        ?>
        <div class="pmb-haller-frontpage-main-sidebar pmb-haller-frontpage-area left">
            <?php echo do_shortcode($left_side);?>
        </div>
        <?php
    }
    ?>
    <div class="pmb-haller-frontpage-main-title-area pmb-haller-frontpage-area">
        <?php
            $image_url = $avpvh_prwb_design->getSetting('publication_logo');
            if( $image_url ){
                ?>
                <img src ="<?php echo esc_url($image_url);?>">
                <?php
            } else {
            ?>
                <h1 class="pmb-haller-frontpage-main-title project-title"><?php echo $avpvh_prwb_design->getSetting('publication_title'); ?></h1>
            <?php
            }
        if ($avpvh_prwb_design->getSetting('publication_subtitle')){
            ?>
            <h2 class="pmb-haller-frontpage-main-subtitle"><?php echo $avpvh_prwb_design->getSetting('publication_subtitle');?></h2>
            <?php
        }
        ?>

    </div>
    <?php $right_side = $avpvh_prwb_project->getSetting('frontpage_right_side');
    if($right_side){
        ?>
        <div class="pmb-haller-frontpage-main-sidebar pmb-haller-frontpage-area right">
            <?php echo do_shortcode($right_side);?>
        </div>
        <?php
    }
    ?>
</div>
<div class="pmb-haller-frontpage-preamble">
<span class="pmb-haller-frontpage-preamble-text"><?php echo $avpvh_prwb_design->getSetting('cover_preamble');?></span>
</div>