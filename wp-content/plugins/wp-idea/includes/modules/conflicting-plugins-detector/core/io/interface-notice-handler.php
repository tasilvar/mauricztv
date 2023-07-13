<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\conflicting_plugins_detector\core\io;

interface Interface_Notice_Handler
{
    public function display_notice(string $notice): void;
}