<?php

namespace bpmj\wpidea\admin\settings\infrastructure\persistence\storage_place;

class Payment_Reminders_Module_Storage_Place implements Interface_Settings_Storage_Place
{
    private const ON = 'on';
    private const OFF = 'off';
    private const PAYMENT_REMINDERS = 'payment_reminders';
    public function get_data(string $name)
    {
        $payment_reminders = $this->get_option();
        $value = $payment_reminders[$name] ?? null;

        if ($value === '0') {
            return false;
        }

        return $value ?? null;
    }

    public function update_data(string $name, $value): void
    {
        if($value === self::ON){
            $value = 1;
        }

        if($value === self::OFF){
            $value = 0;
        }

        $payment_reminders = $this->get_option();
        $payment_reminders[$name] = $value;

        $this->update_option($payment_reminders);
    }

    private function get_option()
    {
        return get_option(self::PAYMENT_REMINDERS);
    }

    private function update_option($value): void
    {
        update_option(self::PAYMENT_REMINDERS, $value);
    }
}