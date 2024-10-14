<?php

namespace bpmj\wpidea\admin\subscription\models;

class Subscription_Const
{
    public const TYPE_GO = 1;
    public const TYPE_BOX = 2;

    public const TYPE_SAAS_NAME = 'GO';
    public const TYPE_ON_PREMISE_NAME = 'BOX';

    public const PLAN_START = 'start';
    public const PLAN_PLUS = 'plus';
    public const PLAN_PRO = 'pro';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_TRIALING = 'trialing';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_DEV = 'dev';

    public const BASE_VALUE_PRO = 2497;
    public const BASE_VALUE_PLUS = 1597;
    public const BASE_VALUE_START = 997;
}
