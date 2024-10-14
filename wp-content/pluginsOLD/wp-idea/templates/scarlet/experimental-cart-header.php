<!DOCTYPE html>
<html <?php
language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php
    bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    if (is_singular() && pings_open(get_queried_object())) : ?>
        <link rel="pingback" href="<?php
        bloginfo('pingback_url'); ?>">
    <?php
    endif; ?>
    <?php
    wp_head(); ?>

</head>


<body <?php
body_class(array(WPI()->templates->get_body_class())); ?>>
<?php
do_action('bpmj_eddc_after_body_open_tag'); ?>
<div id="page">
    <div class="contenter">
        <header class="experimental-cart-header">
            <div id="logo-cell" class='experimental-cart-column-left'>
                <?php
                echo WPI()->templates->get_logo(); ?>
            </div>
            <div class="experimental-cart-column-right">
                <?php
                if (WPI()->templates->should_show_go_back_button()): ?>
                    <a class="go-back-button" href="<?php
                    echo WPI()->templates->get_go_back_button_url(); ?>">
                        <?php
                        echo WPI()->templates->get_go_back_button_text(); ?>
                    </a>
                <?php
                endif; ?>
            </div>
    </div>
    </header>