<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\packages;

use bpmj\wpidea\admin\settings\web\Settings_Info_Box;
use bpmj\wpidea\Packages;
use bpmj\wpidea\translator\Interface_Translator;

class Packages_API implements Interface_Packages_API
{
    private Packages $packages;
    private Interface_Translator $translator;
	private Feature_Access_Manual_Manager $feature_access_manual_manager;

	public function __construct(
        Packages $packages,
        Interface_Translator $translator,
	    Feature_Access_Manual_Manager $feature_access_manual_manager
    )
    {
        $this->packages = $packages;
        $this->translator = $translator;
	    $this->feature_access_manual_manager = $feature_access_manual_manager;
    }

    public function has_access_to_feature(string $feature): bool
    {
        return $this->packages->has_access_to_feature($feature)
               || $this->feature_access_manual_manager->is_access_to_feature_forced($feature);
    }

    public function render_no_access_to_feature_info(string $feature, ?string $custom_message = null, bool $short = false): string
    {
        if($this->has_access_to_feature($feature)) {
            return '';
        }

        $message = $this->get_required_package_info($feature, $custom_message, $short);
        $message_full_raw = $this->get_full_raw_required_package_info($feature);

        return Settings_Info_Box::render_package_warning_box(
            $message, $message_full_raw
        );
    }

	public function get_feature_required_package( string $feature ): ?string
	{
		return $this->packages->get_required_package($feature);
	}

	private function get_required_package_info(string $feature, ?string $custom_message = null, bool $short = false): string
    {
        $required_package = $this->packages->get_required_package($feature);

        if ($required_package === Packages::REQUIRED_PACKAGE_PRO) {
			$plan = '<strong><span class="package-name">' . Packages::PLAN_PRO . '</span></strong>';

			if($short) {
				return sprintf(
					$this->translator->translate('packages.info.you_need_to_upgrade_your_plan_to.short'),
					$plan
				);
			}

            $in_package_message = sprintf(
                $this->translator->translate('packages.info.you_need_to_upgrade_your_plan_to'),
                $plan
            );
        } else {
	        $plan_plus = '<strong><span class="package-name">' . Packages::PLAN_PLUS . '</span>';
	        $plan_pro = '<span class="package-name">' . Packages::PLAN_PRO . '</span></strong>';

	        if($short) {
		        return sprintf($this->translator->translate('packages.info.you_need_to_upgrade_your_plan_to.one_of.short'),
			        $plan_plus,
			        $plan_pro
		        );
	        }

            $in_package_message = sprintf($this->translator->translate('packages.info.you_need_to_upgrade_your_plan_to.one_of'),
	            $plan_plus,
	            $plan_pro
            );
        }

        $message_base = $custom_message ?: $this->translator->translate('packages.info.you_need_to_upgrade_your_plan');

        return sprintf(
            $message_base,
            $in_package_message
        );
    }

	private function get_full_raw_required_package_info(string $feature): string
    {
        $required_package = $this->packages->get_required_package($feature);

        if ($required_package === Packages::REQUIRED_PACKAGE_PRO) {
            $in_package_message = sprintf(
                $this->translator->translate('packages.info.you_need_to_upgrade_your_plan_to'),
	            strtoupper(Packages::PLAN_PRO)
            );
        } else {
            $in_package_message = sprintf($this->translator->translate('packages.info.you_need_to_upgrade_your_plan_to.one_of'),
	            strtoupper(Packages::PLAN_PLUS),
	            strtoupper(Packages::PLAN_PRO)
            );
        }

        return sprintf(
	        $this->translator->translate('packages.info.you_need_to_upgrade_your_plan'),
            $in_package_message
        );
    }
}