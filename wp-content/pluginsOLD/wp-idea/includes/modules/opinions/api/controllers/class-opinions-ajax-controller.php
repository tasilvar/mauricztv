<?php

namespace bpmj\wpidea\modules\opinions\api\controllers;

use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\modules\opinions\api\Opinions_API;
use bpmj\wpidea\modules\opinions\core\entities\Opinion;
use bpmj\wpidea\modules\opinions\core\value_objects\Opinion_Content;
use bpmj\wpidea\modules\opinions\core\value_objects\Opinion_Rating;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use bpmj\wpidea\user\User_ID;

class Opinions_Ajax_Controller extends Ajax_Controller
{
    private Opinions_API $opinions_api;
	private Interface_Current_User_Getter $current_user_getter;
    private Interface_Product_API $product_api;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Current_User_Getter $current_user_getter,
        Interface_Product_API $product_api,
        Opinions_API $opinions_api
    ) {
        $this->current_user_getter = $current_user_getter;
        $this->product_api = $product_api;
        $this->opinions_api    = $opinions_api;

		parent::__construct($access_control, $translator, $redirector);
	}

    public function behaviors(): array
    {
        return [
            'roles' => array_merge(Caps::ROLES_ADMINS_SUPPORT_SUBSCRIBER, [Caps::ROLE_LMS_PARTNER]),
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function save_action(Current_Request $current_request): string
    {
        $product_id = (int)$current_request->get_body_arg('product');
        $rating = $current_request->get_body_arg('rating');
        $content = $current_request->get_body_arg('content');

        $current_user_id = $this->get_current_user_id();

        if (!$current_user_id) {
            return $this->fail($this->translator->translate('opinions.add_opinion_form.error.no_user_found'));
        }

		$rated_product = $this->product_api->find($product_id);

	    if (!$rated_product) {
		    return $this->fail($this->translator->translate('opinions.add_opinion_form.error.no_product_found'));
	    }

        if (!$this->product_api->user_has_or_had_access_to_product($current_user_id, $product_id)) {
            return $this->fail($this->translator->translate('opinions.add_opinion_form.error.no_access_to_product'));
        }

        if ($this->opinions_api->product_is_already_rated_by_user(new User_ID($current_user_id), new Product_ID((int)$product_id))) {
            return $this->fail($this->translator->translate('opinions.add_opinion_form.error.product_already_rated'));
        }

	    $opinion = Opinion::create(
		    new Product_ID((int)$product_id),
			new User_ID($current_user_id),
			new Opinion_Content($content),
			new \DateTime('now'),
		    new Opinion_Rating((int)$rating),
	    );

        $this->opinions_api->create($opinion);

        $products = $this->opinions_api->get_products_user_can_rate($current_user_id);

        $newProducts = [];

        foreach($products as $product){
            $newProducts[] = [
                'name' => $product->get_product_name(),
                'id' => $product->get_product_id()
            ];
        }

	    return $this->success(
		    [
			    'message' =>
				    $products->is_empty()
					    ? sprintf($this->translator->translate('opinions.add_opinion_form.success'), $rated_product->get_name())
					    : sprintf($this->translator->translate('opinions.add_opinion_form.success.rate_next'), $rated_product->get_name()),
			    'newProducts' => $newProducts,
			    'selectProductMessage' => $this->translator->translate('user_account.opinions.add.select.select_product')
		    ]
	    );
    }

    private function get_current_user_id(): ?int
    {
        $user = $this->current_user_getter->get();

        if (!$user) {
            return null;
        }

        return $user->get_id()->to_int();
    }

    private function fix_new_lines(string $text): string
    {
        return nl2br(htmlentities($text, ENT_QUOTES, 'UTF-8'));
    }
}

