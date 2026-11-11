<?php

/**
 * @var $print_options Avpvh\PrintWebsite\domain\PrintOptions
 * @var $settings Avpvh\PrintWebsite\domain\FrontendPrintSettings
 * @var $settings_form Avpvh\PrintWebsite\Twine\forms\base\FormSection
 */

use Avpvh\PrintWebsite\domain\PrintOptions;
use Avpvh\PrintWebsite\domain\FrontendPrintSettings;

?>
<div class="wrap nosubsub">
    <h1><?php esc_html_e('AV-PvH Print Website - Settings', 'avpvh-print-website'); ?></h1>
    <form method="post">
        <h2><?php esc_html_e('Front-End Print Buttons', 'avpvh-print-website'); ?></h2>
        <p><?php _e('Viewers will see these buttons when they visit your website.', 'avpvh-print-website'); ?></p>
        <p class="pmb-help"><?php _e('These print buttons use the "Quick Print" technology.', 'avpvh-print-website'); ?></p>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <?php esc_html_e('Show print buttons on:', 'avpvh-print-website'); ?>
                    </th>
                    <td>
                        <input type="checkbox" id="pmb-show-print-buttons" name="avpvh_prwb_show_buttons" value="1" <?php echo $settings->showButtons() ? 'checked="checked"' : '' ?>>
                        <label for="pmb-show-print-buttons"><?php esc_html_e(
                                                                'Posts',
                                                                'avpvh-print-website'
                                                            ); ?></label><br />
                        <input type="checkbox" id="pmb-show-print-buttons-pages" name="avpvh_prwb_show_buttons_pages" value="1" <?php echo $settings->showButtonsPages() ? 'checked="checked"' : '' ?>>
                        <label for="pmb-show-print-buttons-pages"><?php esc_html_e(
                                                                        'Pages',
                                                                        'avpvh-print-website'
                                                                    ); ?>
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
        <details class="pmb-details">
            <summary class="pmb-reveal-options pmb-inline"><?php esc_html_e('Customize Buttons', 'avpvh-print-website'); ?>
            </summary>
            <div class="pmb-reveal-details">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="pmb-place-above"><?php esc_html_e('Place Buttons', 'avpvh-print-website'); ?></label>
                            </th>
                            <td>
                                <select name="avpvh_prwb_place_above" id="pmb-place-above">
                                    <option value="1" <?php echo $settings->showButtonsAbove() ? 'selected="selected"' : ''; ?>><?php esc_html_e('Above Content', 'avpvh-print-website'); ?></option>
                                    <option value="0" <?php echo ! $settings->showButtonsAbove() ? 'selected="selected"' : ''; ?>><?php esc_html_e('Below Content', 'avpvh-print-website'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="pmb-open-new-tab"> <?php esc_html_e('Open Print Page in New Tab', 'avpvh-print-website'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="pmb-open-new-tab" name="avpvh_prwb_open_new_tab" value="1" <?php echo $settings->openNewTab() ? 'checked="checked"' : '' ?>>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h2><?php esc_html_e('Print Formats', 'avpvh-print-website'); ?></h2>
                <?php foreach ($settings->formats() as $slug => $format) { ?>
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label>
                                        <input type="checkbox" id="pmb-<?php echo esc_attr($slug); ?>" name="avpvh_prwb_format[<?php echo esc_attr($slug); ?>]" value="<?php echo esc_attr($slug); ?>" <?php echo $settings->isActive($slug) ? 'checked="checked"' : ''; ?>>
                                        <?php echo $format['admin_label'] ?>
                                    </label>
                                </th>
                                <td>
                                    <label><?php esc_html_e('Label', 'avpvh-print-website'); ?> <input type="text" id="pmb-<?php echo $slug; ?>-label" name="avpvh_prwb_frontend_labels[<?php echo esc_attr($slug); ?>]" value="<?php echo esc_attr($settings->getFrontendLabel($slug)); ?>"></label>

                                    <details class="pmb-details">
                                        <summary class="pmb-reveal-options" id="pmb-reveal-main-options"><?php esc_html_e('Show Options', 'avpvh-print-website'); ?></summary>
                                        <div class="pmb-reveal-details">
                                            <?php
                                            $displayer->setInputPrefixes(['avpvh_prwb_print_options', $slug,]);
                                            $displayer->setNewValues($settings->getPrintOptionsAndValues($slug));
                                            avpvh_prwb_render_template('partials/display_options.php', ['print_options' => $print_options, 'displayer' => $displayer, 'upsells' => false]); ?>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </details>
        <hr>
        <h2><?php _e('Admin Print Buttons', 'avpvh-print-website'); ?></h2>
        <p><?php _e('Administrators and other privileged users will see these in the wp-admin (like when editing a post.)', 'avpvh-print-website'); ?></p>
        <p class="pmb-help"><?php _e('These print buttons use the same technology as Saved Prints.', 'avpvh-print-website'); ?></p>
        <?php echo $settings_form->getHtmlAndJs(); ?>
        <?php wp_nonce_field('pmb-settings'); ?>
        <button class="button-primary"><?php esc_html_e('Save Settings', 'avpvh-print-website'); ?></button> <input name="pmb-reset" class="button button-secondary" onclick="return confirm('<?php echo esc_attr(__('Are you sure you want to reset the settings back to defaults?', 'avpvh-print-website')); ?>');" type="submit" value="<?php esc_html_e('Reset to Defaults', 'avpvh-print-website'); ?>">
    </form>
</div>