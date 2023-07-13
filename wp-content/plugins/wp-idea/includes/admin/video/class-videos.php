<?php

namespace bpmj\wpidea\admin\video;

use bpmj\wpidea\admin\video\ajax\Videos_Ajax_Api;
use bpmj\wpidea\admin\video\attachment\Attachment_Extender;
use bpmj\wpidea\admin\video\settings\Videos_Settings;

class Videos {
    public function __construct(
        Videos_Cron_Actions $videos_cron_actions,
        Videos_Manager $videos_manager
    ) {
        $videos_cron_actions->init();
        $videos_manager->init();
        new Attachment_Extender();
        new Videos_Ajax_Api();
        new Videos_Settings();
        new Video_Content_Parser();
        new Videos_Js_Strings();
        new Upload_Limit_Changer();
    }
}
