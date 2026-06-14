<?php

namespace Jack\RecentlyViewed\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Catalog\Helper\Data;
use Magento\Catalog\Helper\Image as ImageHelper;

class CurrentProduct implements ArgumentInterface
{
    public function __construct(
        private Data $catalogHelper,
        private ImageHelper $imageHelper,
        private PriceCurrencyInterface $priceCurrency
    ) {
    }

    private function getImageUrl(\Magento\Catalog\Model\Product $product): string
    {
        return $this->imageHelper
            ->init($product, 'product_base_image')
            ->getUrl();
    }

    public function getProductData(): array
    {
        $product = $this->catalogHelper->getProduct();

        if (!$product || !$product->getId()) {
            return [];
        }

        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'url' => $product->getProductUrl(),
            'image' => $this->getImageUrl($product),
            'price' => $this->priceCurrency->format($product->getFinalPrice(), false)
        ];
    }
}