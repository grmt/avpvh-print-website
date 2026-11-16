<?php

namespace Avpvh\PrintWebsite\Twine\services\filesystem;

/**
 * Class Folder
 * @package Avpvh\PrintWebsite\Twine\services\filesystem
 */
class Folder extends ThingOnServer
{
    /**
     * @var Folder
     */
    protected $parent;

    /**
     * @var File
     */
    protected $index_file;

    /**
     * @return bool
     */
    public function exists()
    {
        if ($this->exists === null) {
            $this->exists = is_dir($this->getPath());
        }
        return $this->exists;
    }

    /**
     * @return string
     */
    public function parentFolderPath()
    {
        return dirname($this->getPath());
    }

    /**
     * @return Folder
     */
    public function parentFolder()
    {
        if (! $this->parent instanceof Folder) {
            $this->parent = new Folder($this->parentFolderPath());
        }
        return $this->parent;
    }

    /**
     * If folder doesn't exist, creates it.
     */
    public function ensureExists()
    {
        if (! $this->exists()) {
            // Parents first, so each one gets its index file.
            $this->parentFolder()->ensureExists();
            // wp_mkdir_p() creates missing parents and uses the permissions of the parent folder, not 0777.
            wp_mkdir_p($this->getPath());
            $this->exists = true;
            $this->secure();
        }
    }

    /**
     * Make the folder safer by ensuring there is an index file.
     */
    public function secure()
    {
        if (! $this->indexFile()->fileExists()) {
            $this->indexFile()->write('No listing this directory.');
        }
    }

    /**
     * @return File
     */
    protected function indexFile()
    {
        if (! $this->index_file) {
            $this->index_file = new File($this->indexFilePath());
        }
        return $this->index_file;
    }

    /**
     * Returns the path where the index file should be.
     * @return string
     */
    protected function indexFilePath()
    {
        return $this->getPath() . '/index.html';
    }

    /**
     * @return bool success
     */
    public function delete()
    {
        return self::deleteRecursively($this->getPath());
    }

    /**
     * Deletes a folder with everything in it, including subfolders. Symlinks are removed, not followed.
     * @param string $path
     * @return bool success
     */
    protected static function deleteRecursively($path)
    {
        if (! is_dir($path) || is_link($path)) {
            return false;
        }
        $entries = scandir($path);
        if ($entries === false) {
            return false;
        }
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $entry_path = $path . '/' . $entry;
            if (is_dir($entry_path) && ! is_link($entry_path)) {
                self::deleteRecursively($entry_path);
            } else {
                // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
                unlink($entry_path);
            }
        }
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
        return rmdir($path);
    }
}
