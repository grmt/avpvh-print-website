<div class="wrap nosubsub">
    <p class="pmb-center"><?php esc_html_e('It’s finally here...', 'avpvh-print-website');?></p>
    <h1 class="pmb-big-header">🎉<?php esc_html_e( 'AV-PvH Print Website – Pro Print!', 'avpvh-print-website' ); ?>🎉</h1>
    <p class="pmb-center"><?php esc_html_e('The old features are still here: "Print Now" was just renamed "Quick Print", and the Print Buttons are still available under "Settings".', 'avpvh-print-website');?></p>
    <p class="pmb-center"><?php esc_html_e('Pro Print makes more functional and visually-appealing PDFs using your web browser for free, or the new paid Pro PDF Service.', 'avpvh-print-website');?></p>

        <?php
            $features = [
                    [
                            __('Automatic Table of Contents', 'avpvh-print-website'),
                            __('Add a table of contents to your project, with links and page numbers.', 'avpvh-print-website') . avpvh_prwb_pro_print_service_best(__('Pro PDF Service adds page numbers.', 'avpvh-print-website'))
                    ],
                    [
                        __('Automatic page references and footnotes', 'avpvh-print-website'),
                        __('Hyperlinks can be automatically converted into references to the page the linked-to content is located, or a footnote with the web address print-out.', 'avpvh-print-website') . avpvh_prwb_pro_print_service_only(),
                    ],
                    [
                        __('Proper margins, page numbers and running heads', 'avpvh-print-website'),
                        __('Clean margins, alternating page numbers, roman numerals for front matter, running chapter heads, and more.', 'avpvh-print-website') . avpvh_prwb_pro_print_service_best()
                    ],
                    [
                        __('Support Custom Post Types', 'avpvh-print-website'),
                        __('Content from other plugins, like WooCommerce Products, or your LMS\'s course materials, can be included in projects.', 'avpvh-print-website')
                    ],
                    [
                        __('Drag-and-Drop Content Organizer', 'avpvh-print-website'),
                        __('Drop posts, pages, and other post types into projects in any order. Even organize them into parts.', 'avpvh-print-website')
                    ],
                    [
                        __('Premade, Reusable, Customizable Designs', 'avpvh-print-website'),
                        __('AV-PvH Print Website\'s Designs are like WordPress Themes: each has its own look and customization options. Reuse the same design across projects. Or even create your own design using HTML, CSS, and Javascript', 'avpvh-print-website') . avpvh_prwb_pro_print_service_best(__('Pro PDF Service lets designs fully control the margins, page backgrounds, and los of other aspects.', 'avpvh-print-website'))
                    ],
                    [
                            __('Saveable Projects', 'avpvh-print-website'),
                            __('Save where you left off.', 'avpvh-print-website')
                    ],
                    [
                            __('Front matter, back matter, parts, and print materials', 'avpvh-print-website'),
                        __('Add front and back matter to books, organize the content into parts, and add special "print material" posts to use only in your projects', 'avpvh-print-website')
                    ]
            ];
        /**
         * @var $design_managers \Avpvh\PrintWebsite\orm\managers\DesignManager
         */
            $design_managers = \Avpvh\PrintWebsite\system\Context::instance()->reuse('Avpvh\PrintWebsite\orm\managers\DesignManager');
            $print_classic = $design_managers->getBySlug('classic_print');
            $buurma = $design_managers->getBySlug('buurma');
            $mayer = $design_managers->getBySlug('mayer');
            $designs = [$print_classic, $buurma, $mayer];
            ?>
    <div class="pmb-content-boxes">
        <?php
            foreach($designs as $design){
                ?>
                <div class="pmb-content-box-wrap">
                    <div class="pmb-content-box-inner">
                        <?php
                        echo avpvh_prwb_design_preview($design);
                        ?>
                    </div>
                </div>
                <?php
            }
        ?>
    </div>

    <div>
        <div class="pmb-constrained-content">
            <h1><?php esc_html_e('Features Include:', 'avpvh-print-website');?></h1>
                <?php foreach($features as $feature_data){
                    ?>
                    <div class="pmb-row">
                        <h2><?php echo $feature_data[0];?></h2>
                        <p><?php echo $feature_data[1];?></p>
                    </div>
                <?php
                }
                ?>
        </div>
    </div>

    <div class="pmb-center">
    <a href="<?php echo admin_url(AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH);?>" class="button button-primary"><?php esc_html_e('Try Pro Print Now', 'avpvh-print-website');?></a>
    <a href="https://printmy.blog" target="_blank" class="button"><?php esc_html_e('Learn More on printmy.blog', 'avpvh-print-website');?></a>
    </div>
    <div>
        <div class="pmb-constrained-content">
            <p><?php printf(__('Please %1$slet me know%2$s if you have any questions!', 'avpvh-print-website'), '<a href="htts://printmy.blog/contact" target="_blank">', '</a>');?></p>
            <p><?php esc_html_e('Best wishes, Mike Nelson, AV-PvH Print Website Developer', 'avpvh-print-website');?> 🧡</p>
        </div>
    </div>

</div>
