<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

use Mexitek\PHPColors\Color;


class BPMJ_WPIDEA_Colors_Manager {
    public static function getInverted( $color ){
        $color = new Color($color);
        $complementary = $color->complementary();

        $complementary = new Color($complementary);

        if($color->isLight()){
            $complementary = $complementary->darken(50);
        } else {
            $complementary = $complementary->lighten(50);
        }
        //FIXME Z niewyjaśnionych przyczyn rozjaśnianie i przyciemnianie może dawać odrobinę nieprawidłowe wyniki

        if( !strpos($complementary, '#') ) $complementary = '#' . $complementary;
        return $complementary;
    }
}