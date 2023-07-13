<?php

namespace bpmj\wpidea\admin\pages\students;

use bpmj\wpidea\caps\Access_Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;

class Student_Presenter_Filter
{
    private Interface_Filters $filters;

    public function __construct(
        Interface_Filters $filters
    ) {
        $this->filters = $filters;
    }

    public function filtered_array($student_array): array
    {
        $student_first_name = $this->filters->apply(Access_Filter_Name::CUSTOMER_FIRST_NAME, $student_array['first_name'], $student_array['id'], $student_array['email']);
        $student_last_name = $this->filters->apply(Access_Filter_Name::CUSTOMER_LAST_NAME, $student_array['last_name'], $student_array['id'], $student_array['email']);
        $student_full_name = $this->get_filtered_full_name($student_array);
        $student_login = $this->filters->apply(Access_Filter_Name::CUSTOMER_LOGIN, $student_array['login'], $student_array['id'], $student_array['email']);
        $student_email = $this->get_filtered_email($student_array);

        return [
            'id' => (int)$student_array['id'],
            'first_name' => (string)$student_first_name,
            'last_name' => (string)$student_last_name,
            'full_name' => $student_full_name,
            'login' => (string)$student_login,
            'email' => $student_email,
        ];
    }

    public function get_filtered_full_name(array $student_array): string
    {
        return $this->filters->apply(Access_Filter_Name::CUSTOMER_NAME, $student_array['full_name'], $student_array['id'], $student_array['email']);
    }

    public function get_filtered_email(array $student_array): string
    {
        return $this->filters->apply(Access_Filter_Name::CUSTOMER_EMAIL, $student_array['email'], $student_array['id'], $student_array['email']);
    }
}
