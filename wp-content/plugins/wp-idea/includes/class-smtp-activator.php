<?php
namespace bpmj\wpidea;

use bpmj\wpidea\settings\LMS_Settings;

class SMTP_Activator
{
    public function __construct()
    {
        if( defined( 'BPMJ_EDDCM_MAILER_SMTP_HOST' ) ) {
            add_action('phpmailer_init', [$this, 'phpmailer_init_filter']);
        }
    }

    public function phpmailer_init_filter($phpmailer)
    {   
        $phpmailer->isSMTP();

        $phpmailer->Host = BPMJ_EDDCM_MAILER_SMTP_HOST;
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = BPMJ_EDDCM_MAILER_SMTP_PORT;
        $phpmailer->Username = BPMJ_EDDCM_MAILER_SMTP_USERNAME;
        $phpmailer->Password = BPMJ_EDDCM_MAILER_SMTP_PASSWORD;
        $phpmailer->SMTPSecure = BPMJ_EDDCM_MAILER_SMTP_SECURE;

        $phpmailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $phpmailer->FromName = LMS_Settings::get_option('from_name');
        $phpmailer->From = LMS_Settings::get_option('from_email');
    }
}
