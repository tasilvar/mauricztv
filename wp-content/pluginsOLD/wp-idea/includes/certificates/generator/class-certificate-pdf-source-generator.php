<?php namespace bpmj\wpidea\certificates\generator;

use bpmj\wpidea\learning\course\Course;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\user\User;
use DateTime;

class Certificate_Pdf_Source_Generator
{

    private Interface_Settings $settings;

    private Interface_Readable_Course_Repository $course_repository;

    public function __construct(
        Interface_Settings $settings,
        Interface_Readable_Course_Repository $course_repository
    ) {
        $this->settings          = $settings;
        $this->course_repository = $course_repository;
    }

    public function get_pdf_source(Course $course, User $user, DateTime $date_generated): string
    {
        $template_content = $this->replace_placeholders_with_values(
            $this->settings->get('certificate_template', ''),
            $course,
            $user,
            $date_generated
        );

        $certificate_bg = $this->settings->get('certificates_bg', '');

        ob_start();
        ?>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
            <style type="text/css">
                <?php if ( ! empty( $certificate_bg ) ) : ?>
                body {
                    background: url("<?php echo $certificate_bg; ?>") no-repeat center;
                }

                <?php endif; ?>
                <?php echo $this->settings->get('certificate_template_styles', ''); ?>
            </style>
        </head>
        <body>
        <?php echo bpmj_eddcm_prepare_certificate_template_content($template_content); ?>
        </body>
        </html>
        <?php

        return ob_get_clean();
    }

    private function replace_placeholders_with_values(
        $template_content,
        Course $course,
        User $user,
        DateTime $date_generated
    ): string {

        $template_content = str_replace('{course_name}', $course->get_title(), $template_content);
        $template_content = str_replace(
            '{course_price}',
            $this->course_repository->get_course_price_for_user($course->get_id(), $user),
            $template_content);
        $template_content = str_replace('{student_name}', $this->get_student_name($user), $template_content);
        $template_content = str_replace('{student_first_name}', $user->get_first_name(), $template_content);
        $template_content = str_replace('{student_last_name}', $user->get_last_name(), $template_content);

        $certificate_date = is_null($date_generated) ? date('Y-m-d') : date_format($date_generated, 'Y-m-d');
        $template_content = str_replace('{certificate_date}', $certificate_date, $template_content);

        return $template_content;
    }

    protected function get_student_name(User $user): string
    {
        return $user->full_name() ? $user->full_name()->get_full_name() : $user->get_login();
    }
}