<?php

namespace bpmj\wpidea\shared\infrastructure\modules;

use bpmj\wpidea\shared\infrastructure\routing\Routes;
use bpmj\wpidea\shared\infrastructure\translator\Translations;
use Psr\Container\ContainerInterface;

class Module_Registrar
{
    private const MODULES_BASE_PATH = BPMJ_EDDCM_DIR . '/includes/modules/';
    private const MODULES_BASE_NAMESPACE = 'bpmj\\wpidea\\modules\\';

    private ContainerInterface $container;
    private Translations $translations;
    private Routes $routes;

    private array $modules = [];

    public function __construct(ContainerInterface $container, Translations $translations, Routes $routes)
    {
        $this->container = $container;
        $this->translations = $translations;
        $this->routes = $routes;

        $this->find_modules();
        $this->load_translations_from_modules();
        $this->load_routes_from_modules();
        $this->init_modules();
    }

    private function find_modules(): void
    {
        $modules_dir = scandir(self::MODULES_BASE_PATH);
        foreach ($modules_dir as $module_dir) {
            if ($this->entry_module_file_exists($module_dir)) {
                $this->modules[] = $this->container->get(
                    $this->get_module_namespace_from_dir($module_dir) . '\\' . $this->get_module_name_from_dir($module_dir)
                );
            }
        }
    }

    private function entry_module_file_exists(string $dir): bool
    {
        return is_dir(self::MODULES_BASE_PATH . $dir) &&
            file_exists(self::MODULES_BASE_PATH . $dir . '/class-' . $dir . '-module.php');
    }

    private function get_module_name_from_dir(string $dir): string
    {
        $result = str_replace('-', ' ', $dir);
        $result = ucwords($result);
        return str_replace(' ', '_', $result) . '_Module';
    }

    private function get_module_namespace_from_dir(string $dir): string
    {
        return self::MODULES_BASE_NAMESPACE . str_replace('-', '_', $dir);
    }

    private function load_translations_from_modules(): void
    {
        foreach ($this->modules as $module) {
            $translations = $module->get_translations();
            foreach ($translations as $locale => $translation) {
                $this->translations->add($translation, $locale);
            }
        }
    }

    private function load_routes_from_modules(): void
    {
        foreach ($this->modules as $module) {
            $routes = $module->get_routes();
            foreach ($routes as $route => $controller) {
                $this->routes->add($route, $controller);
            }
        }
    }

    private function init_modules(): void
    {
        foreach ($this->modules as $module) {
            $module->init();
        }
    }
}