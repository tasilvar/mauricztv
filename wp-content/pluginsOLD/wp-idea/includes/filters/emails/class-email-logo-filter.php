<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\filters\emails;

use bpmj\wpidea\events\filters\Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\settings\readers\Interface_App_Visual_Settings_Reader;
use bpmj\wpidea\Templates;

class Email_Logo_Filter implements Interface_Initiable
{
    private Interface_Filters $filters;
    private Templates $templates;

    public function __construct(
        Interface_Filters $filters,
        Templates $templates
    )
    {
        $this->filters = $filters;
        $this->templates = $templates;
    }

    public function init(): void
    {
        $this->filters->add(Filter_Name::EMAIL_LOGO_URL, [$this, 'filter_email_logo_url']);
    }

    public function filter_email_logo_url(?string $logo_url): string
    {
        return $this->templates->get_app_logo_url();
    }
}