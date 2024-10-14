<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\infrastructure\error\handler;

use bpmj\wpidea\admin\helpers\utils\Snackbar;
use bpmj\wpidea\Caps;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\options\Interface_Options;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\api\Interface_User_API;

class Airbrake_Integration_Manual_Manager implements Interface_Initiable
{
    public const AIRBRAKE_CONFIG_DATA_OPTION_NAME = 'publigo_airbrake_config_data';
    public const QUERY_ARG_NAME_AIRBRAKE_ID = 'airbrake_id';
    public const QUERY_ARG_NAME_AIRBRAKE_KEY = 'airbrake_key';
    private const QUERY_ARG_NAME_AIRBRAKE_ACCESS = 'airbrake_access';
    private const PATTERN_AIRBRAKE_ID = '/^\d{6}+$/';
    private const PATTERN_AIRBRAKE_KEY = '/^[a-z0-9]+$/';
    private const RESET = 'reset';

    private Current_Request $current_request;
    private Interface_User_API $user_api;
    private Interface_Options $options;
    private Interface_Actions $actions;
	private Snackbar $snackbar;
	private Interface_Translator $translator;

	public function __construct(
        Current_Request $current_request,
        Interface_User_API $user_api,
        Interface_Options $options,
        Interface_Actions $actions,
	    Snackbar $snackbar,
		Interface_Translator $translator
    ) {
        $this->current_request = $current_request;
        $this->user_api = $user_api;
        $this->options = $options;
        $this->actions = $actions;
		$this->snackbar = $snackbar;
		$this->translator = $translator;
	}

    public function init(): void
    {
        $this->actions->add('admin_init', [$this, 'do_init']);
    }

    public function do_init(): void
    {
        $airbrake_id = $this->current_request->get_query_arg(self::QUERY_ARG_NAME_AIRBRAKE_ID);
        $airbrake_key = $this->current_request->get_query_arg(self::QUERY_ARG_NAME_AIRBRAKE_KEY);
        $airbrake_access = $this->current_request->get_query_arg(self::QUERY_ARG_NAME_AIRBRAKE_ACCESS);

        if ((!$airbrake_id || !$airbrake_key) && !$airbrake_access) {
            return;
        }

        if ($airbrake_access) {
            if ($airbrake_access === self::RESET) {
                $this->resetting_airbrake_config_data();
            }
            return;
        }

        if (!$this->validated_airbrake_data($airbrake_id, $airbrake_key)) {
            return;
        }

        if (!$this->user_api->current_user_has_any_of_the_roles(Caps::ROLES_ADMINS_SUPPORT)) {
            return;
        }

        $this->saving_airbrake_config_data((int)$airbrake_id, $airbrake_key);
    }

    private function saving_airbrake_config_data(int $airbrake_id, string $airbrake_key): void
    {
        $airbrake_config_data = [
            self::QUERY_ARG_NAME_AIRBRAKE_ID => $airbrake_id,
            self::QUERY_ARG_NAME_AIRBRAKE_KEY => $airbrake_key
        ];

        $this->options->set(self::AIRBRAKE_CONFIG_DATA_OPTION_NAME, $airbrake_config_data);

	    $obscured_id = substr((string)$airbrake_id, - 2 ) . str_repeat( '*', strlen((string)$airbrake_id)-2);
	    $obscured_key = substr($airbrake_key, - 2 ) . str_repeat( '*', strlen($airbrake_key)-2);

	    $this->snackbar->display_message(sprintf($this->translator->translate('airbrake.key_and_id_set'), $obscured_id, $obscured_key));
    }

    private function resetting_airbrake_config_data(): void
    {
        $this->options->set(self::AIRBRAKE_CONFIG_DATA_OPTION_NAME, '');

	    $this->snackbar->display_message($this->translator->translate('airbrake.key_and_id_unset'));
    }

    private function validated_airbrake_data(string $airbrake_id, string $airbrake_key): bool
    {
        if (!preg_match(self::PATTERN_AIRBRAKE_ID, $airbrake_id)) {
            $this->snackbar->display_message($this->translator->translate('airbrake.invalid_id'));
            return false;
        }

        if (!preg_match(self::PATTERN_AIRBRAKE_KEY, $airbrake_key)) {
            $this->snackbar->display_message($this->translator->translate('airbrake.invalid_key'));
            return false;
        }

        return true;
    }
}