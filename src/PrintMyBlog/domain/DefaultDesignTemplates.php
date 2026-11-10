<?php

namespace Avpvh\PrintWebsite\domain;

use Avpvh\PrintWebsite\helpers\ImageHelper;
use Avpvh\PrintWebsite\orm\entities\Design;
use Avpvh\PrintWebsite\Twine\forms\base\FormSectionDetails;
use Avpvh\PrintWebsite\Twine\forms\base\FormSection;
use Avpvh\PrintWebsite\Twine\forms\helpers\InputOption;
use Avpvh\PrintWebsite\Twine\forms\inputs\AdminFileUploaderInput;
use Avpvh\PrintWebsite\Twine\forms\inputs\CheckboxMultiInput;
use Avpvh\PrintWebsite\Twine\forms\inputs\ColorInput;
use Avpvh\PrintWebsite\Twine\forms\inputs\DatepickerInput;
use Avpvh\PrintWebsite\Twine\forms\inputs\FormInputBase;
use Avpvh\PrintWebsite\Twine\forms\inputs\IntegerInput;
use Avpvh\PrintWebsite\Twine\forms\inputs\SelectInput;
use Avpvh\PrintWebsite\Twine\forms\inputs\SelectRevealInput;
use Avpvh\PrintWebsite\Twine\forms\inputs\TextAreaInput;
use Avpvh\PrintWebsite\Twine\forms\inputs\TextInput;
use Avpvh\PrintWebsite\Twine\forms\inputs\WysiwygInput;
use Avpvh\PrintWebsite\Twine\forms\inputs\YesNoInput;
use Avpvh\PrintWebsite\Twine\forms\strategies\validation\TextValidation;

/**
 * Class DefaultDesignTemplates
 * @package Avpvh\PrintWebsite\domain
 */
class DefaultDesignTemplates
{

    /**
     * @var ImageHelper
     */
    private $image_helper;

    /**
     * @param ImageHelper $image_helper
     */
    public function inject(ImageHelper $image_helper)
    {
        $this->image_helper = $image_helper;
    }

    /**
     * Registers design templates.
     */
    public function registerDesignTemplates()
    {
        avpvh_prwb_register_design_template(
            'classic_print',
            function () {
                return [
                    'title'                 => __('Classic Print PDf', 'avpvh-print-website'),
                    'format'                => 'print_pdf',
                    'dir'                   => AVPVH_PRWB_DESIGNS_DIR . 'pdf/print/classic',
                    'url' => plugins_url('designs/pdf/print/classic', AVPVH_PRWB_MAIN_FILE),
                    'default' => 'classic_print',
                    'docs' => 'https://printmy.blog/user-guide/pdf-design/classic-print-pdf-and-variations/',
                    'supports' => [
                        'front_matter',
                        'part',
                        'back_matter',
                    ],
                    'design_form_callback'  => function () {
                        return $this->getDefaultDesignForm()->merge($this->getDefaultPdfDesignForm())->merge(
                            new FormSection(
                                [
                                    'subsections' => [
                                        'image' => new FormSection(
                                            [
                                                'subsections' => $this->getImageSnapInputs(),
                                            ]
                                        ),
                                        'fonts' => new FormSectionDetails(
                                            [
                                                'html_summary' => __('Font Settings', 'avpvh-print-website'),
                                                'subsections' => [
                                                    'paragraph_indent' => new YesNoInput(
                                                        [
                                                            'default' => true,
                                                            'html_label_text' => __('Paragraph Indent', 'avpvh-print-website'),
                                                            'html_help_text' => __('Indent the first line of each new paragraph instead of adding a paragraph break.', 'avpvh-print-website'),
                                                        ]
                                                    ),
                                                ],
                                            ]
                                        ),
                                        'links' => new FormSectionDetails(
                                            [
                                                'html_summary' => __('Link, Page Reference, and Footnote Settings', 'avpvh-print-website'),
                                                'subsections' => [
                                                    'internal' => new FormSection(
                                                        [
                                                            'subsections' => [
                                                                'internal_links' => new SelectRevealInput(
                                                                    [
                                                                        'remove' => new InputOption(__('Remove', 'avpvh-print-website')),
                                                                        'parens' => new InputOption(__('Replace with page reference', 'avpvh-print-website')),
                                                                        'footnote' => new InputOption(__('Replace with footnote', 'avpvh-print-website')),
                                                                    ],
                                                                    [
                                                                        'default' => avpvh_prwb_is_premium() ? 'footnote' : 'parens',
                                                                        'html_label_text' => __('Internal Hyperlinks', 'avpvh-print-website') . avpvh_prwb_pro_print_service_only(__('Footnotes and page references only work with Pro PDF Service', 'avpvh-print-website')),
                                                                        'html_help_text' => __('How to display hyperlinks to content included in this project.', 'avpvh-print-website'),
                                                                    ]
                                                                ),
                                                                'parens' => new FormSection(
                                                                    [
                                                                        'subsections' => [
                                                                            'page_reference_text' => $this->getPageReferenceTextInput(),
                                                                        ],
                                                                    ]
                                                                ),
                                                                'footnote' => new FormSection(
                                                                    [
                                                                        'subsections' => [
                                                                            'internal_footnote_text' => $this->getInternalFootnoteTextInput(),
                                                                        ],
                                                                    ]
                                                                ),
                                                            ],
                                                        ]
                                                    ),
                                                    'external' => new FormSection(
                                                        [
                                                            'subsections' => [
                                                                'external_links' => new SelectRevealInput(
                                                                    [
                                                                        'remove' => new InputOption(__('Remove', 'avpvh-print-website')),
                                                                        'footnote' => new InputOption(
                                                                            __('Replace with footnote', 'avpvh-print-website')
                                                                        ),
                                                                    ],
                                                                    [
                                                                        'default' => avpvh_prwb_is_premium() ? 'footnote' : 'remove',
                                                                        'html_label_text' => __('External Hyperlinks', 'avpvh-print-website') . avpvh_prwb_pro_print_service_only(__('Footnotes require Pro', 'avpvh-print-website')),
                                                                        'html_help_text' => __('How to display hyperlinks to content not included in this project.', 'avpvh-print-website'),
                                                                    ]
                                                                ),
                                                                'footnote' => new FormSection(
                                                                    [
                                                                        'subsections' => [
                                                                            'footnote_text' => $this->getExternalFootnoteTextInput(),
                                                                        ],
                                                                    ]
                                                                ),
                                                            ],
                                                        ]
                                                    ),
                                                ],
                                            ]
                                        ),
                                        'video_qr_codes' => $this->getVideoQRCodeInput(),
                                    ],
                                ]
                            )
                        )->merge($this->getGenericDesignForm());
                    },
                    'project_form_callback' => function (Design $design) {
                        return $this->getDefaultProjectForm($design);
                    },
                ];
            }
        );
        avpvh_prwb_register_design_template(
            'classic_digital',
            function () {
                return [
                    'title'           => __('Classic Digital PDF'),
                    'format'          => 'digital_pdf',
                    'default' => 'classic_digital',
                    'dir'             => AVPVH_PRWB_DESIGNS_DIR . 'pdf/digital/classic/',
                    'url' => plugins_url('designs/pdf/digital/classic', AVPVH_PRWB_MAIN_FILE),
                    'docs' => 'https://printmy.blog/user-guide/pdf-design/classic-digital-pdf-settings/',
                    'supports' => [
                        'front_matter',
                        'back_matter',
                        'part',
                    ],
                    'design_form_callback'  => function () {
                        return $this->getDefaultDesignForm()->merge($this->getDefaultPdfDesignForm())->merge(
                            new FormSection(
                                [
                                    'subsections' => [
                                        'image' => new FormSection(
                                            [
                                                'subsections' => $this->getImageSnapInputs(),
                                            ]
                                        ),
                                        'links' => $this->getLinksInputs(),
                                        'video_qr_codes' => $this->getVideoQRCodeInput(),
                                    ],
                                ]
                            )
                        )->merge($this->getGenericDesignForm());
                    },
                    'project_form_callback' => function (Design $design) {
                        return $this->getDefaultProjectForm($design);
                    },
                ];
            }
        );
        avpvh_prwb_register_design_template(
            'buurma',
            function () {
                return [
                    'title'           => __('Buurma Digital PDF'),
                    'format'          => 'digital_pdf',
                    'dir'             => AVPVH_PRWB_DESIGNS_DIR . 'pdf/digital/buurma/',
                    'default' => 'buurma',
                    'url' => plugins_url('designs/pdf/digital/buurma', AVPVH_PRWB_MAIN_FILE),
                    'docs' => 'https://printmy.blog/user-guide/pdf-design/buurma-whitepaper-digital-pdf/',
                    'supports' => [
                        'front_matter',
                        'back_matter',
                    ],
                    'design_form_callback'  => function () {
                        return (new FormSection(
                            [
                                'subsections' =>
                                [
                                    'title_page_banner_color' => new ColorInput(
                                        [
                                            'html_label_text' => __('Title Page Top-Banner Color', 'avpvh-print-website'),
                                            'html_help_text' => __('Image used at the top of the background on the title page.', 'avpvh-print-website'),
                                            'default' => '#02a5fd',
                                        ]
                                    ),
                                    'background_color' => new ColorInput(
                                        [
                                            'html_label_text' => __('Background Color', 'avpvh-print-website'),
                                            'html_help_text' => __(
                                                'A gradient between this color and white will be used in the page backgrounds',
                                                'avpvh-print-website'
                                            ),
                                            'default' => '#82d7ff',
                                        ]
                                    ),
                                    'org' => new TextInput(
                                        [
                                            'html_label_text' => __('Organization Name', 'avpvh-print-website'),
                                            'html_help_text' => __(
                                                'Shown in the title page. Eg "Institute of AV-PvH Print Website"',
                                                'avpvh-print-website'
                                            ),
                                        ]
                                    ),
                                    'background_embellishment' => new AdminFileUploaderInput(
                                        [
                                            'html_label_text' => __('Background Embellishment', 'avpvh-print-website'),
                                            'html_help_text' => __('Faded image used as a full-page background on the title page, and next to the page number on numbered pages.', 'avpvh-print-website'),
                                            'default' => plugins_url('designs/pdf/digital/buurma/assets/logo.svg', AVPVH_PRWB_MAIN_FILE),
                                        ]
                                    ),
                                    'default_alignment' => $this->getDefaultAlignmentInput(),
                                    'internal_footnote_text' => $this->getInternalFootnoteTextInput(),
                                    'footnote_text' => $this->getExternalFootnoteTextInput(),
                                    'video_qr_codes' => $this->getVideoQRCodeInput(),
                                ],
                            ]
                        ))->merge($this->getGenericDesignForm());
                    },
                    'project_form_callback' => function (Design $design) {
                        return new FormSection(
                            [
                                'subsections' => [
                                    'issue' => new TextInput(
                                        [
                                            'html_label_text' => __('Issue', 'avpvh-print-website'),
                                            'html_help_text' => __('Text that appears at the top-right of the cover'),
                                        ]
                                    ),
                                    'byline' => new TextAreaInput(
                                        [
                                            'html_label_text' => __('ByLine', 'avpvh-print-website'),
                                            'html_help_text' => __('Project Author(s)', 'avpvh-print-website'),
                                        ]
                                    ),
                                    'date' => new DatepickerInput(
                                        [
                                            'html_label_text' => __('Date Issued', 'avpvh-print-website'),
                                            'html_help_text' => __('Text that appears under the byline', 'avpvh-print-website'),
                                        ]
                                    ),
                                    'cover_preamble' => new TextAreaInput(
                                        [
                                            'html_label_text' => __('Coverpage Preamble', 'avpvh-print-website'),
                                            'html_help_text' => __('Explanatory text that appears at the bottom of the cover page', 'avpvh-print-website'),
                                        ]
                                    ),
                                ],
                            ]
                        );
                    },
                ];
            }
        );
        avpvh_prwb_register_design_template(
            'mayer',
            function () {
                return [
                    'title'           => __('Mayer Digital PDF'),
                    'format'          => 'digital_pdf',
                    'dir'             => AVPVH_PRWB_DESIGNS_DIR . 'pdf/digital/mayer/',
                    'default' => 'mayer',
                    'docs' => 'https://printmy.blog/user-guide/pdf-design/mayer-magazine-digital-pdf/',
                    'supports' => [
                        'front_matter',
                        'part',
                        'back_matter',
                    ],
                    'url' => plugins_url('designs/pdf/digital/mayer', AVPVH_PRWB_MAIN_FILE),
                    'design_form_callback'  => function () {
                        $design_form = (new FormSection(
                            [
                                'subsections' => [
                                    'post_content' => $this->getPostContentInput(),
                                    'page_per_post' => new YesNoInput(
                                        [
                                            'default' => false,
                                            'html_label_text' => __('Each Post Begins on a New Page', 'avpvh-print-website') . avpvh_prwb_pro_better(__('Browser support for pagebreaks on columns is typically not very good.', 'avpvh-print-website')),
                                            'html_help_text' => __('Whether to force posts to always start on a new page. Doing so makes the page more legible, but uses more paper.', 'avpvh-print-website'),
                                        ]
                                    ),
                                    'post_header_in_columns' => new YesNoInput(
                                        [
                                            'html_label_text' => __('Show Post Header inside Columns', 'avpvh-print-website'),
                                            'html_help_text' => __('Check this to make post header information, like title, date, author, etc, appear inside columns; uncheck this to have it take up the full page width', 'avpvh-print-website'),
                                        ]
                                    ),
                                    'dividing_line' => new YesNoInput(
                                        [
                                            'html_label_text' => __('Show a Dividing Line Between Posts', 'avpvh-print-website'),
                                        ]
                                    ),
                                    'images_full_column' => new YesNoInput(
                                        [
                                            'html_label_text' => __('Full-Column Images', 'avpvh-print-website'),
                                            'html_help_text' => __('Resizes images to be the full column width (except ones with the CSS class "mayer-no-resize")', 'avpvh-print-website'),
                                        ]
                                    ),
                                    'no_extra_columns' => new YesNoInput(
                                        [
                                            'html_label_text' => __('Remove Extra Columns', 'avpvh-print-website'),
                                            'default' => true,
                                            'html_help_text' => __('Forces your content to only use two columns, even if the content itself was divided into more columns (eg using the "Columns" block)', 'avpvh-print-website'),
                                        ]
                                    ),
                                    'image' => new FormSection(
                                        [
                                            'subsections' => $this->getImageSnapInputs(),
                                        ]
                                    ),
                                    'video_qr_codes' => $this->getVideoQRCodeInput(),
                                ],
                            ]
                        ))->merge($this->getGenericDesignForm());
                        $design_form->findSection('image_placement')->removeOption('dynamic-resize');
                        $design_form->findSection('post_content')->setDefault(
                        [
                            'title',
                            'featured_image',
                            'content',
                        ]
                        );
                        $design_form->removeSubsection('dynamic-resize');
                        return $design_form;
                    },
                    'project_form_callback' => function (Design $design) {
                        $sections['byline'] = new TextInput(
                            [
                                'html_display_text' => __('Byline', 'avpvh-print-website'),
                                'html_help_text' => __('Project author(s)', 'avpvh-print-website'),
                            ]
                        );
                        $sections['cover_preamble'] = new TextAreaInput(
                            [
                                'html_label_text' => __('Coverpage Preamble', 'avpvh-print-website'),
                                'html_help_text' => __(
                                    'Explanatory text that appears at the bottom of the cover page',
                                    'avpvh-print-website'
                                ),
                            ]
                        );
                        return new FormSection(
                            [
                                'subsections' => $sections,
                            ]
                        );
                    },
                ];
            }
        );

        // it's ok to register this design even if the format isn't registered (which it isn't for the wp.org version)
        avpvh_prwb_register_design_template(
            'classic_epub',
            function () {
                return [
                    'title'                 => __('Classic ePub', 'avpvh-print-website'),
                    'format'                => 'epub',
                    'dir'                   => AVPVH_PRWB_DESIGNS_DIR . 'epub/classic',
                    'url' => plugins_url('designs/epub/classic', AVPVH_PRWB_MAIN_FILE),
                    'default' => 'classic_epub',
                    'docs' => 'https://printmy.blog/user-guide/pdf-design/6-classic-epub-ebook/',
                    'supports' => [
                        'front_matter',
                        'part',
                        'back_matter',
                    ],
                    'design_form_callback'  => function () {

                        $form = $this->getDefaultDesignForm()->merge($this->getGenericDesignForm());
                        $form->addSubsections(
                            [
                                'convert_videos' => new YesNoInput(
                                    [
                                        'html_label_text' => __('Convert Videos to Images and Links', 'avpvh-print-website'),
                                        'html_help_text' => __('Some eReaders don\'t show videos, in which case you may prefer to replace them with an image and a hyperlink to the online video content.', 'avpvh-print-website'),
                                        'default' => false,
                                    ]
                                ),
                            ],
                            'generic_sections'
                        );
                        $form->addSubsections(
                            [
                                'fonts' => new FormSectionDetails(
                                    [
                                        'html_summary' => __('Font Settings', 'avpvh-print-website'),
                                        'subsections' => [
                                            'main_header_font_size' => new TextInput(
                                                [
                                                    'default' => '24pt',
                                                    'html_label_text' => __('Title page and Part Header Font Size', 'avpvh-print-website'),
                                                    'html_help_text' => sprintf(
                                                    // translators: 1: opening anchor tag, 2: closing anchor tag, 3: opening anchor tag
                                                        __('Font size used for the default title page’s and part’s header (all other headers’ sizes are derived from the main font size). Use any recognized %1$sCSS font-size keyword%2$s (like "large", "medium", "small") or a %3$slength in any units%2$s (eg "14pt", "50%%", or "10px").'),
                                                        '<a href="https://www.w3schools.com/cssref/pr_font_font-size.asp" target="_blank">',
                                                        '</a>',
                                                        '<a href="https://www.w3schools.com/cssref/css_units.asp" target="_blank">'
                                                    ),
                                                ]
                                            ),
                                            'custom_header_font' => new AdminFileUploaderInput(
                                                [
                                                    'default' => '',
                                                    'html_label_text' => __('Custom Header Font', 'avpvh-print-website'),
                                                    'html_help_text' => __('Default font to use for headers of articles, parts, etc. Font files with extension "ttf" work with most eBook readers. Leave blank to use the default font.', 'avpvh-print-website')
                                                ]
                                            ),
                                            'font_size' => new TextInput(
                                                [
                                                    'default' => '12pt',
                                                    'html_label_text' => __('Font Size', 'avpvh-print-website'),
                                                    'html_help_text' => sprintf(
                                                    // translators: 1: opening anchor tag, 2: closing anchor tag, 3: opening anchor tag
                                                        __('Use any recognized %1$sCSS font-size keyword%2$s (like "large", "medium", "small") or a %3$slength in any units%2$s (eg "14pt", "50%%", or "10px").'),
                                                        '<a href="https://www.w3schools.com/cssref/pr_font_font-size.asp" target="_blank">',
                                                        '</a>',
                                                        '<a href="https://www.w3schools.com/cssref/css_units.asp" target="_blank">'
                                                    ),
                                                ]
                                            ),
                                            'custom_font' => new AdminFileUploaderInput(
                                                [
                                                    'default' => '',
                                                    'html_label_text' => __('Custom Font', 'avpvh-print-website'),
                                                    'html_help_text' => __('Default font used in paragraphs, lists, tables, captions, etc. Font files with extension "ttf" work with most eBook readers. Leave blank to use the default font.', 'avpvh-print-website')
                                                ]
                                            ),

                                        ],
                                    ]
                                ),
                            ],
                            'generic_sections',
                            true
                        );
                        return $form;
                    },
                    'project_form_callback' => function (Design $design) {
                        $project_form = $this->getDefaultProjectForm($design);
                        $project_form->merge(
                            new FormSection(
                                [
                                    'subsections' => [
                                        'post_name' => new TextInput(
                                            [
                                                'html_label_text' => __('File name', 'avpvh-print-website'),
                                            ]
                                        ),
                                        'byline' => new TextAreaInput(
                                            [
                                                'html_label_text' => __('ByLine', 'avpvh-print-website'),
                                                'html_help_text' => __('Project Author(s)', 'avpvh-print-website'),
                                            ]
                                        ),
                                        'post_content' => new TextAreaInput(
                                            [
                                                'html_label_text' => __('Description', 'avpvh-print-website'),
                                                'html_help_text' => __('Shown as eBook metadata.', 'avpvh-print-website'),
                                            ]
                                        ),
                                        'cover' => new AdminFileUploaderInput(
                                            [
                                                'html_label_text' => __('Cover Image', 'avpvh-print-website'),
                                                'html_help_text' => __('Cover image used on eBook file (does not necessarily appear inside project). Ideal dimensions are 2,560 x 1,600 pixels.', 'avpvh-print-website'),
                                                'default' => plugins_url('assets/images/icon-128x128.jpg', AVPVH_PRWB_MAIN_FILE),
                                            ]
                                        ),
                                    ],
                                ]
                            )
                        );
                        return $project_form;
                    },
                ];
            }
        );

        // it's ok to register this design even if the format isn't registered (which it isn't for the wp.org version)
        avpvh_prwb_register_design_template(
            'classic_word',
            function () {
                return [
                    'title'                 => __('Classic Word', 'avpvh-print-website'),
                    'format'                => DefaultFileFormats::WORD,
                    'dir'                   => AVPVH_PRWB_DESIGNS_DIR . 'word/classic',
                    'url' => plugins_url('designs/word/classic', AVPVH_PRWB_MAIN_FILE),
                    'default' => 'classic_word',
                    'docs' => 'https://printmy.blog/user-guide/pdf-design/7-classic-word-document/',
                    'supports' => [
                        'front_matter',
                        'part',
                        'back_matter',
                    ],
                    'design_form_callback'  => function () {
                        $unique_form = new FormSection(
                            [
                                'subsections' => [
                                    'headers_and_footers' => new FormSectionDetails(
                                        [
                                            'html_summary' => __('Headers and Footers', 'avpvh-print-website'),
                                            'subsections' => [
                                                'first_header' => new TextAreaInput(
                                                    [
                                                        'html_label_text' => __('Document Header on First Page', 'avpvh-print-website'),
                                                        'html_help_text' => __('Content to appear in top margin area but only on the first page.', 'avpvh-print-website'),
                                                        'default' => ''
                                                    ]
                                                ),
                                                'header' => new TextAreaInput(
                                                    [
                                                        'html_label_text' => __('Document Header on Subsequent Pages', 'avpvh-print-website'),
                                                        'html_help_text' => __('Content to appear in top margin area on all pages except the first page.', 'avpvh-print-website'),
                                                        'default' => ''
                                                    ]
                                                ),
                                                'footer' => new TextAreaInput(
                                                    [
                                                        'html_label_text' => __('Document Footer', 'avpvh-print-website'),
                                                        'html_help_text' => __('Content to appear in bottom margin area.', 'avpvh-print-website'),
                                                        'default' => ''
                                                    ]
                                                ),
                                            ]
                                        ]
                                    ),
                                    'convert_videos' => new YesNoInput(
                                        [
                                            'html_label_text' => __('Convert Videos to Images and Links', 'avpvh-print-website'),
                                            'html_help_text' => __('Some Word Processors don\'t show videos, in which case you may prefer to replace them with an image and a hyperlink to the online video content.', 'avpvh-print-website'),
                                            'default' => true,
                                        ]
                                    ),
                                    'image' => new FormSection(
                                        [
                                            'subsections' => [
                                                'image_size' => new IntegerInput(
                                                    [
                                                        'html_label_text' => __('Maximum Image Height (in pixels)', 'avpvh-print-website'),
                                                        'html_help_text' => sprintf(
                                                            __(
                                                                'Larger images will be resized to this, smaller images will be unchanged.',
                                                                'avpvh-print-website'
                                                            )
                                                        ),
                                                        'default' => 1000,
                                                    ]
                                                ),
                                            ]
                                        ]
                                    ),
                                    'internal_links' => new SelectRevealInput(
                                        [
                                            'remove' => new InputOption(
                                                __('Remove', 'avpvh-print-website')
                                            ),
                                            'leave_external' => new InputOption(
                                                __('Leave as hyperlink to website', 'avpvh-print-website')
                                            ),
                                            'leave' => new InputOption(
                                                __('Leave as hyperlink to document', 'avpvh-print-website')
                                            ),
                                        ],
                                        [
                                            'default' => 'leave',
                                            'html_label_text' => __('Internal Hyperlinks', 'avpvh-print-website'),
                                            'html_help_text' => __('How to display hyperlinks to content included in this project.', 'avpvh-print-website'),
                                        ]
                                    ),
                                    'external_links' => new SelectRevealInput(
                                        [
                                            'remove' => new InputOption(
                                                __('Remove', 'avpvh-print-website')
                                            ),
                                            'leave' => new InputOption(
                                                __('Leave as hyperlink', 'avpvh-print-website')
                                            ),
                                        ],
                                        [
                                            'default' => 'leave',
                                            'html_label_text' => __('External Hyperlinks', 'avpvh-print-website'),
                                            'html_help_text' => __('How to display hyperlinks to content not included in this project.', 'avpvh-print-website'),
                                        ]
                                    ),
                                ]
                            ]
                        );
                        $form = $this->getDefaultDesignForm()->merge($this->getGenericDesignForm())->merge($unique_form);
                        $form->getProperSubsection('generic_sections', false)->removeSubsection('powered_by');
                        return $form;
                    },
                    'project_form_callback' => function (Design $design) {
                        $project_form = $this->getDefaultProjectForm($design);
                        $project_form->merge(
                            new FormSection(
                                [
                                    'subsections' => [
                                        'post_name' => new TextInput(
                                            [
                                                'html_label_text' => __('File name', 'avpvh-print-website'),
                                            ]
                                        ),
                                        'byline' => new TextAreaInput(
                                            [
                                                'html_label_text' => __('ByLine', 'avpvh-print-website'),
                                                'html_help_text' => __('Project Author(s)', 'avpvh-print-website'),
                                            ]
                                        ),
                                    ],
                                ]
                            )
                        );
                        return $project_form;
                    },
                ];
            }
        );

        avpvh_prwb_register_design_template(
            'haller',
            function () {
                return [
                    'title'           => __('Haller Tabloid Print PDF'),
                    'format'          => 'print_pdf',
                    'dir'             => AVPVH_PRWB_DESIGNS_DIR . 'pdf/print/haller/',
                    'default' => 'haller',
                    'docs' => 'https://printmy.blog/user-guide/pdf-design/haller-tabloid-print-ready-pdf/',
                    'supports' => [
                        'front_matter',
                        'part',
                        'back_matter',
                    ],
                    'url' => plugins_url('designs/pdf/print/haller', AVPVH_PRWB_MAIN_FILE),
                    'design_form_callback'  => function () {
                        $custom_logo_info = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
                        if($custom_logo_info){
                            $custom_logo = $custom_logo_info[0];
                        } else {
                            $custom_logo = '';
                        }
                        $design_form = (new FormSection(
                            [
                                'subsections' => [
                                    'publication_logo' => new AdminFileUploaderInput(
                                        [
                                            'html_label_text' => __('Publication Logo Image', 'avpvh-print-website'),
                                            'default' => $custom_logo,
                                            'html_help_text' => __('Logo image to show on the front page and in the header of subsequent pages. Leave blank to just use a "Title of Publication".', 'avpvh-print-website'),
                                        ]
                                    ),
                                    'publication_title' => new TextInput(
                                        [
                                            'html_label_text' => __('Title of Publication', 'avpvh-print-website'),
                                            'html_help_text' => __('Shown in a large font on front page and in the top margin of every subsequent page.'),
                                            'default' => get_bloginfo('name'),
                                        ]
                                    ),
                                    'publication_subtitle' => new TextInput(
                                        [
                                            'html_label_text' => __('Subtitle of Publication', 'avpvh-print-website'),
                                            'html_help_text' => __('Shown under the name of the publication, in a slightly smaller font.', 'avpvh-print-website'),
                                            'default' => get_bloginfo('description'),
                                        ]
                                    ),
                                    'cover_preamble' => new TextInput(
                                        [
                                            'html_label_text' => __('Publication Preamble', 'avpvh-print-website'),
                                            'html_help_text' => __('Shown on the front page under the Title and Subtitle.', 'avpvh-print-website'),
                                        ]
                                    ),
                                    'images_full_column' => new YesNoInput(
                                        [
                                            'html_label_text' => __('Full-Column Images', 'avpvh-print-website'),
                                            'html_help_text' => __('Resizes images to be the full column width (except ones with the CSS class "mayer-no-resize")', 'avpvh-print-website'),
                                        ]
                                    ),
                                    'columns' => new SelectInput(
                                        [
                                            2 => new InputOption('2'),
                                            3 => new InputOption('3'),
                                            4 => new InputOption('4'),
                                        ],
                                        [
                                            'html_label_text' => __('Columns', 'avpvh-print-website'),
                                            'default' => 3,
                                            'html_help_text' => __('Number of columns to use for content.', 'avpvh-print-website'),
                                        ]
                                    ),
                                    'post_content' => $this->getPostContentInput(),
                                    'no_extra_columns' => new YesNoInput(
                                        [
                                            'html_label_text' => __('Remove Extra Columns', 'avpvh-print-website'),
                                            'default' => false,
                                            'html_help_text' => __('Forces your content to only use two columns, even if the content itself was divided into more columns (eg using the "Columns" block)', 'avpvh-print-website'),
                                        ]
                                    ),
                                    'page' => $this->getPageSubsection(),
                                    'links' => $this->getLinksInputs(),
                                    'video_qr_codes' => $this->getVideoQRCodeInput(),
                                ],
                            ]
                        ))->merge($this->getGenericDesignForm());
                        return $design_form;
                    },
                    'project_form_callback' => function (Design $design) {
                        $sections = [
                            'date' => new TextInput(
                                [
                                    'html_label_text' => __('Date', 'avpvh-print-website'),
                                    'html_help_text' => __('Shown on frontpage and in the top margin of all subsequent pages.', 'avpvh-print-website'),
                                ]
                            ),
                            'issue' => new TextInput(
                                [
                                    'html_label_text' => __('Issue Number', 'avpvh-print-website'),
                                    'html_help_text' => __('Shown on the frontpage and in the top margin of all subsequent pages (shortcodes supported).', 'avpvh-print-website'),
                                ]
                            ),
                            'frontpage_left_side' => new WysiwygInput(
                                [
                                    'html_label_text' => __('Frontpage Title Left Call-Out', 'avpvh-print-website'),
                                    'html_help_text' => __('HTML displayed to the left of the title on the frontpage (shortcodes supported).', 'avpvh-print-website'),
                                ]
                            ),
                            'frontpage_right_side' => new WysiwygInput(
                                [
                                    'html_label_text' => __('Frontpage Title Right Call-Out', 'avpvh-print-website'),
                                    'html_help_text' => __('HTML displayed to the right of the title on the frontpage', 'avpvh-print-website'),
                                ]
                            ),
                        ];

                        return new FormSection(
                            [
                                'subsections' => $sections,
                            ]
                        );
                    },
                ];
            }
        );
    }

    /**
     * @param Design $design
     * @return FormSection
     * @throws \Avpvh\PrintWebsite\Twine\forms\helpers\ImproperUsageException
     */
    public function getDefaultProjectForm(Design $design)
    {
        $sections = [];
        $header_content = $design->getSetting('header_content');
        if (in_array('subtitle', $header_content, true)) {
            $sections['subtitle'] = new TextInput(
                [
                    'html_label_text' => __('Subtitle', 'avpvh-print-website'),
                ]
            );
        }
        if (in_array('byline', $header_content, true)) {
            $sections['byline'] = new TextInput(
                [
                    'html_label_text' => __('Byline', 'avpvh-print-website'),
                    'html_help_text' => __('Project author(s)', 'avpvh-print-website'),
                ]
            );
        }
        if (in_array('url', $header_content, true)) {
            $sections['url'] = new TextInput(
                [
                    'default' => site_url(),
                    'html_label_text' => __('Source Location', 'avpvh-print-website'),
                    'html_help_text' => __('Shown on the title page under the subtitle. Could be your website’s URL, or anything else you like.', 'avpvh-print-website'),
                ]
            );
        }
        return new FormSection(
            [
                'subsections' => $sections,
            ]
        );
    }

    /**
     * Gets the default design form sections for classic default designs.
     * @return FormSection
     */
    public function getDefaultDesignForm()
    {
        return new FormSection(
            [
                'subsections' => [
                    'header_content' => new CheckboxMultiInput(
                        [
                            'title' => new InputOption(__('Project Title', 'avpvh-print-website')),
                            'subtitle' => new InputOption(__('Subtitle', 'avpvh-print-website')),
                            'byline' => new InputOption(__('Byline', 'avpvh-print-website')),
                            'url' => new InputOption(__('Site URL', 'avpvh-print-website')),
                            'date_printed' => new InputOption(__('Date Printed', 'avpvh-print-website')),
                        ],
                        [
                            'default' => [
                                'title',
                                'subtitle',
                                'url',
                                'date_printed',
                            ],
                            'html_label_text' => __('Title Page Content'),
                        ]
                    ),
                    'post_content' => $this->getPostContentInput(),
                ],
            ]
        );
    }

    /**
     * @return CheckboxMultiInput
     */
    protected function getPostContentInput()
    {
        return new CheckboxMultiInput(
            [
                'title' => new InputOption(__('Post Title', 'avpvh-print-website')),
                'id' => new InputOption(__('ID', 'avpvh-print-website')),
                'author' => new InputOption(__('Author', 'avpvh-print-website')),
                'published_date' => new InputOption(__('Published Date', 'avpvh-print-website')),
                'categories' => new InputOption(__('Categories and Tags', 'avpvh-print-website')),
                'url' => new InputOption(__('URL', 'avpvh-print-website')),


                'featured_image' => new InputOption(__('Featured Image', 'avpvh-print-website')),
                'excerpt' => new InputOption(__('Excerpt', 'avpvh-print-website')),
                'meta' => new InputOption(__('Custom Fields', 'avpvh-print-website')),
                'content' => new InputOption(__('Content', 'avpvh-print-website')),
            ],
            [
                'default' => [
                    'title',
                    'published_date',
                    'categories',
                    'featured_image',
                    'content',
                ],
                'html_label_text' => __('Post Content'),
                'html_help_text' => __('Content from each post to print.', 'avpvh-print-website'),
            ]
        );
    }

    /**
     * Gets a form specific to classic PDFs
     * @return FormSection
     * @throws \Avpvh\PrintWebsite\Twine\forms\helpers\ImproperUsageException
     */
    protected function getDefaultPdfDesignForm()
    {
        return new FormSection(
            [
                'subsections' => [
                    'page_per_post' => new YesNoInput(
                        [
                            'default' => true,
                            'html_label_text' => __('Each Post Begins on a New Page', 'avpvh-print-website'),
                            'html_help_text' => __('Whether to force posts to always start on a new page. Doing so makes the page more legible, but uses more paper.', 'avpvh-print-website'),
                        ]
                    ),
                    'dividing_line' => new YesNoInput(
                        [
                            'html_label_text' => __('Show a Dividing Line Between Posts', 'avpvh-print-website'),
                        ]
                    ),
                    'fonts' => $this->getPdfFontSettings(),
                    'image' => new FormSectionDetails(
                        [
                            'html_summary' => __('Image & Block Settings', 'avpvh-print-website'),
                            'subsections' => [
                                'image_size' => new IntegerInput(
                                    [
                                        'html_label_text' => __('Maximum Image Height (in pixels)', 'avpvh-print-website'),
                                        'html_help_text' => sprintf(
                                            __(
                                                'Larger images will be resized to this, smaller images will be unchanged.',
                                                'avpvh-print-website'
                                            )
                                        ),
                                        'default' => 500,
                                    ]
                                ),
                                'default_alignment' => $this->getDefaultAlignmentInput(),
                            ],
                        ]
                    ),
                    'page' => $this->getPageSubsection(),
                ],
            ]
        );
    }

    /**
     * @return FormSectionDetails
     * @throws \Avpvh\PrintWebsite\Twine\forms\helpers\ImproperUsageException
     */
    public function getPageSubsection()
    {
        return new FormSectionDetails(
            [
                'html_summary' => __('Page Settings', 'avpvh-print-website'),
                'subsections' => [
                    'page_width' => new TextInput(
                        [
                            'html_label_text' => __('Page Width', 'avpvh-print-website') . avpvh_prwb_pro_print_service_best(__('Not supported by some browsers', 'avpvh-print-website')),
                            'html_help_text' => sprintf(
                                // translators: 1: opening anchor tag, 2: closing anchor tag
                                __('Use standard %1$sCSS units%2$s', 'avpvh-print-website'),
                                '<a href="https://www.w3schools.com/CSSref/css_units.asp">',
                                '</a>'
                            ),
                            'default' => '8.5in',
                        ]
                    ),
                    'page_height' => new TextInput(
                        [
                            'html_label_text' => __('Page Height', 'avpvh-print-website') . avpvh_prwb_pro_print_service_best(__('Not supported by some browsers', 'avpvh-print-website')),
                            'html_help_text' => sprintf(
                                // translators: 1: opening anchor tag, 2: closing anchor tag
                                __('Use standard %1$sCSS units%2$s', 'avpvh-print-website'),
                                '<a href="https://www.w3schools.com/CSSref/css_units.asp">',
                                '</a>'
                            ),
                            'default' => '11in',
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * Returns generic form inputs which should usually appear on all design forms.
     * @return FormSection
     */
    public function getGenericDesignForm()
    {
        $theme = wp_get_theme();
        $use_theme_help_text = sprintf(
            // translators: 1: theme name.
            __('Your theme, "%1$s", can be used in conjunction with your design. Themes are often not intended for print and can conflict with the design, but you may have content that looks broken without the theme.', 'avpvh-print-website'),
            $theme->name
        );
        if (! avpvh_prwb_has_required_plan('business')) {
            $use_theme_help_text = __('Note: this option is only supported for the business license.', 'avpvh-print-website') . '<br>' . $use_theme_help_text;
        }
        if (avpvh_prwb_has_required_plan('hobby')) {
            $powered_by_in_pro_service = false;
        } else {
            $powered_by_in_pro_service = true;
        }

        $image_sizes = $this->image_helper->getAllImageSizes();
        $image_quality_options = [
            '' => new InputOption(__('Don’t Change Image Quality', 'avpvh-print-website')),
        ];

        foreach ($image_sizes as $thumbnail_slug => $thumbnail_data) {
            // Only show non-cropped images because their locations are listed on the img tag's "srcset" attribute.
            // Other devs may have expected loose comparisons so keep doing that.
            // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
            if (isset($thumbnail_data['crop']) && $thumbnail_data['crop'] == true) {
                continue;
            }
            $dimensions = $thumbnail_data['width'];
            $image_quality_options[$dimensions] = new InputOption(
                sprintf(
                    // translators: %s image dimensions, like "120x120"
                    __('%s pixels wide', 'avpvh-print-website'),
                    $dimensions
                )
            );
        }
        ksort($image_quality_options);
        $image_quality_options['scaled'] = new InputOption(__('Full Size (on web)', 'avpvh-print-website'));
        $image_quality_options['uploaded'] = new InputOption(__('Uploaded Size (largest possible)', 'avpvh-print-website'));

        return apply_filters(
            'Avpvh\PrintWebsite\domain\DefaultDesignTemplates->getGenericDesignForm',
            new FormSection(
                [
                    'subsections' => [
                        'image' => new FormSection(
                            [
                                'subsections' => [
                                    'image_quality' => new SelectInput(
                                        $image_quality_options,
                                        [
                                            'html_label_text' => __('Image Quality (in pixels)', 'avpvh-print-website'),
                                            'html_help_text' => sprintf(
                                                // translators: 1: opening anchor tag, 2L closing anchor tag.
                                                __('Lower quality means smaller file size, whereas higher quality means higher resolution images. Note: if images are missing from the generated PDF, the requested image size might not be available. Use the %1$s Regenerate Thumbnails plugin%2$s to create the missing image sizes.', 'avpvh-print-website'),
                                                '<a href="https://wordpress.org/plugins/regenerate-thumbnails/">',
                                                '</a>'
                                            ),
                                            'default' => '',
                                        ]
                                    ),
                                ],
                            ]
                        ),
                        'generic_sections' => new FormSection(
                            [
                                'subsections' =>
                                apply_filters(
                                    'Avpvh\PrintWebsite\domain\DefaultDesignTemplates->getGenericDesignFormSections',
                                    [
                                        'use_theme' => new YesNoInput(
                                            [
                                                'html_label_text' => __('Apply Website Theme', 'avpvh-print-website'),
                                                'html_help_text' => $use_theme_help_text,
                                                'default' => false,
                                            ]
                                        ),
                                        'custom_css' => new TextAreaInput(
                                            [
                                                'html_label_text' => __('Custom CSS', 'avpvh-print-website'),
                                                'html_help_text'  => __('Styles to be applied only when printing projects using this design.', 'avpvh-print-website'),
                                            ]
                                        ),
                                        'powered_by' => new YesNoInput(
                                            [
                                                'html_label_text' => __('Add "Powered By"', 'avpvh-print-website') . ( $powered_by_in_pro_service ? avpvh_prwb_hover_help(__('In compliance with WordPress.org guidelines, not added when printing from your browser.', 'avpvh-print-website')) : ''),
                                                'html_help_text' => __('Instructs the Pro PDF Service to add "Powered by AV-PvH Print Website Pro & WordPress" to your project. Does not appear when printing using your browser.', 'avpvh-print-website'),
                                                'default' => $powered_by_in_pro_service,
                                                'disabled' => $powered_by_in_pro_service,
                                            ]
                                        ),
                                    ]
                                ),
                            ]
                        ),
                    ],
                ]
            )
        );
    }

    /**
     * @return TextInput
     */
    public function getPageReferenceTextInput()
    {
        return new TextInput(
            [
                'html_label_text' => __('Page Reference Text', 'avpvh-print-website'),
                // translators: %s literally the string "%s"
                'html_help_text' => __('Text to use when replacing a hyperlink with a page reference. "%s" will be replaced with the page number.', 'avpvh-print-website'),
                // translators: %s page number
                'default' => __('(see page %s)', 'avpvh-print-website'),
                'validation_strategies' => [
                    // translators: %s literally the string %s.
                    new TextValidation(__('You must include "%s" in the page reference text so we know where to put the page number.', 'avpvh-print-website'), '~.*\%s.*~'),
                ],
            ]
        );
    }

    /**
     * @return TextInput
     */
    public function getInternalFootnoteTextInput()
    {
        return new TextInput(
            [
                'html_label_text' => __('Internal Footnote Text', 'avpvh-print-website'),
                // translators: %s literally the string %s.
                'html_help_text' => __('Text to use when replacing a hyperlink with a footnote. "%s" will be replaced with the page number.', 'avpvh-print-website'),
                // translators: %s literally the string %s.
                'default' => __('See page %s.', 'avpvh-print-website'),
                'validation_strategies' => [
                    // translators: %s literally the string %s.
                    new TextValidation(__('You must include "%s" in the footnote text so we know where to put the URL.', 'avpvh-print-website'), '~.*\%s.*~'),
                ],
            ]
        );
    }

    /**
     * @return TextInput
     */
    public function getExternalFootnoteTextInput()
    {
        return new TextInput(
            [
                'html_label_text' => __('External Footnote Text', 'avpvh-print-website'),
                // translators: %s literally the string %s.
                'html_help_text' => __('Text to use when replacing a hyperlink with a footnote. "%s" will be replaced with the URL', 'avpvh-print-website'),
                // translators: %s literally the string %s.
                'default' => __('See %s.', 'avpvh-print-website'),
                'validation_strategies' => [
                    // translators: %s literally the string %s.
                    new TextValidation(__('You must include "%s" in the footnote text so we know where to put the URL.', 'avpvh-print-website'), '~.*\%s.*~'),
                ],
            ]
        );
    }

    /**
     * @return SelectInput
     */
    public function getDefaultAlignmentInput()
    {
        return new SelectInput(
            [
                'none' => new InputOption(__('None', 'avpvh-print-website')),
                'center' => new InputOption(__('Center', 'avpvh-print-website')),
            ],
            [
                'html_label_text' => __('Default Image Alignment', 'avpvh-print-website'),
                'html_help_text' => __('Images normally default to "no alignment", which can look jumbled in printouts. Usually it’s best to automatically switch those to align to the center.', 'avpvh-print-website'),
                'default' => 'center',
            ]
        );
    }

    /**
     * @return SelectRevealInput
     */
    public function getImageSnapInput()
    {
        return new SelectRevealInput(
            [
                'default' => new InputOption(__('Don’t move', 'avpvh-print-website')),
                'snap' => new InputOption(__('Snap to the top or bottom of the page', 'avpvh-print-website')),
                'snap-unless-fit' => new InputOption(__('Only snap if it would otherwise cause a page break', 'avpvh-print-website')),
                'dynamic-resize' => new InputOption(__('Resize images if they don’t fit on the page')),
            ],
            [
                'html_label_text' => __('Image and Block Placement', 'avpvh-print-website') . avpvh_prwb_pro_print_service_only(__('Image snapping and dynamic resizing only works using the Pro PDF Service.', 'avpvh-print-website')),
                'html_help_text' => __('To reduce whitespace around images, galleries, and tables, AV-PvH Print Website can adjust the placement of your content, or resize it according to the space on the page.', 'avpvh-print-website'),
                'default' => 'snap-unless-fit',
            ]
        );
    }

    /**
     * Gets the image placement input and its sister dynamic-resize input (which gets revealed when choosing to resize images)
     * @return FormInputBase[]
     */
    protected function getImageSnapInputs()
    {
        return [
            'image_placement' => $this->getImageSnapInput(),
            'dynamic-resize' => new FormSection(
                [
                    'subsections' => [
                        'dynamic_resize_min' => new TextInput(
                            [
                                'html_label_text' => __('Minimum Image Size (in pixels)', 'avpvh-print-website'),
                                'html_help_text' => __('Any images larger than this may be resized to fit onto the page, but they will be no smaller than this size.', 'avpvh-print-website'),
                                'default' => '300',
                            ]
                        ),
                    ],
                ]
            ),
        ];
    }

    /**
     * Gets a form section regarding how to handle links, footnotes, page refs, etc.
     * @return FormSectionDetails
     * @throws \Avpvh\PrintWebsite\Twine\forms\helpers\ImproperUsageException
     */
    public function getLinksInputs()
    {
        return new FormSectionDetails(
            [
                'html_summary' => __('Link, Page Reference, and Footnote Settings', 'avpvh-print-website'),
                'subsections' => [

                    'internal' => new FormSection(
                        [
                            'subsections' => [
                                'internal_links' => new SelectRevealInput(
                                    [
                                        'remove' => new InputOption(
                                            __('Remove', 'avpvh-print-website')
                                        ),
                                        'leave' => new InputOption(
                                            __('Leave as hyperlink', 'avpvh-print-website')
                                        ),
                                        'parens' => new InputOption(
                                            __('Replace with page reference', 'avpvh-print-website')
                                        ),
                                        'footnote' => new InputOption(
                                            __('Replace with footnote', 'avpvh-print-website')
                                        ),
                                    ],
                                    [
                                        'default' => avpvh_prwb_is_premium() ? 'parens' : 'remove',
                                        'html_label_text' => __('Internal Hyperlinks', 'avpvh-print-website') . avpvh_prwb_pro_print_service_best(__('Footnotes and page references only work with Pro PDF Service', 'avpvh-print-website')),
                                        'html_help_text' => __(
                                            'How to display hyperlinks to content included in this project.',
                                            'avpvh-print-website'
                                        ),
                                    ]
                                ),
                                'parens' => new FormSection(
                                    [
                                        'subsections' => [
                                            'page_reference_text' => $this->getPageReferenceTextInput(),
                                        ],
                                    ]
                                ),
                                'footnote' => new FormSection(
                                    [
                                        'subsections' => [
                                            'internal_footnote_text' => $this->getInternalFootnoteTextInput(),
                                        ],
                                    ]
                                ),
                            ],
                        ]
                    ),
                    'external' => new FormSection(
                        [
                            'subsections' => [
                                'external_links' => new SelectRevealInput(
                                    [
                                        'remove' => new InputOption(__('Remove', 'avpvh-print-website')),
                                        'leave' => new InputOption(__('Leave as hyperlink', 'avpvh-print-website')),
                                        'footnote' => new InputOption(
                                            __('Replace with footnote', 'avpvh-print-website')
                                        ),
                                    ],
                                    [
                                        'default' => avpvh_prwb_is_premium() ? 'footnote' : 'leave',
                                        'html_label_text' => __('External Hyperlinks', 'avpvh-print-website') . avpvh_prwb_pro_print_service_best(__('Footnotes require Pro', 'avpvh-print-website')),
                                        'html_help_text' => __(
                                            'How to display hyperlinks to content not included in this project.',
                                            'avpvh-print-website'
                                        ),
                                    ]
                                ),
                                'footnote' => new FormSection(
                                    [
                                        'subsections' => [
                                            'footnote_text' => $this->getExternalFootnoteTextInput(),
                                        ],
                                    ]
                                ),
                            ],
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * Gets the input that control displaying qr codes on videos.
     * @return YesNoInput
     */
    public function getVideoQRCodeInput()
    {
        return new YesNoInput(
            [
                'default' => true,
                'html_label_text' => __('Add QR Code to Videos', 'avpvh-print-website'),
                'html_help_text' => __('After a video is converted into a screenshot and URL, you can optionally add a QR code that readers can scan with their phones to view the video.', 'avpvh-print-website'),
            ]
        );
    }

    /**
     * @return FormSectionDetails
     * @throws \Avpvh\PrintWebsite\Twine\forms\helpers\ImproperUsageException
     */
    public function getPdfFontSettings(){
        return new FormSectionDetails(
            [
                'html_summary' => __('Font Settings', 'avpvh-print-website'),
                'subsections' => [
                    'main_header_font_size' => new TextInput(
                        [
                            'default' => '4em',
                            'html_label_text' => __('Title page and Part Header Font Size', 'avpvh-print-website'),
                            'html_help_text' => sprintf(
                            // translators: 1: opening anchor tag, 2: closing anchor tag, 3: opening anchor tag
                                __('Font size used for the default title page’s and part’s header (all other headers’ sizes are derived from the main font size). Use any recognized %1$sCSS font-size keyword%2$s (like "large", "medium", "small") or a %3$slength in any units%2$s (eg "14pt", "50%%", or "10px").'),
                                '<a href="https://www.w3schools.com/cssref/pr_font_font-size.asp" target="_blank">',
                                '</a>',
                                '<a href="https://www.w3schools.com/cssref/css_units.asp" target="_blank">'
                            ),
                        ]
                    ),
                    'header_font_style' => new SelectRevealInput(
                        [
                            'arial' => new InputOption(__('Arial', 'avpvh-print-website')),
                            'courier new' => new InputOption(__('Courier New', 'avpvh-print-website')),
                            'georgia' => new InputOption(__('Georgia', 'avpvh-print-website')),
                            'impact' => new InputOption(__('Impact', 'avpvh-print-website')),
                            'lucida console' => new InputOption(__('Lucida Console', 'avpvh-print-website')),
                            'palatino linotype' => new InputOption(__('Palatino Linotype', 'avpvh-print-website')),
                            'tahoma' => new InputOption(__('Tahoma', 'avpvh-print-website')),
                            'times new roman' => new InputOption(__('Times New Roman', 'avpvh-print-website')),
                            'verdana' => new InputOption(__('Verdana', 'avpvh-print-website')),
                            'custom_header_font' => new InputOption(__('Custom Font...', 'avpvh-print-website'))
                        ],
                        [
                            'default' => 'arial',
                            'html_label_text' => __('Header Font', 'avpvh-print-website'),
                            'html_help_text' => __('Default font for header tags', 'avpvh-print-website'),
                        ]
                    ),
                    'custom_header_font' => new FormSection(
                        [
                            'subsections' => [
                                'custom_header_font_style' => new AdminFileUploaderInput(
                                    [
                                        'default' => '',
                                        'html_label_text' => __('Custom Header Font', 'avpvh-print-website'),
                                        'html_help_text' => __('Specify the URL of a custom font file, or upload one. The formats "wff" and "wff2" work best, but "ttf" and "otf" also work.', 'avpvh-print-website')
                                    ]
                                ),
                            ]
                        ]
                    ),
                    'font_size' => new TextInput(
                        [
                            'default' => '10pt',
                            'html_label_text' => __('Font Size', 'avpvh-print-website'),
                            'html_help_text' => sprintf(
                            // translators: 1: opening anchor tag, 2: closing anchor tag, 3: opening anchor tag
                                __('Use any recognized %1$sCSS font-size keyword%2$s (like "large", "medium", "small") or a %3$slength in any units%2$s (eg "14pt", "50%%", or "10px").'),
                                '<a href="https://www.w3schools.com/cssref/pr_font_font-size.asp" target="_blank">',
                                '</a>',
                                '<a href="https://www.w3schools.com/cssref/css_units.asp" target="_blank">'
                            ),
                        ]
                    ),
                    'font_style' => new SelectRevealInput(
                        [
                            'arial' => new InputOption(__('Arial', 'avpvh-print-website')),
                            'courier new' => new InputOption(__('Courier New', 'avpvh-print-website')),
                            'georgia' => new InputOption(__('Georgia', 'avpvh-print-website')),
                            'impact' => new InputOption(__('Impact', 'avpvh-print-website')),
                            'lucida console' => new InputOption(__('Lucida Console', 'avpvh-print-website')),
                            'palatino linotype' => new InputOption(__('Palatino Linotype', 'avpvh-print-website')),
                            'tahoma' => new InputOption(__('Tahoma', 'avpvh-print-website')),
                            'times new roman' => new InputOption(__('Times New Roman', 'avpvh-print-website')),
                            'verdana' => new InputOption(__('Verdana', 'avpvh-print-website')),
                            'custom_font' => new InputOption(__('Custom Font...', 'avpvh-print-website'))
                        ],
                        [
                            'default' => 'times new roman',
                            'html_label_text' => __('Font', 'avpvh-print-website'),
                            'html_help_text' => __('Default font used in paragraphs, bulleted lists, tables, etc.'),
                        ]
                    ),
                    'custom_font' => new FormSection(
                        [
                            'subsections' => [
                                'custom_font_style' => new AdminFileUploaderInput(
                                    [
                                        'default' => '',
                                        'html_label_text' => __('Custom Font', 'avpvh-print-website'),
                                        'html_help_text' => __('Specify the URL of a custom font file, or upload one. The formats "wff" and "wff2" work best, but "ttf" and "otf" also work.', 'avpvh-print-website')
                                    ]
                                ),
                            ]
                        ]
                    ),
                ],
            ]
        );
    }
}
