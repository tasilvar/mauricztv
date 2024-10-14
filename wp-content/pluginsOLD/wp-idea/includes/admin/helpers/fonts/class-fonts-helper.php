<?php

namespace bpmj\wpidea\admin\helpers\fonts;

class Fonts_Helper
{
    private const GOOGLE_FONTS_TRANSIENT_NAME = 'bpmj_eddcm_google_fonts';
    private const GOOGLE_FONTS_REMOTE_URL = 'https://n8n-google-fonts-helper.herokuapp.com/api/fonts';

    public static function get_google_fonts_from_remote(): array
    {
        $fonts = get_transient( self::GOOGLE_FONTS_TRANSIENT_NAME );
        if ( false === $fonts ) {
            $fonts    = [];
            $response = wp_remote_get( self::GOOGLE_FONTS_REMOTE_URL );
            if ( ! is_wp_error( $response ) ) {
                $font_list = json_decode( wp_remote_retrieve_body( $response ), true );
                if( empty( $font_list ) ) {
                    $fonts = include('default-fonts-array.php');
                }
                else {
                    foreach ($font_list as $font_spec) {
                        $fonts[$font_spec['id']] = [
                            'id' => $font_spec['id'],
                            'name' => $font_spec['family'],
                        ];
                    }
                }

                set_transient( self::GOOGLE_FONTS_TRANSIENT_NAME, $fonts, 3600 * 24 );
            }
        }

        return $fonts;
    }

    public static function get_google_fonts_list(): array
    {
        $slugify = function ( $text ) {
            return preg_replace( '/[^a-z]/', '', strtolower( $text ) );
        };

        $fonts        = self::get_google_fonts_from_remote();
        $limit        = 10;
        $search       = ! empty( $_REQUEST[ 'term' ] ) ? $slugify( $_REQUEST[ 'term' ] ) : '';
        $search_split = str_split( $search );
        $search_regex = '/' . implode( '.*?', $search_split ) . '/';
        $result       = [];
        $found        = 0;
        foreach ( $fonts as $font_slug => $font ) {
            if ( ! $search || 1 === preg_match( $search_regex, $font_slug ) ) {

                $font['text'] = $font['name'];
                unset($font['name']);

                $result[] = $font;
                ++ $found;
            }
            if ( $found === $limit ) {
                break;
            }
        }

        return $result;
    }

    public static function get_google_font_name_by_slug(string $slug): ?string
    {
        $font = self::get_google_fonts_from_remote()[$slug];

        return $font['name'] ?? null;
    }
}