<?php

namespace bpmj\wpidea\admin\settings\core\factories;

use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\services\Settings_Group_Dependencies_Service;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\translator\Interface_Translator_Aware;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

class Settings_Group_Factory
{
    private ContainerInterface $container;
    private Interface_Translator $translator;
    private Settings_Group_Dependencies_Service $settings_group_dependencies_service;

    public function __construct(
        ContainerInterface $container,
        Interface_Translator $translator,
        Settings_Group_Dependencies_Service $settings_group_dependencies_service
    )
    {
        $this->container = $container;
        $this->translator = $translator;
        $this->settings_group_dependencies_service = $settings_group_dependencies_service;
    }

    public function get_instance(string $class_name): ?Abstract_Settings_Group
    {
        try {
            $instance = $this->container->get($class_name);
            if (!is_a($instance, Abstract_Settings_Group::class)) {
                return null;
            }

            /** @var Abstract_Settings_Group $instance */

            if($instance instanceof Interface_Translator_Aware) {
                $instance->set_translator($this->translator);
            }

            $instance->init($this->settings_group_dependencies_service);

            return $instance;
        } catch (ContainerExceptionInterface $exception) {
            return null;
        }
    }
}