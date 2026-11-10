<?php
/**
 * @var $format \Avpvh\PrintWebsite\entities\FileFormat
 * @var $designs \Avpvh\PrintWebsite\orm\entities\Design[]
 * @var $chosen_design \Avpvh\PrintWebsite\orm\entities\Design
 * @var $active_text string needs one placeholder to hold the theme's name
 * @var $select_button_text string
 * @var $select_button_aria_label strings need a placeholder
 * @var $customize_button_aria_label string needs a placeholder
 * @var $context string either 'designs_list' or 'project'
 */

use Avpvh\PrintWebsite\entities\DesignTemplate;

?>

<div class="pmb-design-browser">


    <?php
    // List all designs
    foreach($designs as $design){
        if(! $design->designTemplateExists()){
            continue;
        }
        /**
         * @var $design \Avpvh\PrintWebsite\orm\entities\Design
         */
        $active = $design->getWpPost()->ID === $chosen_design->getWpPost()->ID;
        ?>
        <div class="pmb-design <?php echo $active ? 'pmb-active' : ''?>">
            <div class="pmb-design-details-opener" data-design-slug="<?php echo esc_attr($design->getWpPost()->post_name);?>">
                <div class="pmb-design-screenshot">
                    <?php echo avpvh_prwb_design_preview($design);?>
                </div>
                <button class="more-details" aria-label="<?php echo esc_attr(sprintf(esc_html__('View details of Design "%s"', 'avpvh-print-website'), $design->getWpPost()->post_title));?>"><?php esc_html_e('Design Details', 'avpvh-print-website');?></button>
            </div>
            <div class="pmb-design-id-container">
                <div class="pmb-actions pmb-design-actions">
                    <form method="POST" action="" id="pmb-design-form-<?php echo esc_attr($design->getWpPost()->post_name);?>">
                        <input type="hidden" name="design" value="<?php echo esc_attr($design->getWpPost()->ID);?>">
                        <input type="hidden" name="format" value="<?php echo esc_attr($format->slug());?>">
                        <?php wp_nonce_field(AVPVH_PRWB_ADMIN_PROJECTS_PAGE_SLUG);?>
                        <?php if( $context === 'project' || ( ! $active)){ ?>
                            <button class="button button-primary pmb-choose-design" value="choose" name="submit-button" aria-label="<?php echo esc_attr(sprintf($select_button_aria_label, $design->getWpPost()->post_title));?>"><?php echo $select_button_text;?></button>
                        <?php } ?>
                        <button class="button button-primary pmb-customize-design" value="customize" name="submit-button" aria-label="<?php echo esc_attr(sprintf($customize_button_aria_label, $design->getWpPost()->post_title));?>"><?php esc_html_e('Customize', 'avpvh-print-website');?></button>
                    </form>
                </div>
                <h2>
                    <?php
                    if($active){
                        printf(
                            $active_text,
                            $design->getWpPost()->post_title
                        );
                    } else {
                        echo $design->getWpPost()->post_title;
                    }
                    ?>
                </h2>
            </div>
            <div class="pmb-details-content-container" id="pmb-design-details-<?php echo esc_attr($design->getWpPost()->post_name);?>">
                <div class="pmb-details-content">
                    <div class="pmb-details-preview-and-summary">
                        <?php echo avpvh_prwb_design_preview($design);?>
                        <h1><?php echo $design->getWpPost()->post_title;?></h1>
                        <p class="pmb-design-quick-description"><?php echo $design->getWpPost()->post_excerpt;?></p>
                        <table class="pmb-details-support-table">
                            <?php if (! $design->isDefault()){?>
                                <tr>
                                    <th><?php esc_html_e('Customization of', 'avpvh-print-website');?></th>
                                    <td><?php echo $design->getCustomizationOf()->getWpPost()->post_title;?></td>
                                </tr>
                            <?php }?>
                            <?php if($design->getPmbMeta('author_name')){
                                $author_name = $design->getPmbMeta('author_name');
                                ?>
                                <tr>
                                    <th><?php esc_html_e('By', 'avpvh-print-website');?></th>
                                    <td>
                                        <?php echo esc_html($author_name);?>
                                    </td>
                                </tr>
                                <?php
                            }?>
                            <tr>
                                <th><?php esc_html_e('Supports', 'avpvh-print-website');?></th>
                                <td>
                                    <ul class="pmb-list"><?php
                                        if($design->getDesignTemplate()->supports(DesignTemplate::IMPLIED_DIVISION_FRONT_MATTER)){
                                            echo '<li>' . __('front matter', 'avpvh-print-website') . '</li>';
                                        }
                                        echo '<li>';
                                        printf(
                                            _n(
                                                '%1$s layer of nesting',
                                                '%1$s layers of nesting',
                                                $design->getDesignTemplate()->getLevels(),
                                                'avpvh-print-website'
                                            ),
                                            $design->getDesignTemplate()->getLevels()
                                        );
                                        echo ' ';
                                        if($design->getDesignTemplate()->getLevels() > 0 ){
                                            for($i=0; $i < $design->getDesignTemplate()->getLevels(); $i++){
                                                echo '(' . sprintf(
                                                        __('each %1$s can put in a %2$s', 'avpvh-print-website'),
                                                        $design->getDesignTemplate()->divisionLabelSingular($i),
                                                        $design->getDesignTemplate()->divisionLabelSingular($i+1)
                                                    ) . ')';
                                            }
                                        } else {
                                            echo __('(no parts)', 'avpvh-print-website');
                                        }
                                        echo '</li>';
                                        if($design->getDesignTemplate()->supports(DesignTemplate::IMPLIED_DIVISION_FRONT_MATTER)){
                                            echo '<li>' . __('back matter', 'avpvh-print-website') . '</li>';
                                        }
                                        ?>
                                    </ul>
                                </td>
                            </tr>
                            <?php if ($design->getDesignTemplate()->getDocs()) { ?>
                            <tr>
                                <th colspan="2"><span class="dashicons dashicons-admin-site-alt3"></span><a target="_blank" href="<?php echo esc_url($design->getDesignTemplate()->getDocs());?>"><?php printf(esc_html__('Read %s Documentation Online', 'avpvh-print-website'), $design->getDesignTemplate()->getTitle());?></a></th>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <div class="pmb-details-description">
                        <?php echo $design->getWpPost()->post_content;?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<hr style="padding-top:100px">