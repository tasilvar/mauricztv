<?php
namespace bpmj\wpidea\api;

use bpmj\wpidea\api\products\Products;

class WPI
{
    private static $instance;

    /**
     * @var Products
     */
    public $products;

    public function __construct() {

        $this->products = new Products();
    }

    public static function instance()
    {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPI ) ) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}