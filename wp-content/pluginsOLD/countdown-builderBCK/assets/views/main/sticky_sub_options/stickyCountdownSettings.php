<?php
use ycd\MultipleChoiceButton;
use ycd\AdminHelper;
?>
<div class="ycd-multichoice-wrapper">
	<?php
	$multipleChoiceButton = new MultipleChoiceButton($defaultData['stickyCountdownMode'], esc_attr($this->getOptionValue('ycd-sticky-countdown-mode')));
	echo wp_kses($multipleChoiceButton, $allowed_html);
	?>
</div>
<div id="ycd-sticky-countdown-custom" class="ycd-sub-option ycd-hide">
	<div class="row form-group">
		<div class="col-md-6">
			<label class="ycd-label-of-input"><?php _e('Select Countdown', YCD_TEXT_DOMAIN); ?></label>
		</div>
		<div class="col-md-4">
			<?php
			if (count(array_keys($countdownsIdAndTitle)) <= 1) {
				echo '<a href="'.esc_attr($createCountdown).'">Create Countdown</a>';
			}
			else {
				$countdownSelect = AdminHelper::selectBox($countdownsIdAndTitle, esc_attr($this->getOptionValue('ycd-sticky-countdown')), array('name' => 'ycd-sticky-countdown', 'class' => 'js-ycd-select'));
				echo wp_kses($countdownSelect, $allowed_html);
			}
			?>
		</div>
	</div>
</div>
<div id="ycd-sticky-countdown-default" class="ycd-sub-option ycd-hide">

	<div class="row form-group">
		<div class="col-md-4">
			<label class="ycd-label-of-input"><?php _e('Texts', YCD_TEXT_DOMAIN); ?></label>
		</div>
		<div class="col-md-2">
			<label for="ycd-sticky-countdown-days" class="yrm-label"><?php _e('Days', YCD_TEXT_DOMAIN); ?></label>
			<input type="text" id="ycd-sticky-countdown-days" name="ycd-sticky-countdown-days" class="form-control button-padding" value="<?php echo esc_attr($this->getOptionValue('ycd-sticky-countdown-days')); ?>">
		</div>
		<div class="col-md-2">
			<label for="ycd-sticky-countdown-hours" class="yrm-label"><?php _e('Hours', YCD_TEXT_DOMAIN); ?></label>
			<input type="text" id="ycd-sticky-countdown-hours" name="ycd-sticky-countdown-hours" class="form-control button-padding" value="<?php echo esc_attr($this->getOptionValue('ycd-sticky-countdown-hours')); ?>">
		</div>
		<div class="col-md-2">
			<label for="ycd-sticky-countdown-minutes" class="yrm-label"><?php _e('Minutes', YCD_TEXT_DOMAIN); ?></label>
			<input type="text" id="ycd-sticky-countdown-minutes" name="ycd-sticky-countdown-minutes" class="form-control button-padding" value="<?php echo esc_attr($this->getOptionValue('ycd-sticky-countdown-minutes')); ?>">
		</div>
		<div class="col-md-2">
			<label for="ycd-sticky-countdown-seconds" class="yrm-label"><?php _e('Seconds', YCD_TEXT_DOMAIN); ?></label>
			<input type="text" id="ycd-sticky-countdown-seconds" name="ycd-sticky-countdown-seconds" class="form-control button-padding" value="<?php echo esc_attr($this->getOptionValue('ycd-sticky-countdown-seconds')); ?>">
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-6">
			<label class="ycd-label-of-input"><?php _e('Color', YCD_TEXT_DOMAIN); echo wp_kses($proSpan, $allowed_html); ?></label>
		</div>
		<div class="col-md-5 ycd-option-wrapper<?php echo esc_attr($isPro); ?>">
			<div class="minicolors minicolors-theme-default minicolors-position-bottom minicolors-position-left">
				<input type="text" id="ycd-sticky-countdown-text-color" placeholder="<?php _e('Select color', YCD_TEXT_DOMAIN)?>" name="ycd-sticky-countdown-text-color" class="minicolors-input form-control js-ycd-sticky-color" value="<?php echo esc_attr($this->getOptionValue('ycd-sticky-countdown-text-color')); ?>">
			</div>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-6">
			<label class="ycd-label-of-input"><?php _e('Font wight', YCD_TEXT_DOMAIN); ?></label>
		</div>
		<div class="col-md-5">
			<?php
			$fontWeight =  AdminHelper::selectBox($defaultData['font-weight'], $this->getOptionValue('ycd-stick-countdown-font-weight'), array('name' => 'ycd-stick-countdown-font-weight', 'class' => 'js-ycd-select'));
			echo wp_kses($fontWeight, $allowed_html);
			?>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-6">
			<label class="ycd-label-of-input"><?php _e('Font size', YCD_TEXT_DOMAIN); ?></label>
		</div>
		<div class="col-md-5">
			<input type="number" class="form-control" name="ycd-stick-countdown-font-size" value="<?php echo esc_attr($this->getOptionValue('ycd-stick-countdown-font-size'));?>">
		</div>
		<div class="col-md-1">
			<?php _e('Px', YCD_TEXT_DOMAIN); ?>
		</div>
	</div>
</div>