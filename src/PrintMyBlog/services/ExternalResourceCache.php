<?php

namespace Avpvh\PrintWebsite\services;

use Avpvh\PrintWebsite\orm\managers\ExternalResourceManager;
use stdClass;
use Avpvh\PrintWebsite\Twine\services\filesystem\File;
use Avpvh\PrintWebsite\Twine\services\filesystem\Folder;
use WP_Error;

/**
 * Class ExternalResourceCache
 * @package Avpvh\PrintWebsite\services
 */
class ExternalResourceCache
{
    /**
     * @var ExternalResourceManager
     */
    private $external_resouce_manager;

    /**
     * @param ExternalResourceManager $external_resource_manager
     */
    public function inject(ExternalResourceManager $external_resource_manager)
    {
        $this->external_resouce_manager = $external_resource_manager;
    }

    /**
     * @return string
     */
    protected function getCacheDir()
    {
        $uploads_dir = wp_upload_dir();
        return $uploads_dir['basedir'] . '/pmb/cache/';
    }

    /**
     * @return string
     */
    protected function getCacheUrl()
    {
        $uploads_dir = wp_upload_dir();
        return $uploads_dir['baseurl'] . '/pmb/cache/';
    }

    /**
     * @param string $external_url
     * @return string|null|false URL of copied resource, null if not yet copied, or false if there was an error
     */
    public function writeAndMapFile($external_url)
    {
        if (! avpvh_prwb_external_requests_enabled()) {
            $this->external_resouce_manager->map($external_url, null);
            return false;
        }
        $start_of_querystring = strpos($external_url, '?');
        if (! $start_of_querystring === false) {
            $querystring = substr($external_url, $start_of_querystring);
            $external_url_sans_querystring = substr($external_url, 0, $start_of_querystring);
        } else {
            $querystring = '';
            $external_url_sans_querystring = $external_url;
        }

        $url_path = (string) wp_parse_url($external_url_sans_querystring, PHP_URL_PATH);
        $extension = strtolower(pathinfo($url_path, PATHINFO_EXTENSION));
        $basename = sanitize_file_name(
            $extension
                ? substr($external_url_sans_querystring, 0, -1 - strlen($extension))
                : $external_url_sans_querystring
        );

        $folder = $this->getCacheDir();

        // wp_safe_remote_get() refuses local and private addresses, so this can't be used to reach internal services.
        $response = wp_safe_remote_get(
            $external_url,
            [
                'timeout' => 15,
                'user-agent' => 'PostmanRuntime/7.26.8',
                'httpversion' => '1.1',
            // streaming the file directly to the FS sounds more efficient, but it actually still goes into memory and seems buggy
            // 'stream' => true,
            // 'filename'=> $folder . $copy_filename,
            ]
        );
        $safe_extension = null;
        if (is_array($response) && $response['response']['code'] === 200 && ! $response instanceof WP_Error) {
            $safe_extension = $this->safeImageExtension(
                $extension,
                (string) wp_remote_retrieve_header($response, 'content-type')
            );
        }
        if ($safe_extension !== null) {
            // Only image extensions are ever written, so the cache can't be used to drop scripts on the server.
            $copy_filename = $basename . '.' . $safe_extension;
            $filepath = $folder . '/' . $copy_filename;
            $content = $response['body'];
            $file = new File($filepath);
            $file->write($content);
            $this->external_resouce_manager->map($external_url, $copy_filename . $querystring);
        } else {
            $this->external_resouce_manager->map($external_url, null);
        }

        return $this->getLocalUrlFromExternalUrl($external_url);
    }

    /**
     * Picks the extension to save a fetched resource under: the URL's extension if it is an image one, otherwise the
     * one that matches the response's image Content-Type. Anything else isn't cached.
     * @param string $url_extension lowercase extension from the URL path, or ''
     * @param string $content_type the response's Content-Type header
     * @return string|null
     */
    protected function safeImageExtension($url_extension, $content_type)
    {
        $allowed = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'avif' => 'image/avif',
            'bmp' => 'image/bmp',
        ];
        if (isset($allowed[$url_extension])) {
            return $url_extension;
        }
        $mime = strtolower(trim(explode(';', $content_type)[0]));
        $by_mime = array_search($mime, $allowed, true);
        return $by_mime === false ? null : $by_mime;
    }

    /**
     * @param string $external_url
     * @return string|null|false null if not yet cached; false if there was an error caching it
     */
    public function getLocalUrlFromExternalUrl($external_url)
    {
        $external_resource = $this->external_resouce_manager->getByExternalUrl($external_url);
        if ($external_resource) {
            if ($external_resource->getCopyFilename()) {
                return $this->getCacheUrl() . $external_resource->getCopyFilename();
            }
            return false;
        }
        return null;
    }

    /**
     * Gets the current mapping from external resources to copied resource URLs
     * @return array|stdClass if it's empty (so WP's localize_script will still make it a JS object)
     */
    public function getMapping()
    {
        foreach ($this->external_resouce_manager->getAllMapping() as $external_resource) {
            $cached_filename = $external_resource->getCopyFilename();
            if ($cached_filename) {
                $cached_file_url = $this->getCacheUrl() . $external_resource->getCopyFilename();
            } else {
                $cached_file_url = null;
            }
            $mapping_absolute_urls[$external_resource->getExternalUrl()] = $cached_file_url;
        }
        if (empty($mapping_absolute_urls)) {
            return new stdClass();
        }
        return $mapping_absolute_urls;
    }

    /**
     * Arrays of domains to treat as local and to not cache locally.
     * @return array
     */
    public function domainsToNotMap()
    {
        return apply_filters(
            'Avpvh\PrintWebsite\services\ExternalResourceCache->domainsToNotMap()',
            [
                str_replace(['http://', 'https://'], '', site_url()),
                '.wp.com',
                'data:',
            ]
        );
    }

    /**
     * Clears the external resource cache.
     */
    public function clear()
    {
        $this->external_resouce_manager->clear();
        $folder = new Folder($this->getCacheDir());
        $folder->delete();
    }
}
