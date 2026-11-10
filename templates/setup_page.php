<?php
/**
 * @var $print_options Avpvh\PrintWebsite\domain\PrintOptions
 * @var $displayer Avpvh\PrintWebsite\services\display\FormInputs
 */

use Avpvh\PrintWebsite\domain\PrintOptions;
use Avpvh\PrintWebsite\services\display\FormInputs;
avpvh_prwb_render_template('partials/free_header.php');
?>
<div class="wrap nosubsub">
<h1><?php esc_html_e('AV-PvH Print Website - Quick Print','avpvh-print-website' );?></h1>
    <p><?php esc_html_e('Configure how you’d like the blog to be printed, or just use our recommended defaults.', 'avpvh-print-website'); ?></p>
    <form action="<?php echo site_url();?>" method="get">
        <?php if(AVPVH_PRWB_REST_PROXY_EXISTS){?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <?php esc_html_e('Site URL (including "https://" or "http://")', 'avpvh-print-website');?>
                    </th>
                    <td>
                        <input name="site" id="pmb-site" class="pmb-wide-input" placeholder="<?php echo site_url();?>">
                        <div class="pmb-spinner-container">
                            <div id="pmb-site-checking" class="pmb-spinner pmb-hidden-initially"></div>
                        </div>
                        <span id="pmb-site-ok" class="pmb-hidden-initially">✅</span><span id="pmb-site-bad" class="pmb-hidden-initially">❌</span><span id="pmb-site-status"></span>
                        <p class="description"><?php esc_html_e('URL of the WordPress site (self-hosted or on WordPress.com) you’d like to print. Leave blank to use this current site.', 'avpvh-print-website');?></p>
                    </td>
                </tr>
        <?php }?>
            </tbody>
        </table>

        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
				    <?php esc_html_e('Format', 'avpvh-print-website');?>
                </th>
                <td>
				    <?php
				    $formats = array(
					    'print' => array(
						    'label' => esc_html__('Paper', 'avpvh-print-website'),
						    'help_text' => esc_html__('Print a physical copy using your web browser’s print functionality.', 'avpvh-print-website'),
						    'checked' => true
					    ),
					    'pdf' => array(
						    'label' => esc_html__('Digital PDF', 'avpvh-print-website'),
						    'help_text' => esc_html__('Make a PDF file, intended for reading from a computer or other device, using your browser or a browser extension.', 'avpvh-print-website'),
						    'link' => 'https://wordpress.org/plugins/print-my-blog/#how%20do%20i%20create%20a%20pdf%20using%20print%20my%20blog%3F'
					    ),
					    'epub' => array(
						    'label' => esc_html__('eBook (ePub)', 'avpvh-print-website'),
						    'help_text' => esc_html__('Create an ePub file locally in your browser for reading on eReaders, tablets, and phones.', 'avpvh-print-website'),
					    ),
					    'word' => array(
						    'label' => esc_html__('Word Document', 'avpvh-print-website'),
						    'help_text' => esc_html__('Create a Word-compatible document locally in your browser for editing in Word, Google Docs, or LibreOffice.', 'avpvh-print-website'),
					    ),
					    'html' => array(
						    'label' => esc_html__('HTML', 'avpvh-print-website'),
						    'help_text' => esc_html__('Easily copy-and-paste into another program like Microsoft Word or Google Docs. Note: this is not recommended for customizing printouts, as other programs usually format the content poorly. Instead, use AV-PvH Print Website’s CSS classes to remove or add content from printouts.', 'avpvh-print-website'),
						    'link' => 'https://wordpress.org/plugins/print-my-blog/#how%20do%20i%20remove%20post%20content%20from%20the%20printout%3F'

					    ),
				    );
				    foreach($formats as $key => $details){
					    ?>
                        <div class="pmb-format-option">
                            <input type="radio" name="format" id="format-<?php echo $key;?>" value="<?php echo $key;?>" <?php echo isset($details['checked']) ? 'checked="checked"' : '';?>>
                            <label for="format-<?php echo $key;?>">
							    <?php echo $details['label']; ?>
                            </label>
                            <p class="description">
							    <?php echo $details['help_text']; ?>
							    <?php echo isset($details['link']) ? '<a href="' . $details['link'] . '" target="_blank">' . esc_html__('Learn More','avpvh-print-website' ) . '</a>' : '';?>
                            </p>
                        </div>
					    <?php
				    }
				    ?>
                </td>
            </tr>
            </tbody>
        </table>

        <details class="pmb-details">
            <summary class="pmb-reveal-options" id="pmb-reveal-main-options"><?php esc_html_e('Show More Print Options', 'avpvh-print-website'); ?></summary>
            <div class="pmb-reveal-details">
                <h1><?php esc_html_e('Options','avpvh-print-website' );?></h1>
                <h2><?php esc_html_e('Post Selection','avpvh-print-website' );?></h2>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><?php esc_html_e('Post Selection','avpvh-print-website' ); avpvh_prwb_pro_better_e(__('Pro Print supports custom post types, combining posts with pages, and print-only materials', 'avpvh-print-website'));?></th>
                        <td>
                            <label><input class="pmb-post-type" type="radio" name="post-type" value="post" checked="checked"><?php esc_html_e('Posts', 'avpvh-print-website');?></label>
                            <br>
                            <label><input class="pmb-post-type" type="radio" name="post-type" value="page"><?php esc_html_e('Pages', 'avpvh-print-website');?></label>

                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Order', 'avpvh-print-website'); avpvh_prwb_pro_better_e(__('Pro Print supports drag-and-drop ordering of content.', 'avpvh-print-website'));?></th>
                        <td>
                            <div id="pmb-order-by-date">
                                <p><?php esc_html_e('Posts will be ordered:', 'avpvh-print-website'); ?></p>
                                <label><input class="pmb-order" type="radio" name="order-date" value="asc" checked="checked"><?php esc_html_e('Oldest First', 'avpvh-print-website'); ?></label><br/>
                                <label><input class="pmb-order" type="radio" name="order-date" value="desc"><?php esc_html_e('Newest First', 'avpvh-print-website'); ?></label>
                            </div>
                            <div id="pmb-order-by-menu">
                                <p><?php esc_html_e('Pages will be ordered:', 'avpvh-print-website'); ?></p>
                                <label><input class="pmb-order" type="radio" name="order-menu" value="menu" checked="checked"><?php esc_html_e('Menu Hierarchy (Top Down, Left to Right)', 'avpvh-print-website'); ?></label><br>
                                <label><input class="pmb-order" type="radio" name="order-menu" value="alpha" ><?php esc_html_e('H1 Alphabetical', 'avpvh-print-website'); ?></label><br>
                                <label><input class="pmb-order" type="radio" name="order-menu" value="desc" ><?php esc_html_e('Menu Hierarchy Reversed', 'avpvh-print-website'); ?></label>
                            </div>
                        </td>
                    </tr>
                    <?php
                    if( is_user_logged_in()){

                        $statuses = [
                                'draft' => esc_html__('Draft' ),
                                'pending' => esc_html__( 'Pending Review' ),
                                'private' => esc_html__('Private'),
                                'password' => esc_html__('Password-Protected', 'avpvh-print-website'),
                                'publish' => esc_html__('Published'),
                                'future' => esc_html__('Scheduled'),
                                'trash' => esc_html__('Trash')
                        ];?>
                        <tr>
                            <th scope="row"><label><?php esc_html_e('Statuses','avpvh-print-website' );?></label></th>
                            <td>
                                <?php foreach($statuses as $value => $label ){
                                   ?>
                                    <input type="checkbox" name="statuses[]" value="<?php echo esc_attr($value);?>" id="<?php echo esc_attr($value);?>-id" <?php echo $value === 'publish' ? 'checked="checked"' : '';?>>
                                <label for="<?php echo esc_attr($value);?>-id"><?php echo $label;?></label><br>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
                <h2><?php esc_html_e('Filters', 'avpvh-print-website');?> <?php avpvh_prwb_pro_better_e(__('Pro Print supports filtering by custom taxonomies.', 'avpvh-print-website'));?> <div style="display:inline-block" id="pmb-categories-spinner"><div class="pmb-spinner"></div></div></h2>
                <table class="form-table">
                    <tbody id="pmb-dynamic-categories">
                    </tbody>
                </table>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><?php esc_html_e('Authored By', 'avpvh-print-website'); ?></th>
                        <td><select id="pmb-author-select" class="pmb-author-select" name="pmb-author"></select></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Posted After (and Including)', 'avpvh-print-website'); ?></th>
                        <td><input type="text" class="pmb-date" name="dates[after]"></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Posted Before (and Including)', 'avpvh-print-website'); ?></th>
                        <td><input type="text" class="pmb-date" name="dates[before]"></td>
                    </tr>
                    </tbody>
                </table>

                <?php avpvh_prwb_render_template('partials/display_options.php',['print_options' => $print_options, 'displayer' => $displayer, 'upsells' => true]); ?>
            </div>
        </details>
        <input type="hidden" name="<?php echo AVPVH_PRWB_PRINTPAGE_SLUG;?>" value="1">
        <button class="button-primary"><?php esc_html_e('Prepare Print-Page','avpvh-print-website' );?></button>
    </form>
</div>
