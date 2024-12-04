<?php
use ycd\AdminHelper;
$defaultData = AdminHelper::defaultData();
?>
<div class="ycd-bootstrap-wrapper">
<div class="row form-group">
    <div class="col-md-6">
        <label for="ycd-countdown-hide-mobile" class="ycd-label-of-switch"><?php _e('Hide On Mobile Devices', YCD_TEXT_DOMAIN); ?></label>
    </div>
    <div class="col-md-6">
        <label class="ycd-switch">
            <input type="checkbox" id="ycd-countdown-hide-mobile" name="ycd-countdown-hide-mobile" class="" <?php echo esc_attr($this->getOptionValue('ycd-countdown-hide-mobile')) ?> >
            <span class="ycd-slider ycd-round"></span>
        </label>
    </div>
</div>
<div class="row form-group">
    <div class="col-md-6">
        <label for="ycd-countdown-show-mobile" class="ycd-label-of-switch"><?php _e('Show Only On Mobile Devices', YCD_TEXT_DOMAIN); ?></label>
    </div>
    <div class="col-md-6">
        <label class="ycd-switch">
            <input type="checkbox" id="ycd-countdown-show-mobile" name="ycd-countdown-show-mobile" class="" <?php echo esc_attr($this->getOptionValue('ycd-countdown-show-mobile')) ?> >
            <span class="ycd-slider ycd-round"></span>
        </label>
    </div>
</div>
<?php if($this->isAllowOption('ycd-countdown-selected-countries')): ?>
<div class="row">
    <div class="col-md-6">
        <label for="ycd-countdown-selected-countries" class="ycd-label-of-switch"><?php _e('Filter Countdown For Selected Countries', YCD_TEXT_DOMAIN); ?></label>
    </div>
    <div class="col-md-6">
        <label class="ycd-switch">
            <input type="checkbox" id="ycd-countdown-selected-countries" name="ycd-countdown-selected-countries" class="ycd-accordion-checkbox" <?php echo esc_attr($this->getOptionValue('ycd-countdown-selected-countries')); ?>>
            <span class="ycd-slider ycd-round"></span>
        </label>
    </div>
</div>
<div class="ycd-accordion-content ycd-hide-content">
    <div class="row form-group">
        <div class="col-md-2">
            <label for="" class="ycd-range-slider-wrapper"><?php _e('countries', YCD_TEXT_DOMAIN); ?></label>
        </div>
        <div class="col-md-4">
            <?php AdminHelper::selectBox($defaultData['countries-is'],$this->getOptionValue('ycd-countries-is'), array('class' => 'js-ycd-select', 'name' => 'ycd-countries-is')); ?>
        </div>
        <div class="col-md-5 ycd-circles-width-wrapper">
            <?php AdminHelper::selectBox($defaultData['countries-names'],$this->getOptionValue('ycd-counties-names'), array('class' => 'js-ycd-select', 'name' => 'ycd-counties-names[]', 'multiple' => 'multiple')); ?>
        </div>
    </div>
</div>
<?php endif; ?>
<?php
	$allowed_html = AdminHelper::getAllowedTags();
	echo wp_kses(AdminHelper::upgradeButton(), $allowed_html);
?>
</div>