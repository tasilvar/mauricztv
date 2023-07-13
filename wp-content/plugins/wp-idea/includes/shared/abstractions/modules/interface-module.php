<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\shared\abstractions\modules;

interface Interface_Module
{
    public function get_routes(): array;
    public function get_translations(): array;
    public function init(): void;
}

