<?php

namespace Avpvh\PrintWebsite\compatibility;

use Avpvh\PrintWebsite\compatibility\plugins\AdvancedCustomFields;
use Avpvh\PrintWebsite\compatibility\plugins\AdvancedExcerpt;
use Avpvh\PrintWebsite\compatibility\plugins\CoBlocks;
use Avpvh\PrintWebsite\compatibility\plugins\ContactForm7;
use Avpvh\PrintWebsite\compatibility\plugins\EasyFootnotes;
use Avpvh\PrintWebsite\compatibility\plugins\GoogleLanguageTranslator;
use Avpvh\PrintWebsite\compatibility\plugins\GTranslate;
use Avpvh\PrintWebsite\compatibility\plugins\Hustle;
use Avpvh\PrintWebsite\compatibility\plugins\JetPack;
use Avpvh\PrintWebsite\compatibility\plugins\LazyLoadingFeaturePlugin;
use Avpvh\PrintWebsite\compatibility\plugins\PaidMembershipsPro;
use Avpvh\PrintWebsite\compatibility\plugins\TablePress;
use Avpvh\PrintWebsite\compatibility\plugins\Wpml;
use Avpvh\PrintWebsite\compatibility\plugins\WpVrView;
use Avpvh\PrintWebsite\compatibility\plugins\YoastSeo;
use Avpvh\PrintWebsite\system\Context;
use Avpvh\PrintWebsite\Twine\compatibility\CompatibilityBase;

/**
 * Class DetectAndActivate
 *
 * Description
 *
 * @package     AV-PvH Print Website
 * @author         Mike Nelson
 * @since         2.1.4
 *
 */
class DetectAndActivate
{
    /**
     * @var array|null
     */
    protected $compatibility_mods = null;

    /**
     * @return CompatibilityBase[]
     */
    protected function getCompatibilityMods()
    {
        if ($this->compatibility_mods === null) {
            /**
             * @var $compatiblity_mods_to_activate CompatibilityBase[]
             */
            $compatiblity_mods_to_activate = [
                new LazyLoadingFeaturePlugin(),
            ];
            if(defined('HUSTLE_MIN_PHP_VERSION')){
                $compatiblity_mods_to_activate[] = new Hustle();
            }
            if (class_exists('easyFootnotes')) {
                $compatiblity_mods_to_activate[] = new EasyFootnotes();
            }
            if (function_exists('vr_creation')) {
                $compatiblity_mods_to_activate[] = new WpVrView();
            }
            if (class_exists('TablePress')) {
                $compatiblity_mods_to_activate[] = new TablePress();
            }
            if (class_exists('CoBlocks')) {
                $compatiblity_mods_to_activate[] = new CoBlocks();
            }
            if (class_exists('WPSEO_Sitemaps')) {
                $compatiblity_mods_to_activate[] = new YoastSeo();
            }
            if (class_exists('google_language_translator')) {
                $compatiblity_mods_to_activate[] = new GoogleLanguageTranslator();
            }
            if (defined('WPCF7_VERSION')) {
                $compatiblity_mods_to_activate[] = new ContactForm7();
            }
            if (defined('ICL_SITEPRESS_VERSION')) {
                $compatiblity_mods_to_activate[] = Context::instance()->reuse('Avpvh\PrintWebsite\compatibility\plugins\Wpml');
            }
            if (defined('JETPACK__VERSION')) {
                $compatiblity_mods_to_activate[] = new JetPack();
            }
            if (defined('PMPRO_VERSION')) {
                $compatiblity_mods_to_activate[] = new PaidMembershipsPro();
            }
            if( class_exists('ACF')){
                $compatiblity_mods_to_activate[] = new AdvancedCustomFields();
            }
            if(class_exists('Advanced_Excerpt')){
                $compatiblity_mods_to_activate[] = new AdvancedExcerpt();
            }
            if(class_exists('GTranslate')){
                $compatiblity_mods_to_activate[] = new GTranslate();
            }
            $this->compatibility_mods = $compatiblity_mods_to_activate;
        }
        return $this->compatibility_mods;
    }
    /**
     * @since 2.1.4
     */
    public function detectAndActivateGlobalCompatibilityMods()
    {
        $compatiblity_mods_to_activate = $this->getCompatibilityMods();
        foreach ($compatiblity_mods_to_activate as $compatibility_mod) {
            $compatibility_mod->setHooks();
        }
    }

    /**
     * Using a filter as an action to initiate our callbacks
     * @param string $pre_dispatch_result
     * @return string
     */
    public function activateRenderingCompatibilityModes($pre_dispatch_result = '')
    {
        foreach ($this->getCompatibilityMods() as $compatibility_mod) {
            $compatibility_mod->setRenderingHooks();
        }
        return $pre_dispatch_result;
    }
}
// End of file DetectAndActivate.php
// Location: Avpvh\PrintWebsite\compatibility/DetectAndActivate.php
