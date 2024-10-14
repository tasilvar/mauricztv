<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\redirects;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\Helper;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\routing\Interface_Url_Generator;

class Old_Products_Page_To_Dashboard_Redirector implements Interface_Initiable
{
    private Interface_Redirector $redirector;
    private Current_Request $current_request;
    private Interface_Url_Generator $url_generator;
    private Interface_Actions $actions;

    public function __construct(
        Interface_Redirector $redirector,
        Current_Request $current_request,
        Interface_Url_Generator $url_generator,
        Interface_Actions $actions
    ) {
        $this->redirector = $redirector;
        $this->current_request = $current_request;
        $this->url_generator = $url_generator;
        $this->actions = $actions;
    }

    public function init(): void
    {
        $this->actions->add('admin_init', function () {
            $this->handle_redirect();
        }, 20);
    }

    private function handle_redirect(): void
    {
        if (Helper::is_dev()) {
            return;
        }

        if ($this->is_old_products_page()) {
            $this->redirect_to_dashboard();
        }
    }

    private function is_old_products_page(): bool
    {
        global $pagenow;

        return
            ($this->current_request->get_query_arg('post_type') === 'download')
            && $pagenow === 'edit.php';
    }

    private function redirect_to_dashboard(): void
    {
        $this->redirector->redirect(
            $this->get_dashboard_url()
        );
    }

    private function get_dashboard_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php?page=wpidea-dashboard');
    }
}
