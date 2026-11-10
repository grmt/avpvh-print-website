<?php

namespace Avpvh\PrintWebsite\Twine\forms\inputs;

use Google\Site_Kit\Core\Util\Input;
use Avpvh\PrintWebsite\Twine\forms\helpers\InputOption;

/**
 * Class FontInput
 * @package Avpvh\PrintWebsite\Twine\forms\inputs
 */
class FontInput extends SelectInput
{
    /**
     * FontInput constructor.
     * @param array $args
     */
    public function __construct($args)
    {
        if(isset($args['format']) && strtolower($args['format']) === 'epub'){
            $options = [
                '' => new InputOption(__('Default', 'avpvh-print-website')),
                'roboto' => new InputOption(__('Roboto', 'avpvh-print-website')),
                'Arial' => new InputOption(__('Arial', 'avpvh-print-website')),
                'Baskerville' => new InputOption(__('Baskerville', 'avpvh-print-website')),
                'bookerly' => new InputOption(__('Bookerly', 'avpvh-print-website')),
                'georgia' => new InputOption(__('Georgia', 'avpvh-print-website')),
                'helvetica' => new InputOption(__('Helvetica', 'avpvh-print-website')),
                'lucida console' => new InputOption(__('Lucida Console', 'avpvh-print-website')),
                'palatino linotype' => new InputOption(__('Palatino Linotype', 'avpvh-print-website')),
                'verdana' => new InputOption(__('Verdana', 'avpvh-print-website')),
            ];
        } else {
            $options = [
                'arial' => new InputOption(__('Arial', 'avpvh-print-website')),
                'courier new' => new InputOption(__('Courier New', 'avpvh-print-website')),
                'georgia' => new InputOption(__('Georgia', 'avpvh-print-website')),
                'impact' => new InputOption(__('Impact', 'avpvh-print-website')),
                'lucida console' => new InputOption(__('Lucida Console', 'avpvh-print-website')),
                'palatino linotype' => new InputOption(__('Palatino Linotype', 'avpvh-print-website')),
                'tahoma' => new InputOption(__('Tahoma', 'avpvh-print-website')),
                'times new roman' => new InputOption(__('Times New Roman', 'avpvh-print-website')),
                'verdana' => new InputOption(__('Verdana', 'avpvh-print-website')),
            ];
        }

        parent::__construct($options, $args);
    }
}
