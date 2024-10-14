<?php

namespace bpmj\wpidea\infrastructure\error\handler;

use Airbrake\ErrorHandler;
use Airbrake\Instance;
use Airbrake\Notifier;
use bpmj\wpidea\admin\subscription\models\Software_Instance_Type;
use bpmj\wpidea\infrastructure\error\Interface_Handler;
use bpmj\wpidea\infrastructure\system\Env;
use bpmj\wpidea\options\Interface_Options;

class Airbrake implements Interface_Handler
{
    private const PROJECT_ID_ENV = 'BPMJ_WPI_AIRBRAKE_PROJECT_ID';
    private const PROJECT_KEY_ENV = 'BPMJ_WPI_AIRBRAKE_PROJECT_KEY';

    private const E_WARNING = 'E_WARNING';

    private Interface_Options $options;
    private Software_Instance_Type $software_instance_type;
    private ?Notifier $notifier = null;

    private const MESSAGES_THAT_FILTER_OUT_NOTICES = [
        'mkdir(): Permission denied',
        'unlink',
        'is_dir(): open_basedir restriction in effect',
        'Undefined array key "scheme"',
        'Undefined array key "host"',
        'Undefined array key "path"',
        'Cannot modify header information - headers already sent'
    ];

    private ?string $project_id;
    private ?string $project_key;

    public function __construct(
        Software_Instance_Type $software_instance_type,
        Interface_Options $options
    ) {
        $this->software_instance_type = $software_instance_type;
        $this->options = $options;

        $this->fetch_config_data();

        if (!$this->project_id || !$this->project_key) {
            return;
        }

        if ($this->notifier) {
            return;
        }

        $this->create_notifier();
    }

    protected function create_notifier(): void
    {
        $this->notifier = new Notifier([
            'projectId' => $this->project_id,
            'projectKey' => $this->project_key,
            'appVersion' => BPMJ_EDDCM_VERSION,
            'environment' => $this->get_environment()
        ]);

        $this->notifier->addFilter(function ($notice) {
            if ($this->notice_should_be_filtered_out($notice['errors'][0])) {
                return null;
            }
            return $notice;
        });

        Instance::set($this->notifier);

        $handler = new ErrorHandler($this->notifier);
        $handler->register();

    }

    private function get_environment(): string
    {
        return $this->software_instance_type->is_paid() ? 'paid' : ($this->software_instance_type->is_trial() ? 'trial' : 'dev');
    }

    private function notice_should_be_filtered_out(array $error): bool
    {
        if (self::E_WARNING !== $error['type']) {
            return false;
        }

        foreach (self::MESSAGES_THAT_FILTER_OUT_NOTICES as $filter) {
            if (false !== stripos($error['message'], $filter)) {
                return true;
            }
        }
        return false;
    }

    public function notify($message): void
    {
        if (!$this->notifier) {
            return;
        }
        Instance::notify($message);
    }

    private function fetch_config_data(): void
    {
        $project_id_env = Env::get_value(self::PROJECT_ID_ENV);
        $project_key_env = Env::get_value(self::PROJECT_KEY_ENV);

        $airbrake_config_data = $this->options->get(Airbrake_Integration_Manual_Manager::AIRBRAKE_CONFIG_DATA_OPTION_NAME);

        $project_id_option = $airbrake_config_data ? $airbrake_config_data[Airbrake_Integration_Manual_Manager::QUERY_ARG_NAME_AIRBRAKE_ID] : null;
        $project_key_option = $airbrake_config_data ? $airbrake_config_data[Airbrake_Integration_Manual_Manager::QUERY_ARG_NAME_AIRBRAKE_KEY] : null;

        $this->project_id = $project_id_option ?? $project_id_env ?? null;
        $this->project_key = $project_key_option ?? $project_key_env ?? null;
    }
}
