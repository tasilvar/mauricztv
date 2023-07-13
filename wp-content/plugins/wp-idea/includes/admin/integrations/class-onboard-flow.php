<?php
namespace bpmj\wpidea\admin\integrations;

use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\admin\subscription\models\Subscription_Const;
use bpmj\wpidea\Caps;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\wolverine\user\User;

class Onboard_Flow implements Interface_Tracker, Interface_Initiable
{
    private const SITE_KEY = 'yYhr9zvi';
    private const ACTION_NAME_TO_PRINT_THE_SCRIPT = 'admin_footer';

    protected $actions;
    protected $subscription;

    protected $data = [];

    public function __construct(Interface_Actions $actions,
        Subscription $subscription)
    {
        $this->actions = $actions;
        $this->subscription = $subscription;
    }

    public function init(): void
    {

      if(empty($this->subscription->get_id())) {
          return;
      }

        $this->actions->add(self::ACTION_NAME_TO_PRINT_THE_SCRIPT, [ $this, 'print_script' ]);
    }

    public function add_data(string $name, string $value, string $type = Interface_Tracker::TYPE_GENERAL): void
    {
        $value = $this->format_value($value, $type);

        if(is_null($value)) {
            return;
        }

        $this->data[] = ['name'  => $name,
                         'value' => $value
        ];
    }

    /**
     * @return mixed
     */
    private function format_value(string $value, string $type)
    {
        if(Interface_Tracker::TYPE_GENERAL === $type || Interface_Tracker::TYPE_STRING === $type ) {
            return $value;
        }

        if(Interface_Tracker::TYPE_INT === $type) {
            return intval($value);
        }

        if(Interface_Tracker::TYPE_FLOAT === $type) {
            return floatval($value);
        }

        if(Interface_Tracker::TYPE_TOGGLEABLE === $type) {
            return 'on' === $value ? 'on' : null;
        }

        return $value;
    }

    public function print_script(): void
    {
        if($this->subscription->get_type() == Subscription_Const::TYPE_BOX &&
            !User::currentUserHasAnyOfTheRoles([CAPS::ROLE_LMS_ADMIN, CAPS::ROLE_SITE_ADMIN])) {
            return;
        }

        if($this->subscription->get_type() == Subscription_Const::TYPE_GO &&
            !User::currentUserHasAnyOfTheRoles([CAPS::ROLE_LMS_ADMIN])) {
            return;
        }

        $site_key = self::SITE_KEY;
        $id = $this->subscription->get_id();
        $email = $this->subscription->get_subscriber_email();
        $subscription_name = $this->subscription->get_full_name();
        $subscription_status = $this->subscription->get_status();
        $subscription_value = $this->subscription->get_value();
        $subscription_interval = $this->subscription->get_interval();
        $date_start = $this->subscription->get_trial_start_timestamp();
        $date_end = $this->subscription->get_trial_end_timestamp();
        $data = $this->data;

        require_once( BPMJ_EDDCM_DIR. '/includes/admin/views/integrations/onboard-flow-script.php' );
    }
}
