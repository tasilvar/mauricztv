<?php

namespace bpmj\wpidea\scopes;

use bpmj\wpidea\Current_Request;

class Admin_Custom_Post_Courses_List_Scope extends Abstract_Scope
{
    const POST_TYPE_SLUG = 'courses';

    protected $dependents_scopes = [
        Admin_Without_Ajax_Scope::class => true,
    ];

    private $request;

    public function __construct(Current_Request $request)
    {
        $this->request = $request;
    }

    public function check_scope(): bool
    {
        global $pagenow;

        return 'edit.php' === $pagenow && $this->request->get_query_arg('post_type') == self::POST_TYPE_SLUG;
    }
}