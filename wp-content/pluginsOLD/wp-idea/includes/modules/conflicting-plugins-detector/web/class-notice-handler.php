<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\conflicting_plugins_detector\web;

use bpmj\wpidea\modules\conflicting_plugins_detector\core\io\Interface_Notice_Handler;
use bpmj\wpidea\admin\Notices;

class Notice_Handler implements Interface_Notice_Handler
{
    private Notices $notices;

    public function __construct(
        Notices $notices
    )
    {
        $this->notices = $notices;
    }

    public function display_notice(string $notice): void
    {
        $this->notices->display_notice($notice, Notices::TYPE_ERROR);
    }
}
