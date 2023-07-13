<?php

namespace bpmj\wpidea\admin\redirects;

use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\routing\Interface_Url_Generator;

class Media_New_Page_Redirect implements Interface_Initiable
{
    private Interface_Redirector $redirector;

    private Interface_Actions $actions;

    private Interface_Url_Generator $url_generator;

    private string $media_url;

    public function __construct(
        Interface_Redirector $redirector,
        Interface_Actions $actions,
        Interface_Url_Generator $url_generator
    ) {
        $this->redirector = $redirector;
        $this->actions = $actions;
        $this->url_generator = $url_generator;
    }

    public function init(): void
    {
        $this->media_url = $this->url_generator->generate_admin_page_url(Admin_Menu_Item_Slug::MEDIA);

        $this->actions->add(Action_Name::ADMIN_INIT, [$this, 'redirect']);
    }

    public function redirect(): void
    {
        if ( strpos( $_SERVER['REQUEST_URI'], Admin_Menu_Item_Slug::MEDIA_ADD_NEW ) !== false ) {
            $this->redirector->redirect($this->media_url);
        }
    }
}