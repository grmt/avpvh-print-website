<?php

namespace Avpvh\PrintWebsite\system;

use Avpvh\PrintWebsite\Twine\system\Context as BaseContext;

/**
 * Class Context
 *
 * Stores instances of the classes used by AV-PvH Print Website for dependency injection.
 *
 * @package        AV-PvH Print Website
 * @author         Mike Nelson
 * @since          $VID:$
 *
 */
class Context extends BaseContext
{

    /**
     * @var Context
     */
    protected static $instance;

    /**
     * Sets the dependencies in the context. Keys are classnames, values are an array
     * whose keys are classnames dependend on, and values are either self::USE_NEW or self::REUSE.
     * Classes
     */
    protected function setDependencies()
    {
        $this->deps = [
            'Avpvh\PrintWebsite\system\Activation' => [
                'Avpvh\PrintWebsite\Twine\system\RequestType' => self::REUSE,
                'Avpvh\PrintWebsite\db\TableManager' => self::REUSE,
                'Avpvh\PrintWebsite\system\Capabilities' => self::REUSE,
                'Avpvh\PrintWebsite\services\DesignRegistry' => self::REUSE,
                'Avpvh\PrintWebsite\domain\DefaultProjectContents' => self::REUSE,
                'Avpvh\PrintWebsite\db\migrations\MigrationManager' => self::REUSE,
                'Avpvh\PrintWebsite\Twine\system\VersionHistory' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\Twine\system\RequestType'      => [
                'Avpvh\PrintWebsite\Twine\system\VersionHistory' => self::REUSE,
                'avpvh_prwb_activation',
            ],
            'Avpvh\PrintWebsite\Twine\system\VersionHistory'   => [
                AVPVH_PRWB_VERSION,
                'avpvh_prwb_previous_version',
                'avpvh_prwb_version_history',
            ],
            'Avpvh\PrintWebsite\controllers\Admin' => [
                'Avpvh\PrintWebsite\db\PostFetcher'              => self::REUSE,
                'Avpvh\PrintWebsite\orm\managers\ProjectSectionManager' => self::REUSE,
                'Avpvh\PrintWebsite\orm\managers\ProjectManager' => self::REUSE,
                'Avpvh\PrintWebsite\services\FileFormatRegistry'   => self::REUSE,
                'Avpvh\PrintWebsite\orm\managers\DesignManager' => self::REUSE,
                'Avpvh\PrintWebsite\db\TableManager' => self::REUSE,
                'Avpvh\PrintWebsite\services\SvgDoer' => self::REUSE,
                'Avpvh\PrintWebsite\Twine\services\notifications\OneTimeNotificationManager' => self::REUSE,
                'Avpvh\PrintWebsite\services\DebugInfo' => self::REUSE,
                'Avpvh\PrintWebsite\services\PmbCentral' => self::REUSE,
                'Avpvh\PrintWebsite\Twine\orm\managers\PostWrapperManager' => self::REUSE,
                'Avpvh\PrintWebsite\services\ExternalResourceCache' => self::REUSE,
                'Avpvh\PrintWebsite\services\config\Config' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\services\PersistentNotices' => [
                'Avpvh\PrintWebsite\mnelson4\AdminNotices\Notices' => self::REUSE,
                'Avpvh\PrintWebsite\domain\DefaultPersistentNotices' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\controllers\Ajax'  => [
                'Avpvh\PrintWebsite\orm\managers\ProjectManager' => self::REUSE,
                'Avpvh\PrintWebsite\services\FileFormatRegistry' => self::REUSE,
                'Avpvh\PrintWebsite\db\PostFetcher' => self::REUSE,
                'Avpvh\PrintWebsite\services\PmbCentral' => self::REUSE,
                'Avpvh\PrintWebsite\Twine\orm\managers\PostWrapperManager' => self::REUSE,
                'Avpvh\PrintWebsite\services\ExternalResourceCache' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\orm\entities\Project'          => [
                'Avpvh\PrintWebsite\orm\managers\ProjectSectionManager' => self::REUSE,
                'Avpvh\PrintWebsite\services\FileFormatRegistry'  => self::REUSE,
                'Avpvh\PrintWebsite\orm\managers\DesignManager' => self::REUSE,
                'Avpvh\PrintWebsite\services\config\Config' => self::REUSE,
                'Avpvh\PrintWebsite\factories\ProjectGenerationFactory' => self::REUSE,
                'Avpvh\PrintWebsite\services\SectionTemplateRegistry' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\services\DesignRegistry' => [
                'Avpvh\PrintWebsite\orm\managers\DesignManager' => self::REUSE,
                'Avpvh\PrintWebsite\services\DesignTemplateRegistry' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\orm\entities\Design' => [
                'Avpvh\PrintWebsite\services\DesignTemplateRegistry' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\entities\DesignTemplate' => [
                'Avpvh\PrintWebsite\services\FileFormatRegistry' => self::REUSE,
                'Avpvh\PrintWebsite\orm\managers\DesignManager' => self::REUSE,
                'Avpvh\PrintWebsite\services\SectionTemplateRegistry' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\services\config\Config' => [
                'Avpvh\PrintWebsite\services\FileFormatRegistry' => self::REUSE,
                'Avpvh\PrintWebsite\orm\managers\DesignManager' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\entities\ProjectGeneration' => [
                'Avpvh\PrintWebsite\orm\managers\ProjectSectionManager' => self::REUSE,
                'Avpvh\PrintWebsite\factories\ProjectFileGeneratorFactory' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\services\generators\PdfGenerator' => [
                'Avpvh\PrintWebsite\db\PostFetcher' => self::REUSE,
                'Avpvh\PrintWebsite\compatibility\DetectAndActivate' => self::REUSE,
                'Avpvh\PrintWebsite\services\ExternalResourceCache' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\services\generators\EpubGenerator' => [
                'Avpvh\PrintWebsite\db\PostFetcher' => self::REUSE,
                'Avpvh\PrintWebsite\compatibility\DetectAndActivate' => self::REUSE,
                'Avpvh\PrintWebsite\services\ExternalResourceCache' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\services\generators\WordGenerator' => [
                'Avpvh\PrintWebsite\db\PostFetcher' => self::REUSE,
                'Avpvh\PrintWebsite\compatibility\DetectAndActivate' => self::REUSE,
                'Avpvh\PrintWebsite\services\ExternalResourceCache' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\services\FileFormatRegistry' => [
                'Avpvh\PrintWebsite\factories\FileFormatFactory' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\entities\FileFormat' => [
                'Avpvh\PrintWebsite\services\DesignTemplateRegistry' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\db\PostFetcher' => [
                'Avpvh\PrintWebsite\system\CustomPostTypes' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\system\CustomPostTypes' => [
                'Avpvh\PrintWebsite\services\SvgDoer' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\services\DebugInfo' => [
                'Avpvh\PrintWebsite\orm\managers\ProjectManager' => self::REUSE,
                'Avpvh\PrintWebsite\orm\managers\DesignManager' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\services\SectionTemplateRegistry' => [
                'Avpvh\PrintWebsite\services\DesignTemplateRegistry' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\db\migrations\MigrationManager' => [
                'Avpvh\PrintWebsite\Twine\system\RequestType' => self::REUSE,
                'Avpvh\PrintWebsite\Twine\system\VersionHistory' => self::REUSE,
                'avpvh_prwb_',
            ],
            'Avpvh\PrintWebsite\domain\PrintButtons' => [
                'Avpvh\PrintWebsite\domain\FrontendPrintSettings' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\domain\FrontendPrintSettings' => [
                'Avpvh\PrintWebsite\domain\PrintOptions' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\domain\PrintPageUrlGenerator' => [
                'Avpvh\PrintWebsite\domain\FrontendPrintSettings' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\system\Capabilities' => [
                'Avpvh\PrintWebsite\system\CustomPostTypes' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\compatibility\plugins\Wpml' => [
                'Avpvh\PrintWebsite\orm\managers\ProjectManager' => self::REUSE,
                'Avpvh\PrintWebsite\orm\managers\DesignManager' => self::REUSE,
                'Avpvh\PrintWebsite\system\CustomPostTypes' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\domain\DefaultDesignTemplates' => [
                'Avpvh\PrintWebsite\helpers\ImageHelper' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\services\ExternalResourceCache' => [
                'Avpvh\PrintWebsite\orm\managers\ExternalResourceManager' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\controllers\Frontend' => [
                'Avpvh\PrintWebsite\orm\managers\ProjectManager' => self::REUSE,
                'Avpvh\PrintWebsite\services\FileFormatRegistry'   => self::REUSE,
                'Avpvh\PrintWebsite\services\PmbCentral' => self::REUSE,
            ],
            'Avpvh\PrintWebsite\system\FileUploads' => [],
        ];
    }
}
