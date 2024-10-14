<?php
use bpmj\wpidea\translator\Interface_Translator;

/** @var string $currency */
/** @var string $next_step_label */
/** @var Interface_Translator $translator */

?>
<section class='step-one animated fadeInUp'
         data-next-step-label="<?= $next_step_label ?>">
    <div class='row'>
        <div class='container'>
            <div class='panel'>
                <div class='panel-body'>

                    <div class='form-group'>
                        <label for='title'><?php _e('Title', BPMJ_EDDCM_DOMAIN); ?>*</label>
                        <input type="text" name="title" id="title" value="">
                        <div
                            class="desc"><?= $translator->translate('services.creator.enter_service_name_here') ?></div>
                    </div>

                    <div class="form-group">
                        <label for="content"><?php _e('Short description', BPMJ_EDDCM_DOMAIN); ?></label>
                        <?php
                        $settings = [
                            'media_buttons' => false,
                            'editor_height' => 200,
                            'teeny' => true,
                            'quicktags' => false
                        ];
                        wp_editor('', 'content', $settings);
                        ?>
                        <div
                            class="desc"><?php _e('This text will be shown on product page. Feel free, and put here whatever you want.', BPMJ_EDDCM_DOMAIN); ?></div>
                    </div>

                    <div id='single-price'>
                        <div class='form-group'>
                            <label for='price'><?php _e('Price', BPMJ_EDDCM_DOMAIN); ?></label>
                            <input type="number" step="0.01" name="price" id="price"
                                   placeholder="<?= edd_get_currency() ?>" class="quarter_width"
                                   value="">
                            <div
                                class="desc"><?= $translator->translate('services.creator.how_much_costs') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>