<?php
namespace bpmj\wpidea\admin\subscription\models;

use bpmj\wpidea\Trial;

class Software_Instance_Type
{

    const MD5_TRIAL_KEY = 'feab003082b55bc7b5a7512955f2ebeb';

    const TYPE_TRIAL = 1;
    const TYPE_DEV = 3;
    const TYPE_PAID = 2;

    const INSTANCE_TYPE_SLUG = 'wpi_instance_type';
    const METADATA_SLUG = 'wpi_metadata';

    const TYPE_ID_WITH_NAMES = [
        self::TYPE_TRIAL => 'trial',
        self::TYPE_DEV => 'dev',
        self::TYPE_PAID => 'paid',
    ];

    public function set(int $type): bool
    {
        if(!$this->is_valid($type)){
            return false;
        }

        $this->update($type);
        return true;
    }

    public function update(int $type): void
    {
        update_option(self::INSTANCE_TYPE_SLUG, $type);
    }

    private function is_valid(int $type): bool
    {
        if(in_array($type, array_keys(self::TYPE_ID_WITH_NAMES))){
            return true;
        }

        return false;
    }

    public function get(): int
    {
        $type = get_option(self::INSTANCE_TYPE_SLUG);

        if($type){
            return $type;
        }

        if($this->has_trial_key()){
            if(!$this->has_metadata()){
                return self::TYPE_DEV;
            }

            return self::TYPE_TRIAL;
        }

        return self::TYPE_PAID;
    }

    private function has_metadata(): bool
    {
        return !empty(get_option(self::METADATA_SLUG));
    }

    private function has_trial_key(): bool
    {
        if(md5(Trial::get_key()) != self::MD5_TRIAL_KEY){
            return false;
        }

        return true;
    }

    public function is_paid(): bool
    {
        return $this->get() == self::TYPE_PAID;
    }

    public function is_trial(): bool
    {
        return $this->get() == self::TYPE_TRIAL;
    }

    public function is_dev(): bool
    {
        return $this->get() == self::TYPE_DEV;
    }
}
