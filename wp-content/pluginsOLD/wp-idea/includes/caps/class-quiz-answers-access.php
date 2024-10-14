<?php

namespace bpmj\wpidea\caps;

use bpmj\wpidea\Caps;

class Quiz_Answers_Access extends Access
{

    public function verifyPage($post = null)
    {
        if (!empty($post) && 'tests' === get_post_type($post)) {
            return $this->grant_access();
        }

        return parent::verifyPage($post);
    }

    public function grant_access()
    {
        if (!empty($this->all_caps[Caps::CAP_MANAGE_QUIZZES])) {
            $this->all_caps['edit_pages'] = true;
            $this->all_caps['edit_others_pages'] = true;
            $this->all_caps['edit_published_pages'] = true;
            $this->all_caps['edit_post'] = true;
            $this->all_caps['edit_others_posts'] = true;
            $this->all_caps['edit_published_posts'] = true;
        }

        return $this->all_caps;
    }
}