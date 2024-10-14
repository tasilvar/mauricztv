<?php

namespace bpmj\wpidea\modules\logs\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\filters\Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use Psr\Log\LoggerInterface;

class Plugin_Management_Handler implements Interface_Event_Handler
{
    private const UPDATE_ACTION = 'update';
    private const UPDATE_OPTION_TYPE_PLUGIN = 'plugin';
    private Interface_Events $events;
    private LoggerInterface $logger;
    private Interface_Translator $translator;
    private Interface_Current_User_Getter $current_user_getter;
    private Interface_Filters $filters;
    private array $plugins_previous_version = [];
    private string $deleting_plugin_name;

    public function __construct(
        Interface_Events $events,
        LoggerInterface $logger,
        Interface_Translator $translator,
        Interface_Current_User_Getter $current_user_getter,
        Interface_Filters $filters
    )
    {
        $this->events = $events;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->current_user_getter = $current_user_getter;
        $this->filters = $filters;
    }

    public function init(): void
    {
        $this->filters->add(Filter_Name::UPGRADER_PRE_INSTALL, [$this, 'set_plugin_previous_version'], 10, 2);
        $this->events->on(Event_Name::UPGRADER_PROCESS_COMPLETE, [$this, 'handle_plugin_updated_log'], 10, 2);
        $this->events->on(Event_Name::PLUGIN_ACTIVATED, [$this, 'handle_plugin_activated_log']);
        $this->events->on(Event_Name::PLUGIN_DEACTIVATED, [$this, 'handle_plugin_deactivated_log']);
        $this->events->on(Event_Name::PLUGIN_DELETE, [$this, 'handle_plugin_delete_log']);
        $this->events->on(Event_Name::PLUGIN_DELETED, [$this, 'handle_plugin_deleted_log']);
    }

    public function set_plugin_previous_version($response, $plugin)
    {
        if (empty($plugin['plugin'])) {
            return $response;
        }

        $plugin_data = $this->get_plugin_data($plugin['plugin']);
        $this->plugins_previous_version[$plugin['plugin']] = $plugin_data['Version'];

        return $response;
    }

    public function handle_plugin_updated_log(\WP_Upgrader $upgrader, array $options): void
    {
        if (!isset($options['action'], $options['type'])) {
            return;
        }

        if ($options['action'] == self::UPDATE_ACTION && $options['type'] == self::UPDATE_OPTION_TYPE_PLUGIN) {
            $plugins = !empty($options['plugin']) ? [$options['plugin']] : $options['plugins'];
            foreach ($plugins as $plugin) {
                $this->logger->info($this->prepare_plugin_updated_log_text($plugin));
            }
        }
    }

    public function handle_plugin_activated_log(string $plugin): void
    {
        $this->logger->info(
            sprintf(
                $this->translator->translate('logs.log_message.plugin_activated'),
                $this->get_plugin_name($plugin),
                $this->get_current_user_login()
            ));
    }

    public function handle_plugin_deactivated_log(string $plugin): void
    {
        $this->logger->info(
            sprintf(
                $this->translator->translate('logs.log_message.plugin_deactivated'),
                $this->get_plugin_name($plugin),
                $this->get_current_user_login()
            ));
    }

    public function handle_plugin_delete_log(string $plugin): void
    {
        $this->deleting_plugin_name =  $this->get_plugin_name($plugin);
    }

    public function handle_plugin_deleted_log(): void
    {
        $this->logger->info(
            sprintf(
                $this->translator->translate('logs.log_message.plugin_deleted'),
                $this->deleting_plugin_name,
                $this->get_current_user_login()
            ));
    }

    private function get_current_user_login(): string
    {
        $current_user = $this->current_user_getter->get();
        return $current_user ? $current_user->get_login() : '';
    }

    private function get_plugin_data(string $plugin): array
    {
        return get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
    }

    private function get_plugin_name(string $plugin): string
    {
        return $this->get_plugin_data($plugin)['Name'] ?? '';
    }

    private function prepare_plugin_updated_log_text(string $plugin): string
    {
        $plugin_data = $this->get_plugin_data($plugin);
        $plugin_name = $plugin_data['Name'] ?? '';

        $log_text = sprintf(
            $this->translator->translate('logs.log_message.plugin_updated'),
            $plugin_name,
            $this->get_plugin_previous_version($plugin),
            $plugin_data['Version'] ?? ''
        );

        $current_user_login = $this->get_current_user_login();
        if (!empty($current_user_login)) {
            $updated_by_text = sprintf(
                $this->translator->translate('logs.log_message.plugin_updated_by'),
                $current_user_login
            );

            $log_text .= ' ' . $updated_by_text;
        }

        return $log_text;
    }

    private function get_plugin_previous_version(string $plugin_name): string
    {
        return $this->plugins_previous_version[$plugin_name] ?? '';
    }
}
