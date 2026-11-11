<?php

namespace Avpvh\PrintWebsite\system;

/**
 * Moves a site's Print My Blog data over to the AV-PvH Print Website names, once.
 *
 * Print My Blog stored everything under `pmb_` (options, post types, meta, tables, capabilities) and its shortcodes
 * and block under `pmb_` and `printmyblog/`. This runs when the plugin file loads, before the plugin registers its
 * post types or installs its tables, so the existing projects and designs are found under the new names.
 *
 * Options and tables are copied (the old ones are left in place); post types and meta keys are renamed in place,
 * because posts can only have one type. Afterwards the old plugin is deactivated.
 *
 * @package Avpvh\PrintWebsite\system
 */
class LegacyMigration
{
    /**
     * Option that records the migration ran (and what it did).
     */
    const DONE_OPTION = 'avpvh_prwb_legacy_migrated';

    /**
     * The old plugin's basename.
     */
    const OLD_PLUGIN = 'print-my-blog/printmyblog.php';

    /**
     * Shortcode names without prefix.
     */
    const SHORTCODES = [
        'print_buttons',
        'print_page_url',
        'project_title',
        'toc',
        'title_page',
        'byline',
        'footnote',
        'web_only_text',
        'web_only_blocks',
        'print_only_text',
        'print_only_blocks',
        'project_setting',
    ];

    /**
     * @var array<string,int|bool>
     */
    private $counts = [];

    /**
     * Runs the migration when it hasn't run yet.
     *
     * @return void
     */
    public function maybeRun()
    {
        if (get_option(self::DONE_OPTION) !== false) {
            return;
        }
        if (get_option('pmb_version_history') === false && ! $this->tableExists('pmb_project_sections')) {
            // Never had Print My Blog: nothing to move.
            add_option(self::DONE_OPTION, ['legacy' => false, 'time' => time()], '', true);
            return;
        }
        $this->counts = [
            'options' => $this->copyOptions(),
            'tables' => $this->copyTables(),
            'posts' => $this->renamePostTypes(),
            'postmeta' => $this->renameMetaKeys('postmeta'),
            'usermeta' => $this->renameMetaKeys('usermeta'),
            'capabilities' => $this->renameCapabilities(),
            'content' => $this->rewriteContent(),
        ];
        delete_option('rewrite_rules');
        $this->counts['deactivated'] = $this->deactivateOldPlugin();
        add_option(
            self::DONE_OPTION,
            ['legacy' => true, 'time' => time(), 'version' => AVPVH_PRWB_VERSION, 'notice' => true] + $this->counts,
            '',
            true
        );
    }

    /**
     * Shows once, to an admin, what was moved over.
     *
     * @return void
     */
    public static function notice()
    {
        $done = get_option(self::DONE_OPTION);
        if (! is_array($done) || empty($done['notice']) || ! current_user_can('manage_options')) {
            return;
        }
        $done['notice'] = false;
        update_option(self::DONE_OPTION, $done);
        printf(
            '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
            esc_html(
                sprintf(
                    // translators: 1: number of projects, designs and contents, 2: number of posts and widgets.
                    __(
                        'AV-PvH Print Website moved over the Print My Blog data: %1$d saved prints, designs and contents, and the shortcodes in %2$d posts or widgets. The Print My Blog plugin is deactivated and can be deleted.',
                        'avpvh-print-website'
                    ),
                    (int) ($done['posts'] ?? 0),
                    (int) ($done['content'] ?? 0)
                )
            )
        );
    }

    /**
     * Rewrites an old name (`pmb_x`, `_pmb_x`) to the new one, or returns '' when it isn't an old name.
     *
     * @param string $name
     * @return string
     */
    public static function newName($name)
    {
        if (strpos($name, '_pmb_') === 0) {
            return '_avpvh_prwb_' . substr($name, 5);
        }
        if (strpos($name, 'pmb_') === 0) {
            return 'avpvh_prwb_' . substr($name, 4);
        }
        return '';
    }

    /**
     * Copies `pmb_*` options (not transients) to their new names, unless the new one already exists.
     *
     * @return int
     */
    private function copyOptions()
    {
        global $wpdb;
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT option_name, autoload FROM {$wpdb->options} WHERE option_name LIKE %s",
                $wpdb->esc_like('pmb_') . '%'
            )
        );
        $copied = 0;
        foreach ((array) $rows as $row) {
            $new = self::newName($row->option_name);
            if ($new === '' || get_option($new, null) !== null) {
                continue;
            }
            $autoload = in_array($row->autoload, ['no', 'off', 'auto-off'], true) ? false : null;
            add_option($new, get_option($row->option_name), '', $autoload);
            $copied++;
        }
        return $copied;
    }

    /**
     * Copies the `{prefix}pmb_*` tables to `{prefix}avpvh_prwb_*`.
     *
     * @return int
     */
    private function copyTables()
    {
        global $wpdb;
        $copied = 0;
        $tables = $wpdb->get_col(
            $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($wpdb->prefix . 'pmb_') . '%')
        );
        foreach ((array) $tables as $old) {
            $new = $wpdb->prefix . 'avpvh_prwb_' . substr($old, strlen($wpdb->prefix . 'pmb_'));
            // Table names come from SHOW TABLES and the fixed prefix, so they are safe to quote with backticks.
            if (! $this->tableExists(substr($new, strlen($wpdb->prefix)))) {
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $wpdb->query("CREATE TABLE `{$new}` LIKE `{$old}`");
            }
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            if ((int) $wpdb->get_var("SELECT COUNT(*) FROM `{$new}`") === 0) {
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $wpdb->query("INSERT INTO `{$new}` SELECT * FROM `{$old}`");
                $copied++;
            }
        }
        return $copied;
    }

    /**
     * Renames the `pmb_*` post types in place.
     *
     * @return int
     */
    private function renamePostTypes()
    {
        global $wpdb;
        $total = 0;
        foreach (['project', 'content', 'design'] as $type) {
            $total += (int) $wpdb->update(
                $wpdb->posts,
                ['post_type' => 'avpvh_prwb_' . $type],
                ['post_type' => 'pmb_' . $type]
            );
        }
        return $total;
    }

    /**
     * Renames `pmb_*` and `_pmb_*` meta keys in place. Also Twine's user notifications.
     *
     * @param string $table 'postmeta' or 'usermeta'.
     * @return int
     */
    private function renameMetaKeys($table)
    {
        global $wpdb;
        $table_name = $wpdb->{$table};
        $total = 0;
        foreach (['_pmb_' => '_avpvh_prwb_', 'pmb_' => 'avpvh_prwb_'] as $old => $new) {
            $total += (int) $wpdb->query(
                $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    "UPDATE {$table_name} SET meta_key = CONCAT(%s, SUBSTRING(meta_key, %d)) WHERE meta_key LIKE %s",
                    $new,
                    strlen($old) + 1,
                    $wpdb->esc_like($old) . '%'
                )
            );
        }
        if ($table === 'usermeta') {
            $total += (int) $wpdb->update(
                $wpdb->usermeta,
                ['meta_key' => '_avpvh_prwb_twine_ot_notifications'],
                ['meta_key' => '_twine_ot_notifications']
            );
        }
        return $total;
    }

    /**
     * Gives each role the renamed capabilities (edit_pmb_projects becomes edit_avpvh_prwb_projects) and removes
     * the old ones.
     *
     * @return int
     */
    private function renameCapabilities()
    {
        $changed = 0;
        foreach (wp_roles()->role_objects as $role) {
            foreach ((array) $role->capabilities as $cap => $grant) {
                if (strpos($cap, 'pmb_') === false) {
                    continue;
                }
                $role->add_cap(str_replace('pmb_', 'avpvh_prwb_', $cap), $grant);
                $role->remove_cap($cap);
                $changed++;
            }
        }
        return $changed;
    }

    /**
     * Rewrites `[pmb_x]` shortcodes and the `printmyblog/setupform` block in posts and widgets.
     *
     * @param string $content
     * @return string
     */
    public static function rewriteText($content)
    {
        $content = (string) preg_replace(
            '/\[(\/?)pmb_(' . implode('|', self::SHORTCODES) . ')(?=[\s\]\/])/',
            '[$1avpvh_print_website_$2',
            (string) $content
        );
        return str_replace('wp:printmyblog/', 'wp:avpvh-print-website/', $content);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private static function rewriteDeep($value)
    {
        if (is_array($value)) {
            return array_map([self::class, 'rewriteDeep'], $value);
        }
        return is_string($value) ? self::rewriteText($value) : $value;
    }

    /**
     * @return int Number of posts and widget options changed.
     */
    private function rewriteContent()
    {
        global $wpdb;
        $changed = 0;
        $posts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_content FROM {$wpdb->posts} WHERE post_type <> 'revision'"
                . ' AND (post_content LIKE %s OR post_content LIKE %s)',
                '%' . $wpdb->esc_like('[pmb_') . '%',
                '%' . $wpdb->esc_like('wp:printmyblog/') . '%'
            )
        );
        foreach ((array) $posts as $post) {
            $new = self::rewriteText($post->post_content);
            if ($new !== $post->post_content) {
                $wpdb->update($wpdb->posts, ['post_content' => $new], ['ID' => (int) $post->ID]);
                clean_post_cache((int) $post->ID);
                $changed++;
            }
        }
        foreach (['widget_text', 'widget_block', 'widget_custom_html'] as $option) {
            $widgets = get_option($option);
            if (is_array($widgets)) {
                $new = self::rewriteDeep($widgets);
                if ($new !== $widgets) {
                    update_option($option, $new);
                    $changed++;
                }
            }
        }
        return $changed;
    }

    /**
     * @return bool Whether the old plugin was active.
     */
    private function deactivateOldPlugin()
    {
        if (! function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if (! is_plugin_active(self::OLD_PLUGIN)) {
            return false;
        }
        deactivate_plugins(self::OLD_PLUGIN, true);
        return true;
    }

    /**
     * @param string $name Table name without the WordPress prefix.
     * @return bool
     */
    private function tableExists($name)
    {
        global $wpdb;
        $table = $wpdb->prefix . $name;
        return $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table))) === $table;
    }
}
