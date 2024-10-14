<?php
namespace bpmj\wpidea\admin\notices\compatibility;

use bpmj\wpidea\admin\Notices;
use bpmj\wpidea\admin\notices\Interface_Notice_Handler;
use bpmj\wpidea\options\Interface_Options;
use bpmj\wpidea\translator\Interface_Translator;

class Pixel_Caffeine_Handler implements Interface_Notice_Handler
{
    private const PIXEL_CAFFEINE_PLUGIN = 'pixel-caffeine/pixel-caffeine.php';
    private const PLUGIN_NAME = 'Pixel Caffeine';
    private const ACTIVE_PLUGINS = 'active_plugins';
    private const SUPPORT_LINK_PIXEL_FB = 'https://poznaj.wpidea.pl/articles/219082-jak-podpi-i-przetestowa-pixela-facebooka';

    private Interface_Translator $translator;
    private Interface_Options $options;
    private Notices $notices;

    public function __construct(
        Interface_Translator $translator,
        Interface_Options $options,
        Notices $notices
    )
    {
        $this->translator = $translator;
        $this->options = $options;
        $this->notices = $notices;
    }

    public function init(): void
    {
        $this->if_plug_in_active_display_message();
    }

    private function if_plug_in_active_display_message(): void
    {
        if($this->the_plugin_is_active()){

            $url_support_pixel_fb = '<a href="'.self::SUPPORT_LINK_PIXEL_FB.'" target="_blank">'.self::SUPPORT_LINK_PIXEL_FB.'</a>';

            $this->notices->display_notice(
                sprintf( $this->translator->translate('notice.pixel_caffeine'),self::PLUGIN_NAME, $url_support_pixel_fb ),
                Notices::TYPE_ERROR
            );

        }

    }

    private function the_plugin_is_active(): bool
    {
        $active_plugins = $this->options->get(self::ACTIVE_PLUGINS);

        if ( !in_array( self::PIXEL_CAFFEINE_PLUGIN , $active_plugins ) ) {
            return false;
        }

        return true;
    }

}

