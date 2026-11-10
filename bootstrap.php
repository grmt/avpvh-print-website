<?php
/**
 * Run code that PHP 5.2 and 5.3 can't parse. So this only gets included once we've checked PHP can handle it.
*/
require AVPVH_PRWB_VENDOR_DIR . 'autoload.php';
// Move Print My Blog data to the new names before anything registers post types or installs tables.
(new Avpvh\PrintWebsite\system\LegacyMigration())->maybeRun();
add_action('admin_notices', ['Avpvh\PrintWebsite\system\LegacyMigration', 'notice']);
$init = new Avpvh\PrintWebsite\system\Init();
$init->setHooks();
