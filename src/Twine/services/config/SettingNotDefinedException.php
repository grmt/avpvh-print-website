<?php


namespace Avpvh\PrintWebsite\Twine\services\config;


class SettingNotDefinedException extends \Exception
{
    protected $setting_name = '';

    public function __construct($setting_name){
        $this->setting_name = (string)$setting_name;
        parent::__construct(sprintf('Setting "%s" not defined. Please contact AV-PvH Print Website support from the help page.', $setting_name));
    }

    /**
     * @return string
     */
    public function getSettingName(){
        return $this->setting_name;
    }

}