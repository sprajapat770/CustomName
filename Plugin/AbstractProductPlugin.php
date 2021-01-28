<?php


namespace Magento360\CustomeName\Plugin;

/**
 * Class AbstractProductPlugin
 * Magento360\CustomeName\Plugin
 */
class AbstractProductPlugin
{

    protected $imageBuilder;
    protected $productFactory;

    /**
     * AbstractProductPlugin constructor.
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->imageBuilder = $imageBuilder;
        $this->productFactory = $productFactory;
    }

    public function afterGetImage(
        \Magento\Catalog\Block\Product\AbstractProduct $subject,
        $result,
        $_product,
        $imageId,
        $attributes = []
    ) {

        if ($_product->getTypeId() == 'configurable') {
            $items = $_product->getTypeInstance()->getUsedProducts($_product);
            foreach ($items as $simple) {
                //echo $simple->getImage();
                if (!empty($simple->getImage()) && $simple->getImage() != 'no_selection') {
                    echo $simple->getId();
                    $product = $this->productFactory->create();
                    $product->load($simple->getId());
                    return $this->imageBuilder->create($product, $imageId, $attributes);
                }
            }
        }
        return $result;
    }
}
