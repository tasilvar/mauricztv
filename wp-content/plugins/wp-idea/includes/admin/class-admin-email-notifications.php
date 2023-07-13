<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin;

use bpmj\wpidea\Caps;
use bpmj\wpidea\Software_Variant;
use bpmj\wpidea\wolverine\user\User;

class Admin_Email_Notifications
{
    public function __construct()
    {
        $this->register_events();
    }

    protected function register_events(): void
    {
        add_filter( 'wpi_admin_notices_email', [ $this, 'filter_to_email' ] );
        add_filter( 'wp_new_user_notification_email_admin', [ $this, 'filter_wp_new_user_notification_email_admin' ], 10, 3 );
        add_filter( 'comment_moderation_recipients', [ $this, 'fix_comment_moderation_notification_recipient' ], 10, 2 );
        add_filter( 'password_change_email', [$this, 'replace_admin_email_in_notification_emails'] );
    }

    public function filter_to_email( string $email ): string
    {
        return $this->get_email_for_notification( $email );
    }

    public function filter_wp_new_user_notification_email_admin( array $wp_new_user_notification_email_admin, \WP_User $user, string $blogname ): array
    {
        $wp_new_user_notification_email_admin['to'] = $this->get_email_for_notification( $wp_new_user_notification_email_admin['to'] );
        return $wp_new_user_notification_email_admin;
    }

    public function fix_comment_moderation_notification_recipient( array $emails, int $comment_id  ): array
    {
        $first_lms_admin_email = $this->get_email_for_notification();

        return ($first_lms_admin_email === NULL) ? $emails : [$first_lms_admin_email];
    }

    public function replace_admin_email_in_notification_emails( array $pass_change_email ): array {
        if(Software_Variant::is_saas()){
            $email = $this-> get_first_lms_admin_email();
        }else {
            $email = $this->get_first_admin_email();
        }  
        $pass_change_email['message'] = str_replace( '###ADMIN_EMAIL###', $email , $pass_change_email['message'] );
        return $pass_change_email;
      }

    protected function get_email_for_notification( ?string $email = null ): ?string
    {
        if ( $this->should_admin_email_be_filtered() ) {
            $notification_email = $this->get_first_lms_admin_email();
            if ( ! is_null( $notification_email ) ) {
                return $notification_email;
            }
        }

        return $email;
    }

    protected function should_admin_email_be_filtered(): bool
    {
        return Software_Variant::is_saas();
    }

    protected function get_first_lms_admin_email(): ?string
    {
        $users = User::findAllWithRole(Caps::ROLE_LMS_ADMIN);
        if ( count( $users ) > 0 ) {
            return $users[0]->getEmail();
        }

        return null;
    }
    
    protected function get_first_admin_email(): ?string
    {
        $users = User::findAllWithRole(Caps::ROLE_SITE_ADMIN);
        if ( count( $users ) > 0 ) {
            return $users[0]->getEmail();
        }

        return null;
    }
}
