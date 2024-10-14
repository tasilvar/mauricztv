<?php

namespace bpmj\wpidea\view;

interface Interface_View_Provider
{
    public function get(string $name, array $params = []): string;

    public function get_admin(string $name, array $params = []): string;
}