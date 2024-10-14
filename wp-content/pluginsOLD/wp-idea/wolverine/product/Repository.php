<?php

namespace bpmj\wpidea\wolverine\product;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\helpers\Product_Sorter_Static_Helper;
use bpmj\wpidea\resources\Resource_Type;
use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\wolverine\product\course\Product as ProductCourse;

class Repository
{
    const PRODUCT_NOT_INITIALIZED = 'Product is not initialized';

    const GTU_META_NAME = 'gtu';
    const VARIABLE_PRICES_META_NAME = 'edd_variable_prices';
    const INVOICES_VAT_RATE_META_NAME = 'invoices_vat_rate';

    private const CUSTOM_ORDER_NAME = 'custom_order';

    public function findAll($limit = null, $offset = null, $order_by = null, $order = 'ASC')
    {
        if( !in_array(strtoupper($order), ['ASC', 'DESC']) ) {
            $order = 'ASC';
        }

        $productsIdsByCustomOrder = LMS_Settings::get_option(Settings_Const::CUSTOM_SORTING);

        if (self::CUSTOM_ORDER_NAME === $order_by) {

            if(!empty($productsIdsByCustomOrder)){
                return $this->getProductsByCustomSorting($productsIdsByCustomOrder, $limit, $offset);
            }

            $order_by = 'post_date';
            $order = 'DESC';
        }
        
        $productsIds = WPI()->courses->get_all_products_ids($limit, $offset, $order_by, $order);

        $products = [];

        foreach ($productsIds as $key => $id) {
            $products[] = $this->find($id);
        }

        if ('price' === $order_by) {
            usort($products, [$this, 'compareProductsByPrice' . ucfirst(strtolower($order))]);
            $products = array_slice($products, $offset, $limit);
        }

        return $products;
    }

    private function getProductsByCustomSorting(array $productsIdsByCustomSorting, ?int $limit, ?int $offset): array
    {
        $availableProductsIds = [];
        foreach (WPI()->courses->get_all_products_ids() as $product_id) {
            $availableProductsIds[] = [
                'id' => (int)$product_id
            ];

        }

       $productsByCustomSorting = Product_Sorter_Static_Helper::get_products_by_custom_sorting($productsIdsByCustomSorting, $availableProductsIds);

        $products = [];

        foreach ($productsByCustomSorting as  $product) {
            $products[] = $this->find($product['id']);
        }

        return array_slice($products, $offset, $limit);

    }

    private function compareProductsByPriceAsc($prod_a, $prod_b) {
        $price_a = $prod_a->getPriceForSorting();
        $price_b = $prod_b->getPriceForSorting();

        return $price_a <=> $price_b;
    }

    private function compareProductsByPriceDesc($prod_a, $prod_b) {
        return $this->compareProductsByPriceAsc($prod_b, $prod_a);
    }
    
    public function countAll()
    {
        return count(WPI()->courses->get_all_products_ids());
    }

    public function find($id)
    {
        $product = $this->getProperProductInstance($id);
        $product->setId($id);
        $product = $this->load($product);

        return $product;
    }

    public function save(Product $product)
    {
        $params = [
            'name' => !empty($product->getName()) ? $product->getName() : 'New Product',
            'price' => $product->getPrice(),
            'promotionalPrice' => $product->getPromotionalPrice(),
            'variants' => $product->getVariants()
        ];

        if (!$this->isProductStored($product->getId())) {
            $args = [
                'post_title' => $params['name'],
                'post_status' => 'publish'
            ];
            $download = $this->getDownload($product->getId());
            $download->create($args);
            $product->setId($download->ID);
        } else {
            $this->saveName($product->getId(), $params['name']);
        }


        $this->savePrice($product->getId(), $params['price']);
        $this->savePromotionalPrice($product->getId(), $params['promotionalPrice']);
        $this->saveVariants($product->getId(), $params['variants']);

        if($product->getGtu()) {
            $this->saveGtu($product->getId(), $product->getGtu());
        }

        return $product;
    }

    protected function getDownload($product_id = null)
    {
        return new \EDD_Download($product_id);
    }

    protected function getName($id)
    {
        return $this->getDownload($id)->post_title;
    }

    protected function getPrice($id)
    {
        return (double) get_post_meta($id, 'edd_price', true);
    }

    protected function getPromotionalPrice($id)
    {
        $sale_price = get_post_meta($id, 'edd_sale_price', true);

        if ('' === $sale_price)
            return null;

        return (double) $sale_price;
    }

    protected function saveName($productId, $name)
    {
        if (!empty($name)) {
            wp_update_post([ // @todo: tworzy revision - może trzeba aktualizowa bezpośrednio?
                'ID' => $productId,
                'post_title' => $name
            ]);
        }
    }

    protected function savePrice($productId, $price)
    {
        if (!empty($price) || 0 === $price || 0.0 == $price) {
            update_post_meta($productId, 'edd_price', $price);
        }
    }

    protected function savePromotionalPrice($productId, $promotionalPrice)
    {
        if (!empty($promotionalPrice) || 0 === $promotionalPrice || 0.0 == $promotionalPrice) {
            update_post_meta($productId, 'edd_sale_price', $promotionalPrice);
        }
    }

    protected function saveVariants($productId, $variants)
    {
        if (empty($variants) || 0 === $variants->count()) {
            return;
        }
        // https://www.unserialize.com/s/16699256-0f38-7c49-13e1-00004f03c3eb
        $variantsInRepositoryFormat = [];

        foreach ($variants as $variant) {
            $variantsInRepositoryFormat[$variant->getId()] = [
                'name' => $variant->getName(),
                'amount' => $variant->getPrice()
            ];

            if($variant->getGtu()) {
                $variantsInRepositoryFormat[$variant->getId()][self::GTU_META_NAME] = $variant->getGtu()->get_code();
            }
        }

        update_post_meta($productId, self::VARIABLE_PRICES_META_NAME, $variantsInRepositoryFormat);
        update_post_meta($productId, '_edd_price_options_mode', false);
        update_post_meta($productId, '_variable_pricing', true);
    }

    protected function isProductStored($id)
    {
        return !empty($id);
    }

    protected function productHasCourse($id)
    {
        return !empty(WPI()->courses->get_course_by_product($id));
    }

    protected function getProperProductInstance($id)
    {
        $class = Product::class;

        if($this->productHasCourse($id)) $class = ProductCourse::class;

        // if($this->repository->isBundle()) $class = BundleProduct::class;

        return new $class;
    }

    protected function load(Product $product)
    {
        $id = $product->getId();

        $product->setName($this->getName($id))
            ->setPrice($this->getPrice($id))
            ->setPromotionalPrice($this->getPromotionalPrice($id))
            ->setThumbnail($this->getProductThumbnail($id))
            ->setPanelLink($this->getProductPanelLink($id))
            ->setProductLink($this->getProductLink($id))
            ->setExcerpt($this->getProductExcerpt($id))
            ->setCategories($this->getProductCategories($id))
            ->setTags($this->getProductTags($id))
            ->setIsInCart($this->getIsProductInCart($id))
            ->setSalesStatus($this->getProductSalesStatus($id))
            ->setPriceMode($this->getProductPriceMode($id))
            ->setGoStraightToCheckoutModeEnabled($this->getGoStraightToCheckoutModeEnabled($id))
            ->setDefaultVariantId($this->getDefaultVariantId($id))
            ->setLinkedResourceType($this->getLinkedResourceType($id));

        $gtu = $this->getGtu($id);
        if($gtu) {
            $product->setGtu(new Gtu($gtu));
        }

        foreach ($this->getProductVariants($id) as $key => $variant) {
            $product->addVariant($variant);
        }

        return $product;
    }

    protected function loadMany($productIds)
    {
        $products = [];

        foreach ($productIds as $key => $id) {
            $product = $this->getProperProductInstance($id);

            $product = $this->load($product);

            $products[] = $product;
        }

        return $products;
    }

    protected function getProductVariants(int $productId): array
    {
        $prices = apply_filters('edd_purchase_variable_prices', edd_get_variable_prices( $productId ), $productId);
        $variants = [];
        if(empty($prices)) return $variants;

        if(empty(edd_has_variable_prices($productId))) return $variants;

        foreach ($prices as $key => $price) {
            $variant = new Variant();
            $variant->setName($price['name'])
                ->setId((int)$key)
                ->setPrice($price['amount'])
                ->setPurchaseLimitExhausted($this->isLimitOfPurchaseExhausted($price));
            if(isset($price[self::GTU_META_NAME])) {
                $variant->setGtu(new Gtu($price[self::GTU_META_NAME]));
            }
            $variants[] = $variant;
        }

        return $variants;
    }

    protected function isLimitOfPurchaseExhausted(array $price): bool
    {
        return isset($price['bpmj_eddcm_purchase_limit_items_left']) ? $price['bpmj_eddcm_purchase_limit_items_left'] === 0 : false;
    }

    protected function getProductThumbnail($productId)
    {
        if (in_array(get_post_thumbnail_id($productId), array('', - 1))) return null;

        return get_the_post_thumbnail_url($productId, 'post-thumbnail');
    }

    protected function getProductCategories($productId)
    {
        $course     = WPI()->courses->get_course_by_product($productId);
        $wpCategories = get_the_terms($course ? $course->ID : $productId, 'download_category');
        $categories = [];

        if ( empty( $wpCategories ) ) return $categories;

        foreach ($wpCategories as $key => $cat) {
            $category = new Category();
            $category->setName($cat->name)->setLink(get_term_link( $cat ));
            $categories[] = $category;
        }

        return $categories;
    }

    protected function getProductTags($productId)
    {
        $course     = WPI()->courses->get_course_by_product($productId);
        $wpTags = get_the_terms( $course ? $course->ID : $productId, 'download_tag' );
        $tags = [];

        if ( empty( $wpTags ) ) return $tags;

        foreach ($wpTags as $key => $wpTag) {
            $tag = new Tag();
            $tag->setName($wpTag->name)->setLink(get_term_link( $wpTag ));
            $tags[] = $tag;
        }

        return $tags;
    }

    protected function getProductExcerpt($productId)
    {
        $excerpt_length = apply_filters( 'wp_idea_excerpt_length', 30 );

        if (has_excerpt($productId)){
            return apply_filters( 'edd_downloads_excerpt', wp_trim_words( get_post_field( 'post_excerpt', $productId ), $excerpt_length ) );
        } else {
            return apply_filters( 'edd_downloads_excerpt', wp_trim_words( get_post_field( 'post_content', $productId ), $excerpt_length ) );
        }
    }

    protected function getProductPanelLink($productId)
    {
        $links = get_post_meta( $productId, 'edd_download_files', true );
        $link = is_array( $links ) ? array_shift( $links ) : array();

        return !empty($link['file']) ? $link['file'] : null;
    }

    protected function getProductLink($productId)
    {
        return get_the_permalink($productId);
    }

    protected function getIsProductInCart($productId)
    {
        return edd_item_in_cart($productId);
    }

    protected function getProductSalesStatus($productId)
    {
        $course = WPI()->courses->get_course_by_product($productId);
        
        if($course) {
            $status = WPI()->courses->get_sales_status($course->ID, $productId);
        }
        else {
            $status = $this->getNonCourseProductSalesStatus($productId);
        }

        $salesStatus = new SalesStatus();
        $salesStatus->setIsDisabled('disabled' === $status[ 'status' ]);

        if(!empty($status['reason'])) $salesStatus->setReason($status['reason']);
        if(!empty($status['reason_long'])) $salesStatus->setReasonDescription($status['reason_long']);

        return $salesStatus;

    }

    protected function getNonCourseProductSalesStatus($productId)
    {
        if ('on' == get_post_meta($productId, 'sales_disabled', true)) {
            return [
                'status' => 'disabled',
                'reason' => __('Sales disabled', BPMJ_EDDCM_DOMAIN),
                'reason_long' => __('Sales of this product are currently disabled.', BPMJ_EDDCM_DOMAIN),
            ];
        }
        
        $purchase_limit = (int)get_post_meta($productId, '_bpmj_eddcm_purchase_limit', true);
        $purchase_limit_items_left = (int)get_post_meta($productId, '_bpmj_eddcm_purchase_limit_items_left', true);
        $purchase_limit_unlimited = '1' === get_post_meta($productId, '_bpmj_eddcm_purchase_limit_unlimited', true);
        
        if ($purchase_limit > 0 && $purchase_limit_items_left <= 0 && !$purchase_limit_unlimited) {
            return [
                'status' => 'disabled',
                'reason' => __('Sold out', BPMJ_EDDCM_DOMAIN),
                'reason_long' => __('No more items can be purchased at this time.', BPMJ_EDDCM_DOMAIN),
            ];
        }

        return ['status' => 'enabled'];
    }

    protected function getProductPriceMode($productId)
    {
        return edd_single_price_option_mode($productId) ? Product::PRICE_MODE_MULTI : Product::PRICE_MODE_SINGLE;
    }

    protected function getGoStraightToCheckoutModeEnabled($productId)
    {
        //@todo: redirect to checkout jest cechą produktu czy systemu?
        $straightToCheckout = apply_filters( 'edd_download_redirect_to_checkout', edd_straight_to_checkout(), $productId, array() );

        return $straightToCheckout;
    }

    protected function getDefaultVariantId($productId)
    {
        return edd_get_default_variable_price($productId);
    }

    protected function saveGtu(int $productId, Gtu $gtu): self
    {
        update_post_meta($productId, self::GTU_META_NAME, $gtu->get_code());
        return $this;
    }

    protected function getGtu($productId): ?string
    {
        $gtu = get_post_meta($productId, self::GTU_META_NAME, true);
        return $gtu ? $gtu : null;
    }

    private function getLinkedResourceType(int $productId): string
    {
        $resource_type = get_post_meta($productId, 'wpi_resource_type', true);
        return !empty($resource_type) ? $resource_type : Resource_Type::COURSE;
    }
}
