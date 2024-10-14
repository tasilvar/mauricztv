<?php

namespace bpmj\wpidea\wolverine\product\course;

use bpmj\wpidea\Course_Bundle;
use bpmj\wpidea\wolverine\product\Product as BaseProduct;
use bpmj\wpidea\wolverine\product\Repository as BaseRepository;

class Repository extends BaseRepository
{
    protected function saveVariants($productId, $variants)
    {
        parent::saveVariants($productId, $variants);

        if (empty($variants) || 0 === $variants->count()) {
            return;
        }

        $variantsInRepositoryFormat = get_post_meta($productId, 'edd_variable_prices', true);

        foreach ($variants as $variant) {
            if($variant->getAccessDuration()) {
                $variantsInRepositoryFormat[$variant->getId()]['access_time'] = $variant->getAccessDuration();
            }
            if($variant->getAccessDurationUnit()) {
                $variantsInRepositoryFormat[$variant->getId()]['access_time_unit'] = $variant->getAccessDurationUnit();
            }
        }

        // @todo: można pominąć gdy nie było zmiany
        update_post_meta($productId, 'edd_variable_prices', $variantsInRepositoryFormat);
    }

    public function save(BaseProduct $product)
    {
        $product = parent::save($product);

        $product->updateAccessSettings();

        return $product;
    }

    public function checkIfProductBelongsToAnyBundle(int $productId): bool
    {
        $bundles = Course_Bundle::get_list();

        foreach ($bundles as $bundle) {
            if ($bundle->has_course($productId)) {
                return true;
            }
        }

        return false;
    }
}
