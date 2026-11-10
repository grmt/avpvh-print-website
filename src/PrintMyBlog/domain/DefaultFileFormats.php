<?php

namespace Avpvh\PrintWebsite\domain;

use Avpvh\PrintWebsite\entities\FileFormat;

/**
 * Class DefaultFileFormats
 * @package Avpvh\PrintWebsite\domain
 */
class DefaultFileFormats
{
    const DIGITAL_PDF = 'digital_pdf';
    const PRINT_PDF = 'print_pdf';
    const EPUB = 'epub';
    const WORD = 'word';

    /**
     * Registers file formats.
     */
    public function registerFileFormats()
    {
        avpvh_prwb_register_file_format(
            self::DIGITAL_PDF,
            [
                'title' => __('Digital PDF', 'avpvh-print-website'),
                'icon' => 'dashicons-desktop',
                'generator' => 'Avpvh\PrintWebsite\services\generators\PdfGenerator',
                'default' => 'classic_digital',
                'desc' => __('PDF file intended for viewing on a computer, tablet or phone, but not necessarily for printing to paper. Usually includes working hyperlinks, ample colors, and other features that require a device.', 'avpvh-print-website'),
                'color' => '#b3f0ff',
                'extension' => 'pdf',
            ]
        );
        avpvh_prwb_register_file_format(
            self::PRINT_PDF,
            [
                'title' => __('Print-Ready PDF', 'avpvh-print-website'),
                'icon' => 'dashicons-book-alt',
                'generator' => 'Avpvh\PrintWebsite\services\generators\PdfGenerator',
                'default' => 'classic_print',
                'desc' => __('PDF file intended for printing on your home printer or with a printer service. Usually removes hyperlinks, avoids excessive ink use, and are designed for viewing the 2-page spread (using the front and back of a page).', 'avpvh-print-website'),
                'color' => '#B5F2B5',
                'extension' => 'pdf',
            ]
        );

        // only show it enabled if this version of PMB has the necessary files included.
        $ebook_supported =
            file_exists(AVPVH_PRWB_SCRIPTS_DIR . 'libs/epub-gen-memory__premium_only.min.js') &&
            file_exists(AVPVH_PRWB_SCRIPTS_DIR . 'libs/web-streams-ponyfill__premium_only.min.js') &&
            file_exists(AVPVH_PRWB_SCRIPTS_DIR . 'libs/streamsaver__premium_only.min.js') &&
            file_exists(AVPVH_PRWB_SCRIPTS_DIR . 'libs/filesaver__premium_only.min.js') &&
            file_exists(AVPVH_PRWB_SCRIPTS_DIR . 'epub-generator.js');
        $word_supported =
            file_exists(AVPVH_PRWB_SCRIPTS_DIR . 'libs/filesaver__premium_only.min.js') &&
            file_exists(AVPVH_PRWB_SCRIPTS_DIR . 'pmb-word__premium_only.js');
        avpvh_prwb_register_file_format(
            self::EPUB,
            [
                'title' => __('eBook (ePub)', 'avpvh-print-website'),
                'icon' => 'dashicons-tablet',
                'generator' => 'Avpvh\PrintWebsite\services\generators\EpubGenerator',
                'default' => 'classic_epub',
                'desc' => __('ePub file intended for reading from an eReader, tablet, or phone; or for publishing on an eBook marketplace like Amazon\'s Kindle Direct Publishing, Apple Books, or Kobo.', 'avpvh-print-website'),
                'color' => '#ffcc00',
                'extension' => 'epub',
                'supported' => $ebook_supported,
                'upsell' => '',
            ]
        );
        avpvh_prwb_register_file_format(
            self::WORD,
            [
                'title' => __('Word Document', 'avpvh-print-website'),
                'icon' => 'dashicons-media-document',
                'generator' => 'Avpvh\PrintWebsite\services\generators\WordGenerator',
                'default' => 'classic_word',
                'desc' => __('File editable in Microsoft Word, Google Docs, and LibreOffice (not Apple\'s Pages app, unfortunately). Useful for sharing persons and programs that require a ".doc" file. Word Documents typically require manual formatting in your chosen Word Processor.', 'avpvh-print-website'),
                'color' => '#ffbdde',
                'extension' => 'doc',
                'supported' => $word_supported,
                'upsell' => '',
            ]
        );
    }
}
