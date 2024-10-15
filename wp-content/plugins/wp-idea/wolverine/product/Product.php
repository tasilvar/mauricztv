<?php

namespace bpmj\wpidea\wolverine\product;

use ArrayObject;
use bpmj\wpidea\resources\Resource_Type;
use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\wolverine\user\User;

class Product
{
    const PRICE_MODE_SINGLE = 'single';
    const PRICE_MODE_MULTI = 'multi';

    public $id;

    public $name;

    public $price;

    public $promotionalPrice;

    public $variants;

    public $defaultVariantId;

    public $thumbnail;

    public $panelLink;

    public $categories;

    public $tags;

    public $excerpt;

    protected $priceMode;

    protected $goStraightToCheckoutModeEnabled;

    public $isInCart;

    protected $salesStatus;

    public $productAccess;

    public $gtu;

    public string $linkedResourceType;


    public function __construct()
    {
        $this->variants = new ArrayObject();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice()
    {
        return isset($this->price) ? $this->price : null;
    }

    public function getFormattedPrice()
    {
        return $this->formatPrice( $this->getPrice() );
    }

    public function formatPrice($price)
    {
        return edd_format_amount( $price );
    }

    public function getLowestVariantPrice()
    {
        if(!$this->hasVariants()) return null;

        $variants = $this->getVariants();

        $prices = array_map(function($variant){ return floatval($variant->getPrice()); }, $variants->getArrayCopy());
        return min($prices);
    }

    public function getFormattedLowestVariantPrice()
    {
        return $this->formatPrice( $this->getLowestVariantPrice() );
    }

    public function hasPromotionalPrice()
    {
        return !is_null($this->getPromotionalPrice());
    }

    public function getPromotionalPrice()
    {
        return isset($this->promotionalPrice) ? $this->promotionalPrice : null;
    }

    public function getFormattedPromotionalPrice()
    {
        return $this->formatPrice( $this->getPromotionalPrice() );
    }

    public function setPrice($price)
    {
        if (!is_double($price) || $price !== round($price, 2)) {
            throw new \Exception('Trying to set price value other than double or in wrong format');
        }
        $this->price = $price;
        return $this;
    }

    public function getPriceForSorting()
    {
        if($this->hasVariants()) {
            return $this->getLowestVariantPrice();
        }

        if($this->hasPromotionalPrice()) {
            return $this->getPromotionalPrice();
        }

        return $this->getPrice();
    }

    public function setPromotionalPrice($promotionalPrice)
    {
        if (is_null($promotionalPrice)) {
            $this->promotionalPrice = null;
            return $this;
        }

        if (!is_double($promotionalPrice) || $promotionalPrice !== round($promotionalPrice, 2)) {
            throw new \Exception('Trying to set promotional price value other than double or in wrong format');
        }
        $this->promotionalPrice = $promotionalPrice;
        return $this;
    }

    public function initiateNewVariant()
    {
        $variant = new Variant();
        $variant->setId($this->variants->count() + 1);
        return $variant;
    }

    public function addVariant($variant)
    {
        $this->variants->append($variant);
    }

    public function getVariants()
    {
        return $this->variants;
    }

    public function getVariantProductByOptionId(string $optionId): ?Variant
    {

        foreach ($this->getVariants() as $variantProduct){
            if($variantProduct->getId() == $optionId){
                return $variantProduct;
            }
        }

        return null;
    }

    public function getDefaultVariantId()
    {
        if(!empty($this->defaultVariantId)) return $this->defaultVariantId;
        return $this->hasVariants() ? $this->getVariants()[0]->getId() : null;
    }

    public function setDefaultVariantId($id)
    {
        $this->defaultVariantId = $id;

        return $this;
    }

    public function hasVariants()
    {
        return !empty($this->variants[0]);
    }

    public function setSalesStatus(SalesStatus $status)
    {
        $this->salesStatus = $status;

        return $this;
    }

    public function salesDisabled()
    {
        $isDisabled = $this->salesStatus->getIsDisabled();

        if ($isDisabled === null) {
            return get_post_meta($this->id, 'sales_disabled', true) == 'on';
        }

        return $isDisabled;
    }

    public function setSalesDisabled($value)
    {
        $this->salesStatus->setIsDisabled($value);

        return $this;
    }

    public function getSalesDisabledReason()
    {
        return $this->salesStatus->getReason();
    }

    public function setSalesDisabledReason($salesDisabledReason)
    {
        $this->salesStatus->setReason($salesDisabledReason);

        return $this;
    }

    public function getSalesDisabledReasonDescription()
    {
        return $this->salesStatus->getReasonDescription();
    }

    public function setSalesDisabledReasonDescription($salesDisabledReasonDescription)
    {
        $this->salesStatus->setReasonDescription($salesDisabledReasonDescription);

        return $this;
    }

    public function getIsInCart()
    {
        return $this->isInCart;
    }

    public function setIsInCart($isInCart)
    {
        $this->isInCart = $isInCart;

        return $this;
    }

    public function getGoStraightToCheckoutModeEnabled()
    {
        return $this->goStraightToCheckoutModeEnabled;
    }

    public function setGoStraightToCheckoutModeEnabled($goStraightToCheckoutModeEnabled)
    {
        $this->goStraightToCheckoutModeEnabled = $goStraightToCheckoutModeEnabled;

        return $this;
    }

    public function getPriceMode()
    {
        return $this->priceMode;
    }

    public function hasPriceModeMulti()
    {
        return $this->getPriceMode() === self::PRICE_MODE_MULTI;
    }

    public function hasPriceModeSingle()
    {
        return $this->getPriceMode() === self::PRICE_MODE_SINGLE;
    }

    public function setPriceMode($priceMode)
    {
        $this->priceMode = $priceMode;

        return $this;
    }

    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function setPanelLink($link)
    {
        $this->panelLink = $link;

        return $this;
    }

    public function getPanelLink()
    {
        return (string)$this->panelLink;
    }

    public function setProductLink($link)
    {
        $this->productLink = $link;

        return $this;
    }

    public function getProductLink()
    {
        return $this->productLink;
    }

    public function getPanelOrProductLinkForUser($userId): string
    {
        if($this->getLinkedResourceType() === Resource_Type::COURSE && $this->userHasAccess($userId)) {
            return (string)$this->getPanelLink();
        }

        return $this->getProductLink();
    }

    public function hasCategories()
    {
        return !empty($this->getCategories());
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    public function hasTags()
    {
        return !empty($this->getTags());
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    public function getExcerpt()
    {
        return $this->excerpt;
    }

    public function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    public function isFree()
    {
        return $this->getPrice() == 0;
    }

    public function checkUserAccess($userId)
    {
        $productAccess = new Access($this->getId());

        return $productAccess->checkUserAccess($userId);
    }

    public function userHasAccess($userId)
    {
        $access = $this->checkUserAccess($userId);

        return 'valid' === $access[ 'status' ] || 'waiting' === $access[ 'status' ];
    }

    public function userHasNoContentAccess($userId)
    {
        $access = $this->checkUserAccess($userId);
        
        return 'valid' === $access[ 'status' ];
    }

    public function currentUserHasAccess(): bool
    {
        return $this->userHasAccess(User::getCurrentUserId());
    }

    public function currentUserHasNoContentAccess(): bool
    {
        return $this->userHasNoContentAccess(User::getCurrentUserId());
    }

    public function getConvertedPrice()
    {
        return number_format_i18n($this->getPrice(), 2);
    }

    public function setGtu(Gtu $gtu): self
    {
        $this->gtu = $gtu;
        return $this;
    }

    public function getGtu(): ?Gtu
    {
        return $this->gtu;
    }

    public function getLinkedResourceType(): string
    {
        return $this->linkedResourceType;
    }

    public function setLinkedResourceType(string $linkedResourceType): self
    {
        $this->linkedResourceType = $linkedResourceType;
        
        return $this;
    }
}
