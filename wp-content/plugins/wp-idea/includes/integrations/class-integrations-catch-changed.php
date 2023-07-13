<?php

namespace bpmj\wpidea\integrations;

if (!defined('ABSPATH'))
    exit;

class Integrations_Catch_Changed
{
    private $changed_integrations;

    const CHANGED_INTEGRATIONS_SLUG = 'wpi_changed_integrations';

    public function __construct()
    {

        $this->add_hooks();
    }

    private function add_hooks(): void
    {
        $this->catch_update_settings();
    }

    private function catch_update_settings(): void
    {
        add_action('update_option_edd_settings', [$this, 'catch_differences_update_settings_gates'], 10, 2);

        $this->catch_invoices_update_option();
    }

    public function catch_invoices_update_option(): void
    {
        foreach (Integrations::INVOICE_INTEGRATIONS as $integration_name => $integration){
            $option = $integration['option'];
            $self = $this;
            add_action('update_option_'.$option, function ($new_values, $old_values) use ($self, $integration_name, $integration){
                if($self->check_is_integration_inputs_changed($integration['inputs'], $new_values, $old_values)){
                    $self->add_changed_integration_and_save(Integrations::TYPE_INVOICES, $integration_name);
                }
            }, 10, 2);
        }

        $this->changed_integrations = self::get_changed_integrations();
    }
    public function catch_differences_update_settings_gates($new_values, $old_values): void
    {
        $this->changed_integrations = self::get_changed_integrations();
        foreach (Integrations::GATEWAYS_INTEGRATIONS as $integration_name => $integration){
            if($this->check_is_integration_inputs_changed($integration['inputs'], $new_values, $old_values)){
                $this->changed_integrations_add(Integrations::TYPE_GATEWAYS, $integration_name);
            }
        }
        foreach (Integrations::MAILERS_INTEGRATIONS as $integration_name => $integration){
            if($this->check_is_integration_inputs_changed($integration['inputs'], $new_values, $old_values)){
                $this->changed_integrations_add(Integrations::TYPE_MAILERS, $integration_name);
            }
        }
        self::update_changed_integrations($this->changed_integrations);
    }

    private function check_is_integration_inputs_changed(array $inputs, $new_values, $old_values): bool
    {
        foreach ($inputs as $input){
            if (WPI()->settings->settings_api->has_option_key_changed($new_values, $old_values, $input)) {
                return true;
            }
        }
        return false;
    }

    private function changed_integrations_add(string $type, string $name): void
    {
        $this->changed_integrations[$type][] = $name;
    }

    private function add_changed_integration_and_save(string $type, string $name): void
    {
        $changed_integrations = self::get_changed_integrations();
        $changed_integrations[$type][] = $name;
        self::update_changed_integrations($changed_integrations);
    }

    public static function get_changed_integrations()
    {
        return get_option(self::CHANGED_INTEGRATIONS_SLUG);
    }

    public static function update_changed_integrations($integrations_to_check): void
    {
        update_option(self::CHANGED_INTEGRATIONS_SLUG, $integrations_to_check);
    }

    public static function reset_changed_integrations(): void
    {
        self::update_changed_integrations([]);
    }

}
