<?php
namespace bpmj\wpidea\wolverine\product;

class Access
{
    protected $productId;

    public function __construct($productId) {
        $this->productId = $productId;
    }

    public function checkUserAccess($userId)
    {
        $access = bpmj_eddpc_get_user_valid_access($userId, $this->productId);
    
        return $access;
    }
}