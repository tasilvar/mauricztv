<?php
namespace bpmj\wpidea\routing;

interface Interface_Url_Generator
{
    public function generate(string $controller_class, string $action_name, array $args = []): string;

    public function generate_admin_page_url(string $path, array $args = []): string;

    public function get_dashboard_url(): string;
}
