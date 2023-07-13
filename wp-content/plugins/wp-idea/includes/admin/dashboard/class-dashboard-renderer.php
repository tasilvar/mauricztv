<?php
namespace bpmj\wpidea\admin\dashboard;

use bpmj\wpidea\View;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\settings\LMS_Settings;

class Dashboard_Renderer
{
    public function render()
    {
        $courses_functionality_enabled = LMS_Settings::get_option(Settings_Const::COURSES_ENABLED) ?? true;

        echo View::get_admin('/dashboard/dashboard', [
            'greeting' => $this->get_personalized_greeting(),
            'courses_functionality_enabled' => $courses_functionality_enabled
        ]);

    }

    protected function get_personalized_greeting()
    {
        $hour = current_time('H');
        
        $greeting = ($hour > 17) ? __('Good evening', BPMJ_EDDCM_DOMAIN) : (($hour < 12) ? __('Good morning', BPMJ_EDDCM_DOMAIN) : __('Good afternoon', BPMJ_EDDCM_DOMAIN));        
        
        $user = wp_get_current_user();
        $greeting_name = $user->first_name ? ' <strong>' .$user->first_name . '</strong>' : '';
        
        return $greeting . $greeting_name . ', ' . __('your WP Idea is ready to go!', BPMJ_EDDCM_DOMAIN);
    }
}