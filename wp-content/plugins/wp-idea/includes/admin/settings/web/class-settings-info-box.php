<?php

namespace bpmj\wpidea\admin\settings\web;

class Settings_Info_Box
{
    public const INFO_BOX_TYPE_DEFAULT =  'type-default';

    public const INFO_BOX_TYPE_FIELD_DISABLED_FOR_A_REASON = 'type-field-disabled-for-a-reason';

    public const INFO_BOX_TYPE_WARNING = 'type-warning';

    private const INFO_BOX_TYPE_PACKAGE_WARNING =  'type-higher-package-required';
    private const PUBLIGO_SETTINGS_INFO_BOX_BASE_CLASS = 'publigo-settings-info-box';

    public static function render_info_box(string $message, string $type = self::INFO_BOX_TYPE_DEFAULT): string
    {
        return self::render_box($type, $message);
    }

    public static function render_package_warning_box(string $message, string $title = ''): string
    {
        return self::render_box(self::INFO_BOX_TYPE_PACKAGE_WARNING, $message, $title);
    }

    private static function render_box(string $type, string $message, string $title = ''): string
    {
        return "<div class='" . self::PUBLIGO_SETTINGS_INFO_BOX_BASE_CLASS . ' ' . self::PUBLIGO_SETTINGS_INFO_BOX_BASE_CLASS . '--' . $type . "' title='" . $title . "'><p>" . $message . '</p></div>';
    }
}