<?php
namespace bpmj\wpidea\templates_system\admin\blocks;

class Breadcrumbs_Block extends Block
{
    const BLOCK_NAME = 'wpi/breadcrumbs';

    public function __construct() {
        parent::__construct();
        
        $this->title = __('Breadcrumbs', BPMJ_EDDCM_DOMAIN);
    }
	
    public function get_content_to_render($atts)
    {
        //@todo: przeniesć do widoków
		ob_start();
		WPI()->templates->breadcrumbs();
		$content = ob_get_contents();
		ob_get_clean();

		return $content; 
    }
}