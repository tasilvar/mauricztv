<?php
	use ycd\AdminHelper;
	$defaultData = AdminHelper::defaultData();
	$isPro = '';
	$proSpan = '';
	if(YCD_PKG_VERSION == YCD_FREE_VERSION) {
		$isPro = '-pro';
		$proSpan = '<span class="ycd-pro-span">'.__('pro', YCD_TEXT_DOMAIN).'</span>';
	}
	$allowed_html = AdminHelper::getAllowedTags();
?>
<div class="ycd-bootstrap-wrapper">
	<div class="row form-group">
		<div class="col-md-6">
			<label for="ycd-countdown-enable-floating-countdown" class="ycd-label-of-switch"><?php _e('Enable floating countdown', YCD_TEXT_DOMAIN); ?></label>
		</div>
		<div class="col-md-6">
			<label class="ycd-switch">
				<input type="checkbox" id="ycd-countdown-enable-floating-countdown" name="ycd-countdown-enable-floating-countdown" class="ycd-accordion-checkbox" <?php echo esc_attr($this->getOptionValue('ycd-countdown-enable-floating-countdown')); ?>>
				<span class="ycd-slider ycd-round"></span>
			</label>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-6">
			<label for="ycd-countdown-floating-position" class="ycd-label-of-switch"><?php _e('Position', YCD_TEXT_DOMAIN); ?></label>
		</div>
		<div class="col-md-6">
			<?php echo AdminHelper::selectBox($defaultData['floating-positions'],$this->getOptionValue('ycd-countdown-floating-position'), array('class' => 'js-ycd-select ycd-fixed-floating-position-val', 'name' => 'ycd-countdown-floating-position')); ?>
		</div>
	</div>
	<div class="row ypm-margin-bottom-15 form-group">
		<div class="col-xs-3 ycd-fixed-floating-positions-wrapper ycd-floating-position-wrapper-top ycd-hide">
			<label for="ycd-countdown-floating-position-top"><?php _e('Top',YCD_TEXT_DOMAIN )?></label>
			<input name="ycd-countdown-floating-position-top" value="<?php esc_attr_e($this->getOptionValue('ycd-countdown-floating-position-top'));?>" id="ycd-fixed-positions-top" class="form-control">
		</div>
		<div class="col-xs-3 ycd-fixed-floating-positions-wrapper ycd-floating-position-wrapper-right ycd-hide">
			<label for="ycd-countdown-floating-position-right"><?php _e('Right',YCD_TEXT_DOMAIN )?></label>
			<input name="ycd-countdown-floating-position-right" value="<?php esc_attr_e($this->getOptionValue('ycd-countdown-floating-position-right'));?>" id="ycd-fixed-positions-right" class="form-control">
		</div>
		<div class="col-xs-3 ycd-fixed-floating-positions-wrapper ycd-floating-position-wrapper-bottom ycd-hide">
			<label for="ycd-countdown-floating-position-bottom"><?php _e('Bottom',YCD_TEXT_DOMAIN )?></label>
			<input name="ycd-countdown-floating-position-bottom" value="<?php esc_attr_e($this->getOptionValue('ycd-countdown-floating-position-bottom'));?>" id="ycd-fixed-positions-bottom" class="form-control">
		</div>
		<div class="col-xs-3 ycd-fixed-floating-positions-wrapper ycd-floating-position-wrapper-left ycd-hide">
			<label for="ycd-countdown-floating-position-left"><?php _e('Left',YCD_TEXT_DOMAIN )?></label>
			<input name="ycd-countdown-floating-position-left" value="<?php esc_attr_e($this->getOptionValue('ycd-countdown-floating-position-left'));?>" id="ycd-fixed-positions-left" class="form-control">
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-6">
			<label for="ycd-countdown-floating-text"><?php _e('Text', YCD_TEXT_DOMAIN); ?></label>
		</div>
		<div class="col-md-6">
			<input name="ycd-countdown-floating-text" value="<?php esc_attr_e($this->getOptionValue('ycd-countdown-floating-text'));?>" id="ycd-fixed-positions-left" class="form-control">
		</div>
	</div>
	<!-- Close text -->
	<div class="row form-group">
		<div class="col-md-6">
			<label for="ycd-countdown-floating-close-text-status" class="ycd-label-of-switch"><?php _e('Enable Close text', YCD_TEXT_DOMAIN); ?></label>
		</div>
		<div class="col-md-6">
			<label class="ycd-switch">
				<input type="checkbox" id="ycd-countdown-floating-close-text-status" name="ycd-countdown-floating-close-text-status" class="ycd-accordion-checkbox" <?php echo esc_attr($this->getOptionValue('ycd-countdown-floating-close-text-status')); ?>>
				<span class="ycd-slider ycd-round"></span>
			</label>
		</div>
	</div>
	<div class="ycd-accordion-content ycd-hide-content">
		<div class="row form-group">
			<div class="col-md-6">
				<label for="ycd-countdown-floating-close-text"><?php _e('Text', YCD_TEXT_DOMAIN); ?></label>
			</div>
			<div class="col-md-6">
				<input name="ycd-countdown-floating-close-text" value="<?php esc_attr_e($this->getOptionValue('ycd-countdown-floating-close-text'));?>" id="ycd-countdown-floating-close-text" class="form-control">
			</div>
		</div>
	</div>
	<!-- Close text -->
	<div class="row form-group">
		<div class="col-xs-4">
			<label class="control-label"><?php _e('Content Padding', YCD_TEXT_DOMAIN);?></label>
		</div>
		<div class="col-xs-2">
			<label for="ycd-countdown-floating-padding-top" class="ycd-label">Top</label>
			<input type="text" id="ycd-countdown-floating-padding-top" data-direction="top" name="ycd-countdown-floating-padding-top" class="form-control button-padding" value="<?php echo esc_attr($this->getOptionValue('ycd-countdown-floating-padding-top')); ?>">
		</div>
		<div class="col-xs-2">
			<label for="ycd-countdown-floating-padding-right" class="ycd-label">Right</label>
			<input type="text" id="ycd-countdown-floating-padding-right" data-direction="right" name="ycd-countdown-floating-padding-right" class="form-control button-padding" value="<?php echo esc_attr($this->getOptionValue('ycd-countdown-floating-padding-right')); ?>">
		</div>
		<div class="col-xs-2">
			<label for="ycd-countdown-floating-padding-bottom" class="ycd-label">Bottom</label>
			<input type="text" id="ycd-countdown-floating-padding-bottom" data-direction="bottom" name="ycd-countdown-floating-padding-bottom" class="form-control button-padding" value="<?php echo esc_attr($this->getOptionValue('ycd-countdown-floating-padding-bottom')); ?>">
		</div>
		<div class="col-xs-2">
			<label for="ycd-countdown-floating-padding-left" class="ycd-label">Left</label>
			<input type="text" id="ycd-countdown-floating-padding-left" data-direction="left" name="ycd-countdown-floating-padding-left" class="form-control button-padding" value="<?php echo esc_attr($this->getOptionValue('ycd-countdown-floating-padding-left')); ?>">
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-6">
			<label for="ycd-countdown-floating-text-size"><?php _e('Font size', YCD_TEXT_DOMAIN); ?></label>
		</div>
		<div class="col-md-6">
			<input name="ycd-countdown-floating-text-size" id="ycd-countdown-floating-text-size" value="<?php esc_attr_e($this->getOptionValue('ycd-countdown-floating-text-size'));?>" id="ycd-fixed-positions-left" class="form-control" placeholder="<?php _e('Font size')?>">
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-6">
			<label for="ycd-countdown-floating-text-color" class=""><?php _e('Text color', YCD_TEXT_DOMAIN); echo wp_kses($proSpan, $allowed_html); ?></label>
		</div>
		<div class="col-md-6 ycd-option-wrapper<?php echo esc_attr($isPro); ?>">
			<div class="">
				<input type="text" id="ycd-countdown-floating-text-color" placeholder="<?php _e('Select color', YCD_TEXT_DOMAIN)?>" name="ycd-countdown-floating-text-color" class="minicolors-input form-control generalColors" value="<?php echo esc_attr($this->getOptionValue('ycd-countdown-floating-text-color')); ?>">
			</div>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-6">
			<label for="ycd-countdown-floating-text-content-bg-color" class=""><?php _e('Content background color', YCD_TEXT_DOMAIN); echo wp_kses($proSpan, $allowed_html); ?></label>
		</div>
		<div class="col-md-6 ycd-option-wrapper<?php echo esc_attr($isPro); ?>">
			<div class="">
				<input type="text" id="ycd-countdown-floating-text-content-bg-color" placeholder="<?php _e('Select color', YCD_TEXT_DOMAIN)?>" name="ycd-countdown-floating-text-content-bg-color" class="minicolors-input form-control generalColors" value="<?php echo esc_attr($this->getOptionValue('ycd-countdown-floating-text-content-bg-color')); ?>">
			</div>
		</div>
	</div>
</div>