<?php foreach ( $buttons as $button ) :
    $classes_string = '';
    if ( ! empty( $button['classes'] ) )
        $classes_string = $button['classes'];

    $data_string = '';
    if ( ! empty( $button['html-data'] ) )
        foreach ( $button['html-data'] as $data_key => $data_value )
            $data_string .= 'data-' . $data_key . '="' . $data_value . '"';
    ?>
    <div class="bpmj-eddcm-expiration-notice__button-container">
        <div class="bpmj-eddcm-expiration-notice__button <?= $classes_string; ?>" <?= $data_string; ?>>
            <a href="<?= $button['url']; ?>" type="button"
               class="btn-eddcm btn-eddcm-primary btn-eddcm-big"
               style="vertical-align: middle;"><?= $button['text']; ?></a>
        </div>
    </div>

    <?php if ( ! empty( $button['text_separator'] ) ) : ?>
        <div class="bpmj-eddcm-expiration-notice__button-container text-separator"><?= $button['text_separator']; ?></div>
    <?php endif; ?>
<?php endforeach; ?>

