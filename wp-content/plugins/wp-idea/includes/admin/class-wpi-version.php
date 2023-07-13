<?php
namespace bpmj\wpidea\admin;

// @todo: tymczasowo brak testów, z powodu problemu z przetestowaniem transientsów

class Wpi_Version{
    public static function get_newest_version(): ?string
    {
        $transient = get_site_transient( 'update_plugins' );

        if ( ! empty( $transient->response[ 'wp-idea/wp-idea.php' ]->new_version ) ) {
            return $transient->response[ 'wp-idea/wp-idea.php' ]->new_version;
        }

        return null;
    }

    public function get_new_version(): ?string
    {
        return self::get_newest_version();
    }

    public static function needs_update()
    {
        $newest_version = self::get_newest_version();

        if ( ! empty( $newest_version ) ) {
			if ( version_compare( BPMJ_EDDCM_VERSION, $newest_version, '<' ) ) {
                return true;
            }
        }

        return false;
    }

    public function is_new_version_available(): bool
    {
        return self::needs_update();
    }
}