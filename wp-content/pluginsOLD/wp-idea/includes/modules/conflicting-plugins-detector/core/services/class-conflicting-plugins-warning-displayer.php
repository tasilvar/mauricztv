<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\conflicting_plugins_detector\core\services;

use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\modules\conflicting_plugins_detector\core\io\Interface_Notice_Handler;

class Conflicting_Plugins_Warning_Displayer implements Interface_Initiable
{
    private const ADMIN_INIT_ACTION = 'admin_init';

    private Conflicting_Plugins_Detector $conflicting_plugins_detector;
    private Interface_Notice_Handler $notice_handler;
    private Interface_Translator $translator;
    private Interface_Actions $actions;

    public function __construct(
        Interface_Notice_Handler $notice_handler,
        Interface_Translator $translator,
        Interface_Actions $actions,
        Conflicting_Plugins_Detector $conflicting_plugins_detector
    )
    {
        $this->notice_handler = $notice_handler;
        $this->translator = $translator;
        $this->actions = $actions;
        $this->conflicting_plugins_detector = $conflicting_plugins_detector;
    }
    public function init(): void
    {
        $this->actions->add(self::ADMIN_INIT_ACTION, function(){
            $this->maybe_show_warning();
        });
    }

    private function maybe_show_warning(): void
    {
        $active_conflicting_plugins = $this->conflicting_plugins_detector->get_active_conflicting_plugins_name_list();

        if(empty($active_conflicting_plugins)) {
            return;
        }

        $this->show_conflicting_plugins_warning($active_conflicting_plugins);
    }

    private function show_conflicting_plugins_warning(array $plugins_name_list): void
    {
        $message = $this->create_message($plugins_name_list);

        $this->notice_handler->display_notice($message);
    }

    private function create_message(array $plugins_name_list): string
    {
        $plugins_comma_separated_list = implode(', ', $plugins_name_list);

        return sprintf(
            $this->translator->translate('conflicting_plugins_detector.warning_message'),
            $plugins_comma_separated_list
        );
    }

}