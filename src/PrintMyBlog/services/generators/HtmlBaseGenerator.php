<?php

namespace Avpvh\PrintWebsite\services\generators;

use Avpvh\PrintWebsite\entities\DesignTemplate;
use Avpvh\PrintWebsite\orm\entities\Design;
use Avpvh\PrintWebsite\orm\entities\Project;
use Avpvh\PrintWebsite\orm\entities\ProjectSection;
use Avpvh\PrintWebsite\services\ExternalResourceCache;
use Avpvh\PrintWebsite\system\CustomPostTypes;
use Avpvh\PrintWebsite\Twine\services\filesystem\File;
use WP_Query;

/**
 * Class HtmlBaseGenerator
 * Generators that create an HTML intermediary file.
 * @package Avpvh\PrintWebsite\services\generators
 */
abstract class HtmlBaseGenerator extends ProjectFileGeneratorBase
{

    /**
     * @var File
     */
    protected $file_writer;

    /**
     * @global Project $avpvh_prwb_project
     * @global Design $avpvh_prwb_design
     */
    protected function startGenerating()
    {
        // Setup the "main post" for use in templates before the first article.
        $post = $this->project->getMainPost();

        $wp_the_query = new WP_Query(
            [
                'post_status' => 'any',
                'post__in' => [$post->ID],
                'showposts' => count([$post->ID]),
                'post_type' => $post->post_type,
            ]
        );
        $wp_the_query->have_posts();
        $wp_the_query->the_post();
        $this->setupPostData();

        parent::startGenerating();
        // Try to get enqueued after the theme, if we're doing that, so we get precedence.
        add_action('wp_enqueue_scripts', [$this, 'enqueueStylesAndScripts'], 1000);
        do_action('avpvh_prwb_pdf_generation_start', $this->project_generation, $this->design);
        add_filter('should_load_block_editor_scripts_and_styles', '__return_true');
        add_action('avpvh_prwb_pro_print_window', [$this, 'addPrintWindowToPage']);
        $this->writeDesignTemplateInDivision(DesignTemplate::IMPLIED_DIVISION_PROJECT);

        wp_reset_postdata();
    }

    /**
     * Writes html to the file.
     * @return void
     */
    protected function generateSection()
    {
        global $post;
        // determine which template to use, depending on the current section's height and how template specified
        if ($post->avpvh_prwb_section instanceof ProjectSection) {
            $this->project_generation->setLastSection($post->avpvh_prwb_section);
            $template = $post->avpvh_prwb_section->getTemplate();
            $template = $this->design->getDesignTemplate()->resolveSectionTemplateToUse($template);
            if ($template) {
                $this->writeDesignTemplateInDivision($template);
            } else {
                $this->writeDesignTemplateInDivision(
                    avpvh_prwb_map_section_to_division($post->avpvh_prwb_section)
                );
            }
        }
    }

    /**
     * Finishes writing to html file.
     * @return void
     */
    protected function finishGenerating()
    {
        $this->writeDesignTemplateInDivision(DesignTemplate::IMPLIED_DIVISION_PROJECT, false);
        do_action('\Avpvh\PrintWebsite\services\generators\ProjectFileGeneratorBase->finishGenerating', $this);
    }

    /**
     * @param string $template_file
     * @param array $context
     */
    protected function writeTemplateToFile($template_file, $context = [])
    {
        $this->getFileWriter()->write(
            '<!-- pmb template: ' . $template_file . '-->' . $this->getHtmlFrom($template_file, $context)
        );
    }

    /**
     * @param string $division
     * @param bool $beginning whether to show the beginning, or end, of this division.
     * @param array $context
     */
    protected function writeDesignTemplateInDivision($division, $beginning = true, $context = [])
    {
        $this->writeTemplateToFile(
            $this->design->getDesignTemplate()->getTemplatePathToDivision(
                $division,
                $beginning
            ),
            $context
        );
    }

    /**
     * @return File
     */
    protected function getFileWriter()
    {
        if (! $this->file_writer instanceof File) {
            $this->file_writer = new File($this->project_generation->getGeneratedIntermediaryFilePath());
        }
        return $this->file_writer;
    }

    /**
     * @param string $division
     */
    protected function writeClosingForDesignTemplate($division)
    {
        if ($this->design->getDesignTemplate()->templateFileExists($division, false)) {
            $this->writeDesignTemplateInDivision($division, false);
        } else {
            // If the design template didn't delcare that file, it's ok. Assume it just ends in a div.
            $this->getFileWriter()->write('</div>');
        }
    }

    /**
     * @param ProjectSection|null $last_section
     * @param ProjectSection|null $current_section
     */
    protected function maybeGenerateDivisionStart(
        ProjectSection $last_section = null,
        ProjectSection $current_section = null
    ) {
        if (! $current_section instanceof ProjectSection) {
            return;
        }

        $last_section_placement = null;
        if ($last_section instanceof ProjectSection) {
            $last_section_placement = $last_section->getPlacement();
        }
        if (
            $last_section_placement !== $current_section->getPlacement()
            && $this->design->getDesignTemplate()->supports($current_section->getPlacement())
        ) {
            $this->writeDesignTemplateInDivision($current_section->getPlacement());
        }
    }

    /**
     * @param ProjectSection|null $previous_section
     * @param ProjectSection|null $current_section
     */
    protected function maybeGenerateDivisionEnd(
        ProjectSection $previous_section = null,
        ProjectSection $current_section = null
    ) {
        if (! $previous_section) {
            // no transition necessary
            return;
        }
        $iterate_depth = $previous_section->getDepth();
        $current_depth = 0;
        $current_placement = null;
        if ($current_section instanceof ProjectSection) {
            $current_depth = $current_section->getDepth();
            $current_placement = $current_section->getPlacement();
        }
        while ($iterate_depth >= $current_depth) {
            $this->writeClosingForDesignTemplate(
                avpvh_prwb_map_section_to_division(
                    $previous_section
                )
            );
            $iterate_depth--;
        }
        // take care of closing front_matter, main_matter, and back_matter
        if ($previous_section->getPlacement() !== $current_placement) {
            $this->writeClosingForDesignTemplate(
                avpvh_prwb_map_section_to_division($previous_section)
            );
        }
    }

    /**
     * Adds all the body to the html file.
     */
    protected function generateMatter()
    {
        if($this->project->getWpPost()->post_type === CustomPostTypes::PROJECT){
            $sections = $this->project->getFlatSections(1000, 0, false);
        } else {
            // it's a "post project", meaning it's only this one post in the "project".
            $fake_row = new \stdClass();
            $fake_row->ID = 0;
            $fake_row->post_id = $this->project->getWpPost()->ID;
            $fake_row->post_title = $this->project->getWpPost()->post_title;
            $fake_row->parent_id = 0;
            $fake_row->section_order = 1;
            $fake_row->template = '';
            $fake_row->placement = DesignTemplate::IMPLIED_DIVISION_MAIN_MATTER;
            $fake_row->height = 0;
            $fake_row->depth = 0;
            $sections = [
                new ProjectSection($fake_row)
            ];
        }
        $this->generateSections($sections);
    }


    /**
     * Deletes the generated HTML file, if it exists.
     * @return bool
     */
    public function deleteFile()
    {
        return $this->getFileWriter()->delete();
    }

    /**
     * Enqueues the scripts and styles
     * @return void
     */
    abstract public function enqueueStylesAndScripts();

    /**
     * Gets the URL back to the page to generate step.
     * @return string
     */
    abstract protected function addPrintWindowToPage();

    /**
     * @return string
     */
    protected function getUrlBackToGenerateStep()
    {
        return add_query_arg(
            [
                'ID' => $this->project->getWpPost()->ID,
                'action' => \Avpvh\PrintWebsite\controllers\Admin::SLUG_ACTION_EDIT_PROJECT,
                'subaction' => \Avpvh\PrintWebsite\entities\ProjectProgress::GENERATE_STEP,
            ],
            admin_url(AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH)
        );
    }
}
