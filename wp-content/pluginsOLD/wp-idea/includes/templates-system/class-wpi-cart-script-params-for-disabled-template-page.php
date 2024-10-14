<?php

namespace bpmj\wpidea\templates_system;

use bpmj\wpidea\controllers\Payment_Controller;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;

class Wpi_Cart_Script_Params_For_Disabled_Template_Page implements Interface_Initiable
{
    private $url_generator;
    private $nonce_handler;

    public function __construct(Nonce_Handler $nonce_handler, Interface_Url_Generator $url_generator)
    {
        $this->nonce_handler = $nonce_handler;
        $this->url_generator = $url_generator;
    }

    public function init(): void
    {
        add_action('template_redirect', function (){

            $value = get_post_meta(get_the_ID(), 'bpmj_eddcm_disable_wp_idea_template', true);

            if($value !== 'yes'){
                return;
            }

            wp_register_script( 'wp_cart_helper', false  );
            wp_localize_script('wp_cart_helper', 'wpidea', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce_value' => $this->nonce_handler::create(),
                'nonce_name' => $this->nonce_handler::DEFAULT_REQUEST_VARIABLE_NAME,
                'urls' => [
                    'payment_load_gateway' => $this->url_generator->generate(Payment_Controller::class,'load_gateway'),
                    'payment_process_checkout' => $this->url_generator->generate(Payment_Controller::class,'process_checkout'),
                    'payment_add_to_cart' =>$this->url_generator->generate(Payment_Controller::class,'add_to_cart'),
                ]
            ));
            wp_enqueue_script('wp_cart_helper');
        });
    }
}

