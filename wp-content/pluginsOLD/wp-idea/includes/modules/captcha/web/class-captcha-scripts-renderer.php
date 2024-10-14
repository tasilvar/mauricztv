<?php

namespace bpmj\wpidea\modules\captcha\web;

use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\modules\captcha\core\providers\Interface_Captcha_Config_Provider;

class Captcha_Scripts_Renderer
{
    private Interface_Captcha_Config_Provider $captcha_config_provider;
    private Interface_Actions $actions;

    public function __construct(
        Interface_Captcha_Config_Provider $captcha_config_provider,
        Interface_Actions $actions
    ) {
        $this->captcha_config_provider = $captcha_config_provider;
        $this->actions = $actions;
    }

    public function init(): void
    {
        $this->actions->add(Action_Name::HEAD, [$this, 'before_head_close_tag_scripts'], 1000);
    }

    public function before_head_close_tag_scripts(): void
    {
        echo $this->get_head_scripts();
    }

    private function get_head_scripts(): string
    {
        ob_start();
        ?>
        <script src="https://www.google.com/recaptcha/api.js?render=<?= $this->captcha_config_provider->get_site_key() ?>"></script>
        <script>
            jQuery(document).ready(function ($) {
                $('#contact_form_submit_button').on('click', function (e) {
                    e.preventDefault();
                    grecaptcha.ready(function () {
                        grecaptcha.execute("<?= $this->captcha_config_provider->get_site_key() ?>", {action: "submit"}).then(function (token) {
                            let contactForm = $('#contact_form');
                            contactForm.append('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
                            contactForm.submit();
                        });
                    });
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
