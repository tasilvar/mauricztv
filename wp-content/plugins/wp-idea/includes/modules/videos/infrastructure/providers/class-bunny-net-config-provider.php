<?php

namespace bpmj\wpidea\modules\videos\infrastructure\providers;

use bpmj\wpidea\modules\videos\core\providers\Interface_Video_Config_Provider;
use bpmj\wpidea\options\Interface_Options;

class Bunny_Net_Config_Provider implements Interface_Video_Config_Provider
{
    private const BUNNY_NET_SLUG = 'bunnynet_library_access';

    private Interface_Options $options;

    public function __construct(Interface_Options $options)
    {
        $this->options = $options;
    }

    public function get_configuration(): array
    {
       $data_bunnynet = $this->get_bunnynet_library_access();

        return [
            'library_id' => $data_bunnynet['id'] ?? null,
            'api_key' => $data_bunnynet['api_key'] ?? null
        ];
    }

    public function is_set(): bool
    {
        if(!$this->get_bunnynet_library_access()){
          return false;
        }

        return true;
    }

    private function get_bunnynet_library_access(): ?array
    {
        $bunnynet_library_access = $this->options->get(self::BUNNY_NET_SLUG);

        return json_decode($bunnynet_library_access, true);
    }
}