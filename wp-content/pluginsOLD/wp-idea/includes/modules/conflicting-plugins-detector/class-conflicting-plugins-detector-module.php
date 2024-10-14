<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\conflicting_plugins_detector;

use bpmj\wpidea\shared\abstractions\modules\Interface_Module;
use bpmj\wpidea\modules\conflicting_plugins_detector\core\services\Conflicting_Plugins_Warning_Displayer;
use bpmj\wpidea\modules\conflicting_plugins_detector\api\Conflicting_Plugins_API;
use bpmj\wpidea\modules\conflicting_plugins_detector\api\Conflicting_Plugins_API_Static_Helper;

class Conflicting_Plugins_Detector_Module implements Interface_Module
{

    private Conflicting_Plugins_Warning_Displayer $conflicting_plugins_warning_displayer;
    private Conflicting_Plugins_API $conflicting_plugins_API;

    public function __construct(
        Conflicting_Plugins_Warning_Displayer $conflicting_plugins_warning_displayer,
        Conflicting_Plugins_API $conflicting_plugins_API
    )
    {
        $this->conflicting_plugins_warning_displayer = $conflicting_plugins_warning_displayer;
        $this->conflicting_plugins_API = $conflicting_plugins_API;
    }

    public function init(): void
    {
        Conflicting_Plugins_API_Static_Helper::init($this->conflicting_plugins_API);

        $this->conflicting_plugins_warning_displayer->init();
    }

    public function get_routes(): array
    {
        return [
        ];
    }

    public function get_translations(): array
    {
        return [
            'pl_PL' => [
                'conflicting_plugins_detector.warning_message' => 'Publigo: Uwaga! Następujące wtyczki mogą powodować nieprawidłowe działanie Publigo: %s. Zalecamy ich natychmiastową dezaktywację.',
                'conflicting_plugins_detector.diagnostics.name' => 'Konfliktowe wtyczki',
                'conflicting_plugins_detector.diagnostics.fix_hint' => 'Wyłącz następujące wtyczki: %s.',
                'conflicting_plugins_detector.diagnostics.no_conflicting_plugins' => 'brak',
            ],
            'en_US' => [
                'conflicting_plugins_detector.warning_message' => 'Publigo: Attention! The following plugins may cause Publigo to malfunction: %s. We recommend deactivating them immediately.',
                'conflicting_plugins_detector.diagnostics.name' => 'Conflicting plugins',
                'conflicting_plugins_detector.diagnostics.fix_hint' => 'Disable following plugins: %s.',
                'conflicting_plugins_detector.diagnostics.no_conflicting_plugins' => 'none',
            ]
        ];
    }
}